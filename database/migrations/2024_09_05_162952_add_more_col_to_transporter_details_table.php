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
        Schema::table('transporter_details', function (Blueprint $table) {
            $table->string('alternate_phone')->nullable()->after('address');
            $table->string('aadhar_number')->nullable()->after('alternate_phone');
            $table->string('vehicle')->nullable()->after('aadhar_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transporter_details', function (Blueprint $table) {
            $table->dropColumn('alternate_phone');
            $table->dropColumn('aadhar_number');
            $table->dropColumn('vehicle');
        });
    }
};
