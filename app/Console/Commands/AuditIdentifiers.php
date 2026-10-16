<?php

namespace App\Console\Commands;

use App\Support\IndianFormat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * One-time audit of identifiers saved before validation existed (B32).
 *
 *   php artisan biznie:audit-identifiers
 *   php artisan biznie:audit-identifiers --csv=storage/app/invalid-identifiers.csv
 *   php artisan biznie:audit-identifiers --repair-wallet      # B31 data fix
 *
 * Read-only by default: it lists every saved mobile, pincode, GSTIN and PAN
 * that fails App\Support\IndianFormat, so admin can ask those users to correct
 * their profile. Nothing is rewritten — a wrong GSTIN cannot be guessed.
 *
 * --repair-wallet fixes cash-wallet rows written with the bank account number
 * as their amount (ReceivePayment / refund, before the fix) by restoring the
 * amount of the ledger entry they were created with.
 */
class AuditIdentifiers extends Command
{
    protected $signature = 'biznie:audit-identifiers
        {--csv= : Also write the findings to this CSV path}
        {--repair-wallet : Restore cash-wallet amounts that hold a bank account number}';

    protected $description = 'Flag saved mobiles, pincodes, GSTINs and PANs that fail validation';

    /** table => [column => check] */
    private const CHECKS = [
        'users'                     => ['phone' => 'mobile'],
        'user_details'              => ['gst_number' => 'gstin', 'pan_number' => 'pan', 'postal_code' => 'pincode'],
        'user_addresses'            => ['phone' => 'mobile', 'gst' => 'gstin', 'pincode' => 'pincode'],
        'seller_kyc_details'        => ['gst_number' => 'gstin', 'postal_code' => 'pincode'],
        'transporter_details'       => ['gst_number' => 'gstin', 'pan_number' => 'pan', 'alternate_phone' => 'mobile'],
        'seller_onboarding_details' => ['gst_number' => 'gstin', 'pan_number' => 'pan', 'pincode' => 'pincode', 'mobile' => 'mobile', 'alternate_mobile' => 'mobile'],
    ];

    public function handle(): int
    {
        $findings = [];

        foreach (self::CHECKS as $table => $columns) {
            $schema = DB::getSchemaBuilder();
            if (! $schema->hasTable($table)) {
                continue;
            }
            $hasUserId = $schema->hasColumn($table, 'user_id');
            $select = array_merge(['id'], $hasUserId ? ['user_id'] : [], array_keys($columns));

            DB::table($table)
                ->when($schema->hasColumn($table, 'deleted_at'), fn ($q) => $q->whereNull('deleted_at'))
                ->select($select)
                ->orderBy('id')
                ->chunk(500, function ($rows) use ($table, $columns, $hasUserId, &$findings) {
                    foreach ($rows as $row) {
                        foreach ($columns as $column => $check) {
                            $value = trim((string) ($row->{$column} ?? ''));
                            if ($value === '' || $this->passes($check, $value)) {
                                continue;
                            }
                            $findings[] = [
                                'table'   => $table,
                                'id'      => $row->id,
                                'user_id' => $hasUserId ? $row->user_id : $row->id,
                                'field'   => $column,
                                'value'   => $value,
                                'problem' => $this->problem($check, $value),
                            ];
                        }
                    }
                });
        }

        if ($findings) {
            $this->table(['table', 'id', 'user_id', 'field', 'value', 'problem'], $findings);
        }
        $this->info(count($findings) . ' invalid value(s) found.');

        if ($path = $this->option('csv')) {
            $handle = fopen(base_path($path), 'w');
            fputcsv($handle, ['table', 'id', 'user_id', 'field', 'value', 'problem']);
            foreach ($findings as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
            $this->info("Written to {$path}");
        }

        if ($this->option('repair-wallet')) {
            $this->repairWalletAmounts();
        }

        return self::SUCCESS;
    }

    private function passes(string $check, string $value): bool
    {
        return match ($check) {
            'mobile'  => IndianFormat::isMobile($value),
            'pincode' => IndianFormat::isPincode($value),
            'gstin'   => IndianFormat::isGstin($value),
            'pan'     => IndianFormat::isPan($value),
        };
    }

    private function problem(string $check, string $value): string
    {
        if ($check === 'gstin' && preg_match(IndianFormat::GSTIN_REGEX, strtoupper($value))) {
            return 'GSTIN check digit / state code invalid';
        }

        return match ($check) {
            'mobile'  => 'not a 10-digit mobile starting 6-9',
            'pincode' => 'not a 6-digit pincode',
            'gstin'   => 'GSTIN format invalid',
            'pan'     => 'PAN format invalid',
        };
    }

    /**
     * Cash-wallet rows whose `amount` equals the account number typed on the
     * ledger entry of the same order. Their real amount is the ledger amount.
     */
    private function repairWalletAmounts(): void
    {
        $rows = DB::table('cash_wallet_transactions as w')
            ->join('commodity_product_order_ledgers as l', function ($join) {
                $join->on('l.order_id', '=', 'w.commodity_product_order_id')
                    ->whereNotNull('l.transaction_account_number')
                    ->where('l.transaction_account_number', '!=', '')
                    ->whereColumn('w.amount', 'l.transaction_account_number');
            })
            ->whereNull('w.deleted_at')
            ->select('w.id', 'w.amount as wrong_amount', 'l.amount as ledger_amount')
            ->get();

        foreach ($rows as $row) {
            DB::table('cash_wallet_transactions')->where('id', $row->id)->update([
                'amount'     => $row->ledger_amount,
                'updated_at' => now(),
            ]);
            $this->line("cash_wallet_transactions #{$row->id}: amount restored to {$row->ledger_amount}");
        }

        $this->info($rows->count() . ' wallet row(s) repaired.');
    }
}
