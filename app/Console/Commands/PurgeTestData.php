<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Finds QA / demo records that must not be visible to buyers (B41) and, with
 * --apply, deactivates and soft-deletes them.
 *
 *   php artisan biznie:test-data                 # report only
 *   php artisan biznie:test-data --apply         # deactivate + soft delete
 *   php artisan biznie:test-data --ref=SP-20260714-D7E1EF --enquiry=ENQ-4O7ZJFXI --apply
 *
 * Nothing is hard-deleted: every table touched has `deleted_at`, so a row
 * removed by mistake is restored by clearing that column.
 */
class PurgeTestData extends Command
{
    protected $signature = 'biznie:test-data
        {--apply : Deactivate and soft-delete the matches (default is a dry run)}
        {--ref=* : Extra seller product request references, e.g. SP-20260714-D7E1EF}
        {--enquiry=* : Extra general enquiry ids, e.g. ENQ-4O7ZJFXI}';

    protected $description = 'Report (and with --apply, remove) test products, categories, enquiries and addresses';

    /** Catalog names seen in QA that are not part of the steel catalog. */
    private const JUNK_CATEGORY_NAMES = ['rice', 'yellow dal'];

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $refs = array_filter((array) $this->option('ref'));
        $enquiries = array_merge(['ENQ-4O7ZJFXI'], array_filter((array) $this->option('enquiry')));

        $groups = [
            'seller_commodity_products' => [
                'label'  => 'Seller products',
                'cols'   => ['id', 'request_reference', 'name', 'status', 'user_id'],
                'query'  => fn ($q) => $q->where(function ($q) use ($refs) {
                    $q->whereRaw('LOWER(name) REGEXP ?', ['(^|[^a-z])(test|dummy|demo|sample)([^a-z]|$)'])
                        ->when($refs, fn ($q) => $q->orWhereIn('request_reference', $refs));
                }),
                'disable' => ['status' => 'inactive'],
            ],
            'product_categories' => [
                'label'  => 'Product categories',
                'cols'   => ['id', 'name', 'status'],
                'query'  => fn ($q) => $this->junkNames($q),
                'disable' => ['status' => 0],
            ],
            'product_sub_categories' => [
                'label'  => 'Product sub-categories',
                'cols'   => ['id', 'name', 'status'],
                'query'  => fn ($q) => $this->junkNames($q),
                'disable' => ['status' => 0],
            ],
            'general_enquiries' => [
                'label'  => 'General enquiries',
                'cols'   => ['id', 'unique_id', 'name', 'contact_number'],
                'query'  => fn ($q) => $q->where(fn ($q) => $q->whereIn('unique_id', $enquiries)->orWhereRaw('LOWER(name) = ?', ['test user'])),
                'disable' => [],
            ],
            'user_addresses' => [
                'label'  => 'Saved addresses',
                'cols'   => ['id', 'user_id', 'company_name', 'city', 'pincode'],
                'query'  => fn ($q) => $q->whereRaw('LOWER(company_name) REGEXP ?', ['(^|[^a-z])(test|dummy)([^a-z]|$)']),
                'disable' => [],
            ],
        ];

        $total = 0;
        foreach ($groups as $table => $group) {
            if (! DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }

            $rows = $group['query'](DB::table($table)->whereNull('deleted_at'))->get($group['cols']);
            $this->line('');
            $this->info("{$group['label']} ({$table}): {$rows->count()}");
            if ($rows->isNotEmpty()) {
                $this->table($group['cols'], $rows->map(fn ($r) => (array) $r)->all());
            }

            if ($apply && $rows->isNotEmpty()) {
                DB::table($table)->whereIn('id', $rows->pluck('id'))->update(array_merge(
                    $group['disable'],
                    ['deleted_at' => now(), 'updated_at' => now()]
                ));
            }
            $total += $rows->count();
        }

        $this->line('');
        if ($apply) {
            $this->info("Deactivated and soft-deleted {$total} record(s).");
        } else {
            $this->warn("Dry run: {$total} record(s) matched. Review the list, then re-run with --apply.");
        }

        return self::SUCCESS;
    }

    private function junkNames($query)
    {
        return $query->where(function ($q) {
            $q->whereIn(DB::raw('LOWER(TRIM(name))'), self::JUNK_CATEGORY_NAMES)
                ->orWhereRaw('LOWER(name) REGEXP ?', ['(^|[^a-z])test([^a-z]|$)']);
        });
    }
}
