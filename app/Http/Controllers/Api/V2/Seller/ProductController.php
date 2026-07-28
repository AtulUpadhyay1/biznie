<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\Seller\SellerProductResource;
use App\Mail\SellerProductSubmittedAdminMail;
use App\Mail\SellerProductSubmittedUserMail;
use App\Models\SellerCommodityProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /** Total wizard steps (1..7 collect + submit). */
    private const TOTAL_STEPS = 7;

    public function index(Request $request): JsonResponse
    {
        $ownerId = $this->ownerId($request);
        $perPage = min((int) $request->get('per_page', 15), 50);

        $list = SellerCommodityProduct::where('user_id', $ownerId)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->request_status, fn ($q) => $q->whereIn('request_status', array_filter(explode(',', (string) $request->request_status))))
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->with([
                'getCommodityProduct:id,name',
                'getBrand:id,name',
                'getCategory:id,name',
                'getUnit:id,name',
            ])
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => SellerProductResource::collection($list)->resolve(),
            'meta'    => [
                'current_page' => $list->currentPage(),
                'last_page'    => $list->lastPage(),
                'per_page'     => $list->perPage(),
                'total'        => $list->total(),
            ],
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $ownerId = $this->ownerId($request);
        $product = SellerCommodityProduct::where('user_id', $ownerId)
            ->with([
                'getCommodityProduct:id,name',
                'getBrand:id,name',
                'getCategory:id,name',
                'getUnit:id,name',
            ])
            ->find($id);

        if (! $product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new SellerProductResource($product),
        ]);
    }

    /**
     * Return a draft/rejected product mapped back into the wizard's form shape,
     * so the seller can edit and resubmit it.
     */
    public function editData(Request $request, int $id): JsonResponse
    {
        $ownerId = $this->ownerId($request);
        $record = SellerCommodityProduct::where('user_id', $ownerId)->find($id);

        if (! $record) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        $payload = $this->requestPayload($record);
        $payload['data'] = $this->formData($record);
        $payload['existing_images'] = collect(is_array($record->images) ? $record->images : [])
            ->map(fn ($imgId) => imageUrl($imgId))
            ->filter()
            ->values();

        return response()->json(['success' => true, 'data' => $payload]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $ownerId = $this->ownerId($request);
        $product = SellerCommodityProduct::where('user_id', $ownerId)->find($id);

        if (! $product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        $product->status = $data['status'];
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Product status updated.',
            'data'    => new SellerProductResource(
                $product->fresh(['getCommodityProduct', 'getBrand', 'getCategory', 'getUnit'])
            ),
        ]);
    }

    /**
     * Save a single wizard step (creates a fresh draft when no id is sent).
     */
    public function saveStep(Request $request): JsonResponse
    {
        $ownerId = $this->ownerId($request);
        $step = max(1, min(self::TOTAL_STEPS, (int) $request->input('step', 1)));

        $record = $this->resolveDraft($request, $ownerId);
        if ($record && in_array($record->request_status, ['pending_review', 'approved'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'This product is locked while under review or approved.',
            ], 422);
        }

        $data = $request->validate($this->stepRules($step));

        if ($step === 2 && ! $request->hasFile('product_images') && empty($record?->images)) {
            return response()->json([
                'success' => false,
                'message' => 'Please upload at least one product image.',
                'errors'  => ['product_images' => ['Please upload at least one product image.']],
            ], 422);
        }

        $record = $record ?: $this->newDraft($ownerId);

        DB::transaction(function () use ($request, $record, $data, $step) {
            $this->applyProductData($record, $request, $data);
            $record->current_step = max((int) ($record->current_step ?? 1), $step);
            if ($record->request_status === 'rejected') {
                $record->request_status = 'draft';
            }
            $record->appendTimeline('step_saved', ['step' => $step, 'label' => $this->stepLabel($step)]);
            $record->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Step saved successfully.',
            'data'    => $this->requestPayload($record->fresh()),
        ]);
    }

    /**
     * Finalise a draft product: apply the last step then send it for review.
     */
    public function submit(Request $request, int $id): JsonResponse
    {
        $ownerId = $this->ownerId($request);
        $record = SellerCommodityProduct::where('user_id', $ownerId)->find($id);

        if (! $record) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }
        if (in_array($record->request_status, ['pending_review', 'approved'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'This product is already submitted for review.',
            ], 422);
        }

        $data = $request->validate($this->stepRules(7));

        $missing = $this->firstMissingRequirement($record, $request, $data);
        if ($missing) {
            return response()->json([
                'success' => false,
                'message' => "Please complete the product details ({$missing}) before submitting.",
            ], 422);
        }

        DB::transaction(function () use ($request, $record, $data) {
            $this->applyProductData($record, $request, $data);

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
            $record->appendTimeline('submitted', ['reference' => $record->request_reference]);
            $record->save();

            $this->sendSubmissionNotifications($record);
        });

        return response()->json([
            'success' => true,
            'message' => 'Product submitted successfully. Our team will review it shortly.',
            'data'    => $this->requestPayload($record->fresh()),
        ]);
    }

    /* --------------------------------------------------------------------- */
    /* Helpers                                                               */
    /* --------------------------------------------------------------------- */

    private function ownerId(Request $request): int
    {
        $user = $request->user();
        return $user->is_staff ? (int) $user->added_by : (int) $user->id;
    }

    private function resolveDraft(Request $request, int $ownerId): ?SellerCommodityProduct
    {
        $id = (int) $request->input('id', 0);
        if (! $id) {
            return null;
        }
        return SellerCommodityProduct::where('user_id', $ownerId)->find($id);
    }

    private function newDraft(int $ownerId): SellerCommodityProduct
    {
        $record = new SellerCommodityProduct();
        $record->user_id = $ownerId;
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
                'physical_specs.*.image'     => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
                'chemical_specs'             => ['nullable', 'array'],
                'chemical_specs.*.parameter' => ['nullable', 'string', 'max:160'],
                'chemical_specs.*.value'     => ['nullable', 'string', 'max:190'],
                'chemical_specs.*.image'     => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
                'weight_unit'                => ['required', 'string', 'max:60'],
                'net_weight'                 => ['required', 'string', 'max:60'],
                'tolerance'                  => ['nullable', 'string', 'max:60'],
            ],
            5 => [
                'variants'        => ['required', 'array', 'min:1'],
                'variants.*.size' => ['required', 'string', 'max:120'],
                'variants.*.unit' => ['required', 'string', 'max:60'],
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
                // `status` (active/inactive) is decided by the admin on approval,
                // not by the seller, so it is not collected here.
                'publish_on'     => ['nullable', 'date'],
                'charges'        => ['nullable', 'array'],
                'charges.*.type' => ['nullable', 'string', 'max:120'],
                'charges.*.percent' => ['nullable', 'numeric', 'min:0'],
                'charges.*.amount'  => ['nullable', 'numeric', 'min:0'],
            ],
            default => throw new \InvalidArgumentException('Invalid product step.'),
        };
    }

    private function applyProductData(SellerCommodityProduct $record, Request $request, array $data): void
    {
        if (array_key_exists('product_name', $data)) {
            $record->name = $data['product_name'];
            if (! $record->slug) {
                $record->slug = Str::slug($data['product_name']).'-'.Str::lower(Str::random(5));
            }
        }
        foreach (['category_id', 'sub_category_id', 'product_type', 'hsn_code'] as $field) {
            if (array_key_exists($field, $data)) {
                $record->{$field} = $data[$field] ?: null;
            }
        }
        if (array_key_exists('short_description', $data)) {
            $record->short_description = $data['short_description'] ?: null;
        }
        if (array_key_exists('detailed_description', $data)) {
            $record->description = $data['detailed_description'] ?: null;
        }
        if (array_key_exists('tax_rate', $data)) {
            $record->tax_rate = ($data['tax_rate'] ?? '') !== '' ? $data['tax_rate'] : null;
        }
        if (array_key_exists('product_video', $data)) {
            $record->video_url = $data['product_video'] ?: null;
        }

        if ($request->hasFile('product_images')) {
            $ids = is_array($record->images) ? $record->images : [];
            foreach ((array) $request->file('product_images') as $file) {
                $ids[] = imageUpload($file, 'seller-products/'.$record->user_id);
            }
            $record->images = array_slice($ids, 0, 5);
        }

        if (array_key_exists('quality', $data)) {
            $record->quality = $data['quality'] ? [$data['quality']] : [];
            $record->is_quality = $data['quality'] ? 1 : 0;
        }
        if (array_key_exists('quality_charge', $data)) {
            $record->quality_charge = ($data['quality_charge'] ?? '') !== '' ? $data['quality_charge'] : 0;
        }
        if (array_key_exists('quality_description', $data)) {
            $record->quality_description = $data['quality_description'] ?: null;
        }

        if (array_key_exists('packaging', $data)) {
            $types = [];
            $prices = [];
            foreach ((array) $data['packaging'] as $row) {
                if (empty($row['type']) && ($row['charge'] ?? '') === '') {
                    continue;
                }
                $types[] = $row['type'] ?? null;
                $prices[] = ($row['charge'] ?? '') !== '' ? (float) $row['charge'] : null;
            }
            $record->packaging_type = $types;
            $record->packaging_type_price = $prices;
        }
        if (array_key_exists('brand', $data)) {
            $record->brand_name = $data['brand'] ?: null;
        }
        if (array_key_exists('make', $data)) {
            $record->make = $data['make'] ?: null;
        }

        foreach (['physical_specs' => 'physical_specification', 'chemical_specs' => 'chemical_specification'] as $key => $column) {
            if (! array_key_exists($key, $data)) {
                continue;
            }
            $rows = [];
            foreach ((array) $data[$key] as $i => $row) {
                if (empty($row['parameter']) && empty($row['value'])) {
                    continue;
                }
                $imageId = null;
                $file = $request->file("{$key}.{$i}.image");
                if ($file) {
                    $imageId = imageUpload($file, 'seller-products/'.$record->user_id);
                }
                $rows[] = [
                    'parameter' => $row['parameter'] ?? null,
                    'value'     => $row['value'] ?? null,
                    'image'     => $imageId,
                ];
            }
            $record->{$column} = $rows;
        }

        if (array_key_exists('weight_unit', $data)) {
            $record->weight_unit = $data['weight_unit'] ?: null;
        }
        if (array_key_exists('net_weight', $data)) {
            $record->net_weight = $data['net_weight'] ?: null;
        }
        if (array_key_exists('tolerance', $data)) {
            $record->tolerance = $data['tolerance'] ?: null;
        }

        if (array_key_exists('variants', $data)) {
            $record->applyVariants((array) $data['variants']);
        }

        if (array_key_exists('loading_city', $data)) {
            $record->city = $data['loading_city'] ?: null;
        }
        if (array_key_exists('loading_state', $data)) {
            $record->state = $data['loading_state'] ?: null;
        }
        if (array_key_exists('country', $data)) {
            $record->country = $data['country'] ?: null;
        }
        if (array_key_exists('loading_city', $data) || array_key_exists('loading_state', $data) || array_key_exists('country', $data)) {
            $record->loading_address = [
                'city'    => $record->city,
                'state'   => $record->state,
                'country' => $record->country,
            ];
        }
        if (array_key_exists('moq', $data)) {
            $record->moq = $data['moq'] ?: null;
        }
        if (array_key_exists('moq_unit', $data)) {
            $record->moq_unit = $data['moq_unit'] ?: null;
        }

        if (array_key_exists('charges', $data)) {
            $names = [];
            $prices = [];
            $record->gst = 0;
            $record->loading_charge = 0;
            $record->insurance_charge = 0;
            foreach ((array) $data['charges'] as $row) {
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
        }

        if (array_key_exists('publish_on', $data)) {
            $record->publish_on = $data['publish_on'] ?: null;
        }
    }

    /** Returns the label of the first unmet requirement, or null when complete. */
    private function firstMissingRequirement(SellerCommodityProduct $record, Request $request, array $incoming): ?string
    {
        if (! $record->name) return 'basic information';
        if (! $record->category_id) return 'category';
        if (! $record->product_type) return 'product type';
        if (! $record->hsn_code) return 'HSN code';
        if (empty($record->images) && ! $request->hasFile('product_images')) return 'product images';
        if (empty($record->quality)) return 'quality';
        if (! $record->weight_unit || ! $record->net_weight) return 'weight / quantity';
        if (empty($record->variation)) return 'size variants';
        if (! $record->city || ! $record->state || ! $record->country) return 'loading details';
        if (! $record->moq || ! $record->moq_unit) return 'minimum order quantity';

        return null;
    }

    private function requestPayload(SellerCommodityProduct $record): array
    {
        return [
            'id'                => $record->id,
            'request_reference' => $record->request_reference,
            'request_status'    => $record->request_status,
            'current_step'      => $record->current_step,
            'can_edit'          => in_array($record->request_status, ['draft', 'rejected'], true),
            'review_note'       => $record->review_note,
            'submitted_at'      => optional($record->submitted_at)->toIso8601String(),
            'timeline'          => $record->timeline ?? [],
        ];
    }

    /** Reverse of applyProductData: record columns -> wizard form fields. */
    private function formData(SellerCommodityProduct $record): array
    {
        $packagingTypes = is_array($record->packaging_type) ? $record->packaging_type : [];
        $packagingPrices = is_array($record->packaging_type_price) ? $record->packaging_type_price : [];
        $packaging = [];
        foreach ($packagingTypes as $i => $type) {
            $packaging[] = ['type' => $type, 'charge' => isset($packagingPrices[$i]) ? (string) $packagingPrices[$i] : ''];
        }

        $chargeNames = is_array($record->charge_name) ? $record->charge_name : [];
        $chargePrices = is_array($record->charge_price) ? $record->charge_price : [];
        $charges = [];
        foreach ($chargeNames as $i => $name) {
            $charges[] = [
                'type'    => $name,
                'enabled' => true,
                'percent' => '',
                'amount'  => isset($chargePrices[$i]) ? (string) $chargePrices[$i] : '',
            ];
        }

        $specMap = function ($rows) {
            return collect(is_array($rows) ? $rows : [])->map(fn ($row) => [
                'parameter' => $row['parameter'] ?? '',
                'value'     => $row['value'] ?? '',
                'image'     => null,
            ])->values()->all();
        };

        $variants = collect(is_array($record->variation) ? $record->variation : [])->map(fn ($row) => [
            'size'   => $row['size'] ?? '',
            'unit'   => $row['unit'] ?? '',
            'charge' => isset($row['charge']) ? (string) $row['charge'] : '',
            'stock'  => isset($row['stock']) ? (string) $row['stock'] : '',
        ])->values()->all();

        return [
            'product_name'         => $record->name ?? '',
            'category'             => $record->category_id ? (string) $record->category_id : '',
            'sub_category'         => $record->sub_category_id ? (string) $record->sub_category_id : '',
            'product_type'         => $record->product_type ?? 'raw_material',
            'short_description'    => $record->short_description ?? '',
            'detailed_description' => $record->description ?? '',
            'hsn_code'             => $record->hsn_code ?? '',
            'tax_rate'             => $record->tax_rate !== null ? (string) $record->tax_rate : '',
            'product_video'        => $record->video_url ?? '',
            'quality'              => is_array($record->quality) ? ($record->quality[0] ?? '') : (string) ($record->quality ?? ''),
            'quality_charge'       => $record->quality_charge ? (string) $record->quality_charge : '',
            'quality_description'  => $record->quality_description ?? '',
            'packaging'            => $packaging,
            'brand'                => $record->brand_name ?? '',
            'make'                 => $record->make ?? '',
            'physical_specs'       => $specMap($record->physical_specification),
            'chemical_specs'       => $specMap($record->chemical_specification),
            'weight_unit'          => $record->weight_unit ?? '',
            'net_weight'           => $record->net_weight ?? '',
            'tolerance'            => $record->tolerance ?? '',
            'variants'             => $variants,
            'loading_city'         => $record->city ?? '',
            'loading_state'        => $record->state ?? '',
            'country'              => $record->country ?? 'India',
            'moq'                  => $record->moq ?? '',
            'moq_unit'             => $record->moq_unit ?? '',
            'charges'              => $charges,
            'publish_on'           => $record->publish_on ? \Illuminate\Support\Carbon::parse($record->publish_on)->toDateString() : '',
        ];
    }

    private function stepLabel(int $step): string
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
}
