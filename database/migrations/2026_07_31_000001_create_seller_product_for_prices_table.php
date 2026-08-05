<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A seller's doorstep (F.O.R) price for one listing in one city.
     *
     * F.O.R is otherwise derived — ex-works price plus the cheapest transporter
     * rate to the destination. This table lets a seller quote a city directly,
     * and where a row exists it is the price the buyer sees.
     *
     * State and city are stored as text, matching how `addresses`,
     * `transporter_address_prices` and the seller product's own loading address
     * already record them; there is no cities table to point a key at.
     */
    public function up(): void
    {
        Schema::create('seller_product_for_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->string('state');
            $table->string('city');
            $table->double('price', 15, 2)->default(0);
            $table->timestamps();

            // One price per destination per listing — the save is an upsert on
            // this key, so a seller re-quoting a city overwrites rather than
            // stacking a second row the pricing lookup would have to choose from.
            $table->unique(['product_id', 'state', 'city'], 'seller_for_price_destination_unique');
            $table->index(['user_id', 'product_id']);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('seller_commodity_products')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_product_for_prices');
    }
};
