<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Another company's numeric ids must not open (B13 / 4.13): `/orders/24`,
 * `/rfq/49`, `/seller/products/9`.
 *
 * Every authenticated V2 route that takes a record id is called, as a fresh
 * account of the right type, with an id that belongs to someone else. Reads
 * must answer 403 or 404 (404 is preferred — it does not confirm the record
 * exists); writes must not succeed. A new `{id}` route without an entry
 * below fails the coverage check, so it cannot ship unchecked.
 */
class RecordAccessControlTest extends TestCase
{
    use DatabaseTransactions;

    /** route => [account type, table the id comes from, extra where] */
    private const ROUTES = [
        ['GET',    'me/orders/{id}',                         'buyer',     'commodity_product_orders'],
        ['GET',    'me/orders/{id}/ledger',                  'buyer',     'commodity_product_orders'],
        ['GET',    'me/enquiries/{id}',                      'buyer',     'product_enquiries'],
        ['PUT',    'me/enquiries/{id}',                      'buyer',     'product_enquiries'],
        ['POST',   'me/enquiries/{id}/accept',               'buyer',     'product_enquiries'],
        ['GET',    'me/enquiries/{id}/bidding',              'buyer',     'product_enquiries'],
        ['GET',    'me/enquiries/{id}/live',                 'buyer',     'product_enquiries'],
        ['POST',   'me/enquiries/{id}/mark-seller',          'buyer',     'product_enquiries'],
        ['PUT',    'me/addresses/{id}',                      'buyer',     'user_addresses'],
        ['DELETE', 'me/addresses/{id}',                      'buyer',     'user_addresses'],
        ['GET',    'me/credit-requests/{id}',                'buyer',     'credit_wallet_requests'],
        ['PUT',    'me/credit-requests/{id}',                'buyer',     'credit_wallet_requests'],
        ['PUT',    'me/notifications/{id}/read',             'buyer',     'notifications', 'user_id'],
        ['GET',    'me/staff/{id}',                          'buyer',     'users', 'is_staff'],
        ['PUT',    'me/staff/{id}',                          'buyer',     'users', 'is_staff'],
        ['DELETE', 'me/staff/{id}',                          'buyer',     'users', 'is_staff'],
        ['DELETE', 'me/watchlist/{id}',                      'buyer',     'bookmark_products'],
        ['GET',    'seller/orders/{id}',                     'seller',    'commodity_product_orders'],
        ['GET',    'seller/orders/{id}/ledger',              'seller',    'commodity_product_orders'],
        ['PUT',    'seller/orders/{id}/status',              'seller',    'commodity_product_orders'],
        ['GET',    'seller/products/{id}',                   'seller',    'seller_commodity_products'],
        ['GET',    'seller/products/{id}/attributes',        'seller',    'seller_commodity_products'],
        ['GET',    'seller/products/{id}/edit',              'seller',    'seller_commodity_products'],
        ['GET',    'seller/products/{id}/for-prices',        'seller',    'seller_commodity_products'],
        ['POST',   'seller/products/{id}/for-prices',        'seller',    'seller_commodity_products'],
        ['DELETE', 'seller/products/{id}/for-prices/{priceId}', 'seller', 'seller_commodity_products'],
        ['PUT',    'seller/products/{id}/price',             'seller',    'seller_commodity_products'],
        ['PUT',    'seller/products/{id}/price-visibility',  'seller',    'seller_commodity_products'],
        ['PUT',    'seller/products/{id}/quality',           'seller',    'seller_commodity_products'],
        ['PUT',    'seller/products/{id}/status',            'seller',    'seller_commodity_products'],
        ['PUT',    'seller/products/{id}/stock',             'seller',    'seller_commodity_products'],
        ['POST',   'seller/products/{id}/submit',            'seller',    'seller_commodity_products'],
        ['PUT',    'seller/products/{id}/variants',          'seller',    'seller_commodity_products'],
        ['GET',    'seller/commodity-products/{id}',         'seller',    'seller_commodity_products'],
        ['PUT',    'seller/commodity-products/{id}/status',  'seller',    'seller_commodity_products'],
        ['GET',    'seller/rfqs/{id}',                       'seller',    'product_enquiries'],
        ['GET',    'seller/rfqs/{id}/leaderboard',           'seller',    'product_enquiries'],
        ['POST',   'seller/rfqs/{id}/quote',                 'seller',    'product_enquiries'],
        ['POST',   'seller/rfqs/{id}/respond',               'seller',    'product_enquiries'],
        ['GET',    'transporter/orders/{id}',                'transport', 'commodity_product_orders'],
        ['PUT',    'transporter/orders/{id}/status',         'transport', 'commodity_product_orders'],
        ['GET',    'transporter/enquiries/{id}',             'transport', 'transporter_product_enquiries'],
        ['GET',    'transporter/enquiries/{id}/bidding',     'transport', 'transporter_product_enquiries'],
        ['POST',   'transporter/enquiries/{id}/respond',     'transport', 'transporter_product_enquiries'],
        ['DELETE', 'transporter/belts/{id}',                 'transport', 'transporter_address_prices'],
    ];

    /**
     * Legacy (v1, mobile) routes: [method, uri, account type, table, where the
     * id goes — 'route' or a body key]. These loaded records with no owner
     * check until the `owns` middleware.
     */
    private const LEGACY_ROUTES = [
        ['GET',    'api/customer/order-ledger/{id}',               'buyer',  'commodity_product_orders', 'route'],
        ['POST',   'api/customer/pay-order-due/{id}',              'buyer',  'commodity_product_orders', 'route'],
        ['POST',   'api/customer/order-status-update',             'buyer',  'commodity_product_orders', 'order_id'],
        ['POST',   'api/customer/update-final-quantity',           'buyer',  'commodity_product_orders', 'order_id'],
        ['POST',   'api/customer/update-quality-check-status',     'buyer',  'commodity_product_orders', 'order_id'],
        ['PUT',    'api/notification/{id}',                        'buyer',  'notifications',            'route'],
        ['POST',   'api/product-enquiry-update/{id}',              'buyer',  'product_enquiries',        'route'],
        ['POST',   'api/product-enquiry-to-order/{id}',            'buyer',  'product_enquiries',        'route'],
        ['GET',    'api/seller/get-commodity-product-basic/{id}',  'seller', 'seller_commodity_products', 'route'],
        ['POST',   'api/seller/update-commodity-product-basic/{id}', 'seller', 'seller_commodity_products', 'route'],
        ['POST',   'api/seller/update-commodity-product-price/{id}', 'seller', 'seller_commodity_products', 'route'],
        ['POST',   'api/seller/update-commodity-product-status/{id}', 'seller', 'seller_commodity_products', 'route'],
        ['POST',   'api/seller/update-commodity-product-variation/{id}', 'seller', 'seller_commodity_products', 'route'],
        ['POST',   'api/seller/product-enquiry/{id}',              'seller', 'seller_product_enquiries', 'route'],
        ['GET',    'api/seller/order-detail/{id}',                 'seller', 'commodity_product_orders', 'route'],
        ['GET',    'api/seller/order-ledger/{id}',                 'seller', 'commodity_product_orders', 'route'],
        ['POST',   'api/seller/order-status-update',               'seller', 'commodity_product_orders', 'order_id'],
        ['POST',   'api/seller/update-invoice',                    'seller', 'commodity_product_orders', 'order_id'],
        ['POST',   'api/seller/driver',                            'seller', 'commodity_product_orders', 'order_id'],
        ['DELETE', 'api/seller/driver/{id}',                       'seller', 'commodity_product_order_drivers', 'route'],
        ['GET',    'api/transporter/order-detail/{id}',            'transport', 'commodity_product_orders', 'route'],
    ];

    /** Shared on purpose: the catalog is the same for every seller. */
    private const PUBLIC_BY_DESIGN = ['seller/catalog-products/{id}'];

    private function freshUser(string $type): User
    {
        $u = new User();
        $u->name     = 'Outsider ' . uniqid();
        $u->type     = $type;
        $u->phone    = (string) random_int(7000000000, 9999999999);
        $u->email    = 'outsider-' . uniqid() . '@example.test';
        $u->password = bcrypt('secret123');
        $u->status   = 'active';
        $u->save();

        return $u;
    }

    public function test_every_id_route_is_covered(): void
    {
        $covered = array_map(fn ($r) => $r[0] . ' ' . $r[1], self::ROUTES);
        $missing = [];

        foreach (app('router')->getRoutes() as $route) {
            $uri = $route->uri();
            if (! str_starts_with($uri, 'api/v2/') || ! str_contains($uri, '{id}')) {
                continue;
            }
            if (! in_array('auth:sanctum', $route->gatherMiddleware(), true)) {
                continue;
            }
            $path = substr($uri, strlen('api/v2/'));
            if (in_array($path, self::PUBLIC_BY_DESIGN, true)) {
                continue;
            }
            foreach (array_diff($route->methods(), ['HEAD']) as $method) {
                if (! in_array("{$method} {$path}", $covered, true)) {
                    $missing[] = "{$method} {$path}";
                }
            }
        }

        $this->assertSame([], $missing, 'Add these id routes to RecordAccessControlTest::ROUTES');
    }

    public function test_other_companies_records_do_not_open(): void
    {
        $outsiders = [];
        $leaks = [];
        $checked = 0;

        foreach (self::ROUTES as $route) {
            [$method, $path, $type, $table] = $route;
            $flag = $route[4] ?? null;

            $query = DB::table($table);
            if ($flag === 'user_id') {
                $query->whereNotNull('user_id');
            } elseif ($flag === 'is_staff') {
                $query->where('is_staff', 1);
            }
            $id = $query->orderBy('id')->value('id');
            if (! $id) {
                continue; // nothing of that kind in this database
            }

            $user = $outsiders[$type] ??= $this->freshUser($type);
            Sanctum::actingAs($user);

            $url = '/api/v2/' . str_replace(['{id}', '{priceId}'], [$id, 1], $path);
            $status = $this->json($method, $url)->status();
            $checked++;

            $refused = $method === 'GET'
                ? in_array($status, [403, 404], true)
                : $status >= 400 && $status < 500;

            if (! $refused) {
                $leaks[] = "{$method} {$url} -> {$status}";
            }
        }

        $this->assertGreaterThan(0, $checked, 'No records to test against.');
        $this->assertSame([], $leaks, "Another company's records opened");
    }

    public function test_owners_and_their_staff_still_get_in(): void
    {
        $order = DB::table('commodity_product_orders')->whereNotNull('customer_user_id')->whereNotNull('seller_user_id')->orderBy('id')->first();
        if (! $order) {
            $this->markTestSkipped('No order to test against.');
        }

        Sanctum::actingAs(User::findOrFail($order->customer_user_id));
        $this->getJson("/api/customer/order-ledger/{$order->id}")->assertOk();

        Sanctum::actingAs(User::findOrFail($order->seller_user_id));
        $this->getJson("/api/seller/order-ledger/{$order->id}")->assertOk();

        // A staff member acts for the company that added them.
        $staff = $this->freshUser('seller');
        $staff->is_staff = 1;
        $staff->added_by = $order->seller_user_id;
        $staff->save();
        Sanctum::actingAs($staff);
        $this->getJson("/api/seller/order-ledger/{$order->id}")->assertOk();
    }

    public function test_legacy_mobile_routes_refuse_other_companies_records(): void
    {
        $outsiders = [];
        $leaks = [];
        $checked = 0;

        foreach (self::LEGACY_ROUTES as [$method, $uri, $type, $table, $idAt]) {
            $query = DB::table($table);
            if ($table === 'notifications') {
                $query->whereNotNull('user_id');
            }
            $id = $query->orderBy('id')->value('id');
            if (! $id) {
                continue;
            }

            Sanctum::actingAs($outsiders[$type] ??= $this->freshUser($type));

            $url = '/' . str_replace('{id}', $id, $uri);
            $body = $idAt === 'route' ? [] : [$idAt => $id];
            $status = $this->json($method, $url, $body)->status();
            $checked++;

            // The owner check runs before validation, so a stranger always
            // gets 404 — never a validation error that confirms the record.
            if ($status !== 404) {
                $leaks[] = "{$method} {$url} -> {$status}";
            }
        }

        $this->assertGreaterThan(0, $checked, 'No records to test against.');
        $this->assertSame([], $leaks, "Another company's records opened");
    }
}
