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
            $table->string('ebill_expiry_date')->nullable()->after('ebill');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commodity_product_order_drivers', function (Blueprint $table) {
            $table->dropColumn('ebill_expiry_date');
        });
    }
};
