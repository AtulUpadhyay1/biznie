<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\CommodityProduct;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Catalog search end to end (B05/B06/B07), against products made here so the
 * assertions do not depend on what the working database holds.
 */
class ProductSearchApiTest extends TestCase
{
    use DatabaseTransactions;

    private string $tag;

    /** @var array<string, CommodityProduct> */
    private array $made = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->tag = 'q' . substr(uniqid(), -6);

        $finished = $this->category('FINISHED');
        $semi = $this->category('SEMI FINISHED');

        $this->made['tmt']   = $this->product("QA TMT {$this->tag}", $finished, ['Fe 415', 'Fe 500D']);
        $this->made['flat']  = $this->product("QA FLAT {$this->tag}", $finished);
        $this->made['angle'] = $this->product("QA ANGLE {$this->tag}", $finished);
        $this->made['rect']  = $this->product("QA Pipe - Rectangle {$this->tag}", $finished);
        $this->made['round'] = $this->product("QA Pipe - Round {$this->tag}", $finished);
        $this->made['bar']   = $this->product("QA ROUND BAR {$this->tag}", $finished);
        $this->made['billet'] = $this->product("QA BILLET {$this->tag}", $semi);
    }

    private function category(string $name): ProductCategory
    {
        $existing = ProductCategory::where('name', $name)->first();
        if ($existing) {
            return $existing;
        }
        $c = new ProductCategory();
        $c->name = $name;
        $c->slug = \Illuminate\Support\Str::slug($name) . '-' . $this->tag;
        $c->status = 'active';
        $c->save();

        return $c;
    }

    private function product(string $name, ProductCategory $category, array $quality = []): CommodityProduct
    {
        $p = new CommodityProduct();
        $p->name        = $name;
        $p->slug        = \Illuminate\Support\Str::slug($name);
        $p->category_id = $category->id;
        $p->status      = 'active';
        $p->quality     = $quality;
        $p->brand_id    = [];
        $p->save();

        return $p;
    }

    /** Names of our products returned for `$q`, in result order. */
    private function search(string $q): array
    {
        $names = collect($this->getJson('/api/v2/products?per_page=100&search=' . urlencode($q))->assertOk()->json('data'))
            ->pluck('name');

        return $names->filter(fn ($n) => str_contains($n, $this->tag))->values()->all();
    }

    public function test_synonyms_find_the_catalog_product(): void
    {
        $this->assertSame(["QA TMT {$this->tag}"], $this->search("saria 12mm {$this->tag}"));
        $this->assertSame(["QA FLAT {$this->tag}"], $this->search("patti {$this->tag}"));
        $this->assertSame(["QA Pipe - Round {$this->tag}"], $this->search("gol pipe {$this->tag}"));
    }

    public function test_words_match_from_their_start(): void
    {
        // "angle" is inside "Rectangle"; it must not drag the pipe in.
        $this->assertSame(["QA ANGLE {$this->tag}"], $this->search("angle 50 {$this->tag}"));
    }

    public function test_shape_word_ranks_without_excluding(): void
    {
        $this->assertSame(
            ["QA ROUND BAR {$this->tag}", "QA Pipe - Round {$this->tag}"],
            $this->search("round bar {$this->tag}")
        );
    }

    public function test_finished_does_not_include_semi_finished(): void
    {
        $finished = $this->search("finished {$this->tag}");
        $this->assertNotContains("QA BILLET {$this->tag}", $finished);
        $this->assertContains("QA TMT {$this->tag}", $finished);

        $this->assertSame(["QA BILLET {$this->tag}"], $this->search("semi finished {$this->tag}"));
    }

    public function test_brand_from_the_brand_master_finds_its_product(): void
    {
        $brand = new Brand();
        $brand->name = "Shoor{$this->tag}";
        $brand->status = '1';
        $brand->save();

        $this->made['tmt']->brand_id = [(string) $brand->id];
        $this->made['tmt']->save();

        $this->assertSame(["QA TMT {$this->tag}"], $this->search("shoor{$this->tag}"));
    }

    public function test_response_says_how_the_query_was_read(): void
    {
        $this->getJson('/api/v2/products?search=' . urlencode('saria 12mm'))
            ->assertOk()
            ->assertJsonPath('meta.search.interpreted', 'TMT · 12mm')
            ->assertJsonPath('meta.search.synonym_applied', true);
    }

    public function test_suggest_completes_the_last_word(): void
    {
        $names = collect($this->getJson('/api/v2/products/suggest?q=' . urlencode("{$this->tag} sar"))->assertOk()->json('data'))
            ->pluck('name')->all();
        $this->assertContains("QA TMT {$this->tag}", $names);

        $this->getJson('/api/v2/products/suggest?q=s')->assertOk()->assertJsonCount(0, 'data');
    }
}
