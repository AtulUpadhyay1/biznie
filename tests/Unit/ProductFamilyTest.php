<?php

namespace Tests\Unit;

use App\Models\CommodityProduct;
use App\Support\ProductFamily;
use PHPUnit\Framework\TestCase;

/** Each product gets its own family's reference content (B11). */
class ProductFamilyTest extends TestCase
{
    private function product(string $name): CommodityProduct
    {
        $p = new CommodityProduct();
        $p->name = $name;

        return $p;
    }

    public function test_family_is_read_from_the_product_name(): void
    {
        $cases = [
            'MS TMT' => 'tmt',
            'MS Pipe - Square' => 'hollow_section',
            'MS Pipe - Rectangle' => 'hollow_section',
            'MS Pipe - Round' => 'round_pipe',
            'MS ROUND BAR' => 'round_bar',
            'MS SQUARE BAR' => 'square_bar',
            'MS ANGLE' => 'angle',
            'MS CHANNEL' => 'channel',
            'MS FLAT' => 'flat',
            'MS BILLET' => 'billet',
            'SPONGE IRON' => 'sponge_iron',
            'HR Sheet' => 'sheet',
            'Copper wire' => 'generic',
        ];
        foreach ($cases as $name => $family) {
            $this->assertSame($family, ProductFamily::detect($this->product($name)), $name);
        }
    }

    public function test_tmt_no_longer_shows_hr_sheet_content(): void
    {
        $ref = ProductFamily::reference($this->product('MS TMT'));

        $this->assertSame('IS 1786', $ref['standard']);
        $this->assertStringNotContainsString('Hot Rolled', $ref['overview']);
        $this->assertNotContains('Thickness', array_column($ref['quick_specs'], 'label'));
    }

    public function test_grade_tables_narrow_to_the_grades_sold(): void
    {
        $ref = ProductFamily::reference($this->product('MS TMT'), ['Fe 500', 'Fe 500D']);
        $mech = collect($ref['sections'])->firstWhere('id', 'mechanical-properties');

        $this->assertSame(['Fe 500', 'Fe 500D'], array_column($mech['rows'], 'grade'));
        $this->assertArrayNotHasKey('by_grade', $mech);
    }

    public function test_sizes_on_offer_come_from_the_listing(): void
    {
        $size = fn ($v) => ['value' => [['name' => 'Size', 'value' => (string) $v, 'unit' => ['short_name' => 'MM']]]];
        $ref = ProductFamily::reference($this->product('MS TMT'), [], [$size(8), $size(12), $size(32)]);

        $this->assertSame(['label' => 'Sizes on offer', 'value' => '8 – 32 mm'], $ref['quick_specs'][0]);
        $this->assertNotContains('Diameter', array_column($ref['quick_specs'], 'label'));
    }
}
