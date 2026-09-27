<?php

/*
| Biznie AI chat copy — Hindi in Devanagari. Common English business and
| technical terms (order, enquiry, status, quote, dispatch, login, chat…) stay
| in Latin script, the way buyers actually use them. Plain text only.
| Keep the key set identical to lang/en/chat.php.
*/

return [

    'greeting_guest'     => "नमस्ते! मैं Biznie AI हूँ। मैं prices check करने, best quote के लिए enquiry डालने और आपके orders track करने में मदद कर सकता हूँ।\nआप क्या करना चाहेंगे?",
    'greeting_user'      => 'नमस्ते :name! आपका स्वागत है।',
    'greeting_user_anon' => 'नमस्ते! आपका स्वागत है।',
    'summary_orders'     => '{1} आपका 1 order active है।|[2,*] आपके :count orders active हैं।',
    'summary_enquiries'  => '{1} आपकी 1 enquiry पर अभी live bidding चल रही है।|[2,*] आपकी :count enquiries पर अभी live bidding चल रही है।',
    'menu_prompt'        => 'आप क्या करना चाहेंगे?',

    'login_required' => 'आगे बढ़ने के लिए कृपया login करें — हम वहीं से शुरू करेंगे जहाँ आपने छोड़ा था।',
    'login_prompt'   => 'अपने orders track करने, अपनी enquiries की live bidding देखने और personalised updates पाने के लिए login करें।',

    'menu' => [
        'track_order'    => 'मेरा order track करें',
        'my_enquiries'   => 'मेरी enquiries',
        'create_enquiry' => 'नई enquiry बनाएँ',
        'check_prices'   => 'Price check करें',
        'support'        => 'Support से बात करें',
        'callback'       => 'Callback का अनुरोध करें',
        'login'          => 'Login करें',
        'main_menu'      => 'Main menu',
        'all_orders'     => 'सभी orders',
        'all_enquiries'  => 'सभी enquiries',
        'show_more'      => 'और दिखाएँ',
        'get_quote'      => 'Quote लें: :name',
        'track_enquiry'  => 'यह enquiry track करें',
    ],

    'orders_active_intro' => 'ये रहे आपके active orders:',
    'orders_all_intro'    => 'ये रहे आपके हाल के orders:',
    'orders_none_active'  => 'अभी आपका कोई active order नहीं है।',
    'orders_none'         => 'आपने अभी तक कोई order नहीं किया है। Verified sellers से live quotes पाने के लिए enquiry डालें।',
    'order_found'         => 'Order :ref की ताज़ा जानकारी:',
    'order_not_found'     => 'यह order आपके account में नहीं मिला। कृपया order ID check करें — यह OID-20260901-1234 जैसा दिखता है।',

    'enquiries_open_intro' => 'ये रहीं आपकी open enquiries:',
    'enquiries_all_intro'  => 'ये रहीं आपकी हाल की enquiries:',
    'enquiries_none_open'  => 'अभी आपकी कोई open enquiry नहीं है।',
    'enquiries_none'       => 'आपने अभी तक कोई enquiry नहीं डाली है।',
    'enquiry_found'        => 'Enquiry :ref की ताज़ा जानकारी:',
    'enquiry_not_found'    => 'यह enquiry आपके account में नहीं मिली। कृपया ID check करें — यह BZN-RFQ-260901-0001 जैसी दिखती है।',

    'enquiry_form_intro' => 'नीचे दी गई details check करके submit करें — आपकी enquiry तुरंत verified sellers के लिए live हो जाएगी।',
    'enquiry_created'    => 'आपकी enquiry :ref live हो गई है! Sellers अभी bidding कर रहे हैं, best price आते ही आपको यहीं दिखेगा।',

    'products_prompt' => 'Price check करने के लिए product का नाम लिखें (जैसे "TMT bar" या "HR coil")। Popular products:',
    'products_intro'  => '":query" के लिए ये products मिले। Verified sellers से live price पाने के लिए "Quote लें" दबाएँ।',
    'products_none'   => '":query" से मिलता-जुलता कोई product नहीं मिला। Enquiry डालें, sellers आपको quote भेजेंगे।',

    'lead_form_intro' => 'अपनी details share करें, हमारी team आपको call back करेगी।',
    'lead_created'    => 'धन्यवाद! हमारी team जल्द ही आपको call करेगी। आपका reference number :ref है।',

    'contact_intro' => 'Biznie team से यहाँ संपर्क करें:',
    'credit_info'   => 'Biznie योग्य businesses को credit wallet की सुविधा देता है, ताकि आप अभी खरीदें और payment बाद में करें। अपने dashboard के Credit Request page से apply करें। Eligibility और documents में हमारी support team आपकी मदद करेगी।',
    'thanks'        => 'मदद करके खुशी हुई! क्या मैं किसी और चीज़ में आपकी मदद कर सकता हूँ?',
    'fallback'      => 'माफ़ कीजिए, मैं समझ नहीं पाया। नीचे से कोई option चुनें, या ऐसे लिखें: "OID-20260901-1234 track करो", "मेरी enquiries" या "TMT bar का rate"।',

    'error_unknown_action' => 'माफ़ कीजिए, यह अभी संभव नहीं है। कृपया नीचे से कोई option चुनें।',
    'error_in_progress'    => 'आपका पिछला message अभी process हो रहा है। कृपया थोड़ी देर में फिर से कोशिश करें।',
    'rate_limited'         => 'आप बहुत जल्दी-जल्दी messages भेज रहे हैं। कृपया थोड़ा रुककर फिर से कोशिश करें।',
    'rate_limited_writes'  => 'पिछले एक घंटे में बहुत ज़्यादा requests submit हो चुकी हैं। कृपया बाद में कोशिश करें या support से संपर्क करें।',
    'validation_one_of'    => 'या तो message भेजें या action।',
    'validation_payload'   => 'Action payload बहुत बड़ा है।',
    'validation_encoding'  => 'Text के कुछ characters पढ़े नहीं जा सके। कृपया दोबारा type करें।',

    'order_stage' => [
        'placed'     => 'Order place हो गया',
        'confirmed'  => 'Confirm हो गया',
        'loading'    => 'Loading हो रहा है',
        'dispatched' => 'Dispatch हो चुका है',
        'delivered'  => 'Deliver हो गया',
        'completed'  => 'Completed',
        'cancelled'  => 'Cancel हो गया',
    ],

    'enquiry_stage' => [
        'sent'        => 'Sellers को भेजी गई',
        'bidding'     => 'Bidding live है',
        'best_found'  => 'Best price मिल गया',
        'offer_ready' => 'Offer तैयार है',
        'closed'      => 'बंद हो गई',
        'ordered'     => 'Order हो गया',
        'cancelled'   => 'Cancel हो गई',
    ],

    'enquiry_next_step' => [
        'sent'        => 'हम अभी verified sellers को invite कर रहे हैं। Bidding जल्द ही शुरू होगी।',
        'bidding'     => 'Sellers bidding कर रहे हैं। Best price देखने के लिए enquiry खुली रखें।',
        'best_found'  => 'Best price आ गया है और sellers इसे अभी भी कम कर सकते हैं। Timer खत्म होने का इंतज़ार करें या अभी आगे बढ़ें।',
        'offer_ready' => 'Bidding बंद हो गई है और best price lock हो गया है। Offer देखने और order place करने के लिए enquiry खोलें।',
        'closed'      => 'Bidding बिना किसी offer के बंद हो गई। नई enquiry डालें या support से बात करें, हम sourcing में आपकी मदद करेंगे।',
        'ordered'     => 'इस enquiry का order बन चुका है। इसे अपने orders में track करें।',
        'cancelled'   => 'यह enquiry बंद है। आप कभी भी नई enquiry डाल सकते हैं।',
        'awaiting_offers' => 'आपकी enquiry sellers को भेज दी गई है। Offer आते ही हम आपको यहीं update करेंगे।',
        'offer_received'  => 'Sellers ने offers भेजे हैं। उन्हें देखने और order place करने के लिए enquiry खोलें।',
        'team_contact'    => 'हमारी team ने इस enquiry के लिए seller तय कर लिया है और deal confirm करने के लिए आपसे संपर्क करेगी।',
    ],

];
