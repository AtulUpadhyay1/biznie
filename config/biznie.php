<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Price visibility
    |--------------------------------------------------------------------------
    |
    | Sellers allowed to choose which of ex-works / F.O.R / F.O.B their listings
    | publish. Every other seller keeps whatever their products are already set
    | to and is not shown the panel.
    |
    | Held here rather than hardcoded in the controller so the list can be
    | widened from .env once the feature is opened up beyond the pilot seller.
    |
    */

    'price_visibility_user_ids' => array_values(array_filter(array_map(
        'intval',
        explode(',', (string) env('BIZNIE_PRICE_VISIBILITY_USER_IDS', '1'))
    ))),

];
