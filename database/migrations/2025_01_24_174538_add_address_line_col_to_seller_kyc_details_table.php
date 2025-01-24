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
        Schema::table('seller_kyc_details', function (Blueprint $table) {
            $table->string('address_line_one')->nullable()->after('address');
            $table->string('address_line_two')->nullable()->after('address_line_one');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_kyc_details', function (Blueprint $table) {
            $table->dropColumn('address_line_two');
            $table->dropColumn('address_line_one');
        });
    }
};
