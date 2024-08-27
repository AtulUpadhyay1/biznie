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
            $table->text('cancel_reason')->nullable()->after('final_quantity_by_customer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commodity_product_orders', function (Blueprint $table) {
            $table->dropColumn('cancel_reason');
        });
    }
};
