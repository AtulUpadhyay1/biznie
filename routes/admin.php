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
        Route::get('business-category', AdminRoot\BusinessCategory\Index::class)->name('business-category');
        Route::get('create-business-category', AdminRoot\BusinessCategory\Create::class)->name('create-business-category');
        Route::get('edit-business-category/{id}', AdminRoot\BusinessCategory\Edit::class)->name('edit-business-category');

        //Vendor Type
        Route::get('vendor-type', AdminRoot\VendorTypeLivewire::class)->name('vendor-type');

         //Seller Type
         Route::get('seller-type', AdminRoot\SellerTypeLivewire::class)->name('seller-type');

        //Product Category
        Route::get('product-category', AdminRoot\ProductCategory\Index::class)->name('product-category');
        Route::get('create-product-category', AdminRoot\ProductCategory\Create::class)->name('create-product-category');
        Route::get('edit-product-category/{id}', AdminRoot\ProductCategory\Edit::class)->name('edit-product-category');

        //Product Sub Category
        Route::get('product-sub-category', AdminRoot\ProductSubCategory\Index::class)->name('product-sub-category');
        Route::get('create-product-sub-category', AdminRoot\ProductSubCategory\Create::class)->name('create-product-sub-category');
        Route::get('edit-product-sub-category/{id}', AdminRoot\ProductSubCategory\Edit::class)->name('edit-product-sub-category');

        //Product Sub Sub Category
        Route::get('product-sub-subcategory', AdminRoot\ProductSubSubCategory\Index::class)->name('product-sub-subcategory');
        Route::get('create-product-sub-subcategory', AdminRoot\ProductSubSubCategory\Create::class)->name('create-product-sub-subcategory');
        Route::get('edit-product-sub-subcategory/{id}', AdminRoot\ProductSubSubCategory\Edit::class)->name('edit-product-sub-subcategory');

        //Product Unit
        Route::get('product-unit', AdminRoot\ProductUnitLivewire::class)->name('product-unit');

        //Tax Types
        Route::get('tax-type', AdminRoot\TaxTypeLivewire::class)->name('tax-type');

        //GST Types
        Route::get('gst-type', AdminRoot\GstTypeLivewire::class)->name('gst-type');

        //Brands
        Route::get('brand', AdminRoot\Brand\Index::class)->name('brand');
        Route::get('create-brand', AdminRoot\Brand\Create::class)->name('create-brand');
        Route::get('edit-brand/{id}', AdminRoot\Brand\Edit::class)->name('edit-brand');

         //Identity Types
         Route::get('identity-type', AdminRoot\IdentityTypeLivewire::class)->name('identity-type');

        //Sellers List
        Route::get('seller-list', AdminRoot\Seller\Index::class)->name('seller-list');
        Route::get('edit-seller', AdminRoot\Seller\Edit::class)->name('edit-seller');

        //Business Listing
        Route::get('business-listing', AdminRoot\BusinessListing\Index::class)->name('business-listing');
        Route::get('edit-business', AdminRoot\BusinessListing\Edit::class)->name('edit-business');

        //All Customers List
         Route::get('customer-list', AdminRoot\Customer\Index::class)->name('customer-list');

        //Cutomer Profile
        Route::get('customer-profile', AdminRoot\Customer\Profile::class)->name('customer-profile');

        //Edit Customer Info
        Route::get('edit-customer-info', AdminRoot\Customer\EditInfo::class)->name('edit-customer-info');

        //Cutomer Orders List
        Route::get('customer-orders-list', AdminRoot\Customer\Orders::class)->name('customer-orders-list');

        //Cutomer Payment List
        Route::get('customer-payment-list', AdminRoot\Customer\Payments::class)->name('customer-payment-list');

        //Product List
        Route::get('product-list', AdminRoot\Product\Index::class)->name('product-list');

        //Edit Product
        Route::get('edit-product', AdminRoot\Product\Edit::class)->name('edit-product');

        //Logout
        Route::post('logout', [App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('logout');
    });

});
