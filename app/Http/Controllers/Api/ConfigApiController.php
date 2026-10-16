<?php

namespace App\Http\Controllers\Api;

use App\Models\Faq;
use App\Support\CompanyProfile;
use App\Http\Controllers\Controller;

class ConfigApiController extends Controller
{
    /**
     * Site-wide settings for the storefront and the mobile apps.
     *
     * Contact details come from one place (CompanyProfile) and policy pages
     * have their {{company_*}} tokens filled from it, so the footer, contact
     * page and Terms can never show different numbers (B21/B22).
     */
    public function getConfig()
    {
        $faq_list = Faq::where('status', 1)->select('title', 'description')->get();
        return response([
            'success'           => true,
            'about_us'          => CompanyProfile::render(websiteSetupValue('about_us')),
            'terms_condition'   => CompanyProfile::render(websiteSetupValue('terms_condition')),
            'returns_policy'    => CompanyProfile::render(websiteSetupValue('returns_policy')),
            'privacy_policy'    => CompanyProfile::render(websiteSetupValue('privacy_policy')),
            'payment_method'    => CompanyProfile::render(websiteSetupValue('payment_method')),
            'logistics'         => CompanyProfile::render(websiteSetupValue('logistics')),
            'general_setup'     => [
                'phone1'        => websiteSetupValue('phone1'),
                'phone2'        => websiteSetupValue('phone2'),
                'whatsapp'      => websiteSetupValue('whatsapp'),
                'email'         => websiteSetupValue('email'),
                'short_about'   => websiteSetupValue('short_about'),
                'address'       => websiteSetupValue('address'),
                // Empty unless the link is a real profile page (B04).
                'facebook'      => CompanyProfile::socialUrl('facebook') ?? '',
                'twitter'       => CompanyProfile::socialUrl('twitter') ?? '',
                'instagram'     => CompanyProfile::socialUrl('instagram') ?? '',
                'youtube'       => CompanyProfile::socialUrl('youtube') ?? '',
                'linkedin'      => CompanyProfile::socialUrl('linkedin') ?? '',
            ],
            // Statutory identity and the IT Rules 2021 grievance officer,
            // shown in the footer and on About / Terms / Privacy.
            'legal'             => [
                'legal_name'    => CompanyProfile::get('legal_name'),
                'llpin'         => CompanyProfile::get('company_llpin'),
                'gstin'         => CompanyProfile::get('company_gstin'),
                // Shown at checkout and in the Return Policy (4.1).
                'booking_policy' => CompanyProfile::get('booking_policy'),
                'grievance_officer' => [
                    'name'          => CompanyProfile::get('grievance_officer_name'),
                    'email'         => CompanyProfile::get('grievance_officer_email'),
                    'phone'         => CompanyProfile::get('grievance_officer_phone'),
                    'response_time' => CompanyProfile::get('grievance_response_time'),
                ],
            ],
            'faq'               => $faq_list,
            'permission'        => permissionList(),
            'customer_permission' => customerPermissionList(),
        ],200);
    }
}
