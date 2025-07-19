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
        Schema::table('commodity_product_order_drivers', function (Blueprint $table) {
            $table->string('debit_note')->nullable()->after('amount');
            $table->decimal('debit_note_amount', 10, 2)->nullable()->after('debit_note');
            $table->string('credit_note')->nullable()->after('debit_note_amount');
            $table->decimal('credit_note_amount', 10, 2)->nullable()->after('credit_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commodity_product_order_drivers', function (Blueprint $table) {
            $table->dropColumn('debit_note');
            $table->dropColumn('debit_note_amount');
            $table->dropColumn('credit_note');
            $table->dropColumn('credit_note_amount');
        });
    }
};
