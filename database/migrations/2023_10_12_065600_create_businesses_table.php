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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('name')->nullable()->index();
            $table->longText('about')->nullable();
            $table->longText('category')->nullable();
            $table->longText('type')->nullable();
            $table->longText('seller_type')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('cover')->nullable();
            $table->longText('gallery')->nullable();
            $table->enum('is_close', ['no', 'yes'])->nullable()->default('no');
            $table->tinyInteger('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
