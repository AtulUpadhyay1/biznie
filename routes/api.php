<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {

    // Registeration form info
    Route::get('registration-form-info', 'InfoApiController@registrationFormInfo');

    // Registration & login
    Route::post('register', 'Auth\AuthApiController@register');
    Route::post('email-login', 'Auth\AuthApiController@emailLogin');
    Route::post('otp-login', 'Auth\AuthApiController@otpLogin');
    Route::post('verify-otp', 'Auth\AuthApiController@verifyOtp');

    // Category
    Route::get('category', 'CategoryApiController@category');

    // Address
    Route::get('get-address/{pincode}', 'AddressApiController@getAddress');

    // Home Api
    Route::get('home', 'HomeApiController@home');
    Route::get('view-market-news/{slug}', 'HomeApiController@viewMarketNews');

    // Product List
    Route::get('product-list', 'ProductApiController@index');
    Route::get('commodity-product-list', 'ProductApiController@commodityProductList');
    Route::get('product-detail/{id}', 'ProductApiController@show');
    Route::get('all-seller-commodity-product-list', 'ProductApiController@allSellerCommodityProductList');

    // Common api
    Route::group(['middleware' => ['auth:sanctum']], function () {

        // Image upload
        Route::post('image-upload', 'ImageUploadController@imageUpload');

        Route::post('business-interest', 'Auth\AuthApiController@businessInterest');

        // Notification
        Route::get('notification', 'NotificationApiController@index');
        Route::put('notification/{id}', 'NotificationApiController@update');
        Route::delete('notification', 'NotificationApiController@destroy');
        Route::post('update-fcm-token', 'NotificationApiController@updateToken');

        // Product enquiry
        Route::get('product-enquiry', 'ProductEnquiryApiController@index');
        Route::get('product-enquiry/{id}', 'ProductEnquiryApiController@show');
        Route::post('product-enquiry', 'ProductEnquiryApiController@save');
        Route::post('product-enquiry-update/{id}', 'ProductEnquiryApiController@update');
        Route::post('product-enquiry-to-order/{id}', 'ProductEnquiryApiController@enquiryToOrder');

        // Credit Wallet Requests
        Route::get('credit-wallet', 'CreditWalletApiController@creditWallet');
        Route::post('credit-wallet-request', 'CreditWalletApiController@creditWalletRequest');

        // Wallet Recharge
        Route::get('cash-wallet', 'CashWalletApiController@cashWallet');

    });
});


// Customer api routes
Route::group(['namespace' => 'App\Http\Controllers\Api\Customer', 'prefix' => 'customer'], function () {

    // Customer authenticated route
    Route::group(['middleware' => ['auth:sanctum']], function () {

        // Become Seller
        Route::post('become-seller', 'Authenticated\BecomeSellerApiController@becomeSeller');
        Route::post('updated-address', 'Authenticated\BecomeSellerApiController@updatedAddress');
        Route::post('updated-bank-details', 'Authenticated\BecomeSellerApiController@updatedBankDetails');
        Route::post('updated-kyc-details', 'Authenticated\BecomeSellerApiController@updatedKycDetails');

        // Become Transporter
        Route::post('become-transporter', 'Authenticated\BecomeTransporterApiController@becomeTransport');

        // Profile
        Route::get('profile', 'Authenticated\ProfileApiController@profile');
        Route::post('update-profile', 'Authenticated\ProfileApiController@updateProfile');

        // Order
        Route::get('order', 'Authenticated\OrderApiController@index');
        Route::get('order-ledger/{order_id}', 'Authenticated\OrderApiController@ledger');
        Route::get('order-detail/{id}', 'Authenticated\OrderApiController@show');
        Route::post('update-quality-check-status', 'Authenticated\OrderApiController@qualityCheckStatus');
        Route::post('update-final-quantity', 'Authenticated\OrderApiController@updateFinalQuantity');

    });

});

// Seller api routes
Route::group(['namespace' => 'App\Http\Controllers\Api\Seller', 'prefix' => 'seller'], function () {

    // Seller authenticated route
    Route::group(['middleware' => ['auth:sanctum']], function () {

        // Profile
        Route::get('profile', 'Authenticated\ProfileApiController@profile');
        Route::post('update-profile', 'Authenticated\ProfileApiController@updateProfile');

        // Product Catalogue Api
        Route::get('commodity-product-list', 'Authenticated\CommodityProductApiController@index');
        Route::get('get-brand', 'Authenticated\CommodityProductApiController@getBrand');
        Route::get('get-state', 'Authenticated\CommodityProductApiController@getState');
        Route::get('get-city', 'Authenticated\CommodityProductApiController@getCity');

        Route::get('my-commodity-product-list', 'Authenticated\CommodityProductApiController@myCommodityProductList');
        Route::post('store-commodity-product', 'Authenticated\CommodityProductApiController@store');
        Route::get('get-commodity-product-basic/{id}', 'Authenticated\CommodityProductApiController@edit');
        Route::post('update-commodity-product-basic/{id}', 'Authenticated\CommodityProductApiController@update');
        Route::get('get-commodity-product-price/{id}', 'Authenticated\CommodityProductApiController@getPrice');
        Route::post('update-commodity-product-price/{id}', 'Authenticated\CommodityProductApiController@updatePrice');
        Route::get('get-commodity-product-variation/{id}', 'Authenticated\CommodityProductApiController@getVariation');
        Route::post('update-commodity-product-variation/{id}', 'Authenticated\CommodityProductApiController@updateVariation');
        Route::get('get-commodity-product-variation-stock/{id}', 'Authenticated\CommodityProductApiController@getVariationStock');
        Route::post('update-commodity-product-variation-stock/{id}', 'Authenticated\CommodityProductApiController@updateVariationStock');

        // Product enquiry
        Route::get('product-enquiry', 'Authenticated\ProductEnquiryApiController@index');
        Route::get('product-enquiry/{id}', 'Authenticated\ProductEnquiryApiController@show');
        Route::post('product-enquiry/{id}', 'Authenticated\ProductEnquiryApiController@update');

        // Order
        Route::get('order', 'Authenticated\OrderApiController@index');
        Route::get('order-detail/{id}', 'Authenticated\OrderApiController@show');
        Route::post('order-status-update', 'Authenticated\OrderApiController@statusUpdate');
        Route::post('update-quality-check', 'Authenticated\OrderApiController@qualityCheck');
        Route::post('update-invoice', 'Authenticated\OrderApiController@updateInvoice');
        Route::post('update-final-quantity', 'Authenticated\OrderApiController@updateFinalQuantity');

        Route::post('driver', 'Authenticated\DriverApiController@store');
        Route::put('driver/{id}', 'Authenticated\DriverApiController@update');
        Route::delete('driver/{id}', 'Authenticated\DriverApiController@destroy');

    });

});
