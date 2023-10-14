<?php

namespace App\Http\Controllers\Api\Customer\Authenticated;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\ProfileResource;

class ProfileApiCustomer extends Controller
{
    public function profile()
    {
        return response([
            'success'   => true,
            'data'      => new ProfileResource(auth()->user())
        ],200);
    }
}
