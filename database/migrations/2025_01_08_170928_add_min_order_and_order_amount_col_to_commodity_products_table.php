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
        Schema::table('commodity_products', function (Blueprint $table) {
            $table->string('min_order_qty')->nullable()->after('video_url');
            $table->string('order_amount_type')->nullable()->after('min_order_qty');
            $table->string('required_order_amount')->nullable()->after('order_amount_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commodity_products', function (Blueprint $table) {
            $table->dropColumn('min_order_qty');
            $table->dropColumn('order_amount_type');
            $table->dropColumn('required_order_amount');
        });
    }
};
