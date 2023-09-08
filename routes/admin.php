<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\DashboardLivewire;

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

Route::group(['as'=>'admin.'], function () {

    Route::get('login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'login'])->name('login');

    Route::get('dashboard', DashboardLivewire::class)->name('dashboard');

    // Authenticated Routes
    Route::group(['middleware' => 'auth:admin'], function () {

        // Admin Dashboard
        // Route::get('dashboard', DashboardLivewire::class)->name('dashboard');

        //Logout
        Route::post('logout', [App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('logout');
    });

});
