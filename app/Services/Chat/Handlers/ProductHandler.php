<?php

namespace App\Services\Chat\Handlers;

use App\Models\CommodityProduct;
use App\Services\Chat\Blocks;
use App\Services\Chat\ChatContext;
use App\Services\Chat\Presenters\ProductPresenter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

/** "Check prices": catalogue search with a one-tap route into an enquiry. */
class ProductHandler
{
    private const LIMIT = 6;

    public function __construct(
        private readonly ProductPresenter $presenter,
        private readonly MenuHandler $menu,
    ) {
    }

    public function search(ChatContext $ctx, array $payload): array
    {
        $data  = Validator::make($payload, ['query' => ['nullable', 'string', 'max:120']])->validate();
        $query = trim((string) ($data['query'] ?? ''));

        if ($query === '') {
            $popular = CommodityProduct::active()->with(ProductPresenter::RELATIONS)->latest()->limit(self::LIMIT)->get();

            return $this->listing($ctx, $ctx->t('products_prompt'), $popular);
        }

        $products = $this->find($query);

        if ($products->isEmpty()) {
            return [
                Blocks::text($ctx->t('products_none', ['query' => $query])),
                $this->menu->backToMenu($ctx, [
                    Blocks::option($ctx->t('menu.create_enquiry'), Blocks::action('enquiry.start', ['product_query' => $query])),
                    Blocks::option($ctx->t('menu.support'), Blocks::action('support.contact')),
                ]),
            ];
        }

        return $this->listing($ctx, $ctx->t('products_intro', ['query' => $query]), $products);
    }

    /** The whole phrase first; failing that, its longest meaningful word. */
    private function find(string $query): Collection
    {
        $base = fn () => CommodityProduct::active()->with(ProductPresenter::RELATIONS);

        $products = $base()->search($query)->limit(self::LIMIT)->get();
        if ($products->isNotEmpty()) {
            return $products;
        }

        $words = array_filter(preg_split('/\s+/u', $query) ?: [], fn ($w) => mb_strlen($w) >= 3);
        usort($words, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));

        foreach (array_slice($words, 0, 3) as $word) {
            $products = $base()->search($word)->limit(self::LIMIT)->get();
            if ($products->isNotEmpty()) {
                return $products;
            }
        }

        return collect();
    }

    private function listing(ChatContext $ctx, string $intro, Collection $products): array
    {
        if ($products->isEmpty()) {
            return [Blocks::text($intro), $this->menu->backToMenu($ctx)];
        }

        // Every product gets a "Get quote" path straight into a pre-filled form.
        $quotes = $products->take(3)->map(fn (CommodityProduct $p) => Blocks::option(
            $ctx->t('menu.get_quote', ['name' => mb_strimwidth((string) $p->name, 0, 28, '…')]),
            Blocks::action('enquiry.start', ['product_id' => (int) $p->id]),
        ))->all();

        return [
            Blocks::text($intro),
            Blocks::productList($products->map(fn (CommodityProduct $p) => $this->presenter->present($p))->all()),
            $this->menu->backToMenu($ctx, $quotes),
        ];
    }
}
