<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The sheet a seller uploads when they pick "Bulk Upload" on the store step.
     *
     * Until now the three upload options only recorded which one was chosen and
     * offered nothing to do with it, so the bulk option had nowhere to put a file.
     */
    public function up(): void
    {
        Schema::table('seller_onboarding_details', function (Blueprint $table) {
            $table->string('product_sheet_path')->nullable()->after('product_upload_mode');
        });
    }

    public function down(): void
    {
        Schema::table('seller_onboarding_details', function (Blueprint $table) {
            $table->dropColumn('product_sheet_path');
        });
    }
};
