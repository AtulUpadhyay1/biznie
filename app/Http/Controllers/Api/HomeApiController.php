<?php

namespace App\Http\Controllers\Api;

use App\Models\Banner;
use App\Models\MarketNews;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeApiController extends Controller
{
    public function home()
    {
        try {
            $banner_list = Banner::where('published', 1)->get(['photo', 'banner_type', 'url', 'resource_type']);
            foreach ($banner_list as $banner_data) {
                $banner_data->photo = imageUrl($banner_data->photo);
            }

            $market_news = MarketNews::active()->latest()->get(['title', 'slug', 'image', 'description']);
            foreach ($market_news as $news) {
                $news->image = imageUrl($news->image);
            }

            $testimonial_list = Testimonial::active()->latest()->get(['name', 'image', 'designation', 'message']);
            foreach ($testimonial_list as $testimonial_data) {
                $testimonial_data->image = imageUrl($testimonial_data->image);
            }

            return response([
                'success'           => true,
                'banners'           => $banner_list,
                'market_news'       => $market_news,
                'testimonial_list'  => $testimonial_list
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }
}
