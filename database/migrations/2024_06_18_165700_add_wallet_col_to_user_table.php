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
            $table->decimal('cash_balance', 15, 2)->nullable()->default(0.00)->after('phone');
            $table->decimal('credit_balance', 15, 2)->nullable()->default(0.00)->after('cash_balance');
            $table->decimal('assign_credit_balance', 15, 2)->nullable()->default(0.00)->after('credit_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cash_balance');
            $table->dropColumn('credit_balance');
            $table->dropColumn('assign_credit_balance');
        });
    }
};
