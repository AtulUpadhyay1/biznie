<?php

namespace App\Support;

use App\Models\CommodityProduct;

/**
 * Reference content per product family (B11, B12).
 *
 * Every product page used to show one hard-coded HR-sheet template — IS 2062,
 * thickness × width, a "Hot Rolled Steel Sheet Manufacturing" video — on TMT,
 * pipes and billets alike. The catalog has no spec columns, so the reference
 * data lives here, one template per family, and is narrowed by what the
 * product actually carries (the grades it sells, the sizes on offer).
 *
 * Values are the standard's limits where the family has one (IS 1786 for TMT,
 * IS 2062 for structurals, IS 4923 / IS 1239 for pipes). Where no single
 * standard applies (billet, sponge iron) they are labelled "typical", and the
 * page carries a "confirm with seller / MTC" disclaimer.
 */
final class ProductFamily
{
    private const GRADE_COLUMNS_MECH = [
        ['key' => 'grade', 'label' => 'Grade'],
        ['key' => 'yield', 'label' => 'Yield Strength (Min.)'],
        ['key' => 'tensile', 'label' => 'Tensile Strength (Min.)'],
        ['key' => 'elongation', 'label' => 'Elongation (Min.)'],
    ];

    private const DIMENSION_COLUMNS = [
        ['key' => 'parameter', 'label' => 'Parameter'],
        ['key' => 'specification', 'label' => 'Specification'],
        ['key' => 'tolerance', 'label' => 'Tolerance'],
        ['key' => 'standard', 'label' => 'Standard'],
    ];

    private const DISPATCH = [
        'Dispatch: 24 – 72 hours, subject to availability',
        'Weighment: mill / factory weighbridge; re-check at destination allowed',
    ];

    /** Family key for a catalog product, read from its name. */
    public static function detect(CommodityProduct $product): string
    {
        $words = ' ' . preg_replace('/[^a-z0-9]+/', ' ', mb_strtolower((string) $product->name)) . ' ';
        $has = fn (string ...$terms) => (bool) array_filter($terms, fn ($t) => str_contains($words, " {$t} ") || str_contains($words, " {$t}s "));

        return match (true) {
            $has('tmt', 'rebar', 'saria', 'sariya')                  => 'tmt',
            $has('pipe', 'tube') && $has('square', 'rectangle', 'rectangular', 'rhs', 'shs', 'hollow') => 'hollow_section',
            $has('pipe', 'tube')                                     => 'round_pipe',
            $has('sponge', 'dri')                                    => 'sponge_iron',
            $has('billet')                                           => 'billet',
            $has('ingot')                                            => 'ingot',
            $has('angle')                                            => 'angle',
            $has('channel', 'ismc')                                  => 'channel',
            $has('beam', 'joist', 'girder', 'ismb')                  => 'beam',
            $has('flat', 'patti')                                    => 'flat',
            $has('round')                                            => 'round_bar',
            $has('square')                                           => 'square_bar',
            $has('sheet', 'plate', 'coil', 'hr', 'cr')               => 'sheet',
            default                                                  => 'generic',
        };
    }

    /**
     * @param list<string> $grades  grade names the product sells (e.g. "Fe 500D")
     * @param list<array>  $variations resolved variation rows (sizes on offer)
     */
    public static function reference(CommodityProduct $product, array $grades = [], array $variations = []): array
    {
        $family = self::detect($product);
        $t = self::template($family);

        $sizes = self::sizesOnOffer($variations);
        $quick = $t['quick_specs'];
        if ($sizes) {
            // What sellers actually list beats the standard's full range.
            $quick = array_values(array_filter($quick, fn ($q) => ! in_array($q['label'], ['Diameter', 'Size', 'Sizes'], true)));
            array_unshift($quick, ['label' => 'Sizes on offer', 'value' => $sizes]);
        }

        return [
            'family'         => $family,
            'label'          => $t['label'],
            'standard'       => $t['standard'],
            'overview'       => $t['overview'],
            'features'       => $t['features'],
            'quick_specs'    => $quick,
            'sections'       => array_values(array_map(
                fn ($s) => self::narrowToGrades($s, $grades),
                $t['sections']
            )),
            'applications'   => $t['applications'],
            'packaging'      => [...($t['packaging'] ?? []), ...self::DISPATCH],
            'certifications' => $t['certifications'],
        ];
    }

    /** A grade table shows only the grades this product is sold in, when known. */
    private static function narrowToGrades(array $section, array $grades): array
    {
        if (! $grades || ! ($section['by_grade'] ?? false)) {
            unset($section['by_grade']);

            return $section;
        }

        $norm = fn ($g) => preg_replace('/\s+/', '', mb_strtolower((string) $g));
        $sold = array_map($norm, $grades);
        $rows = array_values(array_filter($section['rows'], fn ($r) => in_array($norm($r['grade'] ?? ''), $sold, true)));

        unset($section['by_grade']);
        if ($rows) {
            $section['rows'] = $rows;
        }

        return $section;
    }

    /** "8 – 32 MM" from the sizes sellers list, when they share one unit. */
    private static function sizesOnOffer(array $variations): ?string
    {
        $values = [];
        $unit = null;
        foreach ($variations as $v) {
            foreach ((array) ($v['value'] ?? []) as $attr) {
                if (strcasecmp((string) ($attr['name'] ?? ''), 'size') !== 0 || ! is_numeric($attr['value'] ?? null)) {
                    continue;
                }
                $u = $attr['unit']['short_name'] ?? '';
                if ($unit !== null && $u !== $unit) {
                    return null;
                }
                $unit = $u;
                $values[] = (float) $attr['value'];
            }
        }
        if (! $values) {
            return null;
        }

        $fmt = fn ($n) => rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
        $min = min($values);
        $max = max($values);

        return trim(($min === $max ? $fmt($min) : $fmt($min) . ' – ' . $fmt($max)) . ' ' . mb_strtolower((string) $unit));
    }

    private static function structuralSections(array $dimensions): array
    {
        return [
            [
                'id' => 'specifications', 'title' => 'Dimensions & Tolerance',
                'note' => 'Rolling tolerances as per IS 1852; sectional weights as per IS 808.',
                'columns' => self::DIMENSION_COLUMNS, 'rows' => $dimensions,
            ],
            [
                'id' => 'chemical-composition', 'title' => 'Chemical Composition', 'subtitle' => 'Max %, as per IS 2062',
                'note' => 'CE = carbon equivalent. Heat analysis is on the mill test certificate.',
                'columns' => [
                    ['key' => 'grade', 'label' => 'Grade'], ['key' => 'c', 'label' => 'C'], ['key' => 'mn', 'label' => 'Mn'],
                    ['key' => 's', 'label' => 'S'], ['key' => 'p', 'label' => 'P'], ['key' => 'si', 'label' => 'Si'], ['key' => 'ce', 'label' => 'CE'],
                ],
                'rows' => [
                    ['grade' => 'E250', 'c' => '0.23', 'mn' => '1.50', 's' => '0.045', 'p' => '0.045', 'si' => '0.40', 'ce' => '0.42'],
                    ['grade' => 'E350', 'c' => '0.20', 'mn' => '1.55', 's' => '0.045', 'p' => '0.045', 'si' => '0.45', 'ce' => '0.47'],
                ],
                'by_grade' => true,
            ],
            [
                'id' => 'mechanical-properties', 'title' => 'Mechanical Properties', 'subtitle' => 'As per IS 2062',
                'note' => 'Yield strength for thickness below 20 mm.',
                'columns' => self::GRADE_COLUMNS_MECH,
                'rows' => [
                    ['grade' => 'E250', 'yield' => '250 MPa', 'tensile' => '410 MPa', 'elongation' => '23%'],
                    ['grade' => 'E350', 'yield' => '350 MPa', 'tensile' => '490 MPa', 'elongation' => '22%'],
                ],
                'by_grade' => true,
            ],
        ];
    }

    private static function structural(string $label, string $overview, array $dimensions, array $applications, array $quick): array
    {
        return [
            'label'    => $label,
            'standard' => 'IS 2062',
            'overview' => $overview,
            'features' => [
                ['key' => 'strength', 'label' => 'Structural Strength'],
                ['key' => 'weld', 'label' => 'Easy to Weld'],
                ['key' => 'precision', 'label' => 'Uniform Section'],
                ['key' => 'recycle', 'label' => 'Recyclable'],
            ],
            'quick_specs'    => [['label' => 'Standard', 'value' => 'IS 2062 E250 / E350'], ...$quick],
            'sections'       => self::structuralSections($dimensions),
            'applications'   => $applications,
            'packaging'      => ['Packaging: bundles, strapped'],
            'certifications' => [
                'Manufactured as per IS 2062',
                'Mill Test Certificate (MTC) with every dispatch',
                'Third-party inspection on request',
                'GST-compliant, verified suppliers',
            ],
        ];
    }

    private static function template(string $family): array
    {
        $dim = fn ($parameter, $specification, $tolerance = '—', $standard = '—') => compact('parameter', 'specification', 'tolerance', 'standard');

        return match ($family) {
            'tmt' => [
                'label'    => 'TMT Bar',
                'standard' => 'IS 1786',
                'overview' => 'TMT (Thermo-Mechanically Treated) bars are reinforcement bars for RCC work. Quenching and self-tempering give a hard outer layer and a ductile core, so the bar combines high strength with bendability and good weldability.',
                'features' => [
                    ['key' => 'strength', 'label' => 'High Strength'],
                    ['key' => 'ductility', 'label' => 'Ductile Core'],
                    ['key' => 'weld', 'label' => 'Weldable'],
                    ['key' => 'bend', 'label' => 'Bend / Rebend Tested'],
                    ['key' => 'recycle', 'label' => 'Recyclable'],
                ],
                'quick_specs' => [
                    ['label' => 'Standard', 'value' => 'IS 1786'],
                    ['label' => 'Diameter', 'value' => '8 – 32 mm'],
                    ['label' => 'Length', 'value' => '12 m'],
                ],
                'sections' => [
                    [
                        'id' => 'specifications', 'title' => 'Dimensions & Tolerance',
                        'note' => 'Mass tolerance is on the batch, as per IS 1786.',
                        'columns' => self::DIMENSION_COLUMNS,
                        'rows' => [
                            $dim('Diameter', '8, 10, 12, 16, 20, 25, 28, 32 mm', '—', 'IS 1786'),
                            $dim('Length', '12 m (standard)', '—', 'IS 1786'),
                            $dim('Mass per metre', 'D² ÷ 162 kg/m (12 mm = 0.889 kg/m)', '±7% up to 10 mm · ±5% 12–16 mm · ±3% above 16 mm', 'IS 1786'),
                            $dim('Supply form', 'Bundles (master bundle), tagged with grade and heat number'),
                        ],
                    ],
                    [
                        'id' => 'chemical-composition', 'title' => 'Chemical Composition', 'subtitle' => 'Max %, as per IS 1786',
                        'note' => 'Heat analysis is on the mill test certificate.',
                        'columns' => [
                            ['key' => 'grade', 'label' => 'Grade'], ['key' => 'c', 'label' => 'Carbon'], ['key' => 's', 'label' => 'Sulphur'],
                            ['key' => 'p', 'label' => 'Phosphorus'], ['key' => 'sp', 'label' => 'S + P'],
                        ],
                        'rows' => [
                            ['grade' => 'Fe 415', 'c' => '0.30', 's' => '0.060', 'p' => '0.060', 'sp' => '0.110'],
                            ['grade' => 'Fe 500', 'c' => '0.30', 's' => '0.055', 'p' => '0.055', 'sp' => '0.105'],
                            ['grade' => 'Fe 500D', 'c' => '0.25', 's' => '0.040', 'p' => '0.040', 'sp' => '0.075'],
                            ['grade' => 'Fe 550D', 'c' => '0.25', 's' => '0.040', 'p' => '0.040', 'sp' => '0.075'],
                        ],
                        'by_grade' => true,
                    ],
                    [
                        'id' => 'mechanical-properties', 'title' => 'Mechanical Properties', 'subtitle' => 'As per IS 1786',
                        'note' => 'Yield = 0.2% proof stress. Tensile strength must also exceed yield by the ratio IS 1786 sets for the grade.',
                        'columns' => self::GRADE_COLUMNS_MECH,
                        'rows' => [
                            ['grade' => 'Fe 415', 'yield' => '415 MPa', 'tensile' => '485 MPa', 'elongation' => '14.5%'],
                            ['grade' => 'Fe 500', 'yield' => '500 MPa', 'tensile' => '545 MPa', 'elongation' => '12%'],
                            ['grade' => 'Fe 500D', 'yield' => '500 MPa', 'tensile' => '565 MPa', 'elongation' => '16%'],
                            ['grade' => 'Fe 550D', 'yield' => '550 MPa', 'tensile' => '600 MPa', 'elongation' => '14.5%'],
                        ],
                        'by_grade' => true,
                    ],
                ],
                'applications' => [
                    'RCC buildings and housing', 'Bridges and flyovers', 'Dams and canals',
                    'Industrial and commercial structures', 'Foundations and columns',
                ],
                'packaging'      => ['Packaging: master bundles, grade-tagged'],
                'certifications' => [
                    'Manufactured as per IS 1786 (BIS licensed mills)',
                    'Mill Test Certificate (MTC) with every dispatch',
                    'Third-party inspection on request',
                    'GST-compliant, verified suppliers',
                ],
            ],

            'angle' => self::structural(
                'MS Angle',
                'MS angles are L-shaped hot-rolled sections used wherever a light, rigid member is needed — frames, trusses, towers and general fabrication.',
                [
                    $dim('Size', 'Leg × leg × thickness, e.g. 25×25×3 to 200×200×25 mm', 'As per IS 1852', 'IS 808'),
                    $dim('Length', '6 m / 12 m (5.5 – 13 m on order)', 'As per IS 1852', 'IS 1852'),
                    $dim('Mass per metre', 'As per IS 808 (e.g. 50×50×5 = 3.8 kg/m)', '±2.5%', 'IS 1852'),
                ],
                ['Transmission and telecom towers', 'Roof trusses and sheds', 'Frames and supports', 'General fabrication'],
                [['label' => 'Size', 'value' => '25×25×3 – 200×200×25 mm'], ['label' => 'Length', 'value' => '6 / 12 m']]
            ),
            'channel' => self::structural(
                'MS Channel',
                'MS channels (ISMC) are C-shaped hot-rolled sections for frames, purlins and chassis, where a flat back and stiff flanges are needed.',
                [
                    $dim('Size', 'ISMC 75 to ISMC 400 (depth in mm)', 'As per IS 1852', 'IS 808'),
                    $dim('Length', '6 m / 12 m', 'As per IS 1852', 'IS 1852'),
                    $dim('Mass per metre', 'As per IS 808 for the section', '±2.5%', 'IS 1852'),
                ],
                ['Building frames and purlins', 'Vehicle and trailer chassis', 'Machinery bases', 'Industrial sheds'],
                [['label' => 'Size', 'value' => 'ISMC 75 – 400'], ['label' => 'Length', 'value' => '6 / 12 m']]
            ),
            'beam' => self::structural(
                'MS Beam',
                'MS beams (ISMB) are I-sections that carry bending loads in building frames, bridges and industrial structures.',
                [
                    $dim('Size', 'ISMB 100 to ISMB 600 (depth in mm)', 'As per IS 1852', 'IS 808'),
                    $dim('Length', '6 m / 12 m', 'As per IS 1852', 'IS 1852'),
                    $dim('Mass per metre', 'As per IS 808 for the section', '±2.5%', 'IS 1852'),
                ],
                ['Building frames and columns', 'Bridges', 'Industrial sheds and mezzanines', 'Crane girders'],
                [['label' => 'Size', 'value' => 'ISMB 100 – 600'], ['label' => 'Length', 'value' => '6 / 12 m']]
            ),
            'flat' => self::structural(
                'MS Flat',
                'MS flats (patti) are rectangular hot-rolled bars for base plates, brackets, grills and general fabrication.',
                [
                    $dim('Size', 'Width × thickness, e.g. 20×3 to 150×25 mm', 'As per IS 1852', 'IS 1730'),
                    $dim('Length', '6 m', 'As per IS 1852', 'IS 1852'),
                    $dim('Mass per metre', 'W × T × 0.00785 kg/m (W, T in mm; 50×6 = 2.36 kg/m)', 'As per IS 1852', '—'),
                ],
                ['Base plates and brackets', 'Gates, grills and railings', 'Fabrication and machine parts', 'Earthing strips'],
                [['label' => 'Size', 'value' => '20×3 – 150×25 mm'], ['label' => 'Length', 'value' => '6 m']]
            ),
            'round_bar' => self::structural(
                'MS Round Bar',
                'MS round bars are solid hot-rolled rounds for shafts, fasteners, machined parts and fabrication.',
                [
                    $dim('Diameter', '16 – 150 mm', 'As per IS 1852', 'IS 1732'),
                    $dim('Length', '6 m', 'As per IS 1852', 'IS 1852'),
                    $dim('Mass per metre', 'D² ÷ 162 kg/m (D in mm)', 'As per IS 1852', '—'),
                ],
                ['Shafts and axles', 'Bolts and fasteners', 'Machined components', 'Fabrication'],
                [['label' => 'Diameter', 'value' => '16 – 150 mm'], ['label' => 'Length', 'value' => '6 m']]
            ),
            'square_bar' => self::structural(
                'MS Square Bar',
                'MS square bars are solid hot-rolled squares for gates, grills, forgings and machine parts.',
                [
                    $dim('Size', 'Side 10 – 100 mm', 'As per IS 1852', 'IS 1732'),
                    $dim('Length', '6 m', 'As per IS 1852', 'IS 1852'),
                    $dim('Mass per metre', 'A² × 0.00785 kg/m (A in mm)', 'As per IS 1852', '—'),
                ],
                ['Gates and grills', 'Forgings', 'Machine parts', 'Fabrication'],
                [['label' => 'Size', 'value' => '10 – 100 mm'], ['label' => 'Length', 'value' => '6 m']]
            ),

            'round_pipe' => [
                'label'    => 'MS ERW Pipe (Round)',
                'standard' => 'IS 1239',
                'overview' => 'MS ERW round pipes are electric-resistance-welded steel tubes for water, gas, scaffolding and structural use, supplied in light, medium and heavy classes.',
                'features' => [
                    ['key' => 'strength', 'label' => 'Pressure Tested'],
                    ['key' => 'precision', 'label' => 'Uniform Wall'],
                    ['key' => 'weld', 'label' => 'Easy to Weld'],
                    ['key' => 'recycle', 'label' => 'Recyclable'],
                ],
                'quick_specs' => [
                    ['label' => 'Standard', 'value' => 'IS 1239 / IS 3589'],
                    ['label' => 'Size', 'value' => 'NB 15 – 150 mm'],
                    ['label' => 'Length', 'value' => '6 m'],
                ],
                'sections' => [
                    [
                        'id' => 'specifications', 'title' => 'Dimensions & Tolerance',
                        'note' => 'Wall thickness by class (light / medium / heavy) as per IS 1239 Part 1.',
                        'columns' => self::DIMENSION_COLUMNS,
                        'rows' => [
                            $dim('Nominal bore', 'NB 15 – 150 mm (OD 21.3 – 165.1 mm)', 'As per IS 1239', 'IS 1239'),
                            $dim('Class', 'Light (A) / Medium (B) / Heavy (C)', '—', 'IS 1239'),
                            $dim('Length', '6 m (random 4 – 7 m)', '—', 'IS 1239'),
                            $dim('Ends', 'Plain / bevelled / screwed & socketed', '—', '—'),
                        ],
                    ],
                    [
                        'id' => 'mechanical-properties', 'title' => 'Mechanical Properties', 'subtitle' => 'As per IS 1239',
                        'note' => 'Every length is hydro-tested; results are on the test certificate.',
                        'columns' => [['key' => 'property', 'label' => 'Property'], ['key' => 'value', 'label' => 'Value'], ['key' => 'standard', 'label' => 'Standard']],
                        'rows' => [
                            ['property' => 'Tensile Strength (Min.)', 'value' => '320 MPa', 'standard' => 'IS 1239'],
                            ['property' => 'Elongation (Min.)', 'value' => '20%', 'standard' => 'IS 1239'],
                            ['property' => 'Hydrostatic Test', 'value' => '5 MPa', 'standard' => 'IS 1239'],
                        ],
                    ],
                ],
                'applications' => ['Water and gas lines', 'Scaffolding', 'Structural and fencing posts', 'Fabrication'],
                'packaging'      => ['Packaging: hexagonal bundles, strapped'],
                'certifications' => [
                    'Manufactured as per IS 1239 / IS 3589',
                    'Test certificate with every dispatch',
                    'Third-party inspection on request',
                    'GST-compliant, verified suppliers',
                ],
            ],

            'hollow_section' => [
                'label'    => 'MS Hollow Section (Square / Rectangular)',
                'standard' => 'IS 4923',
                'overview' => 'Square and rectangular hollow sections are cold-formed, ERW-welded tubes for structures, frames, gates and furniture — light for their strength and easy to fabricate.',
                'features' => [
                    ['key' => 'strength', 'label' => 'High Strength-to-Weight'],
                    ['key' => 'precision', 'label' => 'Sharp, Uniform Corners'],
                    ['key' => 'weld', 'label' => 'Easy to Weld'],
                    ['key' => 'recycle', 'label' => 'Recyclable'],
                ],
                'quick_specs' => [
                    ['label' => 'Standard', 'value' => 'IS 4923'],
                    ['label' => 'Size', 'value' => '20×20 – 300×300 mm'],
                    ['label' => 'Length', 'value' => '6 m'],
                ],
                'sections' => [
                    [
                        'id' => 'specifications', 'title' => 'Dimensions & Tolerance',
                        'note' => 'Weight is approximate; the corner radius makes the actual value slightly lower.',
                        'columns' => self::DIMENSION_COLUMNS,
                        'rows' => [
                            $dim('Square', '20×20 – 300×300 mm', 'As per IS 4923', 'IS 4923'),
                            $dim('Rectangular', '40×20 – 400×200 mm', 'As per IS 4923', 'IS 4923'),
                            $dim('Wall thickness', '1.2 – 12 mm', 'As per IS 4923', 'IS 4923'),
                            $dim('Length', '6 m', '—', 'IS 4923'),
                            $dim('Mass per metre (approx.)', '(2 × (A + B) − 4 × t) × t × 0.00785 kg/m', '—', '—'),
                        ],
                    ],
                    [
                        'id' => 'mechanical-properties', 'title' => 'Mechanical Properties', 'subtitle' => 'As per IS 4923',
                        'columns' => self::GRADE_COLUMNS_MECH,
                        'rows' => [
                            ['grade' => 'YSt 210', 'yield' => '210 MPa', 'tensile' => '330 MPa', 'elongation' => '20%'],
                            ['grade' => 'YSt 240', 'yield' => '240 MPa', 'tensile' => '410 MPa', 'elongation' => '17%'],
                            ['grade' => 'YSt 310', 'yield' => '310 MPa', 'tensile' => '450 MPa', 'elongation' => '14%'],
                        ],
                        'by_grade' => true,
                    ],
                ],
                'applications' => ['Structural frames and sheds', 'Gates, grills and fencing', 'Furniture and racks', 'Automobile and machine frames'],
                'packaging'      => ['Packaging: bundles, strapped'],
                'certifications' => [
                    'Manufactured as per IS 4923',
                    'Test certificate with every dispatch',
                    'Third-party inspection on request',
                    'GST-compliant, verified suppliers',
                ],
            ],

            'billet', 'ingot' => [
                'label'    => $family === 'billet' ? 'MS Billet' : 'MS Ingot',
                'standard' => 'IS 2830',
                'overview' => $family === 'billet'
                    ? 'MS billets are continuously cast semi-finished steel for re-rolling into TMT bars, rounds and structural sections.'
                    : 'MS ingots (pencil ingots) are cast semi-finished steel for re-rolling mills producing TMT bars and sections.',
                'features' => [
                    ['key' => 'precision', 'label' => 'Controlled Chemistry'],
                    ['key' => 'strength', 'label' => 'Sound Internal Quality'],
                    ['key' => 'recycle', 'label' => 'Recyclable'],
                ],
                'quick_specs' => [
                    ['label' => 'Size', 'value' => $family === 'billet' ? '100 / 125 / 130 / 150 mm sq.' : '100 / 125 mm sq.'],
                    ['label' => 'Unit', 'value' => 'MT'],
                ],
                'sections' => [
                    [
                        'id' => 'specifications', 'title' => 'Dimensions',
                        'columns' => self::DIMENSION_COLUMNS,
                        'rows' => [
                            $dim('Cross-section', $family === 'billet' ? '100×100, 125×125, 130×130, 150×150 mm' : '100×100, 125×125 mm', 'As agreed with the mill', 'IS 2830'),
                            $dim('Length', $family === 'billet' ? '3 – 6 m (as ordered)' : 'As per mill', '—', '—'),
                        ],
                    ],
                    [
                        'id' => 'chemical-composition', 'title' => 'Chemical Composition', 'subtitle' => 'Typical, %',
                        'note' => 'Typical for MS re-rolling grades; the actual grade is per heat analysis on the MTC.',
                        'columns' => [['key' => 'element', 'label' => 'Element'], ['key' => 'value', 'label' => 'Typical %']],
                        'rows' => [
                            ['element' => 'Carbon (C)', 'value' => '0.15 – 0.25'],
                            ['element' => 'Manganese (Mn)', 'value' => '0.40 – 0.80'],
                            ['element' => 'Silicon (Si)', 'value' => '0.10 – 0.30'],
                            ['element' => 'Sulphur (S)', 'value' => '≤ 0.045'],
                            ['element' => 'Phosphorus (P)', 'value' => '≤ 0.045'],
                        ],
                    ],
                ],
                'applications' => ['Re-rolling into TMT bars', 'Rounds, squares and flats', 'Structural sections', 'Forging'],
                'packaging'      => ['Supplied loose, by weight'],
                'certifications' => [
                    'Heat-wise chemical analysis (MTC)',
                    'Third-party inspection on request',
                    'GST-compliant, verified suppliers',
                ],
            ],

            'sponge_iron' => [
                'label'    => 'Sponge Iron (DRI)',
                'standard' => 'Lot analysis',
                'overview' => 'Sponge iron (Direct Reduced Iron) is iron ore reduced in solid state, used with or in place of scrap as feed for induction and electric arc furnaces.',
                'features' => [
                    ['key' => 'precision', 'label' => 'High Metallisation'],
                    ['key' => 'strength', 'label' => 'Low Residuals'],
                    ['key' => 'cost', 'label' => 'Scrap Substitute'],
                ],
                'quick_specs' => [
                    ['label' => 'Form', 'value' => 'Lumps / Fines'],
                    ['label' => 'Unit', 'value' => 'MT'],
                ],
                'sections' => [
                    [
                        'id' => 'specifications', 'title' => 'Size',
                        'columns' => self::DIMENSION_COLUMNS,
                        'rows' => [
                            $dim('Lumps', '3 – 20 mm', 'Oversize / undersize as agreed', '—'),
                            $dim('Fines', '0 – 3 mm', '—', '—'),
                        ],
                    ],
                    [
                        'id' => 'chemical-composition', 'title' => 'Chemical Composition', 'subtitle' => 'Typical, %',
                        'note' => 'Typical for coal-based DRI; each lot is sold on its own analysis.',
                        'columns' => [['key' => 'element', 'label' => 'Parameter'], ['key' => 'value', 'label' => 'Typical %']],
                        'rows' => [
                            ['element' => 'Fe (Total)', 'value' => '88 – 92'],
                            ['element' => 'Fe (Metallic)', 'value' => '78 – 84'],
                            ['element' => 'Metallisation', 'value' => '86 – 92'],
                            ['element' => 'Carbon (C)', 'value' => '0.10 – 0.25'],
                            ['element' => 'Sulphur (S)', 'value' => '≤ 0.03'],
                            ['element' => 'Phosphorus (P)', 'value' => '≤ 0.06'],
                        ],
                    ],
                ],
                'applications' => ['Induction furnace steelmaking', 'Electric arc furnaces', 'Scrap substitute in melt shops'],
                'packaging'      => ['Supplied loose in trucks, by weight'],
                'certifications' => [
                    'Lot-wise chemical analysis',
                    'Third-party sampling on request',
                    'GST-compliant, verified suppliers',
                ],
            ],

            'sheet' => [
                'label'    => 'HR Sheet / Plate',
                'standard' => 'IS 2062',
                'overview' => 'HR (Hot Rolled) sheets and plates are rolled above the recrystallisation temperature. They are cost-effective and widely used in structural and general engineering work.',
                'features' => [
                    ['key' => 'strength', 'label' => 'High Strength'],
                    ['key' => 'weld', 'label' => 'Easy to Weld'],
                    ['key' => 'cost', 'label' => 'Cost Effective'],
                    ['key' => 'recycle', 'label' => 'Recyclable'],
                ],
                'quick_specs' => [
                    ['label' => 'Standard', 'value' => 'IS 2062 E250'],
                    ['label' => 'Thickness', 'value' => '1.6 – 25 mm'],
                    ['label' => 'Width', 'value' => '1250 / 1500 mm'],
                ],
                'sections' => self::structuralSections([
                    $dim('Thickness', '1.6 – 25 mm', 'As per IS 1852', 'IS 1852'),
                    $dim('Width', '1000 – 1500 mm', '± 5 mm', 'IS 1852'),
                    $dim('Length', '2500 / 6000 mm', '± 10 mm', 'IS 1852'),
                    $dim('Mass', 'L × W × T × 7.85 kg (m, m, mm)', '—', '—'),
                ]),
                'applications' => ['Structural fabrication', 'General engineering', 'Machinery and equipment', 'Tanks and containers'],
                'packaging'      => ['Packaging: loose / bundles'],
                'certifications' => [
                    'Manufactured as per IS 2062',
                    'Mill Test Certificate (MTC) with every dispatch',
                    'Third-party inspection on request',
                    'GST-compliant, verified suppliers',
                ],
            ],

            default => [
                'label'          => 'Product',
                'standard'       => null,
                'overview'       => 'Specifications as listed by the seller. Ask for the mill test certificate with your quote.',
                'features'       => [],
                'quick_specs'    => [],
                'sections'       => [],
                'applications'   => [],
                'packaging'      => [],
                'certifications' => ['GST-compliant, verified suppliers'],
            ],
        };
    }
}
