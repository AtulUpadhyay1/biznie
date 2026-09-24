<?php

namespace App\Services\Chat\Handlers;

use App\Models\CommodityProductOrder;
use App\Services\Chat\Blocks;
use App\Services\Chat\ChatContext;
use App\Services\Chat\Presenters\OrderPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Order list and order status. Every query is scoped to the owner id; a
 * reference that belongs to someone else gets the same "not found" as one
 * that does not exist, so it cannot be used to probe for ids.
 */
class OrderHandler
{
    private const PER_PAGE = 5;

    public function __construct(
        private readonly OrderPresenter $presenter,
        private readonly MenuHandler $menu,
    ) {
    }

    /** Orders that still need watching (not delivered, completed or cancelled). */
    public static function whereActive(Builder $query): Builder
    {
        $closed = OrderPresenter::CLOSED_STATUSES;

        return $query->where(function (Builder $q) use ($closed) {
            $q->whereNull('status')
                ->orWhereRaw('LOWER(status) NOT IN (' . implode(',', array_fill(0, count($closed), '?')) . ')', $closed);
        });
    }

    public function list(ChatContext $ctx, array $payload): array
    {
        $data = Validator::make($payload, [
            'scope' => ['nullable', Rule::in(['active', 'all'])],
            'page'  => ['nullable', 'integer', 'min:1', 'max:100'],
        ])->validate();

        $scope = $data['scope'] ?? 'active';
        $page  = (int) ($data['page'] ?? 1);

        $query = CommodityProductOrder::where('customer_user_id', $ctx->ownerId());
        if ($scope === 'active') {
            self::whereActive($query);
        }

        $orders = $query->with(OrderPresenter::LIST_RELATIONS)
            ->latest()
            ->skip(($page - 1) * self::PER_PAGE)
            ->take(self::PER_PAGE + 1)
            ->get();

        if ($orders->isEmpty()) {
            $options = [];
            if ($scope === 'active') {
                $options[] = Blocks::option($ctx->t('menu.all_orders'), Blocks::action('order.list', ['scope' => 'all']));
            }
            $options[] = Blocks::option($ctx->t('menu.create_enquiry'), Blocks::action('enquiry.start'));

            return [
                Blocks::text($ctx->t($scope === 'active' ? 'orders_none_active' : 'orders_none')),
                $this->menu->backToMenu($ctx, $options),
            ];
        }

        $hasMore = $orders->count() > self::PER_PAGE;
        $rows    = $orders->take(self::PER_PAGE)->map(fn ($o) => $this->presenter->summary($o, $ctx))->all();

        $options = [];
        if ($hasMore) {
            $options[] = Blocks::option($ctx->t('menu.show_more'), Blocks::action('order.list', ['scope' => $scope, 'page' => $page + 1]));
        }
        if ($scope === 'active') {
            $options[] = Blocks::option($ctx->t('menu.all_orders'), Blocks::action('order.list', ['scope' => 'all']));
        }

        return [
            Blocks::text($ctx->t($scope === 'active' ? 'orders_active_intro' : 'orders_all_intro')),
            Blocks::orderList($rows, $hasMore),
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
            return $this->list($ctx, ['scope' => 'active']);
        }

        $order = CommodityProductOrder::where('customer_user_id', $ctx->ownerId())
            ->when(! empty($data['id']), fn ($q) => $q->whereKey((int) $data['id']))
            ->when(! empty($data['ref']), fn ($q) => $q->where('order_id', strtoupper(trim($data['ref']))))
            ->with(OrderPresenter::DETAIL_RELATIONS)
            ->first();

        if (! $order) {
            return [
                Blocks::text($ctx->t('order_not_found')),
                $this->menu->backToMenu($ctx, [
                    Blocks::option($ctx->t('menu.track_order'), Blocks::action('order.list', ['scope' => 'active'])),
                    Blocks::option($ctx->t('menu.support'), Blocks::action('support.contact')),
                ]),
            ];
        }

        return [
            Blocks::text($ctx->t('order_found', ['ref' => $order->order_id ?: '#' . $order->id])),
            Blocks::orderDetail($this->presenter->detail($order, $ctx)),
            $this->menu->backToMenu($ctx, [
                Blocks::option($ctx->t('menu.track_order'), Blocks::action('order.list', ['scope' => 'active'])),
                Blocks::option($ctx->t('menu.support'), Blocks::action('support.contact')),
            ]),
        ];
    }
}
