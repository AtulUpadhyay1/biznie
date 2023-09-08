<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "admin" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::group(['namespace' => 'App\Http\Controllers\Admin', 'as'=>'admin.'], function () {

    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login')->name('login');

    // Authenticated Routes
    Route::group(['middleware' => 'auth:admin'], function () {

        Route::get('dashboard', 'DashboardController@index')->name('dashboard');


        //Logout
        Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    });

});
