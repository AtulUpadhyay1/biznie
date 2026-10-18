<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The price basis the buyer chose on the product page (Basic / Ex-Works /
 * F.O.R — App\Enums\PriceOption). It only lived in the page's state, so
 * neither the sellers nor the admin could tell which price the buyer was
 * comparing on. Null for RFQs raised before, or without the product page.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_enquiries', function (Blueprint $table) {
            $table->string('price_option', 10)->nullable()->after('size_label');
        });
    }

    public function down(): void
    {
        Schema::table('product_enquiries', function (Blueprint $table) {
            $table->dropColumn('price_option');
        });
    }
};
