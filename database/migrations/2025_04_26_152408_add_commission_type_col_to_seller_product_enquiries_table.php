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
        Schema::table('seller_product_enquiries', function (Blueprint $table) {
            $table->string('commission_type')->nullable()->after('transport_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_product_enquiries', function (Blueprint $table) {
            $table->dropColumn('commission_type');
        });
    }
};
