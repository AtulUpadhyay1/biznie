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
            $table->string('payment_mode')->nullable()->after('status');
            $table->string('credit_day')->nullable()->after('payment_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_enquiries', function (Blueprint $table) {
            $table->dropColumn('payment_mode');
            $table->dropColumn('credit_day');
        });
    }
};
