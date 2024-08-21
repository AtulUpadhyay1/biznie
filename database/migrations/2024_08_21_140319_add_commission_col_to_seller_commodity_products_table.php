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
        Schema::table('seller_commodity_products', function (Blueprint $table) {
            $table->enum('commission_type', ['exclude', 'include'])->nullable()->default('exclude')->after('loading_address');
            $table->double('commission_amount', 15, 2)->nullable()->default(0)->after('commission_type');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_commodity_products', function (Blueprint $table) {
            $table->dropColumn('commission_type');
            $table->dropColumn('commission_amount');
        });
    }
};
