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
        Schema::table('commodity_product_states', function (Blueprint $table) {
            $table->string('pincode')->nullable()->after('city');
            $table->string('address_line_one')->nullable()->after('pincode');
            $table->string('address_line_two')->nullable()->after('address_line_one');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commodity_product_states', function (Blueprint $table) {
            $table->dropColumn('pincode');
            $table->dropColumn('address_line_one');
            $table->dropColumn('address_line_two');
        });
    }
};
