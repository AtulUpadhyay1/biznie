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
            $table->date('seller_credit_due_date')->nullable()->after('final_quantity_by_customer');
            $table->date('customer_credit_due_date')->nullable()->after('seller_credit_due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commodity_product_orders', function (Blueprint $table) {
            $table->dropColumn('seller_credit_due_date');
            $table->dropColumn('customer_credit_due_date');
        });
    }
};
