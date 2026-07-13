<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds the seller "add product" request/approval workflow (mirroring the
     * seller_onboarding_details KYC flow) plus the extra product fields the
     * Add Product wizard collects that were not already on the table.
     */
    public function up(): void
    {
        Schema::table('seller_commodity_products', function (Blueprint $table) {
            // --- Request / approval workflow ---
            $table->enum('request_status', ['draft', 'pending_review', 'approved', 'rejected'])
                ->default('draft')->after('status');
            $table->string('request_reference')->nullable()->unique()->after('request_status');
            $table->unsignedTinyInteger('current_step')->default(1)->after('request_reference');
            $table->longText('timeline')->nullable()->after('current_step');
            $table->timestamp('submitted_at')->nullable()->after('timeline');
            $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('reviewed_at');
            $table->text('review_note')->nullable()->after('reviewed_by');

            // --- Extra product fields from the wizard ---
            $table->string('product_type')->nullable()->after('name');
            $table->text('short_description')->nullable()->after('description');
            $table->string('hsn_code')->nullable()->after('short_description');
            $table->double('tax_rate', 15, 2)->nullable()->after('hsn_code');
            $table->text('quality_description')->nullable()->after('quality_price');
            $table->string('brand_name')->nullable()->after('brand_id');
            $table->string('make')->nullable()->after('brand_name');
            $table->longText('physical_specification')->nullable()->after('specification');
            $table->longText('chemical_specification')->nullable()->after('physical_specification');
            $table->string('weight_unit')->nullable()->after('chemical_specification');
            $table->string('net_weight')->nullable()->after('weight_unit');
            $table->string('tolerance')->nullable()->after('net_weight');
            $table->string('moq')->nullable()->after('tolerance');
            $table->string('moq_unit')->nullable()->after('moq');
            $table->string('country')->nullable()->after('state');
            $table->date('publish_on')->nullable()->after('country');
            // Seller's desired publish state (active/inactive/draft) kept separate
            // from `status` (the live listing flag) and `request_status` (approval).
            $table->string('product_status')->nullable()->after('publish_on');

            $table->index(['request_status', 'updated_at']);
            $table->foreign('reviewed_by')->references('id')->on('admins')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_commodity_products', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropIndex(['request_status', 'updated_at']);
            $table->dropColumn([
                'request_status', 'request_reference', 'current_step', 'timeline',
                'submitted_at', 'reviewed_at', 'reviewed_by', 'review_note',
                'product_type', 'short_description', 'hsn_code', 'tax_rate',
                'quality_description', 'brand_name', 'make',
                'physical_specification', 'chemical_specification',
                'weight_unit', 'net_weight', 'tolerance', 'moq', 'moq_unit',
                'country', 'publish_on', 'product_status',
            ]);
        });
    }
};
