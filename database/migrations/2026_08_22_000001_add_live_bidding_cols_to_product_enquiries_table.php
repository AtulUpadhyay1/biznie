<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Turns an enquiry into a live reverse auction.
 *
 * The quantity/unit/size/delivery-city the buyer types on the Rate Finder used
 * to be appended to `description` as free text ("Quantity: 30 Tons"), which
 * meant nothing downstream could rank on it or price freight against it. They
 * become real columns here.
 *
 * `bidding_ends_at` is the auction deadline every surface counts down to; it is
 * the single source of truth so the buyer, the seller and the admin never
 * disagree about how much time is left.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_enquiries', function (Blueprint $table) {
            $table->decimal('quantity', 15, 3)->nullable()->after('origin_city');
            $table->unsignedBigInteger('unit_id')->nullable()->after('quantity');
            $table->string('unit_label', 30)->nullable()->after('unit_id');
            $table->string('size_label', 150)->nullable()->after('unit_label');
            $table->string('delivery_city', 120)->nullable()->after('size_label');
            $table->string('delivery_state', 120)->nullable()->after('delivery_city');
            $table->string('required_by', 120)->nullable()->after('delivery_state');

            $table->timestamp('bidding_started_at')->nullable();
            $table->timestamp('bidding_ends_at')->nullable();
            // draft | live | closed | awarded
            $table->string('bidding_status', 20)->nullable()->default('draft');
            $table->decimal('best_for_price', 15, 2)->nullable();

            // The admin "Live RFQs" list is ordered by deadline within a status.
            $table->index(['bidding_status', 'bidding_ends_at'], 'pe_bidding_status_ends_idx');
        });
    }

    public function down(): void
    {
        Schema::table('product_enquiries', function (Blueprint $table) {
            $table->dropIndex('pe_bidding_status_ends_idx');
            $table->dropColumn([
                'quantity',
                'unit_id',
                'unit_label',
                'size_label',
                'delivery_city',
                'delivery_state',
                'required_by',
                'bidding_started_at',
                'bidding_ends_at',
                'bidding_status',
                'best_for_price',
            ]);
        });
    }
};
