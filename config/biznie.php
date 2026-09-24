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

    /*
    |--------------------------------------------------------------------------
    | Biznie AI chat assistant
    |--------------------------------------------------------------------------
    |
    | POST /api/v2/chat/messages. A deterministic rule engine always answers;
    | the Claude layer only helps it *understand* free text, and only when an
    | API key is present. It never sees order/enquiry data and never writes.
    |
    | `required_by_options` must stay in step with the Rate Finder form on the
    | frontend — the chat enquiry form offers exactly the same choices.
    |
    */

    'chat' => [
        'languages'       => ['en', 'hi', 'bn', 'mr', 'ta', 'te'],
        'retention_days'  => (int) env('BIZNIE_CHAT_RETENTION_DAYS', 90),

        'rate_limits' => [
            'user_per_minute'  => 30,
            'guest_per_minute' => 15,
            'writes_per_hour'  => 10,
        ],

        'required_by_options' => [
            'As soon as possible',
            'Within 3 days',
            'Within 1 week',
            'Within 15 days',
            'Within a month',
        ],

        'llm' => [
            'enabled'         => (bool) env('BIZNIE_CHAT_LLM_ENABLED', true) && (bool) env('ANTHROPIC_API_KEY'),
            'api_key'         => env('ANTHROPIC_API_KEY'),
            'model'           => env('BIZNIE_CHAT_LLM_MODEL', 'claude-opus-5'),
            'effort'          => 'low',
            'timeout'         => (float) env('BIZNIE_CHAT_LLM_TIMEOUT', 10),
            // Interactive path: one attempt, one overall budget. A retry would
            // double the worst case, and the rules answer if this fails.
            'max_retries'     => (int) env('BIZNIE_CHAT_LLM_MAX_RETRIES', 0),
            'history_turns'   => 6,
            // Server-side refusal fallback (beta). Off switch in case the
            // account or region does not have the beta.
            'server_fallback' => (bool) env('BIZNIE_CHAT_LLM_SERVER_FALLBACK', true),
        ],
    ],

];
