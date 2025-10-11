<?php

namespace App\Http\Controllers\Api;

use App\Models\Faq;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfigApiController extends Controller
{
    public function getConfig()
    {
        $faq_list = Faq::where('status', 1)->select('title', 'description')->get();
        return response([
            'success'           => true,
            'about_us'          => websiteSetupValue('about_us'),
            'terms_condition'   => websiteSetupValue('terms_condition'),
            'returns_policy'    => websiteSetupValue('returns_policy'),
            'privacy_policy'    => websiteSetupValue('privacy_policy'),
            'payment_method'    => websiteSetupValue('payment_method'),
            'logistics'         => websiteSetupValue('logistics'),
            'general_setup'     => [
                'phone1'        => websiteSetupValue('phone1'),
                'phone2'        => websiteSetupValue('phone2'),
                'whatsapp'      => websiteSetupValue('whatsapp'),
                'email'         => websiteSetupValue('email'),
                'short_about'   => websiteSetupValue('short_about'),
                'address'       => websiteSetupValue('address'),
                'facebook'      => websiteSetupValue('facebook'),
                'twitter'       => websiteSetupValue('twitter'),
                'instagram'     => websiteSetupValue('instagram'),
                'youtube'       => websiteSetupValue('youtube'),
            ],
            'faq'               => $faq_list,
            'permission'        => permissionList(),
            'customer_permission' => customerPermissionList(),
        ],200);
    }
}
