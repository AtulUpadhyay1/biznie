<?php

namespace App\Http\Controllers\Api\Seller\Authenticated;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Seller\ProfileResource;

class ProfileApiController extends Controller
{
    public function profile()
    {
        return response([
            'success'   => true,
            'data'      => new ProfileResource(auth()->user())
        ],200);
    }
}
