<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\Auth\AuthController;
use App\Http\Controllers\Api\V2\Auth\PasswordController;
use App\Http\Controllers\Api\V2\Auth\PasswordResetController;
use App\Http\Controllers\Api\V2\Auth\RegisterOtpController;
use App\Http\Controllers\Api\V2\HomeController;
use App\Http\Controllers\Api\V2\ProductController;
use App\Http\Controllers\Api\V2\CategoryController;
use App\Http\Controllers\Api\V2\NewsController;
use App\Http\Controllers\Api\V2\PriceController;
use App\Http\Controllers\Api\V2\ContactController;
use App\Http\Controllers\Api\V2\EnquiryController;
use App\Http\Controllers\Api\V2\BrandController;
use App\Http\Controllers\Api\V2\PackagingTypeController;
use App\Http\Controllers\Api\V2\ProductUnitController;
use App\Http\Controllers\Api\V2\AddressLookupController;
use App\Http\Controllers\Api\V2\Customer\AddressController as CustomerAddressController;
use App\Http\Controllers\Api\V2\Customer\CreditRequestController as CustomerCreditRequestController;
use App\Http\Controllers\Api\V2\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Api\V2\Customer\EnquiryController as CustomerEnquiryController;
use App\Http\Controllers\Api\V2\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Api\V2\Customer\OrderLedgerController as CustomerOrderLedgerController;
use App\Http\Controllers\Api\V2\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Api\V2\Customer\PromotionController as CustomerPromotionController;
use App\Http\Controllers\Api\V2\Customer\WalletController as CustomerWalletController;
use App\Http\Controllers\Api\V2\Customer\WatchlistController as CustomerWatchlistController;
use App\Http\Controllers\Api\V2\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Api\V2\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Api\V2\Seller\CatalogProductController as SellerCatalogProductController;
use App\Http\Controllers\Api\V2\Seller\CommodityProductController as SellerCommodityProductController;
use App\Http\Controllers\Api\V2\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Api\V2\Seller\ProductForPriceController as SellerProductForPriceController;
use App\Http\Controllers\Api\V2\Seller\ProductUpdateController as SellerProductUpdateController;
use App\Http\Controllers\Api\V2\Seller\QuotationController as SellerQuotationController;
use App\Http\Controllers\Api\V2\NotificationController;
use App\Http\Controllers\Api\V2\StaffController;
use App\Http\Controllers\Api\V2\Transporter\DashboardController as TransporterDashboardController;
use App\Http\Controllers\Api\V2\Transporter\EnquiryController as TransporterEnquiryController;
use App\Http\Controllers\Api\V2\Transporter\OrderController as TransporterOrderController;
use App\Http\Controllers\Api\V2\Transporter\AssetController as TransporterAssetController;

/*
|--------------------------------------------------------------------------
| Biznie v2 API Routes (consumed by biznie-next-app)
|--------------------------------------------------------------------------
| All routes are prefixed with /api/v2.
| Legacy /api routes in routes/api.php remain untouched.
*/

// ---------- Public ----------
Route::get('handshake', fn () => response()->json(['success' => true, 'version' => 'v2']));

// Auth (public)
Route::prefix('auth')->group(function () {
    Route::post('register/send-otp', [RegisterOtpController::class, 'send'])
        ->middleware('throttle:6,1');
    Route::post('register', [AuthController::class, 'register'])
        ->middleware('throttle:10,1');
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [PasswordResetController::class, 'forgot'])
        ->middleware('throttle:6,1');
    Route::post('reset-password', [PasswordResetController::class, 'reset'])
        ->middleware('throttle:6,1');
});

// Public catalog
Route::get('home', [HomeController::class, 'home']);
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{categoryId}/sub-categories', [CategoryController::class, 'subCategories'])
    ->whereNumber('categoryId');
Route::get('categories/{slug}', [CategoryController::class, 'show']);
Route::get('brands', [BrandController::class, 'index']);
Route::get('packaging-types', [PackagingTypeController::class, 'index']);
Route::get('units', [ProductUnitController::class, 'index']);

Route::get('products', [ProductController::class, 'index']);
Route::get('products/{slug}', [ProductController::class, 'show']);
Route::get('products/{slug}/offers', [ProductController::class, 'offers']);

Route::get('news', [NewsController::class, 'index']);
Route::get('news/{slug}', [NewsController::class, 'show']);

Route::get('prices', [PriceController::class, 'index']);
Route::get('prices/{id}/history', [PriceController::class, 'history']);

Route::post('contact', [ContactController::class, 'store']);
Route::post('enquiry', [EnquiryController::class, 'store']);

// Address lookup (public)
Route::get('address/pincode/{pincode}', [AddressLookupController::class, 'pincode'])->whereNumber('pincode');
Route::get('address/states', [AddressLookupController::class, 'states']);
Route::get('address/states/{state}/cities', [AddressLookupController::class, 'cities']);

// ---------- Authenticated ----------
Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('auth/logout-all', [AuthController::class, 'logoutAll']);
    Route::put('auth/password', [PasswordController::class, 'update']);

    // Customer dashboard surface
    Route::prefix('me')->group(function () {
        Route::get('dashboard', [CustomerDashboardController::class, 'index']);

        Route::get('profile', [CustomerProfileController::class, 'show']);
        Route::put('profile', [CustomerProfileController::class, 'update']);

        // Role promotion (customer → seller / transporter)
        Route::get('seller-types', [CustomerPromotionController::class, 'sellerTypes']);
        Route::get('seller-request', [CustomerPromotionController::class, 'sellerRequest']);
        Route::post('seller-request/step', [CustomerPromotionController::class, 'saveSellerRequestStep']);
        Route::post('seller-request/submit', [CustomerPromotionController::class, 'submitSellerRequest']);
        Route::post('become-seller', [CustomerPromotionController::class, 'becomeSeller']);
        Route::post('become-transporter', [CustomerPromotionController::class, 'becomeTransporter']);

        Route::get('addresses', [CustomerAddressController::class, 'index']);
        Route::post('addresses', [CustomerAddressController::class, 'store']);
        Route::put('addresses/{id}', [CustomerAddressController::class, 'update']);
        Route::delete('addresses/{id}', [CustomerAddressController::class, 'destroy']);

        Route::get('orders', [CustomerOrderController::class, 'index']);
        Route::get('orders/{id}', [CustomerOrderController::class, 'show']);
        Route::get('orders/{id}/ledger', [CustomerOrderLedgerController::class, 'index']);

        Route::get('enquiries', [CustomerEnquiryController::class, 'index']);
        Route::post('enquiries', [CustomerEnquiryController::class, 'store']);
        Route::get('enquiries/{id}', [CustomerEnquiryController::class, 'show']);
        Route::put('enquiries/{id}', [CustomerEnquiryController::class, 'update']);
        Route::get('enquiries/{id}/bidding', [CustomerEnquiryController::class, 'biddingList']);
        // The blind "best price" panel: winning F.O.R price and the countdown,
        // with no seller identity in the payload.
        Route::get('enquiries/{id}/live', [CustomerEnquiryController::class, 'live']);
        Route::post('enquiries/{id}/mark-seller', [CustomerEnquiryController::class, 'markSeller']);
        Route::post('enquiries/{id}/accept', [CustomerEnquiryController::class, 'acceptQuotation']);

        Route::get('wallet', [CustomerWalletController::class, 'summary']);
        Route::get('wallet/cash', [CustomerWalletController::class, 'cash']);
        Route::get('wallet/credit', [CustomerWalletController::class, 'credit']);

        // Watchlist (bookmarks)
        Route::get('watchlist', [CustomerWatchlistController::class, 'index']);
        // Declared before `watchlist/{id}` so the literal segment wins.
        Route::get('watchlist/status', [CustomerWatchlistController::class, 'status']);
        Route::post('watchlist', [CustomerWatchlistController::class, 'store']);
        Route::delete('watchlist/{id}', [CustomerWatchlistController::class, 'destroy'])->whereNumber('id');

        // Credit wallet requests
        Route::get('credit-document-types', [CustomerCreditRequestController::class, 'documentTypes']);
        Route::get('credit-requests', [CustomerCreditRequestController::class, 'index']);
        Route::post('credit-requests', [CustomerCreditRequestController::class, 'store']);
        Route::get('credit-requests/{id}', [CustomerCreditRequestController::class, 'show']);
        Route::put('credit-requests/{id}', [CustomerCreditRequestController::class, 'update']);

        // Staff (sub-user) management — owner only
        Route::get('staff/permission-template', [StaffController::class, 'permissionTemplate']);
        Route::get('staff', [StaffController::class, 'index']);
        Route::post('staff', [StaffController::class, 'store']);
        Route::get('staff/{id}', [StaffController::class, 'show'])->whereNumber('id');
        Route::put('staff/{id}', [StaffController::class, 'update'])->whereNumber('id');
        Route::delete('staff/{id}', [StaffController::class, 'destroy'])->whereNumber('id');

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::put('notifications/read-all', [NotificationController::class, 'markAllRead']);
        Route::put('notifications/{id}/read', [NotificationController::class, 'markRead'])->whereNumber('id');
        Route::delete('notifications', [NotificationController::class, 'destroy']);

        Route::get('notification-settings', [NotificationController::class, 'getSettings']);
        Route::put('notification-settings', [NotificationController::class, 'updateSettings']);

        Route::post('fcm-token', [NotificationController::class, 'updateToken']);
        Route::delete('fcm-token', [NotificationController::class, 'clearToken']);
    });

    // Seller surface
    Route::prefix('seller')->middleware('user.type:seller')->group(function () {
        Route::get('dashboard', [SellerDashboardController::class, 'index']);

        Route::get('orders', [SellerOrderController::class, 'index']);
        Route::get('orders/{id}', [SellerOrderController::class, 'show']);
        Route::get('orders/{id}/ledger', [SellerOrderController::class, 'ledger']);
        Route::put('orders/{id}/status', [SellerOrderController::class, 'updateStatus']);

        Route::get('rfqs', [SellerQuotationController::class, 'index']);
        Route::get('rfqs/{id}', [SellerQuotationController::class, 'show']);
        // Anonymised price ladder plus this seller's own rank in it.
        Route::get('rfqs/{id}/leaderboard', [SellerQuotationController::class, 'leaderboard']);
        Route::post('rfqs/{id}/quote', [SellerQuotationController::class, 'quote']);
        // Superseded by `quote`; still routed for older mobile builds.
        Route::post('rfqs/{id}/respond', [SellerQuotationController::class, 'respond']);

        // The admin catalog behind the wizard's Product Name type-ahead.
        // Declared before `products/{id}` so the literal segment wins.
        Route::get('catalog-products', [SellerCatalogProductController::class, 'index']);
        Route::get('catalog-products/{id}', [SellerCatalogProductController::class, 'show'])->whereNumber('id');

        Route::get('products', [SellerProductController::class, 'index']);
        Route::post('products/step', [SellerProductController::class, 'saveStep']);
        Route::post('products/{id}/submit', [SellerProductController::class, 'submit'])->whereNumber('id');
        Route::get('products/{id}/edit', [SellerProductController::class, 'editData'])->whereNumber('id');
        Route::get('products/{id}', [SellerProductController::class, 'show'])->whereNumber('id');
        Route::put('products/{id}/status', [SellerProductController::class, 'updateStatus'])->whereNumber('id');

        // Day-to-day edits on an existing product (outside the request wizard).
        Route::get('products/{id}/attributes', [SellerProductUpdateController::class, 'attributes'])->whereNumber('id');
        Route::put('products/{id}/price', [SellerProductUpdateController::class, 'updatePrice'])->whereNumber('id');
        Route::put('products/{id}/variants', [SellerProductUpdateController::class, 'updateVariants'])->whereNumber('id');
        Route::put('products/{id}/quality', [SellerProductUpdateController::class, 'updateQuality'])->whereNumber('id');
        Route::put('products/{id}/stock', [SellerProductUpdateController::class, 'updateStock'])->whereNumber('id');
        // Which of ex / F.O.R / F.O.B the listing shows buyers.
        Route::put('products/{id}/price-visibility', [SellerProductUpdateController::class, 'updatePriceVisibility'])
            ->whereNumber('id');

        // City-wise F.O.R prices. `store` is create-or-update on (state, city).
        Route::get('products/{id}/for-prices', [SellerProductForPriceController::class, 'index'])->whereNumber('id');
        Route::post('products/{id}/for-prices', [SellerProductForPriceController::class, 'store'])->whereNumber('id');
        Route::delete('products/{id}/for-prices/{priceId}', [SellerProductForPriceController::class, 'destroy'])
            ->whereNumber('id')->whereNumber('priceId');

        Route::get('commodity-products', [SellerCommodityProductController::class, 'index']);
        Route::get('commodity-products/{id}', [SellerCommodityProductController::class, 'show']);
        Route::put('commodity-products/{id}/status', [SellerCommodityProductController::class, 'updateStatus']);
    });

    // Transporter surface
    Route::prefix('transporter')->middleware('user.type:transporter')->group(function () {
        Route::get('dashboard', [TransporterDashboardController::class, 'index']);

        Route::get('orders', [TransporterOrderController::class, 'index']);
        Route::get('orders/{id}', [TransporterOrderController::class, 'show']);
        Route::put('orders/{id}/status', [TransporterOrderController::class, 'updateStatus']);

        Route::get('enquiries', [TransporterEnquiryController::class, 'index']);
        Route::get('enquiries/{id}', [TransporterEnquiryController::class, 'show']);
        Route::get('enquiries/{id}/bidding', [TransporterEnquiryController::class, 'biddingList']);
        Route::post('enquiries/{id}/respond', [TransporterEnquiryController::class, 'respond']);

        // Asset assignment (vehicles, commodity products, belts)
        Route::get('vehicles', [TransporterAssetController::class, 'vehicles']);
        Route::post('vehicles', [TransporterAssetController::class, 'assignVehicles']);
        Route::get('products', [TransporterAssetController::class, 'products']);
        Route::post('products', [TransporterAssetController::class, 'assignProducts']);
        Route::get('belts', [TransporterAssetController::class, 'belts']);
        Route::post('belts', [TransporterAssetController::class, 'storeBelt']);
        Route::delete('belts/{id}', [TransporterAssetController::class, 'destroyBelt'])->whereNumber('id');
    });
});
