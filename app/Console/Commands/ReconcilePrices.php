<?php

namespace App\Console\Commands;

use App\Http\Resources\SellerListByCommodityProductResource;
use App\Http\Resources\V2\ProductOfferResource;
use App\Http\Resources\V2\Seller\SellerProductResource;
use App\Models\SellerCommodityProduct;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

/**
 * Proves every surface prices an offer the same way (B43).
 *
 *   php artisan biznie:reconcile-prices            # table of every live offer
 *   php artisan biznie:reconcile-prices --only-mismatches
 *
 * For each active listing it reads the ex-works price exactly as each client
 * receives it — the seller's own product page, the public offer card / detail
 * page, and the legacy mobile offer list — and flags any that differ by more
 * than a rupee. Exits non-zero on a mismatch, so it can run in CI or cron.
 */
class ReconcilePrices extends Command
{
    protected $signature = 'biznie:reconcile-prices {--only-mismatches : Hide rows where every surface agrees}';

    protected $description = 'Compare seller, storefront and mobile ex-works prices for every live offer';

    public function handle(): int
    {
        $request = Request::create('/');
        $rows = [];
        $mismatches = 0;

        $listings = SellerCommodityProduct::where('status', 'active')
            ->whereNotNull('commodity_product_id')
            ->where('base_price', '>', 0)
            ->with(['getUser', 'getBrand', 'getStatePrice', 'getCommodityProduct'])
            ->orderBy('id')
            ->get();

        foreach ($listings as $listing) {
            $seller  = (float) ((new SellerProductResource($listing))->resolve($request)['ex_price'] ?? 0);
            $public  = (float) ((new ProductOfferResource($listing))->resolve($request)['ex_works_price'] ?? 0);
            $mobile  = (float) ((new SellerListByCommodityProductResource($listing))->toArray($request)['ex_price'] ?? 0);

            $agree = abs($seller - $public) <= 1 && abs($seller - $mobile) <= 1;
            $mismatches += $agree ? 0 : 1;

            if ($agree && $this->option('only-mismatches')) {
                continue;
            }

            $rows[] = [
                $listing->id,
                $listing->getCommodityProduct?->name ?? $listing->name,
                $listing->getUser?->name,
                number_format($seller, 2),
                number_format($public, 2),
                number_format($mobile, 2),
                $agree ? 'OK' : 'MISMATCH',
            ];
        }

        if ($rows) {
            $this->table(['listing', 'product', 'seller', 'seller page', 'storefront', 'mobile', 'result'], $rows);
        }

        if ($mismatches) {
            $this->error("{$mismatches} of {$listings->count()} offer(s) priced differently across surfaces.");

            return self::FAILURE;
        }

        $this->info("All {$listings->count()} live offer(s) price identically on seller page, storefront and mobile.");

        return self::SUCCESS;
    }
}
