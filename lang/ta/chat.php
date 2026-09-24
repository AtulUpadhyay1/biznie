<?php

/*
| Biznie AI chat copy — Tamil (common English business words kept, as in the
| rest of the app). Keep the key set identical to lang/en/chat.php.
*/

return [

    'greeting_guest'     => "வணக்கம்! நான் Biznie AI. Price பார்க்க, best quote-க்கு enquiry போட, உங்கள் order-ஐ track செய்ய நான் உதவ முடியும்.\nநீங்கள் என்ன செய்ய விரும்புகிறீர்கள்?",
    'greeting_user'      => 'வணக்கம் :name! உங்களை மீண்டும் பார்த்ததில் மகிழ்ச்சி.',
    'greeting_user_anon' => 'வணக்கம்! உங்களை மீண்டும் பார்த்ததில் மகிழ்ச்சி.',
    'summary_orders'     => '{1} உங்களிடம் 1 active order உள்ளது.|[2,*] உங்களிடம் :count active orders உள்ளன.',
    'summary_enquiries'  => '{1} உங்கள் 1 enquiry-க்கு இப்போது live bidding நடக்கிறது.|[2,*] உங்கள் :count enquiries-க்கு இப்போது live bidding நடக்கிறது.',
    'menu_prompt'        => 'நீங்கள் என்ன செய்ய விரும்புகிறீர்கள்?',

    'login_required' => 'தொடர login செய்யவும் — நிறுத்திய இடத்திலிருந்தே மீண்டும் தொடர்வோம்.',
    'login_prompt'   => 'உங்கள் orders-ஐ track செய்ய, enquiries-இன் live bidding பார்க்க, personalised updates பெற login செய்யவும்.',

    'menu' => [
        'track_order'    => 'என் order-ஐ track செய்',
        'my_enquiries'   => 'என் enquiries',
        'create_enquiry' => 'புதிய enquiry',
        'check_prices'   => 'Price பார்க்க',
        'support'        => 'Support-உடன் பேச',
        'callback'       => 'Callback கோரிக்கை',
        'login'          => 'Login',
        'main_menu'      => 'Main menu',
        'all_orders'     => 'அனைத்து orders',
        'all_enquiries'  => 'அனைத்து enquiries',
        'show_more'      => 'மேலும் காட்டு',
        'get_quote'      => 'Quote பெற: :name',
        'track_enquiry'  => 'இந்த enquiry-ஐ track செய்',
    ],

    'orders_active_intro' => 'உங்கள் active orders இதோ:',
    'orders_all_intro'    => 'உங்கள் சமீபத்திய orders இதோ:',
    'orders_none_active'  => 'இப்போது உங்களிடம் active order எதுவும் இல்லை.',
    'orders_none'         => 'நீங்கள் இன்னும் order எதுவும் செய்யவில்லை. Verified sellers-இடமிருந்து live quotes பெற ஒரு enquiry போடுங்கள்.',
    'order_found'         => 'Order :ref பற்றிய சமீபத்திய தகவல்:',
    'order_not_found'     => 'இந்த order உங்கள் account-இல் இல்லை. Order ID-ஐ சரிபார்க்கவும் — அது OID-20260901-1234 போல இருக்கும்.',

    'enquiries_open_intro' => 'உங்கள் open enquiries இதோ:',
    'enquiries_all_intro'  => 'உங்கள் சமீபத்திய enquiries இதோ:',
    'enquiries_none_open'  => 'இப்போது உங்களிடம் open enquiry எதுவும் இல்லை.',
    'enquiries_none'       => 'நீங்கள் இன்னும் enquiry எதுவும் போடவில்லை.',
    'enquiry_found'        => 'Enquiry :ref பற்றிய சமீபத்திய தகவல்:',
    'enquiry_not_found'    => 'இந்த enquiry உங்கள் account-இல் இல்லை. ID-ஐ சரிபார்க்கவும் — அது BZN-RFQ-260901-0001 போல இருக்கும்.',

    'enquiry_form_intro' => 'கீழே உள்ள details-ஐ சரிபார்த்து submit செய்யுங்கள் — உங்கள் enquiry உடனே verified sellers-க்கு live ஆகும்.',
    'enquiry_created'    => 'உங்கள் enquiry :ref live ஆகிவிட்டது! Sellers இப்போது bidding செய்கிறார்கள், best price வந்ததும் இங்கே தெரியும்.',

    'products_prompt' => 'Price பார்க்க product பெயரை type செய்யுங்கள் (உதா. "TMT bar" அல்லது "HR coil"). பிரபலமான products:',
    'products_intro'  => '":query"-க்கு கிடைத்தவை இதோ. Verified sellers-இன் live price-க்கு "Quote பெற" அழுத்தவும்.',
    'products_none'   => '":query"-க்கு பொருந்தும் product எதுவும் கிடைக்கவில்லை. ஒரு enquiry போடுங்கள், sellers quote அனுப்புவார்கள்.',

    'lead_form_intro' => 'உங்கள் details-ஐ பகிருங்கள், எங்கள் team உங்களை call back செய்யும்.',
    'lead_created'    => 'நன்றி! எங்கள் team விரைவில் உங்களை அழைக்கும். உங்கள் reference :ref.',

    'contact_intro' => 'Biznie team-ஐ இங்கே தொடர்பு கொள்ளுங்கள்:',
    'credit_info'   => 'தகுதியான businesses-க்கு Biznie credit wallet வழங்குகிறது — இப்போது வாங்கி பிறகு payment செய்யலாம். Dashboard-இல் உள்ள Credit Request page-இல் apply செய்யுங்கள். Eligibility மற்றும் documents பற்றி எங்கள் support team உதவும்.',
    'thanks'        => 'மகிழ்ச்சி! வேறு ஏதாவது உதவி வேண்டுமா?',
    'fallback'      => 'மன்னிக்கவும், எனக்கு சரியாக புரியவில்லை. கீழே ஒரு option-ஐ தேர்ந்தெடுங்கள், அல்லது "OID-20260901-1234 track", "என் enquiries" அல்லது "TMT bar price" என்று type செய்யுங்கள்.',

    'error_unknown_action' => 'மன்னிக்கவும், இதை இன்னும் செய்ய முடியாது. கீழே ஒரு option-ஐ தேர்ந்தெடுங்கள்.',
    'error_in_progress'    => 'உங்கள் முந்தைய message இன்னும் process ஆகிறது. சிறிது நேரம் கழித்து மீண்டும் முயற்சிக்கவும்.',
    'rate_limited'         => 'நீங்கள் மிக வேகமாக messages அனுப்புகிறீர்கள். சிறிது காத்திருந்து மீண்டும் முயற்சிக்கவும்.',
    'rate_limited_writes'  => 'கடந்த ஒரு மணி நேரத்தில் அதிக requests submit செய்யப்பட்டுள்ளன. பிறகு முயற்சிக்கவும் அல்லது support-ஐ தொடர்பு கொள்ளவும்.',
    'validation_one_of'    => 'ஒரு message அல்லது ஒரு action மட்டும் அனுப்பவும்.',
    'validation_payload'   => 'Action payload மிகப் பெரியது.',
    'validation_encoding'  => 'உரையில் படிக்க முடியாத எழுத்துகள் உள்ளன. மீண்டும் தட்டச்சு செய்யவும்.',

    'order_stage' => [
        'placed'     => 'Order செய்யப்பட்டது',
        'confirmed'  => 'Confirm ஆனது',
        'loading'    => 'Loading நடக்கிறது',
        'dispatched' => 'Dispatch ஆனது',
        'delivered'  => 'Deliver ஆனது',
        'completed'  => 'முடிந்தது',
        'cancelled'  => 'Cancel ஆனது',
    ],

    'enquiry_stage' => [
        'sent'        => 'Sellers-க்கு அனுப்பப்பட்டது',
        'bidding'     => 'Bidding live',
        'best_found'  => 'Best price கிடைத்தது',
        'offer_ready' => 'Offer தயார்',
        'closed'      => 'மூடப்பட்டது',
        'ordered'     => 'Order ஆனது',
        'cancelled'   => 'Cancel ஆனது',
    ],

    'enquiry_next_step' => [
        'sent'        => 'நாங்கள் இப்போது verified sellers-ஐ அழைக்கிறோம். Bidding விரைவில் தொடங்கும்.',
        'bidding'     => 'Sellers bidding செய்கிறார்கள். Best price பார்க்க enquiry-ஐ திறந்தே வைத்திருங்கள்.',
        'best_found'  => 'ஒரு best price வந்துள்ளது, sellers இன்னும் அதைக் குறைக்கலாம். Timer முடியும் வரை காத்திருங்கள் அல்லது இப்போதே தொடருங்கள்.',
        'offer_ready' => 'Bidding முடிந்து best price lock ஆகிவிட்டது. Offer-ஐ பார்த்து order செய்ய enquiry-ஐ திறக்கவும்.',
        'closed'      => 'Offer எதுவும் இல்லாமல் bidding முடிந்தது. புதிய enquiry போடுங்கள் அல்லது support-உடன் பேசுங்கள், sourcing-இல் நாங்கள் உதவுவோம்.',
        'ordered'     => 'இந்த enquiry order ஆக மாறிவிட்டது. உங்கள் orders-இல் அதை track செய்யுங்கள்.',
        'cancelled'   => 'இந்த enquiry மூடப்பட்டது. எப்போது வேண்டுமானாலும் புதிய enquiry போடலாம்.',
        'awaiting_offers' => 'உங்கள் enquiry sellers-க்கு அனுப்பப்பட்டது. Offer வந்தவுடன் இங்கே உங்களுக்குத் தெரிவிப்போம்.',
        'offer_received'  => 'Sellers offers அனுப்பியுள்ளனர். அவற்றைப் பார்த்து order செய்ய enquiry-ஐ திறக்கவும்.',
        'team_contact'    => 'எங்கள் team இந்த enquiry-க்கு ஒரு seller-ஐ தேர்வு செய்துள்ளது, deal-ஐ உறுதிப்படுத்த உங்களைத் தொடர்பு கொள்ளும்.',
    ],

];
