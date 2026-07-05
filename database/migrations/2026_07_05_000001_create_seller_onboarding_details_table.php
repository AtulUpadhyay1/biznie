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
        Schema::create('seller_onboarding_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();

            $table->string('business_type')->nullable();
            $table->string('constitution_type')->nullable();
            $table->unsignedSmallInteger('years_in_business')->nullable();
            $table->string('pincode', 10)->nullable();

            $table->string('contact_person')->nullable();
            $table->string('designation')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('alternate_mobile', 20)->nullable();
            $table->string('email')->nullable();

            $table->string('gst_certificate_path')->nullable();
            $table->string('pan_document_path')->nullable();
            $table->string('registration_certificate_path')->nullable();
            $table->string('address_proof_path')->nullable();
            $table->string('cancelled_cheque_path')->nullable();
            $table->string('other_documents_path')->nullable();

            $table->string('account_type')->nullable();
            $table->string('branch')->nullable();

            $table->string('category')->nullable();
            $table->text('products')->nullable();
            $table->string('turnover')->nullable();
            $table->enum('product_upload_mode', ['existing', 'new', 'bulk'])->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['category', 'product_upload_mode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_onboarding_details');
    }
};
