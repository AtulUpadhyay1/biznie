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
        Schema::create('transporter_product_enquiries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_enquiries_id')->nullable();
            $table->unsignedBigInteger('customer_user_id')->nullable();
            $table->unsignedBigInteger('commodity_product_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('unique_id')->nullable();
            $table->text('origin_city')->nullable();
            $table->text('billing_address')->nullable();
            $table->text('delivery_address')->nullable();
            $table->text('consignee_detail')->nullable();
            $table->string('purpose')->nullable();
            $table->text('description')->nullable();
            $table->longText('message')->nullable();
            $table->string('status')->nullable()->default('pending');
            $table->longText('history')->nullable();
            $table->tinyInteger('is_mark')->nullable()->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transporter_product_enquiries');
    }
};
