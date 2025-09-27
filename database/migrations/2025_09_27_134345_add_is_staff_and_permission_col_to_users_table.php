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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('added_by')->nullable()->after('device_type');
            $table->boolean('is_staff')->default(0)->after('added_by');
            $table->longText('permission')->nullable()->after('is_staff');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['added_by']);
            $table->dropColumn('added_by');
            $table->dropColumn('is_staff');
            $table->dropColumn('permission');
        });
    }
};
