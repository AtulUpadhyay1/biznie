<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin as AdminRoot;

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


    // Authenticated Routes
    Route::group(['middleware' => 'auth:admin'], function () {

        // Admin Dashboard
        Route::get('dashboard', AdminRoot\DashboardLivewire::class)->name('dashboard');

        // Business Category
        Route::get('business-category', AdminRoot\BusinessCategoryLivewire::class)->name('business-category');

        //Vendor Type
        Route::get('vendor-type', AdminRoot\VendorTypeLivewire::class)->name('vendor-type');

         //Seller Type
         Route::get('seller-type', AdminRoot\SellerTypeLivewire::class)->name('seller-type');

        //Product Category
        Route::get('product-category', AdminRoot\ProductCategoryLivewire::class)->name('product-category');

        //Product Sub Category
        Route::get('product-sub-category', AdminRoot\ProductSubCategoryLivewire::class)->name('product-sub-category');

        //Product Sub Sub Category
        Route::get('product-sub-subcategory', AdminRoot\ProductSubSubCategoryLivewire::class)->name('product-sub-subcategory');

        //Product Unit
        Route::get('product-unit', AdminRoot\ProductUnitLivewire::class)->name('product-unit');

        //Tax Types
        Route::get('tax-type', AdminRoot\TaxTypeLivewire::class)->name('tax-type');

        //GST Types
        Route::get('gst-type', AdminRoot\GstTypeLivewire::class)->name('gst-type');

         //Identity Types
         Route::get('identity-type', AdminRoot\IdentityTypeLivewire::class)->name('identity-type');

        //All Sellers List
        Route::get('all-seller', AdminRoot\AllSellerLivewire::class)->name('all-seller');

        //Logout
        Route::post('logout', [App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('logout');
    });

});
