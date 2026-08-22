<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The F.O.R (doorstep) price a bid is ranked on.
 *
 * `base_price` / `transport_price` already existed but carry the older
 * per-variation quotation semantics that order conversion still reads, so they
 * are left alone and kept mirrored by the bidding service. The three columns
 * the auction actually ranks on get their own names, and `for_price` is stored
 * rather than derived so the leaderboard can be ordered in SQL.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_product_enquiries', function (Blueprint $table) {
            $table->decimal('ex_works_price', 15, 2)->nullable();
            $table->decimal('freight_charges', 15, 2)->nullable();
            $table->decimal('other_charges', 15, 2)->nullable()->default(0);
            $table->decimal('for_price', 15, 2)->nullable();

            $table->string('ex_works_city', 120)->nullable();
            $table->string('ex_works_state', 120)->nullable();
            // doorstep (F.O.R) | ex_works
            $table->string('freight_type', 30)->nullable()->default('doorstep');

            // app  = the seller submitted it themselves
            // manual = an admin keyed it in on the seller's behalf
            $table->string('price_source', 20)->nullable()->default('app');
            $table->unsignedBigInteger('entered_by_admin_id')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('price_updated_at')->nullable();

            // The leaderboard is "this RFQ, cheapest F.O.R first" on every surface.
            $table->index(['product_enquiries_id', 'for_price'], 'spe_enquiry_for_price_idx');
        });
    }

    public function down(): void
    {
        Schema::table('seller_product_enquiries', function (Blueprint $table) {
            $table->dropIndex('spe_enquiry_for_price_idx');
            $table->dropColumn([
                'ex_works_price',
                'freight_charges',
                'other_charges',
                'for_price',
                'ex_works_city',
                'ex_works_state',
                'freight_type',
                'price_source',
                'entered_by_admin_id',
                'remarks',
                'price_updated_at',
            ]);
        });
    }
};
