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
        Schema::create('seller_commodity_product_state_prices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('commodity_product_id')->unsigned()->nullable();
            $table->bigInteger('commodity_product_variation_id')->unsigned()->nullable();
            $table->bigInteger('commodity_product_state_id')->unsigned()->nullable();
            $table->bigInteger('brand_id')->unsigned()->nullable();
            $table->bigInteger('seller_commodity_product_id')->unsigned()->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->longText('value')->nullable();
            $table->string('price')->nullable();
            $table->string('stock')->nullable();
            $table->tinyInteger('is_selected')->nullable()->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_commodity_product_state_prices');
    }
};
