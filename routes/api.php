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

// Vendors api routes
Route::group(['namespace' => 'App\Http\Controllers\Api\Vendors', 'prefix' => 'vendors'], function () {

    // Registeration form info
    Route::get('registration-form-info', 'InfoApiController@registrationFormInfo');

    // Vendors authenticated route
    Route::group(['middleware' => ['auth:sanctum']], function () {

    });

});
