<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Which of the three prices a seller exposes on their listing.
     *
     * Per product rather than per seller: the same seller can want an ex-works
     * number public on a commodity grade and only a delivered F.O.R number on a
     * branded one. Distinct from `users.for_price_access` / `fob_price_access`,
     * which say whether admin lets the seller quote those prices at all — these
     * say what buyers are shown once they can.
     *
     * Ex-works and F.O.R default on so existing listings keep showing exactly
     * what they show today. F.O.B defaults off because no F.O.B price exists to
     * show yet.
     */
    public function up(): void
    {
        Schema::table('seller_commodity_products', function (Blueprint $table) {
            $table->boolean('show_ex_price')->default(1)->after('status');
            $table->boolean('show_for_price')->default(1)->after('show_ex_price');
            $table->boolean('show_fob_price')->default(0)->after('show_for_price');
        });
    }

    public function down(): void
    {
        Schema::table('seller_commodity_products', function (Blueprint $table) {
            $table->dropColumn(['show_ex_price', 'show_for_price', 'show_fob_price']);
        });
    }
};
