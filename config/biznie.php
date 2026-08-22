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

    /*
    |--------------------------------------------------------------------------
    | Live RFQ bidding
    |--------------------------------------------------------------------------
    |
    | An RFQ is a timed reverse auction: it is dispatched to every matching
    | seller at once and they undercut each other on F.O.R price until the
    | window closes. The window length is a business lever (longer for thin
    | markets, shorter for liquid ones), so it lives in .env rather than in the
    | service.
    |
    | `default_freight_per_unit` is the last-resort freight figure used when no
    | transporter publishes a rate for the delivery city — without it a bid
    | would quote an F.O.R price equal to its ex-works price and win unfairly.
    |
    */

    'rfq' => [
        'bidding_minutes'          => (int) env('BIZNIE_RFQ_BIDDING_MINUTES', 15),
        'extend_minutes'           => (int) env('BIZNIE_RFQ_EXTEND_MINUTES', 5),
        'max_sellers'              => (int) env('BIZNIE_RFQ_MAX_SELLERS', 25),
        'default_freight_per_unit' => (float) env('BIZNIE_RFQ_DEFAULT_FREIGHT', 0),
    ],

];
