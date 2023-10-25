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
            $table->bigInteger('user_id')->unsigned();
            $table->string('name')->nullable()->index();
            $table->longText('description')->nullable();
            $table->bigInteger('category_id')->unsigned();
            $table->bigInteger('sub_category_id')->unsigned();
            $table->bigInteger('sub_sub_category_id')->unsigned();
            $table->bigInteger('brand_id')->unsigned();
            $table->string('code')->nullable();
            $table->bigInteger('unit_id')->unsigned();
            $table->string('search_tags')->nullable();
            $table->decimal('purchase_price', 5, 2)->nullable();
            $table->decimal('unit_price', 5, 2)->nullable();
            $table->bigInteger('minimum_order_qty')->nullable();
            $table->bigInteger('current_stock')->nullable();
            $table->enum('discount_type', ['flat', 'percent'])->nullable()->default('flat');
            $table->decimal('discount', 5, 2)->nullable();
            $table->decimal('tax', 5, 2)->nullable();
            $table->enum('tax_model', ['include', 'exclude'])->nullable()->default('include');
            $table->decimal('shipping_cost', 5, 2)->nullable();
            $table->string('colors_active')->nullable();
            $table->longText('colors')->nullable();
            $table->longText('attributes')->nullable();
            $table->longText('attributes_option')->nullable();
            $table->string('thumbnail')->nullable();
            $table->longText('images')->nullable();
            $table->longText('video_link')->nullable();
            $table->string('meta_title')->nullable();
            $table->longText('meta_description')->nullable();
            $table->string('meta_image')->nullable();
            $table->string('status')->nullable();
            $table->enum('approve_status', ['pending', 'approved', 'rejected'])->nullable()->default('pending');
            $table->bigInteger('approve_status_updated_by')->nullable()->unsigned();
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
