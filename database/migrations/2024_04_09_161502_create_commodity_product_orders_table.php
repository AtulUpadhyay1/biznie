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
        Schema::create('commodity_product_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('seller_user_id')->unsigned()->nullable();
            $table->bigInteger('customer_user_id')->unsigned()->nullable();
            $table->bigInteger('product_enquiries_id')->unsigned()->nullable();
            $table->bigInteger('seller_product_enquiries_id')->unsigned()->nullable();
            $table->bigInteger('commodity_product_id')->unsigned()->nullable();
            $table->bigInteger('brand_id')->unsigned()->nullable();
            $table->string('unique_id')->nullable();
            $table->string('order_id')->nullable();
            $table->text('origin_city')->nullable();
            $table->text('value')->nullable();
            $table->text('billing_address')->nullable();
            $table->text('delivery_address')->nullable();
            $table->text('consignee_detail')->nullable();
            $table->string('purpose')->nullable();
            $table->text('description')->nullable();
            $table->longText('message')->nullable();
            $table->string('price')->nullable();
            $table->string('base_price')->nullable();
            $table->string('token_amount')->nullable();
            $table->string('transport_price')->nullable();
            $table->string('commission')->nullable();
            $table->text('loading_address')->nullable();
            $table->string('delivery_by')->nullable();
            $table->string('status')->nullable()->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commodity_product_orders');
    }
};
