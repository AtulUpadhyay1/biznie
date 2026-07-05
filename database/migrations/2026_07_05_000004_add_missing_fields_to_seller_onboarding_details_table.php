<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_onboarding_details', function (Blueprint $table) {
            $table->string('city')->nullable()->after('company_address');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->nullable()->after('state');
            $table->string('account_holder_name')->nullable()->after('branch');
            $table->string('bank_name')->nullable()->after('account_holder_name');
            $table->string('account_number')->nullable()->after('bank_name');
            $table->string('ifsc_code')->nullable()->after('account_number');
        });
    }

    public function down(): void
    {
        Schema::table('seller_onboarding_details', function (Blueprint $table) {
            $table->dropColumn([
                'ifsc_code',
                'account_number',
                'bank_name',
                'account_holder_name',
                'country',
                'state',
                'city',
            ]);
        });
    }
};