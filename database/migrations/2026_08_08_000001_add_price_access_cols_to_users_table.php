<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Whether this seller may quote their own F.O.R and F.O.B prices.
     *
     * Two grants rather than one: F.O.R (doorstep, per destination city) and
     * F.O.B (loaded on board, per port) are separate commercial terms, and a
     * seller who is trusted to quote one is not automatically trusted with the
     * other. F.O.B has no panel yet — the column is here so the card can be
     * mapped onto it when it is built, without a second migration.
     *
     * Both off by default: a seller-quoted price overrides the calculated one
     * for every buyer at that destination, so admin grants it per seller rather
     * than it being on from the moment an account is approved.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('for_price_access')->default(0)->after('block_reason');
            $table->boolean('fob_price_access')->default(0)->after('for_price_access');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['for_price_access', 'fob_price_access']);
        });
    }
};
