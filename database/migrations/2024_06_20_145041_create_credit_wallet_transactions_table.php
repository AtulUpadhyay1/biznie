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
        Schema::create('credit_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('transaction_id')->nullable();
            $table->decimal('amount', 15, 2)->nullable()->default(0.00);
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['credit', 'debit'])->nullable();
            $table->string('transaction_status')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_wallet_transactions');
    }
};
