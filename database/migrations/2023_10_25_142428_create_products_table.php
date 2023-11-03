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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('added_by')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->string('name')->nullable()->index();
            $table->string('slug')->nullable();
            $table->bigInteger('category_id')->unsigned();
            $table->bigInteger('sub_category_id')->unsigned();
            $table->bigInteger('sub_sub_category_id')->unsigned();
            $table->bigInteger('brand_id')->unsigned();
            $table->bigInteger('unit_id')->unsigned();
            $table->integer('min_qty')->default(1);
            $table->tinyInteger('refundable')->default(1);
            $table->longText('images')->nullable();
            $table->longText('color_image')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('featured')->nullable();
            $table->string('flash_deal')->nullable();
            $table->longText('video_url')->nullable();
            $table->longText('colors')->nullable();
            $table->tinyInteger('variant_product')->default(0);
            $table->longText('attributes')->nullable();
            $table->longText('choice_options')->nullable();
            $table->longText('variation')->nullable();
            $table->tinyInteger('published')->default(1);
            $table->double('unit_price', 15, 2)->default(0);
            $table->double('purchase_price', 15, 2)->default(0);
            $table->double('tax', 15, 2)->default(0);
            $table->string('tax_type')->nullable();
            $table->enum('tax_model', ['include', 'exclude'])->nullable()->default('include');
            $table->double('discount', 15, 2)->default(0);
            $table->enum('discount_type', ['flat', 'percent'])->nullable()->default('flat');
            $table->bigInteger('current_stock')->nullable();
            $table->bigInteger('minimum_order_qty')->nullable();
            $table->longText('details')->nullable();
            $table->tinyInteger('free_shipping')->default(0);
            $table->string('attachment')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('featured_status')->default(1);
            $table->double('shipping_cost', 15, 2)->nullable();
            $table->tinyInteger('multiply_qty')->default(1);
            $table->string('code')->nullable();
            $table->string('meta_title')->nullable();
            $table->longText('meta_description')->nullable();
            $table->string('meta_image')->nullable();
            $table->string('search_tags')->nullable();
            $table->string('origin_city')->nullable();
            $table->string('manufacturing_country')->nullable();
            $table->enum('request_status', ['pending', 'approved', 'rejected'])->nullable()->default('pending');
            $table->bigInteger('request_status_updated_by')->nullable()->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
