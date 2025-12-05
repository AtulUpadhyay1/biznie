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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model')->nullable();
            $table->boolean('notify_new_order_enquiry')->default(true);
            $table->boolean('notify_seller_reply')->default(true);
            $table->boolean('notify_booking_confirmed')->default(true);
            $table->unique(['model', 'model_id'], 'notification_settings_model_modelid_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
