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
        Schema::table('commodity_product_order_drivers', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->nullable()->default(0)->after('final_quantity_by_seller');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commodity_product_order_drivers', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
