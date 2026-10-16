<?php

namespace App\Support;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;

/**
 * How buyers actually type a steel product (B06).
 *
 * "saria 12mm", "patti", "gol pipe", "angle 50x50x5", "tmt fe500d rate" all
 * have to land on a catalog that names things "MS TMT", "MS FLAT",
 * "MS Pipe - Round". A plain LIKE on the whole phrase found none of them.
 *
 * The query is read into:
 *  - required terms: product words after trade/Hindi synonyms are mapped
 *    (saria → tmt, patti → flat). Each must match the product name, its
 *    category / sub-category, or a brand sold under it;
 *  - hints: sizes (12 mm, 50x50x5), grades (Fe 500D, E250, IS 2062) and
 *    shape words like "bar" — they rank results, and only filter when the
 *    query has nothing else to go on;
 *  - noise: "ms", "steel", "rate", "price", "ka", "bhav" … dropped.
 */
final class ProductSearch
{
    /** canonical term => what people type for it. Whole words only. */
    public const SYNONYMS = [
        'tmt'       => ['saria', 'sariya', 'sarya', 'sariyaa', 'sareeya', 'saariya', 'sarria', 'rebar', 'rebars', 'tor', 'torsteel', 'tor steel'],
        'flat'      => ['patti', 'patta', 'pati', 'flats'],
        'round'     => ['gol', 'rounds'],
        'angle'     => ['angel', 'angles', 'l angle', 'angle iron', 'equal angle'],
        'channel'   => ['chanel', 'channels', 'c channel', 'u channel', 'ismc'],
        'beam'      => ['girder', 'gardar', 'joist', 'i beam', 'h beam', 'ismb', 'beams'],
        'pipe'      => ['pipes', 'tube', 'tubes', 'tubing', 'nali', 'hollow section'],
        'square'    => ['chokor', 'chaukor', 'squares'],
        'rectangle' => ['rectangular', 'rect', 'rhs'],
        'billet'    => ['billets', 'bilet'],
        'ingot'     => ['ingots', 'pencil ingot'],
        'sponge'    => ['dri', 'sponge iron'],
        'sheet'     => ['chadar', 'chaadar', 'sheets'],
        'wire'      => ['wire rod', 'wires'],
        // A category, not a shape: kept whole so "finished" alone does not
        // also match SEMI FINISHED.
        'semifinished' => ['semi finished', 'semi finish'],
    ];

    /** How a canonical term reads back to the buyer. */
    public const LABELS = [
        'tmt' => 'TMT', 'flat' => 'Flat', 'round' => 'Round', 'angle' => 'Angle',
        'channel' => 'Channel', 'beam' => 'Beam', 'pipe' => 'Pipe', 'square' => 'Square',
        'rectangle' => 'Rectangle', 'billet' => 'Billet', 'ingot' => 'Ingot',
        'sponge' => 'Sponge Iron', 'sheet' => 'Sheet', 'wire' => 'Wire Rod',
        'semifinished' => 'Semi-finished', 'finished' => 'Finished',
    ];

    /**
     * A product name as space-separated words, with a leading space, so
     * "% angle%" finds "MS ANGLE" but not "MS Pipe - Rectangle".
     */
    private const NAME_WORDS = "CONCAT(' ', REPLACE(REPLACE(REPLACE(REPLACE(LOWER(name), '-', ' '), '(', ' '), '/', ' '), ',', ' '))";

    /** A category name with spaces and hyphens removed: "SEMI FINISHED" -> "semifinished". */
    private const COMPACT_NAME = "REPLACE(REPLACE(LOWER(name), ' ', ''), '-', '')";

    private const STOPWORDS = [
        'ms', 'mild', 'steel', 'iron', 'rate', 'rates', 'price', 'prices', 'today', 'todays',
        'bhav', 'bhaav', 'ka', 'ki', 'ke', 'ko', 'the', 'of', 'for', 'in', 'buy', 'best',
        'online', 'per', 'kg', 'ton', 'tons', 'tonne', 'mt', 'near', 'me', 'and', 'with',
        'wala', 'wali', 'supplier', 'suppliers', 'dealer', 'dealers', 'manufacturer',
    ];

    /** Shape words that tell "round bar" from "round pipe" without excluding either. */
    private const SOFT_WORDS = ['bar', 'bars', 'rod', 'rods'];

    /** @var list<list<string>> each entry: alternatives, any of which satisfies it */
    public array $required = [];

    /** @var list<string> */
    public array $hints = [];

    /** @var list<string> e.g. "12mm", "50x50x5" */
    public array $sizes = [];

    /** @var list<string> compact, e.g. "fe500d", "e250", "is2062" */
    public array $grades = [];

    /** A trade/Hindi word was read as a catalog term. */
    public bool $synonymApplied = false;

    private function __construct(public readonly string $raw)
    {
    }

    /**
     * @param bool $prefix treat the last word as unfinished (autocomplete):
     *                     "sar" also offers TMT, "pat" also offers Flat.
     */
    public static function parse(string $raw, bool $prefix = false): self
    {
        $s = new self(trim($raw));

        $q = mb_strtolower($s->raw);
        $q = str_replace(['×', '*'], 'x', $q);
        $q = preg_replace('/[^\p{L}\p{N}.\s]+/u', ' ', $q) ?? '';

        // Grades first, so "fe 500" is not read as a size.
        $q = preg_replace_callback('/\b(fe)\s*(\d{3})\s*(sd|d|s)?\b/u', function ($m) use ($s) {
            $s->grades[] = 'fe' . $m[2] . ($m[3] ?? '');
            return ' ';
        }, $q);
        $q = preg_replace_callback('/\b(e|is)\s*(\d{3,5})\b/u', function ($m) use ($s) {
            $s->grades[] = $m[1] . $m[2];
            return ' ';
        }, $q);

        $q = preg_replace_callback('/\b\d+(?:\.\d+)?\s*x\s*\d+(?:\.\d+)?(?:\s*x\s*\d+(?:\.\d+)?)?\b/u', function ($m) use ($s) {
            $s->sizes[] = preg_replace('/\s+/', '', $m[0]);
            return ' ';
        }, $q);
        $q = preg_replace_callback('/\b(\d+(?:\.\d+)?)\s*(mm|cm|inch|in|m)?\b/u', function ($m) use ($s) {
            $s->sizes[] = $m[1] . ($m[2] ?? '');
            return ' ';
        }, $q);

        // Multi-word variants before single words ("tor steel", "angle iron").
        $phrases = [];
        foreach (self::SYNONYMS as $canonical => $variants) {
            foreach ($variants as $v) {
                if (str_contains($v, ' ')) {
                    $phrases[$v] = $canonical;
                }
            }
        }
        uksort($phrases, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));
        foreach ($phrases as $phrase => $canonical) {
            $q = preg_replace('/\b' . preg_quote($phrase, '/') . '\b/u', ' ' . $canonical . ' ', $q, -1, $n);
            $s->synonymApplied = $s->synonymApplied || $n > 0;
        }

        $words = array_values(array_filter(preg_split('/\s+/u', $q) ?: [], fn ($w) => $w !== '' && $w !== '.'));
        $last = count($words) - 1;

        foreach ($words as $i => $word) {
            if ($prefix && $i === $last && ! in_array($word, self::STOPWORDS, true)) {
                $canonical = self::canonical($word);
                $s->synonymApplied = $s->synonymApplied || ($canonical !== null && $canonical !== $word);
                $s->required[] = array_values(array_unique([$canonical ?? $word, ...self::completions($word)]));
                continue;
            }
            if (in_array($word, self::STOPWORDS, true)) {
                continue;
            }
            if (in_array($word, self::SOFT_WORDS, true)) {
                $s->hints[] = rtrim($word, 's');
                continue;
            }

            $canonical = self::canonical($word);
            if ($canonical !== null && $canonical !== $word) {
                $s->synonymApplied = true;
            }
            $s->required[] = [$canonical ?? $word];
        }

        $s->required = array_values(array_unique($s->required, SORT_REGULAR));

        return $s;
    }

    /** The catalog term a typed word stands for, if it is one. */
    public static function canonical(string $word): ?string
    {
        if (isset(self::SYNONYMS[$word])) {
            return $word;
        }
        foreach (self::SYNONYMS as $canonical => $variants) {
            if (in_array($word, $variants, true)) {
                return $canonical;
            }
        }

        return null;
    }

    /** Catalog terms an unfinished word could be heading for (≥ 2 letters). */
    private static function completions(string $word): array
    {
        if (mb_strlen($word) < 2) {
            return [];
        }

        $out = [];
        foreach (self::SYNONYMS as $canonical => $variants) {
            foreach ([$canonical, ...$variants] as $v) {
                if (str_starts_with($v, $word)) {
                    $out[] = $canonical;
                    break;
                }
            }
        }

        return $out;
    }

    /** Nothing but noise ("ms steel rate"): treat as no search at all. */
    public function isEmpty(): bool
    {
        return ! $this->required && ! $this->hints && ! $this->sizes && ! $this->grades;
    }

    /** "TMT · 12mm · Fe 500D" — what the query was understood as. */
    public function interpreted(): string
    {
        $parts = [];
        foreach ($this->required as $alternatives) {
            $parts[] = self::LABELS[$alternatives[0]] ?? ucfirst($alternatives[0]);
        }
        foreach ($this->hints as $h) {
            $parts[] = ucfirst($h);
        }
        foreach ($this->sizes as $size) {
            $parts[] = $size;
        }
        foreach ($this->grades as $g) {
            $parts[] = preg_replace_callback('/^(fe|e|is)(\d+)(\w*)$/', fn ($m) => ($m[1] === 'is' ? 'IS ' : ucfirst($m[1]) . ' ') . $m[2] . strtoupper($m[3]), $g);
        }

        return implode(' · ', $parts);
    }

    /**
     * Constrains and ranks a CommodityProduct query. Ranking is added as an
     * ORDER BY, so call this before any other ordering.
     */
    public function apply(Builder $query): Builder
    {
        if ($this->isEmpty()) {
            return $query;
        }

        foreach ($this->required as $alternatives) {
            $query->where(function (Builder $q) use ($alternatives) {
                foreach ($alternatives as $term) {
                    $like = '%' . $term . '%';
                    // Word start in the name ("pipe" finds "Pipes", not "Rectangle"
                    // for "angle"); whole name for a category, so "finished"
                    // is not "SEMI FINISHED" (B07).
                    $q->orWhereRaw(self::NAME_WORDS . ' LIKE ?', ['% ' . $term . '%'])
                        ->orWhereHas('getCategory', fn ($c) => $c->whereRaw(self::COMPACT_NAME . ' = ?', [$term]))
                        ->orWhereHas('getSubCategory', fn ($c) => $c->whereRaw(self::COMPACT_NAME . ' = ?', [$term]));

                    if (mb_strlen($term) >= 3) {
                        // "tata tiscon", "jindal tmt": brands sold under the product.
                        $q->orWhereHas('sellerListings', fn ($l) => $l->where('status', 'active')
                            ->where(fn ($b) => $b->where('brand_name', 'like', $like)->orWhere('make', 'like', $like)));

                        // brand_id is a TEXT column holding ["1","2"] (or [1,2]);
                        // JSON_CONTAINS would throw on any malformed row.
                        foreach (Brand::where('name', 'like', $like)->limit(10)->pluck('id') as $brandId) {
                            $q->orWhere('brand_id', 'like', '%"' . (int) $brandId . '"%')
                                ->orWhereRaw('brand_id REGEXP ?', ['[\\[,] *' . (int) $brandId . ' *[\\],]']);
                        }
                    }
                }
            });
        }

        // With no product word, a grade or size is all there is to go on.
        if (! $this->required) {
            $query->where(function (Builder $q) {
                foreach ([...$this->grades, ...$this->sizes, ...$this->hints] as $term) {
                    $q->orWhereRaw(self::NAME_WORDS . ' LIKE ?', ['% ' . $term . '%'])
                        ->orWhereRaw("REPLACE(LOWER(COALESCE(quality, '')), ' ', '') LIKE ?", ['%' . $term . '%'])
                        ->orWhereRaw("REPLACE(LOWER(COALESCE(size, '')), ' ', '') LIKE ?", ['%' . $term . '%']);
                }
            });
        }

        // Rank: product word in the name beats a category/brand hit; shape
        // words and grades in the name or grade list break ties.
        $cases = [];
        $bindings = [];
        foreach ($this->required as $alternatives) {
            $cases[] = 'CASE WHEN ' . self::NAME_WORDS . ' LIKE ? THEN 2 ELSE 0 END';
            $bindings[] = '% ' . $alternatives[0] . '%';
        }
        foreach ($this->hints as $h) {
            $cases[] = 'CASE WHEN ' . self::NAME_WORDS . ' LIKE ? THEN 1 ELSE 0 END';
            $bindings[] = '% ' . $h . '%';
        }
        foreach ($this->grades as $g) {
            $cases[] = "CASE WHEN REPLACE(LOWER(COALESCE(quality, '')), ' ', '') LIKE ? THEN 1 ELSE 0 END";
            $bindings[] = '%' . $g . '%';
        }
        if ($cases) {
            $query->orderByRaw('(' . implode(' + ', $cases) . ') DESC', $bindings);
        }

        return $query;
    }
}
