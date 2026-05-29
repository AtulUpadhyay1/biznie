<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\Auth\AuthController;
use App\Http\Controllers\Api\V2\Auth\PasswordResetController;
use App\Http\Controllers\Api\V2\HomeController;
use App\Http\Controllers\Api\V2\ProductController;
use App\Http\Controllers\Api\V2\CategoryController;
use App\Http\Controllers\Api\V2\NewsController;
use App\Http\Controllers\Api\V2\PriceController;
use App\Http\Controllers\Api\V2\ContactController;
use App\Http\Controllers\Api\V2\EnquiryController;
use App\Http\Controllers\Api\V2\BrandController;
use App\Http\Controllers\Api\V2\Customer\AddressController as CustomerAddressController;
use App\Http\Controllers\Api\V2\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Api\V2\Customer\EnquiryController as CustomerEnquiryController;
use App\Http\Controllers\Api\V2\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Api\V2\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Api\V2\Customer\WalletController as CustomerWalletController;

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
    Route::post('register', [AuthController::class, 'register']);
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

        Route::get('enquiries', [CustomerEnquiryController::class, 'index']);
        Route::post('enquiries', [CustomerEnquiryController::class, 'store']);
        Route::get('enquiries/{id}', [CustomerEnquiryController::class, 'show']);

        Route::get('wallet', [CustomerWalletController::class, 'summary']);
        Route::get('wallet/cash', [CustomerWalletController::class, 'cash']);
        Route::get('wallet/credit', [CustomerWalletController::class, 'credit']);
    });
});
