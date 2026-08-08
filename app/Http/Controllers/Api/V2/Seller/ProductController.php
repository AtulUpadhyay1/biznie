<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\Seller\SellerProductDetailResource;
use App\Http\Resources\V2\Seller\SellerProductResource;
use App\Mail\SellerProductSubmittedAdminMail;
use App\Mail\SellerProductSubmittedUserMail;
use App\Models\SellerCommodityProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /** Total wizard steps (1..7 collect + submit). */
    private const TOTAL_STEPS = 7;

    /**
     * Readable names for the repeated fields.
     *
     * Without these a missing grade reads "The qualities.0.name field is
     * required." — an array index the seller never sees on screen.
     */
    /**
     * Charges the pricing service reads from a dedicated column, keyed by the
     * label the wizard shows. Everything else a seller adds is an extra charge
     * and goes into charge_name / charge_price / operator.
     */
    private const KNOWN_CHARGES = [
        'Loading'   => 'loading_charge',
        'Insurance' => 'insurance_charge',
        'GST'       => 'gst',
        'TCS'       => 'tcs',
    ];

    private const FIELD_LABELS = [
        'qualities'          => 'quality grades',
        'qualities.*.name'   => 'quality',
        'qualities.*.price'  => 'quality price',
        'packaging.*.type'   => 'packaging type',
        'packaging.*.charge' => 'packaging charge',
        'charges.*.type'     => 'charge type',
        'charges.*.percent'  => 'charge percentage',
        'charges.*.amount'   => 'charge amount',
        'charges.*.operator' => 'charge operator',
        'variants.*.size'    => 'size',
        'variants.*.unit'    => 'unit',
        'variants.*.charge'  => 'variant price',
        'variants.*.stock'   => 'stock',
        'variants.*.id'      => 'variation',
    ];

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
            'data'    => new SellerProductDetailResource($product),
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
        // Sent with their ids, not just urls: the wizard needs an identifier to
        // say which one the seller removed.
        $payload['existing_images'] = collect(is_array($record->images) ? $record->images : [])
            ->map(fn ($imgId) => ['id' => (int) $imgId, 'url' => imageUrl($imgId)])
            ->filter(fn ($image) => $image['url'] !== '')
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

        // Only a reviewer holding the product blocks an edit. An approved
        // product stays editable so a seller can correct its details without
        // taking the listing down and queueing for review again.
        if ($record && $record->request_status === 'pending_review') {
            return response()->json([
                'success' => false,
                'message' => 'This product is locked while it is under review.',
            ], 422);
        }

        $data = $this->validateStep($request, $step);

        // A listing has to keep at least one image, so the removals are counted
        // before the check — otherwise a seller could clear every image by
        // removing them one save at a time.
        if ($step === 2
            && ! $request->hasFile('product_images')
            && ! $this->keptImages($record, $data['removed_images'] ?? [])) {
            return response()->json([
                'success' => false,
                'message' => 'Please keep or upload at least one product image.',
                'errors'  => ['product_images' => ['Please keep or upload at least one product image.']],
            ], 422);
        }

        $record = $record ?: $this->newDraft($ownerId);

        DB::transaction(function () use ($request, $record, $data, $step) {
            $this->applyProductData($record, $request, $data);
            $record->current_step = max((int) ($record->current_step ?? 1), $step);
            // A rejected product returns to draft so it can be resubmitted. An
            // approved one keeps its status — editing a live listing must not
            // silently pull it out of the catalog.
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

        $data = $this->validateStep($request, 7);

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

    /**
     * Validate one step, including the per-row rules the rule array cannot
     * express (a price is required only on a variation the seller ticked).
     */
    private function validateStep(Request $request, int $step): array
    {
        $validator = Validator::make($request->all(), $this->stepRules($step), [], self::FIELD_LABELS);

        if ($step === 5) {
            $validator->after(fn ($v) => $this->variantChecks($v, $request));
        }

        return $validator->validate();
    }

    /**
     * A catalog variation is identified by its row id: ticking it means the
     * seller sells that gauge, and a gauge difference has to come with it. A
     * free-form row has no id, so it needs a size and a unit instead.
     */
    private function variantChecks(ValidatorContract $validator, Request $request): void
    {
        foreach ((array) $request->input('variants', []) as $i => $row) {
            $isCatalogRow = ! empty($row['id']);

            if (! $isCatalogRow) {
                if (trim((string) ($row['size'] ?? '')) === '') {
                    $validator->errors()->add("variants.{$i}.size", 'The size field is required.');
                }
                if (trim((string) ($row['unit'] ?? '')) === '') {
                    $validator->errors()->add("variants.{$i}.unit", 'The unit field is required.');
                }
                continue;
            }

            if (filter_var($row['is_selected'] ?? false, FILTER_VALIDATE_BOOLEAN)
                && trim((string) ($row['charge'] ?? '')) === '') {
                $validator->errors()->add("variants.{$i}.charge", 'Enter a price for the selected variation.');
            }
        }
    }

    private function stepRules(int $step): array
    {
        return match ($step) {
            1 => [
                'product_name'         => ['required', 'string', 'max:190'],
                // Set when the seller picked a suggestion from the admin catalog
                // instead of typing a product of their own. Blank means the
                // product is the seller's, and nothing links back.
                'commodity_product_id' => ['nullable', 'integer', Rule::exists('commodity_products', 'id')],
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
                // Ids of already-saved images the seller took off the listing.
                'removed_images'   => ['nullable', 'array'],
                'removed_images.*' => ['integer'],
            ],
            3 => [
                // Grades are a list, not one value: a seller offers "Fe 500" and
                // "Fe 500D" side by side, each with its own price difference.
                // They land in the parallel quality / quality_price arrays the
                // rest of the platform already reads.
                'qualities'           => ['required', 'array', 'min:1'],
                'qualities.*.name'    => ['required', 'string', 'max:120'],
                'qualities.*.price'   => ['nullable', 'numeric', 'min:0'],
                'quality_charge'      => ['nullable', 'numeric', 'min:0'],
                'quality_description' => ['nullable', 'string', 'max:300'],
                // Packaging is admin-managed master data, so the seller picks
                // ids from packaging_types rather than typing a name.
                'packaging'           => ['nullable', 'array'],
                'packaging.*.type'    => ['required', 'integer', Rule::exists('packaging_types', 'id')],
                'packaging.*.charge'  => ['nullable', 'numeric', 'min:0'],
                'brand_id'            => ['nullable', 'integer', Rule::exists('brands', 'id')],
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
                // Two shapes share this step. A catalog-linked product's
                // variations are fixed rows the seller ticks and prices, so
                // they carry an id; a product the seller created themselves has
                // free-form rows with a size and unit. `variantChecks()` below
                // enforces whichever applies, per row.
                'variants'               => ['required', 'array', 'min:1'],
                'variants.*.id'          => ['nullable', 'integer'],
                'variants.*.is_selected' => ['nullable', 'boolean'],
                'variants.*.size'        => ['nullable', 'string', 'max:120'],
                'variants.*.unit'        => ['nullable', 'string', 'max:60'],
                'variants.*.charge'      => ['nullable', 'numeric', 'min:0'],
                'variants.*.stock'       => ['nullable', 'string', 'max:60'],
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
                'charges.*.type' => ['required', 'string', 'max:120'],
                'charges.*.percent' => ['nullable', 'numeric', 'min:0'],
                'charges.*.amount'  => ['nullable', 'numeric', 'min:0'],
                // Whether an extra charge adds to or subtracts from the ex price.
                'charges.*.operator' => ['nullable', Rule::in(['+', '-'])],
            ],
            default => throw new \InvalidArgumentException('Invalid product step.'),
        };
    }

    private function applyProductData(SellerCommodityProduct $record, Request $request, array $data): void
    {
        if (array_key_exists('commodity_product_id', $data)) {
            $this->applyCatalogLink($record, $data['commodity_product_id'] ?: null);
        }
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

        if (array_key_exists('removed_images', $data) || $request->hasFile('product_images')) {
            $ids = $this->keptImages($record, $data['removed_images'] ?? []);

            foreach ((array) $request->file('product_images') as $file) {
                $ids[] = imageUpload($file, 'seller-products/'.$record->user_id);
            }

            $record->images = array_slice($ids, 0, 5);
        }

        if (array_key_exists('qualities', $data)) {
            // Parallel arrays, positionally paired — the shape every other
            // reader of this product expects (the buyer-facing quality picker,
            // the seller's "Update Quality" dialog, the admin reports).
            $names = [];
            $prices = [];
            foreach ((array) $data['qualities'] as $row) {
                $name = trim((string) ($row['name'] ?? ''));
                if ($name === '' || in_array($name, $names, true)) {
                    continue;
                }
                $names[] = $name;
                $prices[] = (string) (($row['price'] ?? '') !== '' ? $row['price'] : 0);
            }
            $record->quality = $names;
            $record->quality_price = $prices;
            $record->is_quality = $names ? 1 : 0;
        }
        if (array_key_exists('quality_charge', $data)) {
            $record->quality_charge = ($data['quality_charge'] ?? '') !== '' ? $data['quality_charge'] : 0;
        }
        if (array_key_exists('quality_description', $data)) {
            $record->quality_description = $data['quality_description'] ?: null;
        }

        // Sent on its own so an empty selection can be told apart from "the step
        // did not carry packaging at all" — multipart has no empty array.
        if ($request->boolean('packaging_provided')) {
            $types = [];
            $prices = [];
            foreach ((array) ($data['packaging'] ?? []) as $row) {
                $typeId = (int) ($row['type'] ?? 0);
                if ($typeId <= 0 || in_array((string) $typeId, $types, true)) {
                    continue;
                }
                $types[] = (string) $typeId;
                // Keyed by id, which is how ProductPricingService reads it back.
                $prices[$typeId] = ($row['charge'] ?? '') !== '' ? (float) $row['charge'] : 0;
            }
            $record->packaging_type = $types;
            $record->packaging_type_price = $prices;
        }
        if (array_key_exists('brand_id', $data)) {
            // brand_id, not the legacy brand_name text column: the relation is
            // what the listing cards render and what gauge pricing looks up.
            $record->brand_id = $data['brand_id'] ?: null;
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
            $this->applyVariantRows($record, (array) $data['variants']);
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
            // Catalog-linked products carry a list of full loading addresses —
            // street lines, pincode, the lot. The wizard only collects a city,
            // state and country, so overwriting that list would throw the street
            // away. The columns above still record what the seller typed; the
            // richer address is left to the catalog that owns it.
            $existing = $record->loading_address;
            $isCatalogAddress = is_array($existing) && $existing !== [] && array_is_list($existing);

            if (! $isCatalogAddress) {
                $record->loading_address = [
                    'city'    => $record->city,
                    'state'   => $record->state,
                    'country' => $record->country,
                ];
            }
        }
        if (array_key_exists('moq', $data)) {
            $record->moq = $data['moq'] ?: null;
        }
        if (array_key_exists('moq_unit', $data)) {
            $record->moq_unit = $data['moq_unit'] ?: null;
        }

        if ($request->boolean('charges_provided')) {
            // The four charges the pricing service reads from their own columns
            // are cleared first, so unticking one actually removes it. Anything
            // else the seller adds lives in the parallel name/price/operator
            // arrays, where the operator decides whether it lifts or lowers the
            // ex price — without it the amount is simply ignored.
            foreach (self::KNOWN_CHARGES as $column) {
                $record->{$column} = 0;
            }

            $names = [];
            $prices = [];
            $operators = [];

            foreach ((array) ($data['charges'] ?? []) as $row) {
                $type = trim((string) ($row['type'] ?? ''));
                if ($type === '') {
                    continue;
                }
                $amount = ($row['amount'] ?? '') !== ''
                    ? (float) $row['amount']
                    : (($row['percent'] ?? '') !== '' ? (float) $row['percent'] : 0);

                $column = $this->knownChargeColumn($type);
                if ($column) {
                    $record->{$column} = $amount;
                    continue;
                }

                $names[] = $type;
                $prices[] = $amount;
                $operators[] = in_array($row['operator'] ?? '+', ['+', '-'], true) ? $row['operator'] : '+';
            }

            $record->charge_name = $names;
            $record->charge_price = $prices;
            $record->operator = $operators;
        }

        if (array_key_exists('publish_on', $data)) {
            $record->publish_on = $data['publish_on'] ?: null;
        }
    }

    /**
     * Link the listing to an admin catalog product, or unlink it.
     *
     * The wizard prefills itself from `CatalogProductController@show`, so almost
     * everything the catalog holds arrives back as ordinary form values the
     * seller has already seen and can edit. Copied here is only what the form
     * has no field for and would otherwise be lost: the catalog's third-level
     * category, its unit, its photographs, its specification notes and its base
     * price — the last as a starting point the seller adjusts from the Update
     * Price dialog once the product is live.
     *
     * Unlinking clears the link alone. Whatever the seller has already filled in
     * is theirs to keep; wiping it would punish a mis-click.
     */
    private function applyCatalogLink(SellerCommodityProduct $record, ?int $catalogId): void
    {
        if ((int) $record->commodity_product_id === (int) $catalogId) {
            return;
        }

        $record->commodity_product_id = $catalogId;

        if (! $catalogId) {
            return;
        }

        $catalog = \App\Models\CommodityProduct::find($catalogId);
        if (! $catalog) {
            return;
        }

        $record->sub_sub_category_id = $catalog->sub_sub_category_id;
        $record->unit_id = $catalog->unit_id;
        $record->specification_notes = $catalog->specification_notes;

        if (! $record->thumbnail) {
            $record->thumbnail = $catalog->thumbnail;
        }
        // Ids, not files: one ImageUpload row already backs the catalog product
        // and every seller listing copied from it — see keptImages().
        if (empty($record->images) && is_array($catalog->images)) {
            $record->images = array_slice(array_values($catalog->images), 0, 5);
        }
        if (! (float) $record->base_price) {
            $record->base_price = $catalog->base_price;
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
        // Either store counts: catalog variations live in state-price rows, a
        // seller's own in the `variation` column.
        if (empty($record->variation) && $record->getStatePrice()->doesntExist()) return 'size variants';
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
            // Editable unless a reviewer is holding it — see saveStep().
            'can_edit'          => $record->request_status !== 'pending_review',
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
        foreach ($packagingTypes as $type) {
            // Keyed by packaging type id — see ProductPricingService.
            $charge = $packagingPrices[$type] ?? '';
            $packaging[] = ['type' => (string) $type, 'charge' => $charge === '' ? '' : (string) $charge];
        }

        $charges = $this->chargeRows($record);

        $specMap = function ($rows) {
            return collect(is_array($rows) ? $rows : [])->map(fn ($row) => [
                'parameter' => $row['parameter'] ?? '',
                'value'     => $row['value'] ?? '',
                'image'     => null,
            ])->values()->all();
        };

        $variants = $this->variantRows($record);

        return [
            'product_name'         => $record->name ?? '',
            // Kept on the form so re-saving step 1 does not silently drop the
            // link to the catalog product this listing was created from.
            'commodity_product_id' => $record->commodity_product_id ? (string) $record->commodity_product_id : '',
            'category'             => $record->category_id ? (string) $record->category_id : '',
            'sub_category'         => $record->sub_category_id ? (string) $record->sub_category_id : '',
            'product_type'         => $record->product_type ?? 'raw_material',
            'short_description'    => $record->short_description ?? '',
            'detailed_description' => $record->description ?? '',
            'hsn_code'             => $record->hsn_code ?? '',
            'tax_rate'             => $record->tax_rate !== null ? (string) $record->tax_rate : '',
            'product_video'        => $record->video_url ?? '',
            'qualities'            => $this->qualityRows($record),
            'quality_charge'       => $record->quality_charge ? (string) $record->quality_charge : '',
            'quality_description'  => $record->quality_description ?? '',
            'packaging'            => $packaging,
            'brand_id'             => $record->brand_id ? (string) $record->brand_id : '',
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

    /**
     * The product's image ids minus the ones the seller removed.
     *
     * Removing detaches, it does not delete: one ImageUpload row can back the
     * catalog product and several sellers' listings at once, so deleting the
     * file would blank the image on all of them.
     *
     * @param  array<int, mixed>  $removed
     * @return array<int, mixed>
     */
    private function keptImages(?SellerCommodityProduct $record, array $removed): array
    {
        $ids = is_array($record?->images) ? $record->images : [];

        if (! $removed) {
            return array_values($ids);
        }

        $drop = array_map('strval', $removed);

        return array_values(array_filter($ids, fn ($id) => ! in_array((string) $id, $drop, true)));
    }

    /**
     * Save the size variants, to whichever of the two stores owns them.
     *
     * Which one is decided from the database, not from the request: a product
     * with catalog variation rows keeps them, and the free-form JSON columns are
     * left alone. Writing those columns on a catalog product would overwrite
     * `unit`, which there is a map of attribute name to unit id rather than a
     * list, and the variations themselves would end up in two places at once.
     */
    private function applyVariantRows(SellerCommodityProduct $record, array $rows): void
    {
        $stateRows = $record->exists
            ? $record->getStatePrice()->get()->keyBy('id')
            : collect();

        if ($stateRows->isEmpty()) {
            $record->applyVariants($rows);

            return;
        }

        foreach ($rows as $row) {
            $stateRow = $stateRows->get((int) ($row['id'] ?? 0));
            if (! $stateRow) {
                continue;
            }

            $stateRow->is_selected = filter_var($row['is_selected'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            $stateRow->price = trim((string) ($row['charge'] ?? '')) === '' ? '0' : (string) $row['charge'];
            $stateRow->stock = trim((string) ($row['stock'] ?? '')) === '' ? null : $row['stock'];
            $stateRow->save();
        }
    }

    /**
     * The size variants as the form shows them.
     *
     * Catalog variations come from the seller's own state-price rows — the same
     * rows the "Update Variation" and "Update Stock" dialogs edit — so both
     * screens always agree. A product with none of those falls back to the
     * free-form rows the wizard collected.
     *
     * @return array<int, array<string, mixed>>
     */
    private function variantRows(SellerCommodityProduct $record): array
    {
        $stateRows = $record->exists ? $record->getStatePrice()->get() : collect();

        if ($stateRows->isEmpty()) {
            return collect(is_array($record->variation) ? $record->variation : [])->map(fn ($row) => [
                'id'          => '',
                'label'       => '',
                'size'        => (string) ($row['size'] ?? ''),
                'unit'        => (string) ($row['unit'] ?? ''),
                'charge'      => isset($row['charge']) ? (string) $row['charge'] : '',
                'stock'       => isset($row['stock']) ? (string) $row['stock'] : '',
                'is_selected' => true,
            ])->values()->all();
        }

        $unitNames = $this->unitShortNames($record);

        return $stateRows->map(function ($row) use ($unitNames) {
            $values = is_array($row->value) ? $row->value : [];

            $parts = [];
            foreach ($values as $value) {
                $name = (string) ($value['name'] ?? '');
                $parts[] = trim(((string) ($value['value'] ?? '')).' '.($unitNames[$name] ?? ''));
            }
            $parts = array_values(array_filter($parts));

            return [
                'id'    => (string) $row->id,
                // "8 MM" — what the seller sees under Requirements everywhere else.
                'label' => implode(' · ', $parts),
                'size'  => implode(' · ', $parts),
                'unit'  => '',
                'charge' => $row->price !== null ? (string) $row->price : '',
                'stock'  => $row->stock !== null ? (string) $row->stock : '',
                'is_selected' => (int) $row->is_selected === 1,
            ];
        })->values()->all();
    }

    /**
     * Unit short names keyed by attribute name ("Size" => "MM").
     *
     * The product's `unit` column maps each attribute to a product_units id, so
     * one query resolves every label on the variation list.
     *
     * @return array<string, string>
     */
    private function unitShortNames(SellerCommodityProduct $record): array
    {
        $map = is_array($record->unit) ? $record->unit : [];
        if (! $map) {
            return [];
        }

        $names = \App\Models\ProductUnit::whereIn('id', array_values($map))->pluck('short_name', 'id');

        return collect($map)
            ->map(fn ($unitId) => (string) ($names[$unitId] ?? ''))
            ->all();
    }

    /**
     * The grades this product can be sold in, and whether the seller offers each.
     *
     * A catalog-linked product does not get to invent grades: a buyer comparing
     * two sellers on "Fe 500" has to be comparing the same thing, so the parent
     * product's list is the menu and the seller only ticks rows and prices them.
     * A product the seller created themselves has no parent, so its own grades
     * are the whole list and all of them count as offered.
     *
     * @return array<int, array{name: string, price: string, catalog_price: string, is_selected: bool}>
     */
    private function qualityRows(SellerCommodityProduct $record): array
    {
        $chosen = is_array($record->quality) ? array_values($record->quality) : [];
        $chosenPrices = is_array($record->quality_price) ? array_values($record->quality_price) : [];

        $commodity = $record->commodity_product_id ? $record->getCommodityProduct : null;
        $catalog = is_array($commodity?->quality) ? array_values($commodity->quality) : [];
        $catalogPrices = is_array($commodity?->quality_price) ? array_values($commodity->quality_price) : [];

        if (! $catalog) {
            return collect($chosen)->map(fn ($name, $i) => [
                'name'          => (string) $name,
                'price'         => (string) ($chosenPrices[$i] ?? ''),
                'catalog_price' => '',
                'is_selected'   => true,
            ])->values()->all();
        }

        $rows = [];
        foreach ($catalog as $i => $name) {
            $position = array_search($name, $chosen, true);
            $isSelected = $position !== false;

            $rows[] = [
                'name'          => (string) $name,
                'price'         => (string) ($isSelected ? ($chosenPrices[$position] ?? '') : ($catalogPrices[$i] ?? '')),
                'catalog_price' => (string) ($catalogPrices[$i] ?? ''),
                'is_selected'   => $isSelected,
            ];
        }

        // Grades the seller carries that the catalog has since dropped stay on
        // the list — hiding them would silently delete them on the next save.
        foreach ($chosen as $i => $name) {
            if (! in_array($name, $catalog, true)) {
                $rows[] = [
                    'name'          => (string) $name,
                    'price'         => (string) ($chosenPrices[$i] ?? ''),
                    'catalog_price' => '',
                    'is_selected'   => true,
                ];
            }
        }

        return $rows;
    }

    /**
     * Charges as the form shows them: the four the pricing service reads from
     * their own columns, then any extras the seller added.
     *
     * Built from those columns rather than from charge_name / charge_price,
     * which only ever hold the extras — reading the form back from them alone
     * left GST, loading and insurance looking unset on a product that had them.
     *
     * @return array<int, array{type: string, enabled: bool, amount: string, operator: string}>
     */
    private function chargeRows(SellerCommodityProduct $record): array
    {
        $rows = [];
        foreach (self::KNOWN_CHARGES as $label => $column) {
            $value = (float) $record->{$column};
            $rows[] = [
                'type'     => $label,
                'enabled'  => $value > 0,
                'amount'   => $value > 0 ? (string) $value : '',
                'operator' => '+',
            ];
        }

        $names = is_array($record->charge_name) ? array_values($record->charge_name) : [];
        $prices = is_array($record->charge_price) ? array_values($record->charge_price) : [];
        $operators = is_array($record->operator) ? array_values($record->operator) : [];

        foreach ($names as $i => $name) {
            if (trim((string) $name) === '') {
                continue;
            }
            $rows[] = [
                'type'     => (string) $name,
                'enabled'  => true,
                'amount'   => isset($prices[$i]) ? (string) $prices[$i] : '',
                'operator' => in_array($operators[$i] ?? '+', ['+', '-'], true) ? $operators[$i] : '+',
            ];
        }

        return $rows;
    }

    /** The column a charge label maps to, or null when it is an extra charge. */
    private function knownChargeColumn(string $type): ?string
    {
        foreach (self::KNOWN_CHARGES as $label => $column) {
            if (strcasecmp($label, $type) === 0) {
                return $column;
            }
        }

        return null;
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
