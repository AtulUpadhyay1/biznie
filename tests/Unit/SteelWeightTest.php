<?php

namespace Tests\Unit;

use App\Support\SteelWeight;
use PHPUnit\Framework\TestCase;

/** Reference values from the standard IS 1786 / mill section tables. */
class SteelWeightTest extends TestCase
{
    public function test_tmt_kg_per_metre_matches_is_1786_table(): void
    {
        $this->assertEqualsWithDelta(0.395, SteelWeight::roundBarKgPerMetre(8), 0.001);
        $this->assertEqualsWithDelta(0.889, SteelWeight::roundBarKgPerMetre(12), 0.001);
        $this->assertEqualsWithDelta(1.580, SteelWeight::roundBarKgPerMetre(16), 0.001);
        $this->assertEqualsWithDelta(6.321, SteelWeight::roundBarKgPerMetre(32), 0.001);
    }

    public function test_flat_square_angle_pipe_and_sheet(): void
    {
        $this->assertEqualsWithDelta(1.963, SteelWeight::flatKgPerMetre(50, 5), 0.001);
        $this->assertEqualsWithDelta(3.140, SteelWeight::squareBarKgPerMetre(20), 0.001);
        // ISA 50×50×5: table 3.8 kg/m; the root-radius-free formula gives 3.73.
        $this->assertEqualsWithDelta(3.729, SteelWeight::equalAngleKgPerMetre(50, 5), 0.001);
        // 48.3 OD × 3.2 mm pipe: ~3.56 kg/m.
        $this->assertEqualsWithDelta(3.559, SteelWeight::roundPipeKgPerMetre(48.3, 3.2), 0.001);
        // 2500 × 1250 × 2 mm sheet: 49.06 kg.
        $this->assertEqualsWithDelta(49.063, SteelWeight::sheetKgPerPiece(2500, 1250, 2), 0.001);
    }

    public function test_pieces_per_tonne(): void
    {
        // 12 mm TMT, 12 m bars: 1000 / (0.889 × 12) = 93 bars per MT.
        $this->assertSame(93, SteelWeight::piecesPerTonnes(SteelWeight::roundBarKgPerMetre(12), 12));
    }
}
