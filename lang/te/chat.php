<?php

/*
| Biznie AI chat copy — Telugu (common English business words kept, as in the
| rest of the app). Keep the key set identical to lang/en/chat.php.
*/

return [

    'greeting_guest'     => "నమస్కారం! నేను Biznie AI. Price చూడటానికి, best quote కోసం enquiry పెట్టడానికి, మీ order ని track చేయడానికి నేను help చేస్తాను.\nమీరు ఏమి చేయాలనుకుంటున్నారు?",
    'greeting_user'      => 'నమస్కారం :name! మిమ్మల్ని మళ్ళీ చూడటం ఆనందంగా ఉంది.',
    'greeting_user_anon' => 'నమస్కారం! మిమ్మల్ని మళ్ళీ చూడటం ఆనందంగా ఉంది.',
    'summary_orders'     => '{1} మీకు 1 active order ఉంది.|[2,*] మీకు :count active orders ఉన్నాయి.',
    'summary_enquiries'  => '{1} మీ 1 enquiry పై ఇప్పుడు live bidding జరుగుతోంది.|[2,*] మీ :count enquiries పై ఇప్పుడు live bidding జరుగుతోంది.',
    'menu_prompt'        => 'మీరు ఏమి చేయాలనుకుంటున్నారు?',

    'login_required' => 'కొనసాగడానికి దయచేసి login చేయండి — ఆగిన చోట నుంచే మళ్ళీ మొదలుపెడతాం.',
    'login_prompt'   => 'మీ orders track చేయడానికి, enquiries లో live bidding చూడటానికి, personalised updates పొందడానికి login చేయండి.',

    'menu' => [
        'track_order'    => 'నా order track చేయండి',
        'my_enquiries'   => 'నా enquiries',
        'create_enquiry' => 'కొత్త enquiry',
        'check_prices'   => 'Price చూడండి',
        'support'        => 'Support తో మాట్లాడండి',
        'callback'       => 'Callback కోరండి',
        'login'          => 'Login చేయండి',
        'main_menu'      => 'Main menu',
        'all_orders'     => 'అన్ని orders',
        'all_enquiries'  => 'అన్ని enquiries',
        'show_more'      => 'మరిన్ని చూపించు',
        'get_quote'      => 'Quote పొందండి: :name',
        'track_enquiry'  => 'ఈ enquiry track చేయండి',
    ],

    'orders_active_intro' => 'మీ active orders ఇవే:',
    'orders_all_intro'    => 'మీ ఇటీవలి orders ఇవే:',
    'orders_none_active'  => 'ప్రస్తుతం మీకు active order ఏదీ లేదు.',
    'orders_none'         => 'మీరు ఇంకా ఏ order చేయలేదు. Verified sellers నుంచి live quotes కోసం ఒక enquiry పెట్టండి.',
    'order_found'         => 'Order :ref తాజా సమాచారం:',
    'order_not_found'     => 'ఈ order మీ account లో కనిపించలేదు. దయచేసి order ID చెక్ చేయండి — అది OID-20260901-1234 లాగా ఉంటుంది.',

    'enquiries_open_intro' => 'మీ open enquiries ఇవే:',
    'enquiries_all_intro'  => 'మీ ఇటీవలి enquiries ఇవే:',
    'enquiries_none_open'  => 'ప్రస్తుతం మీకు open enquiry ఏదీ లేదు.',
    'enquiries_none'       => 'మీరు ఇంకా ఏ enquiry పెట్టలేదు.',
    'enquiry_found'        => 'Enquiry :ref తాజా సమాచారం:',
    'enquiry_not_found'    => 'ఈ enquiry మీ account లో కనిపించలేదు. దయచేసి ID చెక్ చేయండి — అది BZN-RFQ-260901-0001 లాగా ఉంటుంది.',

    'enquiry_form_intro' => 'కింది details చెక్ చేసి submit చేయండి — మీ enquiry వెంటనే verified sellers కు live అవుతుంది.',
    'enquiry_created'    => 'మీ enquiry :ref live అయింది! Sellers ఇప్పుడు bidding చేస్తున్నారు, best price రాగానే ఇక్కడే కనిపిస్తుంది.',

    'products_prompt' => 'Price చూడటానికి product పేరు type చేయండి (ఉదా. "TMT bar" లేదా "HR coil"). ప్రముఖ products:',
    'products_intro'  => '":query" కోసం దొరికినవి ఇవే. Verified sellers నుంచి live price కోసం "Quote పొందండి" నొక్కండి.',
    'products_none'   => '":query" కు సరిపోయే product ఏదీ దొరకలేదు. ఒక enquiry పెట్టండి, sellers quote పంపిస్తారు.',

    'lead_form_intro' => 'మీ details ఇవ్వండి, మా team మీకు call back చేస్తుంది.',
    'lead_created'    => 'ధన్యవాదాలు! మా team త్వరలో మీకు call చేస్తుంది. మీ reference :ref.',

    'contact_intro' => 'Biznie team ని ఇక్కడ సంప్రదించండి:',
    'credit_info'   => 'అర్హత ఉన్న businesses కు Biznie credit wallet అందిస్తుంది — ఇప్పుడు కొని తర్వాత payment చేయవచ్చు. Dashboard లోని Credit Request page నుంచి apply చేయండి. Eligibility, documents విషయంలో మా support team help చేస్తుంది.',
    'thanks'        => 'సంతోషం! ఇంకేమైనా help కావాలా?',
    'fallback'      => 'క్షమించండి, నాకు సరిగ్గా అర్థం కాలేదు. కింద ఒక option ఎంచుకోండి, లేదా "OID-20260901-1234 track", "నా enquiries" లేదా "TMT bar price" అని type చేయండి.',

    'error_unknown_action' => 'క్షమించండి, ఇది ఇంకా సాధ్యం కాదు. దయచేసి కింద ఒక option ఎంచుకోండి.',
    'error_in_progress'    => 'మీ మునుపటి message ఇంకా process అవుతోంది. కాసేపటి తర్వాత మళ్ళీ ప్రయత్నించండి.',
    'rate_limited'         => 'మీరు చాలా వేగంగా messages పంపుతున్నారు. కాసేపు ఆగి మళ్ళీ ప్రయత్నించండి.',
    'rate_limited_writes'  => 'గత గంటలో చాలా ఎక్కువ requests submit అయ్యాయి. తర్వాత ప్రయత్నించండి లేదా support ని సంప్రదించండి.',
    'validation_one_of'    => 'ఒక message లేదా ఒక action మాత్రమే పంపండి.',
    'validation_payload'   => 'Action payload చాలా పెద్దది.',
    'validation_encoding'  => 'టెక్స్ట్‌లో చదవలేని అక్షరాలు ఉన్నాయి. దయచేసి మళ్ళీ టైప్ చేయండి.',

    'order_stage' => [
        'placed'     => 'Order చేయబడింది',
        'confirmed'  => 'Confirm అయింది',
        'loading'    => 'Loading జరుగుతోంది',
        'dispatched' => 'Dispatch అయింది',
        'delivered'  => 'Deliver అయింది',
        'completed'  => 'పూర్తయింది',
        'cancelled'  => 'Cancel అయింది',
    ],

    'enquiry_stage' => [
        'sent'        => 'Sellers కు పంపబడింది',
        'bidding'     => 'Bidding live',
        'best_found'  => 'Best price దొరికింది',
        'offer_ready' => 'Offer సిద్ధం',
        'closed'      => 'ముగిసింది',
        'ordered'     => 'Order అయింది',
        'cancelled'   => 'Cancel అయింది',
    ],

    'enquiry_next_step' => [
        'sent'        => 'మేము ఇప్పుడు verified sellers ని invite చేస్తున్నాం. Bidding త్వరలో మొదలవుతుంది.',
        'bidding'     => 'Sellers bidding చేస్తున్నారు. Best price చూడటానికి enquiry ని open లో ఉంచండి.',
        'best_found'  => 'Best price వచ్చింది, sellers ఇంకా దాన్ని తగ్గించవచ్చు. Timer అయ్యేవరకు ఆగండి లేదా ఇప్పుడే ముందుకు వెళ్ళండి.',
        'offer_ready' => 'Bidding ముగిసి best price lock అయింది. Offer చూసి order చేయడానికి enquiry ని open చేయండి.',
        'closed'      => 'ఏ offer లేకుండా bidding ముగిసింది. కొత్త enquiry పెట్టండి లేదా support తో మాట్లాడండి, sourcing లో మేము help చేస్తాం.',
        'ordered'     => 'ఈ enquiry order గా మారింది. మీ orders లో దాన్ని track చేయండి.',
        'cancelled'   => 'ఈ enquiry ముగిసింది. మీరు ఎప్పుడైనా కొత్త enquiry పెట్టవచ్చు.',
        'awaiting_offers' => 'మీ enquiry sellers కి పంపబడింది. Offer రాగానే మేము ఇక్కడ మీకు తెలియజేస్తాం.',
        'offer_received'  => 'Sellers offers పంపారు. వాటిని చూసి order చేయడానికి enquiry ని open చేయండి.',
        'team_contact'    => 'మా team ఈ enquiry కోసం ఒక seller ని ఎంచుకుంది, deal confirm చేయడానికి మిమ్మల్ని సంప్రదిస్తుంది.',
    ],

];
