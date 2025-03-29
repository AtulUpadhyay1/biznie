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
        Schema::table('commodity_product_order_ledgers', function (Blueprint $table) {
            $table->string('transaction_account_name')->nullable()->after('description');
            $table->string('transaction_account_number')->nullable()->after('transaction_account_name');
            $table->string('transaction_bank_name')->nullable()->after('transaction_account_number');
            $table->string('transaction_number')->nullable()->after('transaction_bank_name');
            $table->string('payment_method')->nullable()->after('transaction_number');
            $table->string('date_time')->nullable()->after('payment_method');
            $table->string('file')->nullable()->after('date_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commodity_product_order_ledgers', function (Blueprint $table) {
            $table->dropColumn('transaction_account_name');
            $table->dropColumn('transaction_account_number');
            $table->dropColumn('transaction_bank_name');
            $table->dropColumn('transaction_number');
            $table->dropColumn('payment_method');
            $table->dropColumn('date_time');
            $table->dropColumn('file');
        });
    }
};
