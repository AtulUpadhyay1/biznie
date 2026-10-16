<?php

/*
 | Status labels for the canonical order / RFQ codes (App\Enums\OrderStatus,
 | App\Enums\RfqStatus). The storefront mirrors these in src/lib/status.ts.
 */
return [
    'order' => [
        'booking_pending'  => 'Booking pending',
        'booked'           => 'Booked (token paid)',
        'seller_confirmed' => 'Confirmed by seller',
        'loading'          => 'Loading',
        'weighed_invoiced' => 'Weighed & invoiced',
        'dispatched'       => 'Dispatched',
        'delivered'        => 'Delivered',
        'payment_settled'  => 'Payment settled',
        'closed'           => 'Closed',
        'cancelled'        => 'Cancelled',
    ],
    'rfq' => [
        'buyer' => [
            'rfq_open'        => 'Waiting for quotes',
            'quoted'          => 'Quotes received (:count)',
            'closed_no_quote' => 'No quotes received',
            'accepted'        => 'Quote accepted',
            'lost'            => 'Closed',
            'expired'         => 'Expired',
            'cancelled'       => 'Cancelled',
        ],
        'seller' => [
            'rfq_open'        => 'New RFQ – submit rate',
            'quoted'          => 'Rate submitted',
            'closed_no_quote' => 'Closed – not quoted',
            'accepted'        => 'You won this RFQ',
            'lost'            => 'Not selected',
            'expired'         => 'Expired',
            'cancelled'       => 'Cancelled',
        ],
    ],
];
