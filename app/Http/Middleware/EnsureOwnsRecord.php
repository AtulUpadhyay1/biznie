<?php

namespace App\Http\Middleware;

use App\Models\CommodityProductOrder;
use App\Models\CommodityProductOrderDriver;
use App\Models\Notification;
use App\Models\ProductEnquiry;
use App\Models\SellerCommodityProduct;
use App\Models\SellerProductEnquiry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The record behind an id must belong to the caller's company (4.13).
 *
 *   ->middleware('owns:order,seller_user_id,id')        // route {id}
 *   ->middleware('owns:order,customer_user_id,order_id') // body order_id
 *   ->middleware('owns:driver,getOrder.seller_user_id,id')
 *
 * Several legacy (v1, mobile) endpoints loaded a record by id and acted on
 * it with no owner check — a seller could edit another seller's listing, and
 * "pay order due" debited whichever buyer owned the order. One declarative
 * rule per route closes them without touching each controller.
 *
 * Answers 404, not 403: a stranger's id should not confirm the record exists.
 * A staff account acts for the company that added it.
 */
class EnsureOwnsRecord
{
    private const MODELS = [
        'order'          => CommodityProductOrder::class,
        'driver'         => CommodityProductOrderDriver::class,
        'notification'   => Notification::class,
        'enquiry'        => ProductEnquiry::class,
        'seller_enquiry' => SellerProductEnquiry::class,
        'seller_product' => SellerCommodityProduct::class,
    ];

    public function handle(Request $request, Closure $next, string $model, string $ownerPath, string $key = 'id'): Response
    {
        $id = $request->route($key) ?? $request->input($key);

        // Missing id: let the controller's own validation answer.
        if ($id === null || $id === '' || is_array($id)) {
            return $next($request);
        }

        $user = $request->user();
        $owners = array_unique(array_filter([
            (int) $user->id,
            (int) ($user->is_staff ? $user->added_by : $user->id),
        ]));

        $record = (self::MODELS[$model])::find($id);

        if (! $record || ! in_array((int) data_get($record, $ownerPath), $owners, true)) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        return $next($request);
    }
}
