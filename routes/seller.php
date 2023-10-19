<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Seller as SellerRoot;

/*
|--------------------------------------------------------------------------
| Seller Routes
|--------------------------------------------------------------------------
|
| Here is where you can register seller routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "seller" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('seller.dashboard');
});

Route::group(['as'=>'seller.'], function () {

    Route::get('login', [App\Http\Controllers\Seller\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [App\Http\Controllers\Seller\Auth\LoginController::class, 'login'])->name('login');

    // Authenticated Routes
    Route::group(['middleware' => 'auth'], function () {

        //  Dashboard
        Route::get('dashboard', SellerRoot\DashboardLivewire::class)->name('dashboard');




        //Logout
        Route::post('logout', [App\Http\Controllers\Seller\Auth\LoginController::class, 'logout'])->name('logout');
    });

});
