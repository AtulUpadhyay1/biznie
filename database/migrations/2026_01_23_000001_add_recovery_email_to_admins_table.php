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
        Schema::table('admins', function (Blueprint $table) {
            $table->string('recovery_email')->nullable()->after('email');
            $table->string('password_reset_otp')->nullable();
            $table->timestamp('password_reset_otp_expires_at')->nullable();
            $table->string('email_change_otp')->nullable();
            $table->timestamp('email_change_otp_expires_at')->nullable();
            $table->string('pending_recovery_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn([
                'recovery_email',
                'password_reset_otp',
                'password_reset_otp_expires_at',
                'email_change_otp',
                'email_change_otp_expires_at',
                'pending_recovery_email'
            ]);
        });
    }
};
