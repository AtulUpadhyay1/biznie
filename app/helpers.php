<?php

use App\Models\Attribute;
use App\Models\ImageUpload;
use Illuminate\Support\Facades\DB;
    if(! function_exists('isActiveRoute')){
        function isActiveRoute($routes=[])
        {
            foreach ($routes as $key => $route) {
                if(Route::currentRouteName() == $route){
                    return true;
                }
            }
        }
    }

    if(! function_exists('getPaginate')){
        function getPaginate($paginate = 20)
        {
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
        function imageUpload($image, $folder)
        {
            $data = new ImageUpload;
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
?>
