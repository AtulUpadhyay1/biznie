<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Banner;
use App\Models\ContactUs;
use App\Models\IngotPrice;
use App\Models\MarketNews;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\IngotPriceLocation;
use App\Http\Controllers\Controller;
use App\Models\SellerCommodityProduct;

class HomeApiController extends Controller
{
    public function home(Request $request)
    {
        try {
            $banner_list = Banner::whereIn('for', ['both', 'web'])
                ->where('published', 1)
                ->get(['photo', 'banner_type', 'url', 'resource_type']);
            foreach ($banner_list as $banner_data) {
                $banner_data->photo = imageUrl($banner_data->photo);
            }

            $market_news = MarketNews::active()
                ->latest()
                ->get(['title', 'slug', 'image', 'description', 'created_at']);
            foreach ($market_news as $news) {
                $news->image = imageUrl($news->image);
            }

            $testimonial_list = Testimonial::active()
                ->latest()
                ->get(['name', 'image', 'designation', 'message']);
            foreach ($testimonial_list as $testimonial_data) {
                $testimonial_data->image = imageUrl($testimonial_data->image);
            }

            $brand_list = Brand::active()
                // ->where('featured', 1)
                ->orderBy('name', 'ASC')
                ->select('id', 'name', 'thumbnail', 'banner')
                ->get();
            foreach ($brand_list as $brand_data) {
                $brand_data->thumbnail = asset('storage/'.$brand_data->thumbnail);
                $brand_data->banner = asset('storage/'.$brand_data->banner);
            }

            return response([
                'success'           => true,
                'banners'           => $banner_list,
                'market_news'       => $market_news,
                'testimonial_list'  => $testimonial_list,
                'brand_list'        => $brand_list,
                'shop_on'           => [
                    'title'         => websiteSetupValue('shop_on_title'),
                    'description'   => websiteSetupValue('shop_on_description'),
                    'video_link'    => websiteSetupValue('shop_on_video_link'),
                ],
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

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }

    public function viewMarketNews($slug)
    {
        $market_news = MarketNews::where('slug', $slug)->first(['title', 'slug', 'image', 'description', 'created_at']);
        $market_news->image = imageUrl($market_news->image);
        return response([
            'success'           => true,
            'market_news'       => $market_news
        ],200);
    }

    public function ingotPrice(Request $request)
    {
        $ingotPriceLocation = IngotPriceLocation::orderBy('location', 'ASC')->get();
        $defaultIngotPriceLocation = $ingotPriceLocation->where('is_default', 1)->first();

        $ingotLocation = $request->ingot_location ?? ($defaultIngotPriceLocation ? $defaultIngotPriceLocation->location : $ingotPriceLocation->first()->location);
        $ingotPriceType = $request->ingot_price_type ?? 'monthly';

        $month_list = collect();
        for ($i = 1; $i <= 12; $i++) {
            $month_list->push(Carbon::now()->startOfYear()->addMonths($i - 1)->format('m-Y'));
        }

        $last_ingot_price = IngotPrice::where('location', $ingotLocation)
            ->orderBy('updated_at', 'desc')
            ->first();

        $monthly_price = [];
        foreach ($month_list as $month_data) {
            $month = explode('-', $month_data)[0];
            $year = explode('-', $month_data)[1];

            $ingot_prices = IngotPrice::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->where('location', $ingotLocation)
                ->latest()
                ->first();
                // ->pluck('price');

            // $average_price = round($ingot_prices->avg() ?? 0);
            // $average_price = $ingot_prices ? $ingot_prices->price : 0;
            $average_price = $ingot_prices ? $ingot_prices->price : ($last_ingot_price ? $last_ingot_price->price : 0);

            $monthly_price[] = [
                'year' => Carbon::createFromFormat('m-Y', $month_data)->format('M'), // Jan, Feb, etc.
                'price' => (int)$average_price,
            ];
        }

        $weekly_list = collect();
        for ($i = 0; $i <= 6; $i++) {
            $weekly_list->push(Carbon::now()->startOfWeek()->addDays($i)->format('d-Y'));
        }

        $weekly_price = [];
        foreach ($weekly_list as $week_data) {
            $day = explode('-', $week_data)[0];
            $year = explode('-', $week_data)[1];

            $ingot_prices = IngotPrice::whereDay('created_at', $day)
                ->whereYear('created_at', $year)
                ->where('location', $ingotLocation)
                // ->pluck('price');
                ->latest()
                ->first();

            // $average_price = round($ingot_prices->avg() ?? 0);
            // $average_price = $ingot_prices ? $ingot_prices->price : 0;
            $average_price = $ingot_prices ? $ingot_prices->price : ($last_ingot_price ? $last_ingot_price->price : 0);


            $weekly_price[] = [
                'day' => Carbon::createFromFormat('d-Y', $week_data)->format('D'),
                'price' => (int)$average_price,
            ];
        }

        $daily_price = [];
        $today_prices = IngotPrice::where('location', $ingotLocation)
            ->whereDate('created_at', Carbon::today())
            ->get();

        if($today_prices->count() == 0){

            $daily_price = [[
                'time' => '12:00 AM',
                'price' => $last_ingot_price ? $last_ingot_price->price : 0,
            ]];

        }
        foreach ($today_prices as $today_price) {
            $daily_price[] = [
                'time' => timeFormat($today_price->created_at, 'h:i A'),
                'price' => $today_price->price,
            ];
        }

        if($ingotPriceType == 'daily') {
            $price = $daily_price;
        }else if($ingotPriceType == 'weekly') {
            $price = $weekly_price;
        }else {
            $price = $monthly_price;
        }

        $last_ingot_price = IngotPrice::where('location', $ingotLocation)
            ->orderBy('updated_at', 'desc')
            ->first();
        if ($last_ingot_price) {
            $last_update = dateTimeFormat($last_ingot_price->updated_at);
        } else {
            $last_update = '';
        }

        $last_2_ingot_price = IngotPrice::where('location', $ingotLocation)
            ->orderBy('updated_at', 'desc')
            ->limit(2)
            ->get()
            ->pluck('price');


        return response([
            'success'       => true,
            'last_update'   => $last_update,
            'latest_price'  => $last_2_ingot_price,
            'location'      => $ingotPriceLocation->pluck('location'),
            'price'         => $price,
        ],200);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search_key' => 'required|string',
        ]);
        $search_key = $request->search_key;
        $brand_list = Brand::where('name', 'like', '%'.$search_key.'%')
            ->orderBy('name', 'ASC')
            ->select('id', 'name')
            ->get();

        $market_news = MarketNews::where('title', 'like', '%'.$search_key.'%')
            ->latest()
            ->get(['id', 'title', 'slug']);

        $category_list = ProductCategory::where('name', 'like', '%'.$search_key.'%')
            ->orderBy('name', 'ASC')
            ->select('id', 'name')
            ->get();

        $product_list = SellerCommodityProduct::where('name', 'like', '%'.$search_key.'%')
            ->orWhereHas('getCategory', function ($query) use ($search_key) {
            $query->where('name', 'like', '%'.$search_key.'%');
            })
            ->orWhereHas('getBrand', function ($query) use ($search_key) {
                $query->where('name', 'like', '%'.$search_key.'%');
            })
            ->orderBy('name', 'ASC')
            ->select('id', 'name', 'commodity_product_id')
            ->get();

        return response([
            'success'       => true,
            'brand_list'    => $brand_list,
            'category_list' => $category_list,
            'product_list'  => $product_list,
            'market_news'   => $market_news,
        ],200);

    }

    public function contactUs(Request $request)
    {
        $request->validate([
            'name'      => 'required|string',
            'email'     => 'required|email',
            'phone'     => 'required|numeric',
            'message'   => 'required|string',
        ]);

        try {

            $data = new ContactUs;
            $data->name = $request->name;
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->message = $request->message;
            $data->save();

            return response([
                'success'   => true,
                'message'   => 'Your message has been sent successfully.'
            ],200);

        } catch (\Throwable $th) {

            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }

    public function brandList()
    {
        $brand_list = Brand::orderBy('name', 'ASC')->get(['id', 'name']);
        return response([
           'success'   => true,
            'brand_list' => $brand_list
        ],200);
    }

    public function sellerCommodityProductList($brand_id)
    {
        $product_list = SellerCommodityProduct::where('brand_id', $brand_id)
            ->where('user_id', 1)
            ->orderBy('name', 'ASC')
            ->select('id', 'name', 'commodity_product_id')
            ->get();

        return response([
            'success'   => true,
            'product_list' => $product_list
        ],200);
    }
}
