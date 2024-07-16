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
            $table->decimal('total_amount', 15, 2)->nullable()->default(0.00)->after('commission');
            $table->decimal('paid_amount', 15, 2)->nullable()->default(0.00)->after('total_amount');
            $table->decimal('due_amount', 15, 2)->nullable()->default(0.00)->after('paid_amount');
            $table->decimal('final_amount', 15, 2)->nullable()->default(0.00)->after('due_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commodity_product_orders', function (Blueprint $table) {
            $table->dropColumn('total_amount');
            $table->dropColumn('paid_amount');
            $table->dropColumn('due_amount');
            $table->dropColumn('final_amount');
        });
    }
};
