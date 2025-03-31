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
        Schema::table('seller_product_enquiries', function (Blueprint $table) {
            $table->integer('seller_credit_days')->nullable()->after('is_mark');
            $table->integer('customer_credit_days')->nullable()->after('seller_credit_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_product_enquiries', function (Blueprint $table) {
            $table->dropColumn('seller_credit_days');
            $table->dropColumn('customer_credit_days');
        });
    }
};
