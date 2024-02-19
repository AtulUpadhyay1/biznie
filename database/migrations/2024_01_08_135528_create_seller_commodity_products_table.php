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
        Schema::create('seller_commodity_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('commodity_product_id')->unsigned()->nullable();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->bigInteger('sub_category_id')->unsigned()->nullable();
            $table->bigInteger('sub_sub_category_id')->unsigned()->nullable();
            $table->text('brand_id')->nullable();
            $table->bigInteger('unit_id')->unsigned()->nullable();
            $table->longText('description')->nullable();
            $table->longText('packaging_type')->nullable();
            $table->longText('packaging_type_price')->nullable();
            $table->double('base_price', 15, 2)->nullable()->default(0);
            $table->double('loading_charge', 15, 2)->nullable()->default(0);
            $table->double('insurance_charge', 15, 2)->nullable()->default(0);
            $table->double('quality_charge', 15, 2)->nullable()->default(0);
            $table->double('gst', 15, 2)->nullable()->default(0);
            $table->double('tcs', 15, 2)->nullable()->default(0);
            $table->longText('charge_name')->nullable();
            $table->longText('charge_price')->nullable();
            $table->longText('operator')->nullable();
            $table->text('unit')->nullable();
            $table->text('attributes')->nullable();
            $table->longText('variation')->nullable();
            $table->longText('size')->nullable();
            $table->longText('size_price')->nullable();
            $table->longText('dimension')->nullable();
            $table->longText('dimension_price')->nullable();
            $table->longText('specification')->nullable();
            $table->tinyInteger('is_quality')->nullable()->default(0);
            $table->longText('quality')->nullable();
            $table->longText('quality_price')->nullable();
            $table->longText('specification_notes')->nullable();
            $table->string('thumbnail')->nullable();
            $table->longText('images')->nullable();
            $table->text('loading_address')->nullable();
            $table->longText('video_url')->nullable();
            $table->string('meta_title')->nullable();
            $table->longText('meta_description')->nullable();
            $table->string('meta_image')->nullable();
            $table->enum('status', ['active', 'inactive'])->nullable()->default('active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_commodity_products');
    }
};
