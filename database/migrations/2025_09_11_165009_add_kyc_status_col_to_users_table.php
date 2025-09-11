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
        Schema::table('users', function (Blueprint $table) {
            $table->string('kyc_status')->default('pending')->after('status')->comment('pending, approved, rejected');
            $table->timestamp('kyc_verified_at')->nullable()->after('kyc_status');
            $table->text('kyc_description')->nullable()->after('kyc_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kyc_status');
            $table->dropColumn('kyc_verified_at');
            $table->dropColumn('kyc_description');
        });
    }
};
