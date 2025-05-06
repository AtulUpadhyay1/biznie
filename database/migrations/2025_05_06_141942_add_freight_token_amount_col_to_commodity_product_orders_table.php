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
            $table->string('total_ex_factory_amount')->nullable()->after('base_price');
            $table->string('total_freight_amount')->nullable()->after('total_ex_factory_amount');
            $table->string('freight_token_amount')->nullable()->after('token_amount');
            $table->string('commission_type')->nullable()->after('transport_price');
            $table->string('gst')->nullable()->after('commission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commodity_product_orders', function (Blueprint $table) {
            $table->dropColumn('total_ex_factory_amount');
            $table->dropColumn('total_freight_amount');
            $table->dropColumn('freight_token_amount');
            $table->dropColumn('commission_type');
            $table->dropColumn('gst');
        });
    }
};
