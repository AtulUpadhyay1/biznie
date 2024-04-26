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
        Schema::create('commodity_product_order_drivers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->unsigned()->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->string('unloaded_vehicle_photo')->nullable();
            $table->string('loaded_vehicle_photo')->nullable();
            $table->string('driver_with_vehicle_photo')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('tranking_number')->nullable();
            $table->string('status')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commodity_product_order_drivers');
    }
};
