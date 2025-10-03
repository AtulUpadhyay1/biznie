<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'dashboard',

            'seller-list',

            'buyer-list',

            'vehicle-list',
            'vehicle-create',
            'vehicle-edit',

            'transporter-list',
            'transporter-create',
            'transporter-edit',

            'product_wise_seller_buyer-list',

            'commodity_product-list',
            'commodity_product-create',
            'commodity_product-edit',

            'product_category-list',
            'product_category-create',
            'product_category-edit',

            'product_sub_category-list',
            'product_sub_category-create',
            'product_sub_category-edit',

            'product_sub_subcategory-list',
            'product_sub_subcategory-create',
            'product_sub_subcategory-edit',

            'product_attribute-list',
            'product_attribute-create',
            'product_attribute-edit',

            'packaging_type-list',
            'packaging_type-create',
            'packaging_type-edit',

            'brand-list',
            'brand-create',
            'brand-edit',

            'product_enquiry-list',
            'product_enquiry-general_enquiry',
            'product_enquiry-query',

            'commodity_product_order-list',

            'banner-list',
            'banner-create',
            'banner-edit',
            'banner-delete',

            'market_news-list',
            'market_news-create',
            'market_news-edit',
            'market_news-delete',

            'testimonial-list',
            'testimonial-create',
            'testimonial-edit',
            'testimonial-delete',

            'ingot_price_location-list',
            'ingot_price_location-create',
            'ingot_price_location-edit',

            'ingot_price-list',
            'ingot_price-create',
            'ingot_price-edit',
            'ingot_price-delete',

            'cash_wallet',

            'credit_wallet_request-list',
            'credit_wallet_request-create',

            'business_category-list',
            'business_category-create',
            'business_category-edit',
            'business_category-delete',

            'business_type-list',
            'business_type-create',
            'business_type-edit',
            'business_type-delete',

            'seller_type-list',
            'seller_type-create',
            'seller_type-edit',
            'seller_type-delete',

            'gst_type-list',
            'gst_type-create',
            'gst_type-edit',
            'gst_type-delete',

            'product_unit-list',
            'product_unit-create',
            'product_unit-edit',
            'product_unit-delete',

            'tax_type-list',
            'tax_type-create',
            'tax_type-edit',
            'tax_type-delete',

            'identity_type-list',
            'identity_type-create',
            'identity_type-edit',
            'identity_type-delete',

            'address-list',
            'address-create',
            'address-edit',
            'address-delete',

            'seller_tag-list',
            'seller_tag-create',
            'seller_tag-edit',
            'seller_tag-delete',

            'credit_wallet_document_type-list',
            'credit_wallet_document_type-create',
            'credit_wallet_document_type-edit',
            'credit_wallet_document_type-delete',

            'faq-list',
            'faq-create',
            'faq-edit',
            'faq-delete',

            'website_setup-general',
            'website_setup-about',
            'website_setup-product_add_process',
            'website_setup-returns_policy',
            'website_setup-terms_condition',
            'website_setup-privacy_policy',
            'website_setup-shop_on',
            'website_setup-payment_methods',
            'website_setup-logistics',
            'website_setup-product_delivery_info',
            'website_setup-product_terms_condition',
            'website_setup-setting',

            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'staff-list',
            'staff-create',
            'staff-edit',
            'staff-delete',
        ];

        foreach ($permissions as $permission) {
            $data=explode('-',$permission);

            $permissions = Permission::where('name', $permission)->first();
            if(!$permissions){
                $permissions = new Permission;
            }
            $permissions->name=$permission;
            $permissions->parent_name=$data[0];
            $permissions->guard_name='admin';
            $permissions->save();
        }

        $admin = Admin::first();
        if(!$admin){
            $admin = new Admin();
            $admin->name = 'Admin';
            $admin->email = 'admin@yopmail.com';
            $admin->password = bcrypt('123456789');
            $admin->save();
        }

        $role = Role::where('name', "Admin")->first();
        if(!$role){
            $role = new Role;
        }
        $role->name = "Admin";
        $role->guard_name  = 'admin';
        $role->save();

        $permissionIds = Permission::pluck('id')->toArray();

        $chunks = array_chunk($permissionIds, 100);
        $role->permissions()->detach();

        foreach ($chunks as $chunk) {
            $role->permissions()->attach($chunk);
        }
        DB::table('model_has_roles')->where('model_id', $admin->id)->delete();
        $admin->assignRole($role->name);
    }
}
