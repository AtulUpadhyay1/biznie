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
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('address_line_one')->nullable()->after('credit_duration_day');
            $table->string('address_line_two')->nullable()->after('address_line_one');
            $table->string('postal_code')->nullable()->after('address_line_two');
            $table->string('country')->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn('address_line_one');
            $table->dropColumn('address_line_two');
            $table->dropColumn('postal_code');
            $table->dropColumn('country');
        });
    }
};
