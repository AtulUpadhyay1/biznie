<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfigApiController extends Controller
{
    public function getConfig()
    {
        return response([
            'success'           => true,
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
            ]
        ],200);
    }
}
