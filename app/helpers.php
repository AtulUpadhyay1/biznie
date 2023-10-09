<?php
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
?>
