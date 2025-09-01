<?php

use App\Models\Admin;
use App\Models\Brand;
use App\Models\UserOtp;
use App\Models\Attribute;
use App\Models\SellerType;
use App\Models\UserDetail;
use App\Models\ImageUpload;
use App\Models\ProductUnit;
use App\Models\Notification;
use App\Models\WebsiteSetup;
use App\Models\PackagingType;
use App\Firebase\FireBaseManager;
use App\Services\Msg91OtpService;
use Illuminate\Support\Facades\DB;
use App\Models\CommodityProductVariation;
use App\Models\CommodityProductStatePrice;

if (!function_exists('websiteSetupValue')) {
    function websiteSetupValue($key){
        $setup = WebsiteSetup::where('key', $key)->first();
        return $setup ? $setup->value : "";
    }
}

if(! function_exists('isActiveRoute')){
    function isActiveRoute($routes=[]){
        foreach ($routes as $key => $route) {
            if(Route::currentRouteName() == $route){
                return true;
            }
        }
    }
}

if(! function_exists('getPaginate')){
    function getPaginate($paginate = 20){
        return $paginate;
    }
}

if(!function_exists('dateTimeFormat')){
    function dateTimeFormat($datesTime){
        return date('d-m-Y h:i A', strtotime($datesTime));
    }
}

if(!function_exists('dateFormat')){
    function dateFormat($dates){
        return date('d-m-Y', strtotime($dates));
    }
}

if(!function_exists('timeFormat')){
    function timeFormat($time){
        return date('h:i A', strtotime($time));
    }
}

if(!function_exists('lastActive')){
    function lastActive($id){
        $data = DB::table('personal_access_tokens')->where('tokenable_id', $id)->latest()->first();
        $time = '';
        if($data){
            $time = dateTimeFormat($data->updated_at);
        }
        return $time;
    }
}

if(! function_exists('imageUrl')){
    function imageUrl($id){
        $imageUrl="";
        $data = ImageUpload::find($id);
        if($data && $data->image){
            $imageUrl = asset('storage/'.$data->image);
        }
        return $imageUrl;
    }
}

if(! function_exists('imageUpload')){
    function imageUpload($image, $folder, $id=0){
        $data = ImageUpload::find($id);
        if($data){
            $image_path = 'storage/'.$data->image;
            if(File::exists($image_path)) {
                File::delete($image_path);
            }
        }else{
            $data = new ImageUpload;
        }
        $data->extension = $image->extension();
        $image_name = time().'-'.rand(10, 99).'.'.$image->extension();
        $data->image = $image->storeAs($folder, $image_name, 'public');
        $data->save();

        return $data->id;
    }
}

if(! function_exists('getAttribute')){
    function getAttribute($id){
        return Attribute::find($id);
    }
}

if(! function_exists('getPackagingType')){
    function getPackagingType($id){
        return PackagingType::find($id);
    }
}

if(! function_exists('getBrand')){
    function getBrand($id){
        return Brand::find($id);
    }
}

if(! function_exists('getProductUnit')){
    function getProductUnit($id){
        return ProductUnit::find($id);
    }
}

if(! function_exists('sendNotification')){
    function sendNotification($user, $title, $body, $type="notification", $data = [], $save=false,)
    {
        $notificationArr = [
            'title'             => $title,
            'body'              => $body,
        ];
        $in_app_module = [
            "title"          => $title,
            "body"           => $body,
            "type"           => $type,
        ];
        if($user->fcm_token){
            FireBaseManager::sendMessage($notificationArr, $in_app_module, $user->fcm_token);
        }
        if($save){
            $data = new Notification;
            $data->user_id = $user->id;
            $data->title = $title;
            $data->body = $body;
            $data->type = $type;
            $data->data = $data;
            $data->is_read = 0;
            $data->save();
        }
    }
}

if(! function_exists('sendAdminNotification')){
    function sendAdminNotification($title, $body)
    {
        $notificationArr = [
            'title'             => $title,
            'body'              => $body,
        ];
        $in_app_module = [
            "title"          => $title,
            "body"           => $body,
            "type"           => 'notification',
        ];
        $admin_list = Admin::get();
        foreach ($admin_list as $admin_data){
            if($admin_data->fcm_token){
                FireBaseManager::sendMessage($notificationArr, $in_app_module, $admin_data->fcm_token);
            }
        }
    }
}

if(!function_exists('getSellerType')){
    function getSellerType($user_id){
        $type = 'Seller';
        $user_detail = UserDetail::where('user_id', $user_id)->first();
        if($user_detail && $user_detail->type){
            $seller_type = SellerType::whereIn('id', $user_detail->type)->first();
            if($seller_type){
                $type = $seller_type->name;
            }
        }

        return $type;
    }
}

if(!function_exists('formatIndianNumber')){
    function formatIndianNumber($number) {
        $numberParts = explode('.', $number);
        $whole = $numberParts[0];
        $decimal = isset($numberParts[1]) ? '.' . $numberParts[1] : '';

        // Get the length of the whole part of the number
        $length = strlen($whole);

        // If the length is more than 3, we need to format it
        if ($length > 3) {
            $lastThree = substr($whole, -3);
            $restUnits = substr($whole, 0, $length - 3);
            $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
            $whole = $restUnits . ',' . $lastThree;
        }

        // Return the formatted number
        return $whole . $decimal;
    }
}

if(!function_exists('sendOtp')){
    function sendOtp($phone) {
        $otp = rand(1111, 9999);

        $msg91OtpService = Msg91OtpService::sendSms("91".$phone, [
            'type' => 'Login',
            'otp'  => $otp
        ]);

        $data = UserOtp::where('phone', $phone)->first();
        if(!$data){
            $data = new UserOtp;
        }
        $data->phone = $phone;
        $data->otp = $otp;
        $data->save();
    }
}

if(!function_exists('getDefaultCommodityProductVariation')){
    function getDefaultCommodityProductVariation($commodity_id, $brand_id, $state, $city){

        return $variation = CommodityProductVariation::with('getCommodityProduct')
            ->where('commodity_product_id', $commodity_id)
            ->where('is_default', 1)->first();
    }
}

if(!function_exists('getDefaultCommodityProductVariationPrice')){
    function getDefaultCommodityProductVariationPrice($commodity_id, $brand_id, $state, $city){
        $price = 0;
        $variation = CommodityProductVariation::where('commodity_product_id', $commodity_id)
            ->where('is_default', 1)->first();

        if($variation){
            $price = CommodityProductStatePrice::where('commodity_product_id', $commodity_id)
                ->where('commodity_product_variation_id', $variation->id)
                ->where('brand_id', $brand_id)
                ->where('state', $state)
                ->where('city', $city)
                ->first();

            if($price){
                $price = $price->price;
            }
        }

        return $price;
    }
}

if(!function_exists('getIngotPriceLocation')){
    function getIngotPriceLocation() {
        $location = [
            'Durgapur',
            'Jamshedpur',
            'Raigarh',
            'Raipur',
        ];
        return $location;
    }
}

if(!function_exists('sendMySmsShopMessage')){
    function sendMySmsShopMessage($apikey, $senderid, $number, $message) {
        $client = new Client();
        $response = $client->post('http://sms.mysmsshop.in/V2/http-api.php', [
            'form_params' => [
                "apikey"    => $apikey,
                "senderid"  => $senderid,
                "number"    => $number,
                "message"   => $message,
                "format"    => "json"
            ],
        ]);

        $otp = 1234;
        // $otp = rand(1111, 9999);

        // if(config('app.env') == 'production' && $request->phone != "8920976591"){
        //     Msg91::sms()->to('91'.$request->phone)->flow('648d8690d6fc051b591f1ec3')->variable('user', $user->name)->variable('otp', $otp)->send();
        // }

        $data = UserOtp::where('phone', $phone)->first();
        if(!$data){
            $data = new UserOtp;
        }
        $data->phone = $phone;
        $data->otp = $otp;
        $data->save();
        // return $response;
    }
}


?>
