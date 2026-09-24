<?php

/*
| Biznie AI chat copy — Marathi (common English business words kept, as in the
| rest of the app). Keep the key set identical to lang/en/chat.php.
*/

return [

    'greeting_guest'     => "नमस्कार! मी Biznie AI आहे. मी तुम्हाला price तपासण्यात, best quote साठी enquiry टाकण्यात आणि order track करण्यात मदत करू शकतो.\nतुम्हाला काय करायचे आहे?",
    'greeting_user'      => 'नमस्कार :name! तुमचे स्वागत आहे.',
    'greeting_user_anon' => 'नमस्कार! तुमचे स्वागत आहे.',
    'summary_orders'     => '{1} तुमची 1 order active आहे.|[2,*] तुमच्या :count orders active आहेत.',
    'summary_enquiries'  => '{1} तुमच्या 1 enquiry वर सध्या live bidding सुरू आहे.|[2,*] तुमच्या :count enquiries वर सध्या live bidding सुरू आहे.',
    'menu_prompt'        => 'तुम्हाला काय करायचे आहे?',

    'login_required' => 'पुढे जाण्यासाठी कृपया login करा — जिथे थांबलो होतो तिथूनच पुन्हा सुरू करू.',
    'login_prompt'   => 'तुमच्या orders track करण्यासाठी, enquiries ची live bidding पाहण्यासाठी आणि personalised updates मिळवण्यासाठी login करा.',

    'menu' => [
        'track_order'    => 'माझी order track करा',
        'my_enquiries'   => 'माझ्या enquiries',
        'create_enquiry' => 'नवीन enquiry टाका',
        'check_prices'   => 'Price तपासा',
        'support'        => 'Support शी बोला',
        'callback'       => 'Callback मागवा',
        'login'          => 'Login करा',
        'main_menu'      => 'Main menu',
        'all_orders'     => 'सर्व orders',
        'all_enquiries'  => 'सर्व enquiries',
        'show_more'      => 'अजून दाखवा',
        'get_quote'      => 'Quote घ्या: :name',
        'track_enquiry'  => 'ही enquiry track करा',
    ],

    'orders_active_intro' => 'या आहेत तुमच्या active orders:',
    'orders_all_intro'    => 'या आहेत तुमच्या अलीकडच्या orders:',
    'orders_none_active'  => 'सध्या तुमची कोणतीही active order नाही.',
    'orders_none'         => 'तुम्ही अजून कोणतीही order दिलेली नाही. Verified sellers कडून live quotes मिळवण्यासाठी enquiry टाका.',
    'order_found'         => 'Order :ref ची ताजी माहिती:',
    'order_not_found'     => 'ही order तुमच्या account मध्ये सापडली नाही. कृपया order ID तपासा — ती OID-20260901-1234 सारखी दिसते.',

    'enquiries_open_intro' => 'या आहेत तुमच्या open enquiries:',
    'enquiries_all_intro'  => 'या आहेत तुमच्या अलीकडच्या enquiries:',
    'enquiries_none_open'  => 'सध्या तुमची कोणतीही open enquiry नाही.',
    'enquiries_none'       => 'तुम्ही अजून कोणतीही enquiry टाकलेली नाही.',
    'enquiry_found'        => 'Enquiry :ref ची ताजी माहिती:',
    'enquiry_not_found'    => 'ही enquiry तुमच्या account मध्ये सापडली नाही. कृपया ID तपासा — ती BZN-RFQ-260901-0001 सारखी दिसते.',

    'enquiry_form_intro' => 'खालील details तपासून submit करा — तुमची enquiry लगेच verified sellers कडे live होईल.',
    'enquiry_created'    => 'तुमची enquiry :ref live झाली आहे! Sellers आत्ता bidding करत आहेत, best price आला की इथेच दिसेल.',

    'products_prompt' => 'Price तपासण्यासाठी product चे नाव लिहा (उदा. "TMT bar" किंवा "HR coil"). लोकप्रिय products:',
    'products_intro'  => '":query" साठी हे सापडले. Verified sellers कडून live price साठी "Quote घ्या" दाबा.',
    'products_none'   => '":query" शी जुळणारे कोणतेही product सापडले नाही. Enquiry टाका, sellers तुम्हाला quote पाठवतील.',

    'lead_form_intro' => 'तुमचे details द्या, आमची team तुम्हाला call back करेल.',
    'lead_created'    => 'धन्यवाद! आमची team लवकरच तुम्हाला call करेल. तुमचा reference :ref आहे.',

    'contact_intro' => 'Biznie team शी इथे संपर्क साधा:',
    'credit_info'   => 'Biznie पात्र businesses साठी credit wallet देते, म्हणजे आत्ता खरेदी करा आणि नंतर payment करा. Dashboard मधील Credit Request page वरून apply करा. Eligibility आणि documents साठी आमची support team मदत करेल.',
    'thanks'        => 'तुमचे स्वागत आहे! अजून काही मदत हवी आहे का?',
    'fallback'      => 'माफ करा, मला नीट समजले नाही. खालील एखादा option निवडा, किंवा असे लिहा: "OID-20260901-1234 track करा", "माझ्या enquiries" किंवा "TMT bar चा दर".',

    'error_unknown_action' => 'माफ करा, हे अजून शक्य नाही. कृपया खालील एखादा option निवडा.',
    'error_in_progress'    => 'तुमचा आधीचा message अजून process होत आहे. थोड्या वेळाने पुन्हा प्रयत्न करा.',
    'rate_limited'         => 'तुम्ही खूप वेगाने messages पाठवत आहात. थोडे थांबून पुन्हा प्रयत्न करा.',
    'rate_limited_writes'  => 'गेल्या एका तासात खूप requests submit झाल्या आहेत. नंतर पुन्हा प्रयत्न करा किंवा support शी संपर्क साधा.',
    'validation_one_of'    => 'एकतर message पाठवा किंवा action.',
    'validation_payload'   => 'Action payload खूप मोठा आहे.',
    'validation_encoding'  => 'मजकुरातील काही अक्षरे वाचता आली नाहीत. कृपया पुन्हा टाइप करा.',

    'order_stage' => [
        'placed'     => 'Order दिली',
        'confirmed'  => 'Confirm झाली',
        'loading'    => 'Loading सुरू',
        'dispatched' => 'Dispatch झाली',
        'delivered'  => 'Deliver झाली',
        'completed'  => 'पूर्ण',
        'cancelled'  => 'Cancel झाली',
    ],

    'enquiry_stage' => [
        'sent'        => 'Sellers ना पाठवली',
        'bidding'     => 'Bidding live',
        'best_found'  => 'Best price मिळाला',
        'offer_ready' => 'Offer तयार',
        'closed'      => 'बंद',
        'ordered'     => 'Order झाली',
        'cancelled'   => 'Cancel झाली',
    ],

    'enquiry_next_step' => [
        'sent'        => 'आम्ही आत्ता verified sellers ना invite करत आहोत. Bidding लवकरच सुरू होईल.',
        'bidding'     => 'Sellers bidding करत आहेत. Best price पाहण्यासाठी enquiry उघडी ठेवा.',
        'best_found'  => 'Best price आला आहे आणि sellers अजूनही तो कमी करू शकतात. Timer संपेपर्यंत थांबा किंवा आत्ताच पुढे जा.',
        'offer_ready' => 'Bidding संपली असून best price lock झाला आहे. Offer पाहून order देण्यासाठी enquiry उघडा.',
        'closed'      => 'कोणत्याही offer शिवाय bidding संपली. नवीन enquiry टाका किंवा support शी बोला, आम्ही sourcing मध्ये मदत करू.',
        'ordered'     => 'या enquiry ची order झाली आहे. तुमच्या orders मधून ती track करा.',
        'cancelled'   => 'ही enquiry बंद आहे. तुम्ही कधीही नवीन enquiry टाकू शकता.',
        'awaiting_offers' => 'तुमची enquiry sellers ना पाठवली आहे. Offer येताच आम्ही तुम्हाला इथे कळवू.',
        'offer_received'  => 'Sellers नी offers पाठवले आहेत. ते पाहून order देण्यासाठी enquiry उघडा.',
        'team_contact'    => 'आमच्या team ने या enquiry साठी seller निश्चित केला आहे आणि deal confirm करण्यासाठी तुमच्याशी संपर्क करेल.',
    ],

];
