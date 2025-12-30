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
        Schema::table('commodity_product_orders', function (Blueprint $table) {
            $table->string('buyer_invoice_amount')->nullable()->after('seller_invoices');
            $table->string('seller_invoice_amount')->nullable()->after('buyer_invoice_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commodity_product_orders', function (Blueprint $table) {
            $table->dropColumn('buyer_invoice_amount');
            $table->dropColumn('seller_invoice_amount');
        });
    }
};
