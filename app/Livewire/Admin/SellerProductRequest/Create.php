<?php

namespace App\Livewire\Admin\SellerProductRequest;

use App\Mail\SellerProductSubmittedAdminMail;
use App\Mail\SellerProductSubmittedUserMail;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\SellerCommodityProduct;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Raise a product request on behalf of a seller from the admin panel.
 *
 * Mirrors the seller-side wizard in Api\V2\Seller\ProductController: the same
 * seven steps, the same validation and the same columns on
 * seller_commodity_products, so both entry points produce identical records.
 */
class Create extends Component
{
    use WithFileUploads;

    /** Total wizard steps (1..7 collect + submit). */
    private const TOTAL_STEPS = 7;

    public User $seller;
    public ?int $recordId = null;
    public int $step = 1;
    public int $maxStep = 1;

    /* Step 1 - Basic Information */
    public $product_name = '';
    public $category_id = '';
    public $sub_category_id = '';
    public $product_type = 'raw_material';
    public $short_description = '';
    public $detailed_description = '';
    public $hsn_code = '';
    public $tax_rate = '';

    /* Step 2 - Media Upload */
    public $product_images = [];
    public array $existing_images = [];
    public $product_video = '';

    /* Step 3 - Quality & Brand */
    public $quality = '';
    public $quality_charge = '';
    public $quality_description = '';
    public array $packaging = [['type' => '', 'charge' => '']];
    public $brand = '';
    public $make = '';

    /* Step 4 - Specifications */
    public array $physical_specs = [['parameter' => '', 'value' => '']];
    public array $chemical_specs = [['parameter' => '', 'value' => '']];
    public $physical_spec_images = [];
    public $chemical_spec_images = [];
    public $weight_unit = '';
    public $net_weight = '';
    public $tolerance = '';

    /* Step 5 - Size Variants & Pricing */
    public array $variants = [['size' => '', 'unit' => '', 'charge' => '', 'stock' => '']];

    /* Step 6 - Loading & MOQ */
    public $loading_city = '';
    public $loading_state = '';
    public $country = 'India';
    public $moq = '';
    public $moq_unit = '';

    /* Step 7 - Charges & Status */
    public $publish_on = '';
    public array $charges = [['type' => '', 'percent' => '', 'amount' => '']];

    public function mount($user_id): void
    {
        $this->authorize('seller-list');

        $this->seller = User::where('type', 'seller')->findOrFail($user_id);
    }

    /** Sub categories are scoped to the chosen category. */
    public function updatedCategoryId(): void
    {
        $this->sub_category_id = '';
    }

    /* --------------------------------------------------------------------- */
    /* Wizard navigation                                                     */
    /* --------------------------------------------------------------------- */

    public function nextStep(): void
    {
        $this->saveStep();

        if (! $this->getErrorBag()->isEmpty()) {
            return;
        }

        if ($this->step < self::TOTAL_STEPS + 1) {
            $this->step++;
            $this->maxStep = max($this->maxStep, $this->step);
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= $this->maxStep) {
            $this->step = $step;
        }
    }

    /**
     * Persist the current step onto the draft record (created on first save).
     */
    public function saveStep(): void
    {
        if ($this->step > self::TOTAL_STEPS) {
            return;
        }

        $record = $this->record();

        if ($record && in_array($record->request_status, ['pending_review', 'approved'], true)) {
            $this->addError('step', 'This product is locked while under review or approved.');
            $this->dispatch('alert', type: 'error', message: 'This product is locked while under review or approved.');

            return;
        }

        $this->validate($this->stepRules($this->step), [], $this->validationAttributes());

        if ($this->step === 2 && empty($this->product_images) && empty($this->existing_images)) {
            $this->addError('product_images', 'Please upload at least one product image.');

            return;
        }

        $record = $record ?: $this->newDraft();
        $step = $this->step;

        DB::transaction(function () use ($record, $step) {
            $this->applyStepData($record, $step);
            $record->current_step = max((int) ($record->current_step ?? 1), $step);
            if ($record->request_status === 'rejected') {
                $record->request_status = 'draft';
            }
            $record->appendTimeline('step_saved', [
                'step'  => $step,
                'label' => $this->stepLabel($step),
                'by'    => 'admin',
            ]);
            $record->save();
        });

        $this->recordId = $record->id;
        $this->existing_images = collect(is_array($record->images) ? $record->images : [])
            ->map(fn ($imageId) => imageUrl($imageId))
            ->filter()
            ->values()
            ->all();
        $this->product_images = [];
        $this->physical_spec_images = [];
        $this->chemical_spec_images = [];
    }

    /**
     * Finalise the draft and push it into the review queue.
     */
    public function submit()
    {
        $this->authorize('seller-list');

        $record = $this->record();

        if (! $record) {
            $this->dispatch('alert', type: 'error', message: 'Please complete the wizard before submitting.');

            return null;
        }

        if (in_array($record->request_status, ['pending_review', 'approved'], true)) {
            $this->dispatch('alert', type: 'error', message: 'This product is already submitted for review.');

            return null;
        }

        $missing = $this->firstMissingRequirement($record);
        if ($missing) {
            $this->dispatch('alert', type: 'error', message: "Please complete the product details ({$missing}) before submitting.");

            return null;
        }

        DB::transaction(function () use ($record) {
            if (! $record->request_reference) {
                $record->request_reference = $this->generateReference();
            }

            $record->request_status = 'pending_review';
            $record->status = 'inactive'; // stays hidden from the live catalog until approved
            $record->submitted_at = now();
            $record->reviewed_at = null;
            $record->reviewed_by = null;
            $record->review_note = null;
            $record->current_step = self::TOTAL_STEPS + 1;
            $record->appendTimeline('submitted', [
                'reference' => $record->request_reference,
                'by'        => 'admin',
            ]);
            $record->save();

            $this->sendSubmissionNotifications($record);
        });

        session()->flash('success', 'Product request submitted successfully on behalf of '.$this->seller->name.'.');

        return redirect()->route('admin.seller-product-request.index');
    }

    /* --------------------------------------------------------------------- */
    /* Repeater rows                                                         */
    /* --------------------------------------------------------------------- */

    public function addPackaging(): void
    {
        $this->packaging[] = ['type' => '', 'charge' => ''];
    }

    public function removePackaging(int $index): void
    {
        unset($this->packaging[$index]);
        $this->packaging = array_values($this->packaging);
    }

    public function addPhysicalSpec(): void
    {
        $this->physical_specs[] = ['parameter' => '', 'value' => ''];
    }

    public function removePhysicalSpec(int $index): void
    {
        unset($this->physical_specs[$index], $this->physical_spec_images[$index]);
        $this->physical_specs = array_values($this->physical_specs);
        $this->physical_spec_images = array_values($this->physical_spec_images);
    }

    public function addChemicalSpec(): void
    {
        $this->chemical_specs[] = ['parameter' => '', 'value' => ''];
    }

    public function removeChemicalSpec(int $index): void
    {
        unset($this->chemical_specs[$index], $this->chemical_spec_images[$index]);
        $this->chemical_specs = array_values($this->chemical_specs);
        $this->chemical_spec_images = array_values($this->chemical_spec_images);
    }

    public function addVariant(): void
    {
        $this->variants[] = ['size' => '', 'unit' => '', 'charge' => '', 'stock' => ''];
    }

    public function removeVariant(int $index): void
    {
        unset($this->variants[$index]);
        $this->variants = array_values($this->variants);
    }

    public function addCharge(): void
    {
        $this->charges[] = ['type' => '', 'percent' => '', 'amount' => ''];
    }

    public function removeCharge(int $index): void
    {
        unset($this->charges[$index]);
        $this->charges = array_values($this->charges);
    }

    /* --------------------------------------------------------------------- */
    /* Helpers                                                               */
    /* --------------------------------------------------------------------- */

    private function record(): ?SellerCommodityProduct
    {
        if (! $this->recordId) {
            return null;
        }

        return SellerCommodityProduct::where('user_id', $this->seller->id)->find($this->recordId);
    }

    private function newDraft(): SellerCommodityProduct
    {
        $record = new SellerCommodityProduct();
        $record->user_id = $this->seller->id;
        $record->status = 'inactive';
        $record->request_status = 'draft';
        $record->current_step = 1;
        $record->timeline = [];
        $record->save();

        return $record;
    }

    private function stepRules(int $step): array
    {
        return match ($step) {
            1 => [
                'product_name'         => ['required', 'string', 'max:190'],
                'category_id'          => ['required', 'integer', Rule::exists('product_categories', 'id')],
                'sub_category_id'      => ['nullable', 'integer', Rule::exists('product_sub_categories', 'id')],
                'product_type'         => ['required', Rule::in(['raw_material', 'finished_good', 'service'])],
                'short_description'    => ['required', 'string', 'max:200'],
                'detailed_description' => ['nullable', 'string', 'max:1000'],
                'hsn_code'             => ['required', 'string', 'max:40'],
                'tax_rate'             => ['nullable', 'numeric', 'min:0'],
            ],
            2 => [
                'product_images'   => ['nullable', 'array', 'max:5'],
                'product_images.*' => ['file', 'mimes:jpg,jpeg,png', 'max:5120'],
                'product_video'    => ['nullable', 'string', 'max:255'],
            ],
            3 => [
                'quality'             => ['required', 'string', 'max:120'],
                'quality_charge'      => ['nullable', 'numeric', 'min:0'],
                'quality_description' => ['nullable', 'string', 'max:300'],
                'packaging'           => ['nullable', 'array'],
                'packaging.*.type'    => ['nullable', 'string', 'max:120'],
                'packaging.*.charge'  => ['nullable', 'numeric', 'min:0'],
                'brand'               => ['nullable', 'string', 'max:160'],
                'make'                => ['nullable', 'string', 'max:160'],
            ],
            4 => [
                'physical_specs'             => ['nullable', 'array'],
                'physical_specs.*.parameter' => ['nullable', 'string', 'max:160'],
                'physical_specs.*.value'     => ['nullable', 'string', 'max:190'],
                'physical_spec_images.*'     => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
                'chemical_specs'             => ['nullable', 'array'],
                'chemical_specs.*.parameter' => ['nullable', 'string', 'max:160'],
                'chemical_specs.*.value'     => ['nullable', 'string', 'max:190'],
                'chemical_spec_images.*'     => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
                'weight_unit'                => ['required', 'string', 'max:60'],
                'net_weight'                 => ['required', 'string', 'max:60'],
                'tolerance'                  => ['nullable', 'string', 'max:60'],
            ],
            5 => [
                'variants'          => ['required', 'array', 'min:1'],
                'variants.*.size'   => ['required', 'string', 'max:120'],
                'variants.*.unit'   => ['required', 'string', 'max:60'],
                'variants.*.charge' => ['nullable', 'numeric', 'min:0'],
                'variants.*.stock'  => ['nullable', 'string', 'max:60'],
            ],
            6 => [
                'loading_city'  => ['required', 'string', 'max:120'],
                'loading_state' => ['required', 'string', 'max:120'],
                'country'       => ['required', 'string', 'max:120'],
                'moq'           => ['required', 'string', 'max:60'],
                'moq_unit'      => ['required', 'string', 'max:60'],
            ],
            7 => [
                // `status` (active/inactive) is decided on approval, not here.
                'publish_on'        => ['nullable', 'date'],
                'charges'           => ['nullable', 'array'],
                'charges.*.type'    => ['nullable', 'string', 'max:120'],
                'charges.*.percent' => ['nullable', 'numeric', 'min:0'],
                'charges.*.amount'  => ['nullable', 'numeric', 'min:0'],
            ],
            default => [],
        };
    }

    private function validationAttributes(): array
    {
        return [
            'product_name'         => 'product name',
            'category_id'          => 'category',
            'sub_category_id'      => 'sub category',
            'product_type'         => 'product type',
            'short_description'    => 'short description',
            'detailed_description' => 'detailed description',
            'hsn_code'             => 'HSN code',
            'tax_rate'             => 'tax rate',
            'product_images.*'     => 'product image',
            'product_video'        => 'product video',
            'quality_charge'       => 'quality charge',
            'quality_description'  => 'quality description',
            'weight_unit'          => 'weight unit',
            'net_weight'           => 'net weight',
            'loading_city'         => 'loading city',
            'loading_state'        => 'loading state',
            'moq'                  => 'MOQ',
            'moq_unit'             => 'MOQ unit',
            'publish_on'           => 'publish date',
        ];
    }

    /** Maps the wizard fields of one step onto the record columns. */
    private function applyStepData(SellerCommodityProduct $record, int $step): void
    {
        switch ($step) {
            case 1:
                $record->name = $this->product_name;
                if (! $record->slug) {
                    $record->slug = Str::slug($this->product_name).'-'.Str::lower(Str::random(5));
                }
                $record->category_id = $this->category_id ?: null;
                $record->sub_category_id = $this->sub_category_id ?: null;
                $record->product_type = $this->product_type ?: null;
                $record->hsn_code = $this->hsn_code ?: null;
                $record->short_description = $this->short_description ?: null;
                $record->description = $this->detailed_description ?: null;
                $record->tax_rate = $this->tax_rate !== '' ? $this->tax_rate : null;
                break;

            case 2:
                if (! empty($this->product_images)) {
                    $ids = is_array($record->images) ? $record->images : [];
                    foreach ($this->product_images as $file) {
                        $ids[] = imageUpload($file, 'seller-products/'.$record->user_id);
                    }
                    $record->images = array_slice($ids, 0, 5);
                }
                $record->video_url = $this->product_video ?: null;
                break;

            case 3:
                $record->quality = $this->quality ? [$this->quality] : [];
                $record->is_quality = $this->quality ? 1 : 0;
                $record->quality_charge = $this->quality_charge !== '' ? $this->quality_charge : 0;
                $record->quality_description = $this->quality_description ?: null;

                $types = [];
                $prices = [];
                foreach ($this->packaging as $row) {
                    if (empty($row['type']) && ($row['charge'] ?? '') === '') {
                        continue;
                    }
                    $types[] = $row['type'] ?? null;
                    $prices[] = ($row['charge'] ?? '') !== '' ? (float) $row['charge'] : null;
                }
                $record->packaging_type = $types;
                $record->packaging_type_price = $prices;

                $record->brand_name = $this->brand ?: null;
                $record->make = $this->make ?: null;
                break;

            case 4:
                $record->physical_specification = $this->specRows($record, $this->physical_specs, $this->physical_spec_images);
                $record->chemical_specification = $this->specRows($record, $this->chemical_specs, $this->chemical_spec_images);
                $record->weight_unit = $this->weight_unit ?: null;
                $record->net_weight = $this->net_weight ?: null;
                $record->tolerance = $this->tolerance ?: null;
                break;

            case 5:
                $variation = [];
                $sizes = [];
                $sizePrices = [];
                $units = [];
                foreach ($this->variants as $row) {
                    if (empty($row['size']) && empty($row['unit'])) {
                        continue;
                    }
                    $variation[] = [
                        'size'   => $row['size'] ?? null,
                        'unit'   => $row['unit'] ?? null,
                        'charge' => $row['charge'] ?? null,
                        'stock'  => $row['stock'] ?? null,
                    ];
                    $sizes[] = $row['size'] ?? null;
                    $sizePrices[] = ($row['charge'] ?? '') !== '' ? (float) $row['charge'] : null;
                    $units[] = $row['unit'] ?? null;
                }
                $record->variation = $variation;
                $record->size = $sizes;
                $record->size_price = $sizePrices;
                $record->unit = $units;
                break;

            case 6:
                $record->city = $this->loading_city ?: null;
                $record->state = $this->loading_state ?: null;
                $record->country = $this->country ?: null;
                $record->loading_address = [
                    'city'    => $record->city,
                    'state'   => $record->state,
                    'country' => $record->country,
                ];
                $record->moq = $this->moq ?: null;
                $record->moq_unit = $this->moq_unit ?: null;
                break;

            case 7:
                $names = [];
                $prices = [];
                $record->gst = 0;
                $record->loading_charge = 0;
                $record->insurance_charge = 0;
                foreach ($this->charges as $row) {
                    $type = $row['type'] ?? null;
                    if (! $type) {
                        continue;
                    }
                    $amount = ($row['amount'] ?? '') !== ''
                        ? (float) $row['amount']
                        : (($row['percent'] ?? '') !== '' ? (float) $row['percent'] : 0);
                    $names[] = $type;
                    $prices[] = $amount;
                    match (strtolower($type)) {
                        'gst'       => $record->gst = $amount,
                        'loading'   => $record->loading_charge = $amount,
                        'insurance' => $record->insurance_charge = $amount,
                        default     => null,
                    };
                }
                $record->charge_name = $names;
                $record->charge_price = $prices;
                $record->publish_on = $this->publish_on ?: null;
                break;
        }
    }

    /**
     * Builds the specification JSON rows, keeping any image already stored on
     * the record when this save does not carry a replacement upload.
     */
    private function specRows(SellerCommodityProduct $record, array $rows, array $images): array
    {
        $result = [];

        foreach ($rows as $i => $row) {
            if (empty($row['parameter']) && empty($row['value'])) {
                continue;
            }

            $imageId = $row['image'] ?? null;
            if (! empty($images[$i])) {
                $imageId = imageUpload($images[$i], 'seller-products/'.$record->user_id);
            }

            $result[] = [
                'parameter' => $row['parameter'] ?? null,
                'value'     => $row['value'] ?? null,
                'image'     => $imageId,
            ];
        }

        return $result;
    }

    /** Returns the label of the first unmet requirement, or null when complete. */
    private function firstMissingRequirement(SellerCommodityProduct $record): ?string
    {
        if (! $record->name) return 'basic information';
        if (! $record->category_id) return 'category';
        if (! $record->product_type) return 'product type';
        if (! $record->hsn_code) return 'HSN code';
        if (empty($record->images)) return 'product images';
        if (empty($record->quality)) return 'quality';
        if (! $record->weight_unit || ! $record->net_weight) return 'weight / quantity';
        if (empty($record->variation)) return 'size variants';
        if (! $record->city || ! $record->state || ! $record->country) return 'loading details';
        if (! $record->moq || ! $record->moq_unit) return 'minimum order quantity';

        return null;
    }

    public function stepLabel(int $step): string
    {
        return match ($step) {
            1 => 'Basic Information',
            2 => 'Media Upload',
            3 => 'Quality & Brand',
            4 => 'Specifications',
            5 => 'Size Variants & Pricing',
            6 => 'Loading & MOQ',
            7 => 'Charges & Status',
            default => 'Review & Submit',
        };
    }

    private function generateReference(): string
    {
        return 'SP-'.date('Ymd').'-'.Str::upper(Str::random(6));
    }

    private function sendSubmissionNotifications(SellerCommodityProduct $record): void
    {
        $record->loadMissing('getUser');

        Mail::to('contact@biznie.com')->send(new SellerProductSubmittedAdminMail($record));

        if ($record->getUser?->email) {
            Mail::to($record->getUser->email)->send(new SellerProductSubmittedUserMail($record));
        }
    }

    public function render()
    {
        $categories = ProductCategory::active()->orderBy('name')->get(['id', 'name']);
        $subCategories = $this->category_id
            ? ProductSubCategory::active()->where('product_category_id', $this->category_id)->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('admin.seller_product_request.create', compact('categories', 'subCategories'), [
            'page_title' => 'Add Product Request',
        ]);
    }
}
