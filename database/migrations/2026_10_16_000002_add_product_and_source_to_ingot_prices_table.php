<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * B16/B17: market prices had no product — the storefront hard-coded "Live
 * Aluminium Ingot Prices" over a table of MS ingot rates. Each row now names
 * its product and where the rate came from, so the page heading and the
 * Product × Market board are built from the data.
 */
return new class extends Migration
{
    /** Markets the prices board should cover (QA plan 3.6). */
    private const MARKETS = ['RAIPUR', 'RAIGARH', 'DURGAPUR', 'JALNA', 'MUMBAI', 'DELHI'];

    public function up(): void
    {
        Schema::table('ingot_prices', function (Blueprint $table) {
            if (! Schema::hasColumn('ingot_prices', 'product')) {
                $table->string('product', 120)->nullable()->after('id');
            }
            if (! Schema::hasColumn('ingot_prices', 'source')) {
                $table->string('source', 160)->nullable()->after('location');
            }
        });

        // Every existing row was entered through the "Ingot Price" screen.
        DB::table('ingot_prices')->whereNull('product')->update(['product' => 'MS Ingot']);

        $now = now();
        foreach (self::MARKETS as $market) {
            $exists = DB::table('ingot_price_locations')
                ->whereRaw('UPPER(location) = ?', [$market])
                ->exists();
            if (! $exists) {
                DB::table('ingot_price_locations')->insert([
                    'location' => $market, 'is_default' => 0, 'status' => 1,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('ingot_prices', function (Blueprint $table) {
            $table->dropColumn(['product', 'source']);
        });
    }
};
