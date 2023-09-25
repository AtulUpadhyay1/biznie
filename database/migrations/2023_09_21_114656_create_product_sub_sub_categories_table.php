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
        Schema::create('product_sub_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('business_category_id')->unsigned();
            $table->bigInteger('product_category_id')->unsigned();
            $table->bigInteger('product_sub_category_id')->unsigned();
            $table->string('name');
            $table->string('slug');
            $table->string('icon')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('banner')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('featured')->default(0);
            $table->string('meta_title')->nullable();
            $table->longText('meta_keywords')->nullable();
            $table->longText('meta_description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sub_sub_categories');
    }
};
