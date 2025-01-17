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
            $table->string('profile_photo')->nullable()->after('user_id');
            $table->string('company_logo')->nullable()->after('company_name');
            $table->string('company_address')->nullable()->after('company_logo');
            $table->string('state')->nullable()->after('credit_duration_day');
            $table->string('city')->nullable()->after('state');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn('profile_photo');
            $table->dropColumn('company_logo');
            $table->dropColumn('company_address');
            $table->dropColumn('state');
            $table->dropColumn('city');
        });
    }
};
