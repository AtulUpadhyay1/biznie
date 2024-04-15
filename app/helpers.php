<?php

use App\Models\Brand;
use App\Models\Attribute;
use App\Models\SellerType;
use App\Models\UserDetail;
use App\Models\ImageUpload;
use App\Models\ProductUnit;
use App\Models\Notification;
use App\Models\WebsiteSetup;
use App\Models\PackagingType;
use App\Firebase\FireBaseManager;
use Illuminate\Support\Facades\DB;

if (!function_exists('websiteSetupValue')) {
    function websiteSetupValue($key){
        return WebsiteSetup::where('key', $key)->first() ? WebsiteSetup::where('key', $key)->first()->value : "";
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
        return date('M d, Y h:i A', strtotime($datesTime));
    }
}

if(!function_exists('dateFormat')){
    function dateFormat($dates){
        return date('M d, Y', strtotime($dates));
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

if(!function_exists('getSellerType')){
    function getSellerType($user_id){
        $type = 'Seller';
        $user_detail = UserDetail::where('user_id', $user_id)->first();
        if($user_detail){
            $seller_type = SellerType::whereIn('id', $user_detail->type)->first();
            if($seller_type){
                $type = $seller_type->name;
            }
        }

        return $type;
    }
}

?>
