<?php

namespace Tests\Unit;

use App\Support\ProductSearch;
use PHPUnit\Framework\TestCase;

/** How buyer queries are read (B06) — no database needed. */
class ProductSearchTest extends TestCase
{
    public function test_trade_and_hindi_words_map_to_catalog_terms(): void
    {
        $this->assertSame([['tmt']], ProductSearch::parse('saria')->required);
        $this->assertSame([['tmt']], ProductSearch::parse('Sariya')->required);
        $this->assertSame([['flat']], ProductSearch::parse('patti')->required);
        $this->assertSame([['round'], ['pipe']], ProductSearch::parse('gol pipe')->required);
        $this->assertSame([['tmt']], ProductSearch::parse('tor steel')->required);
        $this->assertTrue(ProductSearch::parse('patti')->synonymApplied);
        $this->assertFalse(ProductSearch::parse('flat')->synonymApplied);
    }

    public function test_sizes_and_grades_are_hints_not_required_words(): void
    {
        $s = ProductSearch::parse('saria 12mm');
        $this->assertSame([['tmt']], $s->required);
        $this->assertSame(['12mm'], $s->sizes);
        $this->assertSame('TMT · 12mm', $s->interpreted());

        $s = ProductSearch::parse('angle 50 x 50 x 5');
        $this->assertSame([['angle']], $s->required);
        $this->assertSame(['50x50x5'], $s->sizes);

        $s = ProductSearch::parse('TMT Fe 500D rate');
        $this->assertSame([['tmt']], $s->required);
        $this->assertSame(['fe500d'], $s->grades);
        $this->assertSame('TMT · Fe 500D', $s->interpreted());

        $this->assertSame(['is2062'], ProductSearch::parse('angle IS 2062')->grades);
    }

    public function test_noise_words_are_dropped(): void
    {
        $this->assertTrue(ProductSearch::parse('ms steel rate today')->isEmpty());
        $this->assertSame([['channel']], ProductSearch::parse('MS channel ka bhav')->required);
        $this->assertSame([['round']], ProductSearch::parse('round bar')->required);
        $this->assertSame(['bar'], ProductSearch::parse('round bar')->hints);
    }

    public function test_semi_finished_stays_one_term(): void
    {
        $this->assertSame([['semifinished']], ProductSearch::parse('semi-finished')->required);
        $this->assertSame([['finished']], ProductSearch::parse('finished')->required);
    }

    public function test_autocomplete_reads_the_last_word_as_unfinished(): void
    {
        $this->assertContains('tmt', ProductSearch::parse('sar', prefix: true)->required[0]);
        $this->assertContains('flat', ProductSearch::parse('pat', prefix: true)->required[0]);
        // The typed fragment stays as one alternative next to its completion.
        $this->assertSame([['round'], ['pip', 'pipe']], ProductSearch::parse('gol pip', prefix: true)->required);
    }
}
