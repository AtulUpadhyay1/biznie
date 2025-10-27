<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('seller_commodity_products', function (Blueprint $table) {
            $table->string('load_within')->default('0')->after('price_validity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_commodity_products', function (Blueprint $table) {
            $table->dropColumn('load_within');
        });
    }
};
