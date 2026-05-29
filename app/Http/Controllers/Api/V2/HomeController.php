<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\BrandResource;
use App\Http\Resources\V2\CategoryResource;
use App\Http\Resources\V2\NewsResource;
use App\Models\Brand;
use App\Models\BusinessCategory;
use App\Models\MarketNews;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function home(): JsonResponse
    {
        $categories = BusinessCategory::active()->where('featured', 1)->latest()->limit(12)->get();
        $news       = MarketNews::active()->latest()->limit(6)->get();
        $brands     = Brand::active()->latest()->limit(12)->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'site' => [
                    'name'        => websiteSetupValue('site_name') ?: 'Biznie',
                    'tagline'     => websiteSetupValue('tagline'),
                    'support_email' => websiteSetupValue('support_email'),
                    'support_phone' => websiteSetupValue('support_phone'),
                ],
                'categories'   => CategoryResource::collection($categories),
                'market_news'  => NewsResource::collection($news),
                'brands'       => BrandResource::collection($brands),
            ],
        ]);
    }
}
