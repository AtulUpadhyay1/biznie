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
        Schema::create('seller_kyc_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('account_number')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('bank_proof_type')->nullable();
            $table->string('bank_proof')->nullable();
            $table->string('bank_status')->nullable();
            $table->longText('bank_response')->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('identity_type')->nullable();
            $table->string('identity_number')->nullable();
            $table->string('identity_proof')->nullable();
            $table->string('identity_proof_back')->nullable();
            $table->string('address_type')->nullable();
            $table->string('address_proof')->nullable();
            $table->string('address_proof_back')->nullable();
            $table->string('business_registration_certificate')->nullable();
            $table->string('business_registration_number')->nullable();
            $table->string('trademark_registration_proof')->nullable();
            $table->string('gst_type')->nullable();
            $table->string('gst_number')->nullable();
            $table->enum('status', ['pending', 'uploaded', 'approved', 'rejected'])->nullable()->default('pending');
            $table->bigInteger('status_updated_by')->nullable()->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_kyc_details');
    }
};
