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
        Schema::create('product_enquiry_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('commodity_product_id')->unsigned()->nullable();
            $table->bigInteger('brand_id')->unsigned()->nullable();
            $table->bigInteger('product_enquiry_id')->unsigned()->nullable();
            $table->string('unique_id')->nullable();
            $table->text('origin_city')->nullable();
            $table->text('variation')->nullable();
            $table->text('billing_address')->nullable();
            $table->text('delivery_address')->nullable();
            $table->text('consignee_detail')->nullable();
            $table->string('purpose')->nullable();
            $table->text('description')->nullable();
            $table->longText('message')->nullable();
            $table->string('price')->nullable();
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
        Schema::dropIfExists('product_enquiry_histories');
    }
};
