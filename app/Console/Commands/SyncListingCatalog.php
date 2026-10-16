<?php

namespace App\Console\Commands;

use App\Models\SellerCommodityProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Puts every catalog-linked seller listing back under its catalog product's
 * category, sub-category and unit (B07).
 *
 *   php artisan biznie:sync-listing-catalog            # report only
 *   php artisan biznie:sync-listing-catalog --apply    # and fix
 *
 * Listings kept their own copy of the category, so a seller's pick or a later
 * catalog edit could leave "SPONGE IRON" under FINISHED on one screen and RAW
 * MATERIAL on another. New saves are kept in step by the model; this repairs
 * what was saved before. Exits non-zero while mismatches remain unfixed.
 */
class SyncListingCatalog extends Command
{
    protected $signature = 'biznie:sync-listing-catalog {--apply : Write the catalog values onto mismatched listings}';

    protected $description = 'Align seller listings with their catalog product category, sub-category and unit';

    private const FIELDS = ['category_id', 'sub_category_id', 'sub_sub_category_id', 'unit_id'];

    public function handle(): int
    {
        $rows = DB::table('seller_commodity_products as s')
            ->join('commodity_products as c', 'c.id', '=', 's.commodity_product_id')
            ->whereNull('s.deleted_at')
            ->select('s.id', 's.name', 's.status', 'c.name as catalog_name')
            ->addSelect(array_map(fn ($f) => "s.{$f} as s_{$f}", self::FIELDS))
            ->addSelect(array_map(fn ($f) => "c.{$f} as c_{$f}", self::FIELDS))
            ->orderBy('s.id')
            ->get()
            ->filter(function ($r) {
                foreach (self::FIELDS as $f) {
                    // A catalog product without a unit leaves the listing's own.
                    if ($f === 'unit_id' && ! $r->{"c_{$f}"}) {
                        continue;
                    }
                    if ((string) $r->{"s_{$f}"} !== (string) $r->{"c_{$f}"}) {
                        return true;
                    }
                }

                return false;
            });

        if ($rows->isEmpty()) {
            $this->info('Every catalog-linked listing matches its catalog product.');

            return self::SUCCESS;
        }

        $names = fn (string $table, $id) => $id ? (DB::table($table)->where('id', $id)->value('name') ?? "#{$id}") : '—';

        $this->table(
            ['Listing', 'Name', 'Status', 'Category (listing → catalog)', 'Sub-category (listing → catalog)', 'Unit (listing → catalog)'],
            $rows->map(fn ($r) => [
                $r->id,
                $r->name,
                $r->status,
                $names('product_categories', $r->s_category_id) . ' → ' . $names('product_categories', $r->c_category_id),
                $names('product_sub_categories', $r->s_sub_category_id) . ' → ' . $names('product_sub_categories', $r->c_sub_category_id),
                $names('product_units', $r->s_unit_id) . ' → ' . ($r->c_unit_id ? $names('product_units', $r->c_unit_id) : '(kept)'),
            ])->all()
        );

        if (! $this->option('apply')) {
            $this->warn($rows->count() . ' listing(s) differ from their catalog product. Re-run with --apply to fix.');

            return self::FAILURE;
        }

        // Through the model, so its saving hook — the same rule new saves
        // follow — is what writes the values.
        SellerCommodityProduct::whereIn('id', $rows->pluck('id'))->get()->each(function (SellerCommodityProduct $listing) {
            $listing->syncFromCatalog();
            $listing->saveQuietly();
        });

        $this->info('Fixed ' . $rows->count() . ' listing(s).');

        return self::SUCCESS;
    }
}
