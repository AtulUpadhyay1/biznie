<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A fourth price a listing can publish: the basic (pre-charges) rate.
     *
     * Sits alongside show_ex_price / show_for_price / show_fob_price so a seller
     * can lead with Basic, Ex, F.O.R or F.O.B as the deal needs. Defaults off so
     * existing listings keep showing exactly what they show today.
     */
    public function up(): void
    {
        Schema::table('seller_commodity_products', function (Blueprint $table) {
            $table->boolean('show_basic_price')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('seller_commodity_products', function (Blueprint $table) {
            $table->dropColumn('show_basic_price');
        });
    }
};
