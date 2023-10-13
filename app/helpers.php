<?php

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
?>
