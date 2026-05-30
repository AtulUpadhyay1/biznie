<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\Auth\AuthController;
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
use App\Http\Controllers\Api\V2\AddressLookupController;
use App\Http\Controllers\Api\V2\Customer\AddressController as CustomerAddressController;
use App\Http\Controllers\Api\V2\Customer\CreditRequestController as CustomerCreditRequestController;
use App\Http\Controllers\Api\V2\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Api\V2\Customer\EnquiryController as CustomerEnquiryController;
use App\Http\Controllers\Api\V2\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Api\V2\Customer\OrderLedgerController as CustomerOrderLedgerController;
use App\Http\Controllers\Api\V2\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Api\V2\Customer\WalletController as CustomerWalletController;
use App\Http\Controllers\Api\V2\Customer\WatchlistController as CustomerWatchlistController;
use App\Http\Controllers\Api\V2\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Api\V2\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Api\V2\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Api\V2\Seller\QuotationController as SellerQuotationController;
use App\Http\Controllers\Api\V2\NotificationController;
use App\Http\Controllers\Api\V2\StaffController;
use App\Http\Controllers\Api\V2\Transporter\DashboardController as TransporterDashboardController;
use App\Http\Controllers\Api\V2\Transporter\EnquiryController as TransporterEnquiryController;
use App\Http\Controllers\Api\V2\Transporter\OrderController as TransporterOrderController;

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
Route::get('categories/{slug}', [CategoryController::class, 'show']);
Route::get('brands', [BrandController::class, 'index']);

Route::get('products', [ProductController::class, 'index']);
Route::get('products/{slug}', [ProductController::class, 'show']);

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

    // Customer dashboard surface
    Route::prefix('me')->group(function () {
        Route::get('dashboard', [CustomerDashboardController::class, 'index']);

        Route::get('profile', [CustomerProfileController::class, 'show']);
        Route::put('profile', [CustomerProfileController::class, 'update']);

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
        Route::post('enquiries/{id}/mark-seller', [CustomerEnquiryController::class, 'markSeller']);
        Route::post('enquiries/{id}/accept', [CustomerEnquiryController::class, 'acceptQuotation']);

        Route::get('wallet', [CustomerWalletController::class, 'summary']);
        Route::get('wallet/cash', [CustomerWalletController::class, 'cash']);
        Route::get('wallet/credit', [CustomerWalletController::class, 'credit']);

        // Watchlist (bookmarks)
        Route::get('watchlist', [CustomerWatchlistController::class, 'index']);
        Route::post('watchlist', [CustomerWatchlistController::class, 'store']);
        Route::delete('watchlist/{id}', [CustomerWatchlistController::class, 'destroy']);

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
        Route::put('orders/{id}/status', [SellerOrderController::class, 'updateStatus']);

        Route::get('rfqs', [SellerQuotationController::class, 'index']);
        Route::get('rfqs/{id}', [SellerQuotationController::class, 'show']);
        Route::get('rfqs/{id}/bidding', [SellerQuotationController::class, 'biddingList']);
        Route::post('rfqs/{id}/respond', [SellerQuotationController::class, 'respond']);

        Route::get('products', [SellerProductController::class, 'index']);
        Route::get('products/{id}', [SellerProductController::class, 'show']);
        Route::put('products/{id}/status', [SellerProductController::class, 'updateStatus']);
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
    });
});
