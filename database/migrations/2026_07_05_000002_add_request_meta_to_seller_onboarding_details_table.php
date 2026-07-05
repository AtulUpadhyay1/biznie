<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_onboarding_details', function (Blueprint $table) {
            $table->enum('request_status', ['draft', 'pending_review', 'approved', 'rejected'])
                ->default('draft')
                ->after('user_id');
            $table->string('request_reference')->nullable()->unique()->after('request_status');
            $table->unsignedTinyInteger('current_step')->default(1)->after('request_reference');
            $table->longText('timeline')->nullable()->after('product_upload_mode');
            $table->timestamp('submitted_at')->nullable()->after('timeline');
            $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('reviewed_at');
            $table->text('review_note')->nullable()->after('reviewed_by');

            $table->index(['request_status', 'updated_at']);
            $table->foreign('reviewed_by')->references('id')->on('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('seller_onboarding_details', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropIndex(['request_status', 'updated_at']);
            $table->dropColumn([
                'review_note',
                'reviewed_by',
                'reviewed_at',
                'submitted_at',
                'timeline',
                'current_step',
                'request_reference',
                'request_status',
            ]);
        });
    }
};
