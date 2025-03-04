<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Banner;
use App\Models\IngotPrice;
use App\Models\MarketNews;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

            return response([
                'success'           => true,
                'banners'           => $banner_list,
                'market_news'       => $market_news,
                'testimonial_list'  => $testimonial_list,
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
        $ingotLocation = $request->ingot_location ?? 'Durgapur';
        $ingotPriceType = $request->ingot_price_type ?? 'monthly';
        $ingotPriceLocation = getIngotPriceLocation();

        $month_list = collect();
        for ($i = 1; $i <= 12; $i++) {
            $month_list->push(Carbon::now()->startOfYear()->addMonths($i - 1)->format('m-Y'));
        }

        $monthly_price = [];
        foreach ($month_list as $month_data) {
            $month = explode('-', $month_data)[0];
            $year = explode('-', $month_data)[1];

            $ingot_prices = IngotPrice::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->where('location', $ingotLocation)
                ->pluck('price');

            $average_price = round($ingot_prices->avg() ?? 0);

            $monthly_price[] = [
                'year' => Carbon::createFromFormat('m-Y', $month_data)->format('M'), // Jan, Feb, etc.
                'price' => $average_price,
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
                ->pluck('price');

            $average_price = round($ingot_prices->avg() ?? 0);

            $weekly_price[] = [
                'day' => Carbon::createFromFormat('d-Y', $week_data)->format('D'), // Mon, Tue, etc.
                'price' => $average_price,
            ];
        }

        $last_ingot_price = IngotPrice::where('location', $ingotLocation)
            ->orderBy('updated_at', 'desc')
            ->first();
        if ($last_ingot_price) {
            $last_update = dateTimeFormat($last_ingot_price->updated_at);
        } else {
            $last_update = '';
        }

        return response([
            'success'       => true,
            'last_update'   => $last_update,
            'location'      => getIngotPriceLocation(),
            'price'         => $ingotPriceType == 'weekly' ? $weekly_price :$monthly_price,

        ],200);
    }

}
