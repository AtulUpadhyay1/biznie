<?php

namespace App\Services\Chat\Handlers;

use App\Models\CommodityProduct;
use App\Models\ProductEnquiry;
use App\Models\ProductUnit;
use App\Services\Chat\Blocks;
use App\Services\Chat\ChatContext;
use App\Services\Chat\Presenters\EnquiryPresenter;
use App\Services\Rfq\EnquiryCreator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Enquiry (RFQ) list, status, the pre-filled form and its submission.
 *
 * Creation only ever happens on an explicit `enquiry.submit` from the form —
 * free text at most pre-fills it — and goes through the same EnquiryCreator
 * as `/rfq/new`, so live bidding and seller dispatch run exactly as usual.
 */
class EnquiryHandler
{
    private const PER_PAGE = 5;

    /** Unit spellings people type → what the units table tends to call them. */
    private const UNIT_ALIASES = [
        'mt'      => ['mt', 'mts', 'ton', 'tons', 'tonne', 'tonnes', 'metricton', 'metrictons', 'metrictonne'],
        'kg'      => ['kg', 'kgs', 'kilogram', 'kilograms', 'kilo'],
        'quintal' => ['quintal', 'quintals', 'qtl', 'qtls'],
        'pcs'     => ['pcs', 'pc', 'piece', 'pieces', 'nos', 'no', 'number', 'numbers'],
        'litre'   => ['litre', 'litres', 'liter', 'liters', 'ltr', 'l'],
        'meter'   => ['meter', 'meters', 'metre', 'metres', 'mtr', 'm'],
    ];

    public function __construct(
        private readonly EnquiryPresenter $presenter,
        private readonly EnquiryCreator $creator,
        private readonly MenuHandler $menu,
    ) {
    }

    public function list(ChatContext $ctx, array $payload): array
    {
        $data = Validator::make($payload, [
            'scope' => ['nullable', Rule::in(['open', 'all'])],
            'page'  => ['nullable', 'integer', 'min:1', 'max:100'],
        ])->validate();

        $scope = $data['scope'] ?? 'open';
        $page  = (int) ($data['page'] ?? 1);

        $query = ProductEnquiry::where('user_id', $ctx->ownerId());
        if ($scope === 'open') {
            $this->whereOpen($query);
        }

        $enquiries = $query->with(EnquiryPresenter::RELATIONS)
            ->latest()
            ->skip(($page - 1) * self::PER_PAGE)
            ->take(self::PER_PAGE + 1)
            ->get();

        if ($enquiries->isEmpty()) {
            $options = [Blocks::option($ctx->t('menu.create_enquiry'), Blocks::action('enquiry.start'))];
            if ($scope === 'open') {
                $options[] = Blocks::option($ctx->t('menu.all_enquiries'), Blocks::action('enquiry.list', ['scope' => 'all']));
            }

            return [
                Blocks::text($ctx->t($scope === 'open' ? 'enquiries_none_open' : 'enquiries_none')),
                $this->menu->backToMenu($ctx, $options),
            ];
        }

        $hasMore = $enquiries->count() > self::PER_PAGE;
        $rows    = $enquiries->take(self::PER_PAGE)->map(fn ($e) => $this->presenter->summary($e, $ctx))->all();

        $options = [];
        if ($hasMore) {
            $options[] = Blocks::option($ctx->t('menu.show_more'), Blocks::action('enquiry.list', ['scope' => $scope, 'page' => $page + 1]));
        }
        if ($scope === 'open') {
            $options[] = Blocks::option($ctx->t('menu.all_enquiries'), Blocks::action('enquiry.list', ['scope' => 'all']));
        }
        $options[] = Blocks::option($ctx->t('menu.create_enquiry'), Blocks::action('enquiry.start'));

        return [
            Blocks::text($ctx->t($scope === 'open' ? 'enquiries_open_intro' : 'enquiries_all_intro')),
            Blocks::enquiryList($rows, $hasMore),
            $this->menu->backToMenu($ctx, $options),
        ];
    }

    public function status(ChatContext $ctx, array $payload): array
    {
        $data = Validator::make($payload, [
            'id'  => ['nullable', 'integer', 'min:1'],
            'ref' => ['nullable', 'string', 'max:40'],
        ])->validate();

        if (empty($data['id']) && empty($data['ref'])) {
            return $this->list($ctx, ['scope' => 'open']);
        }

        $enquiry = ProductEnquiry::where('user_id', $ctx->ownerId())
            ->when(! empty($data['id']), fn ($q) => $q->whereKey((int) $data['id']))
            ->when(! empty($data['ref']), fn ($q) => $q->where('unique_id', strtoupper(trim($data['ref']))))
            ->with(EnquiryPresenter::RELATIONS)
            ->first();

        if (! $enquiry) {
            return [
                Blocks::text($ctx->t('enquiry_not_found')),
                $this->menu->backToMenu($ctx, [
                    Blocks::option($ctx->t('menu.my_enquiries'), Blocks::action('enquiry.list', ['scope' => 'open'])),
                    Blocks::option($ctx->t('menu.support'), Blocks::action('support.contact')),
                ]),
            ];
        }

        return [
            Blocks::text($ctx->t('enquiry_found', ['ref' => $enquiry->unique_id ?: '#' . $enquiry->id])),
            Blocks::enquiryDetail($this->presenter->detail($enquiry, $ctx)),
            $this->menu->backToMenu($ctx, [
                Blocks::option($ctx->t('menu.my_enquiries'), Blocks::action('enquiry.list', ['scope' => 'open'])),
            ]),
        ];
    }

    /** The pre-filled form. Nothing is written here — the user still has to submit. */
    public function start(ChatContext $ctx, array $payload): array
    {
        $data = Validator::make($payload, [
            'product_id'    => ['nullable', 'integer', 'min:1'],
            'product_query' => ['nullable', 'string', 'max:120'],
            'quantity'      => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'unit_id'       => ['nullable', 'integer', 'min:1'],
            'unit_label'    => ['nullable', 'string', 'max:30'],
            'delivery_city' => ['nullable', 'string', 'max:120'],
            'required_by'   => ['nullable', 'string', 'max:120'],
            'description'   => ['nullable', 'string', 'max:2000'],
        ])->validate();

        $units   = ProductUnit::active()->orderBy('name')->get(['id', 'name', 'short_name']);
        $query   = isset($data['product_query']) ? trim($data['product_query']) : null;
        $product = $this->resolveProduct($data['product_id'] ?? null, $query);
        $unit    = $this->resolveUnit($units, $data['unit_id'] ?? null, $data['unit_label'] ?? null);
        $options = config('biznie.chat.required_by_options');

        $prefill = [
            'product'       => $product ? ['id' => (int) $product->id, 'name' => (string) $product->name] : null,
            'product_query' => $product ? null : ($query ?: null),
            'quantity'      => isset($data['quantity']) && (float) $data['quantity'] > 0 ? (float) $data['quantity'] : null,
            'unit_id'       => $unit['id'],
            'unit_label'    => $unit['label'],
            'delivery_city' => isset($data['delivery_city']) ? (trim($data['delivery_city']) ?: null) : null,
            'required_by'   => in_array($data['required_by'] ?? null, $options, true) ? $data['required_by'] : null,
            'description'   => $data['description'] ?? null,
        ];

        return [
            Blocks::text($ctx->t('enquiry_form_intro')),
            Blocks::enquiryForm(
                $prefill,
                $units->map(fn (ProductUnit $u) => [
                    'id'         => (int) $u->id,
                    'name'       => (string) $u->name,
                    'short_name' => $u->short_name ?: null,
                ])->values()->all(),
                $options,
            ),
        ];
    }

    public function submit(ChatContext $ctx, array $payload): array
    {
        $data = Validator::make($payload, [
            'commodity_product_id' => [
                'required', 'integer',
                Rule::exists('commodity_products', 'id')->where('status', 'active')->whereNull('deleted_at'),
            ],
            'quantity'       => ['required', 'numeric', 'gt:0', 'max:10000000'],
            // A free-text unit the units table does not know is fine, but one
            // of the two has to be there — a quantity without a unit is not an RFQ.
            'unit_id'        => ['nullable', 'required_without:unit_label', 'integer', Rule::exists('product_units', 'id')->whereNull('deleted_at')],
            'unit_label'     => ['nullable', 'string', 'max:30'],
            'size_label'     => ['nullable', 'string', 'max:150'],
            'delivery_city'  => ['required', 'string', 'max:120'],
            'delivery_state' => ['nullable', 'string', 'max:120'],
            'required_by'    => ['required', 'string', Rule::in(config('biznie.chat.required_by_options'))],
            'description'    => ['nullable', 'string', 'max:2000'],
        ])->validate();

        // The chat turn runs in a transaction; bidding and the seller pushes
        // wait until it has committed (see ChatContext::afterCommit).
        $created = $this->creator->create($ctx->user, $data, fanOut: false);
        $ctx->afterCommit(fn () => $this->creator->fanOut($created));

        $enquiry = $created->fresh()->load(EnquiryPresenter::RELATIONS);
        $summary = $this->presenter->summary($enquiry, $ctx);

        return [
            Blocks::text($ctx->t('enquiry_created', ['ref' => $enquiry->unique_id])),
            Blocks::enquiryCreated($summary),
            $this->menu->backToMenu($ctx, [
                Blocks::option($ctx->t('menu.track_enquiry'), Blocks::action('enquiry.status', ['id' => (int) $enquiry->id])),
                Blocks::option($ctx->t('menu.my_enquiries'), Blocks::action('enquiry.list', ['scope' => 'open'])),
            ]),
        ];
    }

    /**
     * Still in play — the SQL twin of EnquiryPresenter::stage() not being
     * `ordered`, `cancelled` or `closed`:
     *   - not converted to an order, not cancelled, not unfulfillable; and
     *   - bidding never ran (legacy / pre-bidding RFQs are still being worked
     *     by the team), or it holds a price, or it is live right now.
     */
    private function whereOpen(Builder $query): void
    {
        $query->where(function (Builder $q) {
            $q->whereNull('status')->orWhere(function (Builder $q) {
                $q->whereRaw('LOWER(status) <> ?', ['ordered'])
                    ->whereRaw('LOWER(status) NOT LIKE ?', ['%cancel%'])
                    ->whereRaw('LOWER(status) NOT LIKE ?', ['%no seller%']);
            });
        })->where(function (Builder $q) {
            $q->where(function (Builder $q) {
                $q->whereNull('bidding_started_at')
                    ->where(fn (Builder $q) => $q->whereNull('bidding_status')->orWhereIn('bidding_status', ['', 'draft']));
            })
                ->orWhereNotNull('best_for_price')
                ->orWhere(fn (Builder $q) => $q->where('bidding_status', 'live')->where('bidding_ends_at', '>', now()));
        });
    }

    /** An explicit id wins; otherwise the closest active product by name, if any. */
    private function resolveProduct(?int $productId, ?string $query): ?CommodityProduct
    {
        if ($productId) {
            return CommodityProduct::active()->find($productId, ['id', 'name']);
        }

        if (! $query || mb_strlen($query) < 2) {
            return null;
        }

        return CommodityProduct::active()
            ->where('name', 'like', '%' . addcslashes($query, '%_\\') . '%')
            ->orderByRaw('CHAR_LENGTH(name)')
            ->first(['id', 'name']);
    }

    /**
     * @param  Collection<int, ProductUnit>  $units
     * @return array{id: ?int, label: ?string}
     */
    private function resolveUnit(Collection $units, ?int $unitId, ?string $label): array
    {
        if ($unitId && ($unit = $units->firstWhere('id', $unitId))) {
            return ['id' => (int) $unit->id, 'label' => $unit->short_name ?: $unit->name];
        }

        $label = $label !== null ? trim($label) : '';
        if ($label === '') {
            return ['id' => null, 'label' => null];
        }

        $wanted = $this->unitKey($label);
        $group  = collect(self::UNIT_ALIASES)->first(fn (array $aliases) => in_array($wanted, $aliases, true)) ?? [$wanted];

        $unit = $units->first(fn (ProductUnit $u) => in_array($this->unitKey((string) $u->short_name), $group, true)
            || in_array($this->unitKey((string) $u->name), $group, true));

        return $unit
            ? ['id' => (int) $unit->id, 'label' => $unit->short_name ?: $unit->name]
            : ['id' => null, 'label' => mb_substr($label, 0, 30)];
    }

    private function unitKey(string $value): string
    {
        return preg_replace('/[^a-z]/', '', strtolower($value)) ?? '';
    }
}
