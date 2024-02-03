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

    // Common api
    Route::group(['middleware' => ['auth:sanctum']], function () {

        // Image upload
        Route::post('image-upload', 'ImageUploadController@imageUpload');

        Route::post('business-interest', 'Auth\AuthApiController@businessInterest');

        // Product Catalogue Api
        Route::get('commodity-product-list', 'CommodityProductApiController@index');
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


    });

});

// Seller api routes
Route::group(['namespace' => 'App\Http\Controllers\Api\Seller', 'prefix' => 'seller'], function () {

    // Seller authenticated route
    Route::group(['middleware' => ['auth:sanctum']], function () {

        // Profile
        Route::get('profile', 'Authenticated\ProfileApiController@profile');
        Route::post('update-profile', 'Authenticated\ProfileApiController@updateProfile');

    });

});
