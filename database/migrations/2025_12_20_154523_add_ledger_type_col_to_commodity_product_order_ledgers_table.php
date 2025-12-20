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
            $table->string('ledger_type')->nullable()->default('order')->after('type')->comment('order or transporter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commodity_product_order_ledgers', function (Blueprint $table) {
            $table->dropColumn('ledger_type');
        });
    }
};
