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
            $table->text('admin_quality_check_message')->nullable()->after('quality_check_image_status_updated_by');
            $table->text('seller_quality_check_message')->nullable()->after('admin_quality_check_message');
            $table->text('customer_quality_check_message')->nullable()->after('seller_quality_check_message');
            $table->string('customer_quality_check_visibility')->nullable()->after('customer_quality_check_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commodity_product_orders', function (Blueprint $table) {
            $table->dropColumn('admin_quality_check_message');
            $table->dropColumn('seller_quality_check_message');
            $table->dropColumn('customer_quality_check_message');
            // $table->dropColumn('customer_quality_check_visibility');
        });
    }
};
