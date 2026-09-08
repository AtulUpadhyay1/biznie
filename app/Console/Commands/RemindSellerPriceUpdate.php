<?php

namespace App\Console\Commands;

use App\Models\SellerCommodityProduct;
use App\Models\User;
use Illuminate\Console\Command;

/**
 * Daily nudge to sellers whose listed prices have gone stale.
 *
 * Buyers are quoted from the last price a seller saved, so a listing nobody has
 * touched today is quoting yesterday's market. Schedule this once a morning.
 */
class RemindSellerPriceUpdate extends Command
{
    protected $signature = 'biznie:remind-price-update
                            {--hours=20 : How old a price has to be before it is chased}';

    protected $description = 'Remind sellers to update prices on their approved products';

    public function handle(): int
    {
        $cutoff = now()->subHours((int) $this->option('hours'));

        $stale = SellerCommodityProduct::query()
            ->where('status', 'active')
            ->where('updated_at', '<', $cutoff)
            ->selectRaw('user_id, COUNT(*) as stale_count')
            ->groupBy('user_id')
            ->pluck('stale_count', 'user_id');

        if ($stale->isEmpty()) {
            $this->info('Every listed price is current.');

            return self::SUCCESS;
        }

        $users = User::whereIn('id', $stale->keys())->where('status', 1)->get();

        foreach ($users as $user) {
            $count = (int) $stale[$user->id];

            sendNotification(
                $user,
                'Update today\'s prices',
                $count === 1
                    ? 'One of your products still shows an older price. Update it so buyers see today\'s rate.'
                    : $count.' of your products still show older prices. Update them so buyers see today\'s rates.',
                'price_update_reminder',
                ['stale_products' => $count],
                true
            );
        }

        $this->info('Reminded '.$users->count().' seller(s).');

        return self::SUCCESS;
    }
}
