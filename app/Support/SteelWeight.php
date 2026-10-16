<?php

namespace App\Support;

/**
 * Theoretical weight of mild-steel sections (density 7.85 g/cm³), in the units
 * the trade quotes them in (B12).
 *
 * Weight used to be typed as free text ("test", "100"), so nothing could check
 * it or convert pieces to tonnes. These are the standard IS / mill-handbook
 * formulas; the storefront mirrors them in src/lib/steelWeight.ts.
 *
 * All dimensions in millimetres, lengths of sheets in millimetres.
 */
final class SteelWeight
{
    /** TMT bar / round bar: D² / 162 kg per metre. */
    public static function roundBarKgPerMetre(float $diameterMm): float
    {
        return round(($diameterMm ** 2) / 162, 3);
    }

    /** Square bar: a² × 0.00785 kg per metre. */
    public static function squareBarKgPerMetre(float $sideMm): float
    {
        return round(($sideMm ** 2) * 0.00785, 3);
    }

    /** Flat: width × thickness × 0.00785 kg per metre. */
    public static function flatKgPerMetre(float $widthMm, float $thicknessMm): float
    {
        return round($widthMm * $thicknessMm * 0.00785, 3);
    }

    /** Equal angle: (2W − T) × T × 0.00785 kg per metre (root radius ignored). */
    public static function equalAngleKgPerMetre(float $legMm, float $thicknessMm): float
    {
        return round((2 * $legMm - $thicknessMm) * $thicknessMm * 0.00785, 3);
    }

    /** Round pipe (ERW / seamless): (OD − T) × T × 0.02466 kg per metre. */
    public static function roundPipeKgPerMetre(float $outerDiameterMm, float $thicknessMm): float
    {
        return round(($outerDiameterMm - $thicknessMm) * $thicknessMm * 0.02466, 3);
    }

    /** Sheet / plate: L × W × T × 7.85 / 10⁶ kg per piece (all in mm). */
    public static function sheetKgPerPiece(float $lengthMm, float $widthMm, float $thicknessMm): float
    {
        return round($lengthMm * $widthMm * $thicknessMm * 7.85 / 1_000_000, 3);
    }

    /** Pieces of a given length that make up `$tonnes` MT of a section. */
    public static function piecesPerTonnes(float $kgPerMetre, float $lengthM, float $tonnes = 1): int
    {
        $kgPerPiece = $kgPerMetre * $lengthM;

        return $kgPerPiece > 0 ? (int) floor($tonnes * 1000 / $kgPerPiece) : 0;
    }
}
