<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_onboarding_details', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('user_id');
            $table->string('gst_number')->nullable()->after('company_name');
            $table->string('pan_number')->nullable()->after('gst_number');
            $table->text('company_address')->nullable()->after('pan_number');
            $table->unsignedBigInteger('seller_type_id')->nullable()->after('business_type');

            $table->foreign('seller_type_id')->references('id')->on('seller_types')->nullOnDelete();
            $table->index('seller_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('seller_onboarding_details', function (Blueprint $table) {
            $table->dropForeign(['seller_type_id']);
            $table->dropIndex(['seller_type_id']);
            $table->dropColumn(['seller_type_id', 'company_address', 'pan_number', 'gst_number', 'company_name']);
        });
    }
};
