<?php

use App\Models\CommodityProductOrder;
use App\Services\Orders\OrderSummary;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * B37: an order now keeps its own copy of what was agreed — total quantity,
 * dispatch days and credit days — instead of the seller page reading columns
 * that did not exist and showing "—". Filled when the order is created from
 * the accepted quote (OrderSummary::snapshot) and backfilled here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commodity_product_orders', function (Blueprint $table) {
            $table->decimal('total_quantity', 12, 2)->nullable()->after('value');
            $table->string('quantity_unit', 20)->nullable()->after('total_quantity');
            $table->unsignedSmallInteger('dispatch_days')->nullable()->after('quantity_unit');
            $table->unsignedSmallInteger('buyer_credit_days')->nullable()->after('dispatch_days');
            $table->unsignedSmallInteger('seller_credit_days')->nullable()->after('buyer_credit_days');
        });

        CommodityProductOrder::query()->orderBy('id')->chunkById(200, function ($orders) {
            foreach ($orders as $order) {
                OrderSummary::snapshot($order);
            }
        });
    }

    public function down(): void
    {
        Schema::table('commodity_product_orders', function (Blueprint $table) {
            $table->dropColumn(['total_quantity', 'quantity_unit', 'dispatch_days', 'buyer_credit_days', 'seller_credit_days']);
        });
    }
};
