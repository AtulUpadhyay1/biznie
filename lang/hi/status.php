<?php

return [
    'order' => [
        'booking_pending'  => 'बुकिंग लंबित',
        'booked'           => 'बुक हुआ (टोकन जमा)',
        'seller_confirmed' => 'विक्रेता द्वारा पुष्टि',
        'loading'          => 'लोडिंग',
        'weighed_invoiced' => 'तौल व इनवॉइस',
        'dispatched'       => 'रवाना',
        'delivered'        => 'डिलीवर हुआ',
        'payment_settled'  => 'भुगतान पूरा',
        'closed'           => 'बंद',
        'cancelled'        => 'रद्द',
    ],
    'rfq' => [
        'buyer' => [
            'rfq_open'        => 'कोटेशन की प्रतीक्षा',
            'quoted'          => 'कोटेशन मिले (:count)',
            'closed_no_quote' => 'कोई कोटेशन नहीं मिला',
            'accepted'        => 'कोटेशन स्वीकार',
            'lost'            => 'बंद',
            'expired'         => 'समाप्त',
            'cancelled'       => 'रद्द',
        ],
        'seller' => [
            'rfq_open'        => 'नया RFQ – रेट भरें',
            'quoted'          => 'रेट भेजा गया',
            'closed_no_quote' => 'बंद – रेट नहीं भेजा',
            'accepted'        => 'आपको RFQ मिला',
            'lost'            => 'चयन नहीं हुआ',
            'expired'         => 'समाप्त',
            'cancelled'       => 'रद्द',
        ],
    ],
];
