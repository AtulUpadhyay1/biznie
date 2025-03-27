<?php

use App\Models\IngotPrice;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ingot_prices', function (Blueprint $table) {
            $table->dateTime('date_time')->after('price');
        });

        $list = IngotPrice::all();
        foreach ($list as $item) {
            $item->date_time = $item->created_at;
            $item->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingot_prices', function (Blueprint $table) {
            $table->dropColumn('date_time');
        });
    }
};
