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
            $table->decimal('cash_wallet', 15, 2)->nullable()->default(0.00)->after('phone');
            $table->decimal('credit_wallet', 15, 2)->nullable()->default(0.00)->after('cash_wallet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cash_wallet');
            $table->dropColumn('credit_wallet');
        });
    }
};
