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
        Schema::create('commodity_product_transporter_order_ledgers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->unsigned()->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('type')->nullable();
            $table->string('amount')->nullable();
            $table->string('remaining_balance')->nullable();
            $table->longText('description')->nullable();
            $table->string('transaction_account_name')->nullable();
            $table->string('transaction_account_number')->nullable();
            $table->string('transaction_bank_name')->nullable();
            $table->string('transaction_number')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('date_time')->nullable();
            $table->string('file')->nullable();
            $table->string('status')->nullable()->default('success');
            $table->string('added_by')->nullable();
            $table->bigInteger('added_by_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commodity_product_transporter_order_ledgers');
    }
};
