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

    // Route::get('address-syn', [App\Http\Controllers\Admin\DashboardController::class, 'addressSyn']);

    // Authenticated Routes
    Route::group(['middleware' => 'auth:admin'], function () {

        Route::get('notification-read', [App\Http\Controllers\Admin\DashboardController::class, 'notificationRead'])->name('notification.read');
        Route::post('save-fcm-token', [App\Http\Controllers\Admin\DashboardController::class, 'storeFcmToken'])->name('save-fcm-token');
        Route::get('send-notification', [App\Http\Controllers\Admin\DashboardController::class, 'sendNotification'])->name('send-notification');


        // Admin Dashboard
        Route::get('dashboard', AdminRoot\DashboardLivewire::class)->name('dashboard');

        // Business Category
        Route::get('business-category', AdminRoot\BusinessCategory\Index::class)->name('business-category');
        Route::get('create-business-category', AdminRoot\BusinessCategory\Create::class)->name('create-business-category');
        Route::get('edit-business-category/{id}', AdminRoot\BusinessCategory\Edit::class)->name('edit-business-category');

        //Business Type
        Route::get('business-type', AdminRoot\BusinessType\Index::class)->name('business-type');
        Route::get('create-business-type', AdminRoot\BusinessType\Create::class)->name('create-business-type');
        Route::get('edit-business-type/{id}', AdminRoot\BusinessType\Edit::class)->name('edit-business-type');

        //Seller Type
        Route::get('seller-type', AdminRoot\SellerType\Index::class)->name('seller-type');
        Route::get('create-seller-type', AdminRoot\SellerType\Create::class)->name('create-seller-type');
        Route::get('edit-seller-type/{id}', AdminRoot\SellerType\Edit::class)->name('edit-seller-type');

        //Product Attributes
        Route::get('attribute', AdminRoot\Attributes\Index::class)->name('attribute.index');
        Route::get('attribute/create', AdminRoot\Attributes\Create::class)->name('attribute.create');
        Route::get('attribute/edit/{id}', AdminRoot\Attributes\Edit::class)->name('attribute.edit');

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
        Route::get('product-unit', AdminRoot\ProductUnit\Index::class)->name('product-unit');
        Route::get('create-product-unit', AdminRoot\ProductUnit\Create::class)->name('create-product-unit');
        Route::get('edit-product-unit/{id}', AdminRoot\ProductUnit\Edit::class)->name('edit-product-unit');

        //Tax Types
        Route::get('tax-type', AdminRoot\TaxType\Index::class)->name('tax-type');
        Route::get('create-tax-type', AdminRoot\TaxType\Create::class)->name('create-tax-type');
        Route::get('edit-tax-type/{id}', AdminRoot\TaxType\Edit::class)->name('edit-tax-type');

        //GST Types
        Route::get('gst-type', AdminRoot\GstType\Index::class)->name('gst-type');
        Route::get('create-gst-type', AdminRoot\GstType\Create::class)->name('create-gst-type');
        Route::get('edit-gst-type/{id}', AdminRoot\GstType\Edit::class)->name('edit-gst-type');

        //Brands
        Route::get('brand', AdminRoot\Brand\Index::class)->name('brand');
        Route::get('create-brand', AdminRoot\Brand\Create::class)->name('create-brand');
        Route::get('edit-brand/{id}', AdminRoot\Brand\Edit::class)->name('edit-brand');
        Route::get('brand/show/{id}', AdminRoot\Brand\Show::class)->name('brand.show');

        //Identity Types
        Route::get('identity-type', AdminRoot\IdentityType\Index::class)->name('identity-type');
        Route::get('create-identity-type', AdminRoot\IdentityType\Create::class)->name('create-identity-type');
        Route::get('edit-identity-type/{id}', AdminRoot\IdentityType\Edit::class)->name('edit-identity-type');

        //Sellers List
        Route::get('seller', AdminRoot\Seller\Index::class)->name('seller.index');
        Route::get('seller/create', AdminRoot\Seller\Create::class)->name('seller.create');
        Route::get('seller-kyc-detail/{id}', AdminRoot\Seller\KycDetail::class)->name('seller-kyc-detail');
        Route::get('edit-seller', AdminRoot\Seller\Edit::class)->name('edit-seller');
        Route::get('tag-priority/{id}', AdminRoot\Seller\TagPriority::class)->name('tag-priority');
        Route::get('seller/orders/{id}', AdminRoot\Seller\Orders::class)->name('seller.orders');
        Route::get('seller/payments/{id}', AdminRoot\Seller\Payments::class)->name('seller.payments');

        // Seller Product Section
        Route::get('seller-product/{user_id}', AdminRoot\SellerProduct\Index::class)->name('seller-product.index');
        Route::get('seller-product/{user_id}/add', AdminRoot\SellerProduct\Add::class)->name('seller-product.add');
        Route::get('seller-product/{user_id}/edit/{product_id}', AdminRoot\SellerProduct\Edit::class)->name('seller-product.edit');
        Route::get('seller-product/{user_id}/variation/{product_id}', AdminRoot\SellerProduct\Variation::class)->name('seller-product.variation');
        Route::get('seller-product/{user_id}/price/{product_id}', AdminRoot\SellerProduct\Price::class)->name('seller-product.price');
        Route::get('seller-product/{user_id}/stock/{product_id}', AdminRoot\SellerProduct\Stock::class)->name('seller-product.stock');


        //Business Listing
        Route::get('business-listing', AdminRoot\BusinessListing\Index::class)->name('business-listing');
        Route::get('edit-business', AdminRoot\BusinessListing\Edit::class)->name('edit-business');

        //All Customers List
        Route::get('customer-list', AdminRoot\Customer\Index::class)->name('customer-list');
        Route::get('customer/create', AdminRoot\Customer\Create::class)->name('customer.create');

        //Cutomer Profile
        Route::get('customer-profile/{id}', AdminRoot\Customer\Profile::class)->name('customer-profile');

        //Edit Customer Info
        Route::get('edit-customer-info/{id}', AdminRoot\Customer\EditInfo::class)->name('edit-customer-info');

        //Cutomer Orders List
        Route::get('customer-orders-list/{id}', AdminRoot\Customer\Orders::class)->name('customer-orders-list');

        //Cutomer Payment List
        Route::get('customer-payment-list/{id}', AdminRoot\Customer\Payments::class)->name('customer-payment-list');

        //Cutomer Enquiry List
        Route::get('customer-enquiry-list/{id}', AdminRoot\Customer\Enquiry::class)->name('customer-enquiry-list');

        //Product List
        Route::get('product-list', AdminRoot\Product\Index::class)->name('product-list');

        //Edit Product
        Route::get('edit-product', AdminRoot\Product\Edit::class)->name('edit-product');

        // Commodity Product
        Route::get('commodity-product', AdminRoot\CommodityProduct\Index::class)->name('commodity-product.index');
        Route::get('commodity-product/create', AdminRoot\CommodityProduct\Create::class)->name('commodity-product.create');
        Route::get('commodity-product/edit/{id}', AdminRoot\CommodityProduct\Edit::class)->name('commodity-product.edit');
        Route::get('commodity-product/show/{id}', AdminRoot\CommodityProduct\Show::class)->name('commodity-product.show');
        Route::get('commodity-product/price/{id}', AdminRoot\CommodityProduct\PriceForm::class)->name('commodity-product.price');
        Route::get('commodity-product/variation/{id}', AdminRoot\CommodityProduct\VariationForm::class)->name('commodity-product.variation');
        Route::get('commodity-product/variation-old/{id}', AdminRoot\CommodityProduct\VariationFormOld::class)->name('commodity-product.variationOld');
        Route::get('commodity-product/variation-old/{id}', AdminRoot\CommodityProduct\VariationFormNewOld::class)->name('commodity-product.variationNewOld');
        Route::get('commodity-product/quality/{id}', AdminRoot\CommodityProduct\QualityForm::class)->name('commodity-product.quality');
        Route::get('commodity-product/state-price/{id}', AdminRoot\CommodityProduct\StatePriceFrom::class)->name('commodity-product.statePrice');
        Route::get('commodity-product/seller-price/{id}', AdminRoot\CommodityProduct\SellerPrice::class)->name('commodity-product.sellerPrice');

        // Commodity Product Enquiry
        Route::get('commodity-product-enquiry', AdminRoot\CommodityProductEnquiry\Index::class)->name('commodity-product-enquiry.index');
        Route::get('commodity-product-enquiry/create', AdminRoot\CommodityProductEnquiry\Create::class)->name('commodity-product-enquiry.create');
        Route::get('commodity-product-enquiry/create/{id}/variation', AdminRoot\CommodityProductEnquiry\Variation::class)->name('commodity-product-enquiry.variation');
        Route::get('commodity-product-enquiry/convert-to-order/{id}', AdminRoot\CommodityProductEnquiry\ConvertToOrder::class)->name('commodity-product-enquiry.convertToOrder');
        Route::get('commodity-product-enquiry/show/{id}', AdminRoot\CommodityProductEnquiry\Show::class)->name('commodity-product-enquiry.show');
        Route::get('commodity-product-enquiry/history/{id}', AdminRoot\CommodityProductEnquiry\History::class)->name('commodity-product-enquiry.history');
        Route::get('commodity-product-enquiry/seller-reply/{id}', AdminRoot\CommodityProductEnquiry\SellerReply::class)->name('commodity-product-enquiry.sellerReply');

        // Commodity Product Order
        Route::get('commodity-product-order', AdminRoot\CommodityProductOrder\Index::class)->name('commodity-product-order.index');
        Route::get('commodity-product-order/show/{id}', AdminRoot\CommodityProductOrder\Show::class)->name('commodity-product-order.show');
        Route::get('commodity-product-order/status/{id}', AdminRoot\CommodityProductOrder\Status::class)->name('commodity-product-order.status');
        Route::get('commodity-product-order/print-invoice/{id}', AdminRoot\CommodityProductOrder\PrintInvoice::class)->name('commodity-product-order.printInvoice');
        Route::get('commodity-product-order/history/{id}', AdminRoot\CommodityProductOrder\History::class)->name('commodity-product-order.history');

        // Commodity Product Order Payment
        Route::get('commodity-product-order/payment/{id}', AdminRoot\CommodityProductOrder\ReceivePayment::class)->name('commodity-product-order.receive-payment');

        // Commodity Product Order Payment History
        Route::get('commodity-product-order/ledger/{id}', AdminRoot\CommodityProductOrder\Ledger::class)->name('commodity-product-order.ledger');
        Route::get('commodity-product-order/seller-ledger/{id}', AdminRoot\CommodityProductOrder\SellerLedger::class)->name('commodity-product-order.sellerLedger');

        // Commodity Product Order Driver
        Route::get('commodity-product-order/driver/{order_id}', AdminRoot\CommodityProductOrderDriver\Index::class)->name('commodity-product-order-driver.index');
        Route::get('commodity-product-order/driver/create/{order_id}', AdminRoot\CommodityProductOrderDriver\Create::class)->name('commodity-product-order-driver.create');
        Route::get('commodity-product-order/driver/show/{order_id}/{id}', AdminRoot\CommodityProductOrderDriver\Show::class)->name('commodity-product-order-driver.show');
        Route::get('commodity-product-order/driver/edit/{order_id}/{id}', AdminRoot\CommodityProductOrderDriver\Edit::class)->name('commodity-product-order-driver.edit');
        Route::get('commodity-product-order/driver/quantity/{order_id}/{id}', AdminRoot\CommodityProductOrderDriver\Quantity::class)->name('commodity-product-order-driver.quantity');

        // Product wise seller and buyer
        Route::get('product-wise-seller-buyer', AdminRoot\ProductWiseSellerBuyer\Index::class)->name('product-wise-seller-buyer.index');

        // Banner
        Route::get('banner', AdminRoot\Banner\Index::class)->name('banner.index');
        Route::get('banner/create', AdminRoot\Banner\Create::class)->name('banner.create');
        Route::get('banner/edit/{id}', AdminRoot\Banner\Edit::class)->name('banner.edit');

        // Packaging Type
        Route::get('packaging-type', AdminRoot\PackagingType\Index::class)->name('packaging-type.index');
        Route::get('packaging-type/create', AdminRoot\PackagingType\Create::class)->name('packaging-type.create');
        Route::get('packaging-type/edit/{id}', AdminRoot\PackagingType\Edit::class)->name('packaging-type.edit');

        // Market News
        Route::get('market-news', AdminRoot\MarketNews\Index::class)->name('market-news.index');
        Route::get('market-news/create', AdminRoot\MarketNews\Create::class)->name('market-news.create');
        Route::get('market-news/edit/{id}', AdminRoot\MarketNews\Edit::class)->name('market-news.edit');

        // Testimonial
        Route::get('testimonial', AdminRoot\Testimonial\Index::class)->name('testimonial.index');
        Route::get('testimonial/create', AdminRoot\Testimonial\Create::class)->name('testimonial.create');
        Route::get('testimonial/edit/{id}', AdminRoot\Testimonial\Edit::class)->name('testimonial.edit');

        // Address
        Route::get('address', AdminRoot\Address\Index::class)->name('address.index');
        Route::get('address/create', AdminRoot\Address\Create::class)->name('address.create');
        Route::get('address/edit/{id}', AdminRoot\Address\Edit::class)->name('address.edit');

        //Faq
        Route::get('faq', AdminRoot\Faq\Index::class)->name('faq.index');
        Route::get('faq/create', AdminRoot\Faq\Create::class)->name('faq.create');
        Route::get('faq/edit/{id}', AdminRoot\Faq\Edit::class)->name('faq.edit');

        //Website Setup
        Route::get('website-setup/general', AdminRoot\WebsiteSetup\General::class)->name('website_setup.general');
        Route::get('website-setup/about', AdminRoot\WebsiteSetup\About::class)->name('website_setup.about');
        Route::get('website-setup/privacy-policy', AdminRoot\WebsiteSetup\Privacy::class)->name('website_setup.privacy');
        Route::get('website-setup/terms-condition', AdminRoot\WebsiteSetup\Termscondition::class)->name('website_setup.terms');
        Route::get('website-setup/returns-policy', AdminRoot\WebsiteSetup\Returns::class)->name('website_setup.returns');
        Route::get('website-setup/setting', AdminRoot\WebsiteSetup\Setting::class)->name('website_setup.setting');
        Route::get('website-setup/shop-on', AdminRoot\WebsiteSetup\ShopOn::class)->name('website_setup.shop_on');
        Route::get('website-setup/payment-methods', AdminRoot\WebsiteSetup\PaymentMethod::class)->name('website_setup.payment_methods');
        Route::get('website-setup/logistics', AdminRoot\WebsiteSetup\Logistics::class)->name('website_setup.logistics');
        Route::get('website-setup/product-delivery-info', AdminRoot\WebsiteSetup\ProductDeliveryInfo::class)->name('website_setup.product_delivery_info');
        Route::get('website-setup/product-terms-condition', AdminRoot\WebsiteSetup\ProductTermsCondition::class)->name('website_setup.product_terms');

        // Seller Tag
        Route::get('seller-tag', AdminRoot\SellerTag\Index::class)->name('seller-tag.index');
        Route::get('seller-tag/create', AdminRoot\SellerTag\Create::class)->name('seller-tag.create');
        Route::get('seller-tag/edit/{id}', AdminRoot\SellerTag\Edit::class)->name('seller-tag.edit');

        // Case Wallet
        Route::get('cash-wallet', AdminRoot\CashWallet\Index::class)->name('cash-wallet.index');

        // Credit Wallet Document Type
        Route::get('credit-wallet-document-type', AdminRoot\CreditWalletDocumentType\Index::class)->name('credit-wallet-document-type.index');
        Route::get('credit-wallet-document-type/create', AdminRoot\CreditWalletDocumentType\Credit::class)->name('credit-wallet-document-type.create');
        Route::get('credit-wallet-document-type/edit/{id}', AdminRoot\CreditWalletDocumentType\Edit::class)->name('credit-wallet-document-type.edit');

        // Credit Wallet Request
        Route::get('credit-wallet-request', AdminRoot\CreditWalletRequest\Index::class)->name('credit-wallet-request.index');
        Route::get('credit-wallet-request/create', AdminRoot\CreditWalletRequest\Create::class)->name('credit-wallet-request.create');
        Route::get('credit-wallet-request/show/{id}', AdminRoot\CreditWalletRequest\Show::class)->name('credit-wallet-request.show');

        // Transporter
        Route::get('transporter', AdminRoot\Transporter\Index::class)->name('transporter.index');
        Route::get('transporter/create', AdminRoot\Transporter\Create::class)->name('transporter.create');
        Route::get('transporter/edit/{id}', AdminRoot\Transporter\Edit::class)->name('transporter.edit');
        Route::get('transporter/vehicle/{id}', AdminRoot\Transporter\Vehicle::class)->name('transporter.vehicle');
        Route::get('transporter/product/{id}', AdminRoot\Transporter\Product::class)->name('transporter.product');
        Route::get('transporter/address-price/{id}', AdminRoot\Transporter\AddressPrice::class)->name('transporter.addressPrice');
        Route::get('transporter/profile/{id}', AdminRoot\Transporter\Profile::class)->name('transporter.profile');
        Route::get('transporter/enquiry/{id}', AdminRoot\Transporter\Enquiry::class)->name('transporter.enquiry');

        // Vehicle
        Route::get('vehicle', AdminRoot\Vehicle\Index::class)->name('vehicle.index');
        Route::get('vehicle/create', AdminRoot\Vehicle\Create::class)->name('vehicle.create');
        Route::get('vehicle/edit/{id}', AdminRoot\Vehicle\Edit::class)->name('vehicle.edit');

        // Ingot Price
        Route::get('ingot-price', AdminRoot\IngotPrice\Index::class)->name('ingot_price.index');
        Route::get('ingot-price/create', AdminRoot\IngotPrice\Create::class)->name('ingot_price.create');
        Route::get('ingot-price/edit/{id}', AdminRoot\IngotPrice\Edit::class)->name('ingot_price.edit');

        // Ingot Price Location
        Route::get('ingot-price-location', AdminRoot\IngotPriceLocation\Index::class)->name('ingot_price_location.index');
        Route::get('ingot-price-location/create', AdminRoot\IngotPriceLocation\Create::class)->name('ingot_price_location.create');
        Route::get('ingot-price-location/edit/{id}', AdminRoot\IngotPriceLocation\Edit::class)->name('ingot_price_location.edit');

        // General Enquiry
        Route::get('general-enquiry', AdminRoot\GeneralEnquiry\Index::class)->name('general-enquiry.index');
        Route::get('general-enquiry/{id}', AdminRoot\GeneralEnquiry\Show::class)->name('general-enquiry.show');

        // Contact Us
        Route::get('contact-us', AdminRoot\ContactUs\Index::class)->name('contact-us.index');

        //
        //Logout
        Route::post('logout', [App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('logout');
    });

});
