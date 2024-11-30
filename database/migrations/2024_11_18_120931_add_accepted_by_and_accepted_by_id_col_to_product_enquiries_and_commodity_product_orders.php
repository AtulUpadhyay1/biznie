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
        Schema::table('product_enquiries', function (Blueprint $table) {
            $table->string('accepted_by')->nullable()->after('status');
            $table->bigInteger('accepted_by_id')->nullable()->after('accepted_by');
        });

        Schema::table('commodity_product_orders', function (Blueprint $table) {
            $table->string('accepted_by')->nullable()->after('vehicle_notes');
            $table->bigInteger('accepted_by_id')->nullable()->after('accepted_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_enquiries', function (Blueprint $table) {
            $table->dropColumn('accepted_by');
            $table->dropColumn('accepted_by_id');
        });

        Schema::table('commodity_product_orders', function (Blueprint $table) {
            $table->dropColumn('accepted_by');
            $table->dropColumn('accepted_by_id');
        });
    }
};
