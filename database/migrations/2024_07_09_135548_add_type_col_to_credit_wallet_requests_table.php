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
        Schema::table('credit_wallet_requests', function (Blueprint $table) {
            $table->bigInteger('credit_wallet_document_type_id')->nullable()->after('description');
            $table->text('document_type')->nullable()->after('credit_wallet_document_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_wallet_requests', function (Blueprint $table) {
            $table->dropColumn('credit_wallet_document_type_id');
            $table->dropColumn('document_type');
        });
    }
};
