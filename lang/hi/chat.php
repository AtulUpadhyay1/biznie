<?php

/*
| Biznie AI chat copy — Hindi, written in Roman script (Hinglish) to match the
| rest of the app's Hindi copy. Keep the key set identical to lang/en/chat.php.
*/

return [

    'greeting_guest'     => "Namaste! Main Biznie AI hoon. Main aapko prices check karne, best quote ke liye enquiry daalne aur order track karne mein madad kar sakta hoon.\nAap kya karna chahenge?",
    'greeting_user'      => 'Namaste :name! Aapka swagat hai.',
    'greeting_user_anon' => 'Namaste! Aapka swagat hai.',
    'summary_orders'     => '{1} Aapka 1 order active hai.|[2,*] Aapke :count orders active hain.',
    'summary_enquiries'  => '{1} Aapki 1 enquiry par abhi live bidding chal rahi hai.|[2,*] Aapki :count enquiries par abhi live bidding chal rahi hai.',
    'menu_prompt'        => 'Aap kya karna chahenge?',

    'login_required' => 'Aage badhne ke liye kripya login karein — hum wahi se shuru karenge jahan ruke the.',
    'login_prompt'   => 'Apne orders track karne, enquiries ki live bidding dekhne aur personalised updates paane ke liye login karein.',

    'menu' => [
        'track_order'    => 'Mera order track karein',
        'my_enquiries'   => 'Meri enquiries',
        'create_enquiry' => 'Nayi enquiry banayein',
        'check_prices'   => 'Price check karein',
        'support'        => 'Support se baat karein',
        'callback'       => 'Callback request karein',
        'login'          => 'Login karein',
        'main_menu'      => 'Main menu',
        'all_orders'     => 'Saare orders',
        'all_enquiries'  => 'Saari enquiries',
        'show_more'      => 'Aur dikhayein',
        'get_quote'      => 'Quote lein: :name',
        'track_enquiry'  => 'Ye enquiry track karein',
    ],

    'orders_active_intro' => 'Ye rahe aapke active orders:',
    'orders_all_intro'    => 'Ye rahe aapke haal ke orders:',
    'orders_none_active'  => 'Abhi aapka koi active order nahi hai.',
    'orders_none'         => 'Aapne abhi tak koi order nahi kiya hai. Verified sellers se live quotes paane ke liye enquiry daalein.',
    'order_found'         => 'Order :ref ki latest jaankari:',
    'order_not_found'     => 'Ye order aapke account mein nahi mila. Kripya order ID check karein — ye OID-20260901-1234 jaisa dikhta hai.',

    'enquiries_open_intro' => 'Ye rahi aapki open enquiries:',
    'enquiries_all_intro'  => 'Ye rahi aapki haal ki enquiries:',
    'enquiries_none_open'  => 'Abhi aapki koi open enquiry nahi hai.',
    'enquiries_none'       => 'Aapne abhi tak koi enquiry nahi daali hai.',
    'enquiry_found'        => 'Enquiry :ref ki latest jaankari:',
    'enquiry_not_found'    => 'Ye enquiry aapke account mein nahi mili. Kripya ID check karein — ye BZN-RFQ-260901-0001 jaisi dikhti hai.',

    'enquiry_form_intro' => 'Neeche details check karke submit karein — aapki enquiry turant verified sellers tak live ho jayegi.',
    'enquiry_created'    => 'Aapki enquiry :ref live ho gayi hai! Sellers abhi bidding kar rahe hain, best price aate hi aapko yahin dikhega.',

    'products_prompt' => 'Price check karne ke liye product ka naam likhein (jaise "TMT bar" ya "HR coil"). Popular products:',
    'products_intro'  => '":query" ke liye ye mila. Verified sellers se live price ke liye "Quote lein" dabayein.',
    'products_none'   => '":query" se milta-julta koi product nahi mila. Enquiry daalein, sellers aapko quote bhejenge.',

    'lead_form_intro' => 'Apni details share karein, hamari team aapko call back karegi.',
    'lead_created'    => 'Dhanyavaad! Hamari team jald hi aapko call karegi. Aapka reference number :ref hai.',

    'contact_intro' => 'Biznie team se yahan sampark karein:',
    'credit_info'   => 'Biznie eligible businesses ke liye credit wallet deta hai, taaki aap abhi khareedein aur baad mein payment karein. Dashboard ke Credit Request page se apply karein. Eligibility aur documents mein hamari support team madad karegi.',
    'thanks'        => 'Aapka swagat hai! Aur kisi cheez mein madad chahiye?',
    'fallback'      => 'Maaf kijiye, main samajh nahi paaya. Neeche se koi option chunein, ya aise likhein: "OID-20260901-1234 track karo", "meri enquiries" ya "TMT bar ka rate".',

    'error_unknown_action' => 'Maaf kijiye, ye abhi sambhav nahi hai. Kripya neeche se koi option chunein.',
    'error_in_progress'    => 'Aapka pichla message abhi process ho raha hai. Thodi der mein dobara koshish karein.',
    'rate_limited'         => 'Aap bahut jaldi-jaldi messages bhej rahe hain. Thoda rukkar dobara koshish karein.',
    'rate_limited_writes'  => 'Pichhle ek ghante mein bahut zyada requests submit ho chuki hain. Kripya baad mein koshish karein ya support se sampark karein.',
    'validation_one_of'    => 'Ya to message bhejein ya action.',
    'validation_payload'   => 'Action payload bahut bada hai.',
    'validation_encoding'  => 'Text mein kuch characters padhe nahi ja sake. Kripya dobara type karein.',

    'order_stage' => [
        'placed'     => 'Order place ho gaya',
        'confirmed'  => 'Confirm ho gaya',
        'loading'    => 'Loading ho raha hai',
        'dispatched' => 'Dispatch ho chuka hai',
        'delivered'  => 'Deliver ho gaya',
        'completed'  => 'Complete',
        'cancelled'  => 'Cancel ho gaya',
    ],

    'enquiry_stage' => [
        'sent'        => 'Sellers ko bheji gayi',
        'bidding'     => 'Bidding live hai',
        'best_found'  => 'Best price mil gaya',
        'offer_ready' => 'Offer taiyaar hai',
        'closed'      => 'Band ho gayi',
        'ordered'     => 'Order ho gaya',
        'cancelled'   => 'Cancel ho gayi',
    ],

    'enquiry_next_step' => [
        'sent'        => 'Hum abhi verified sellers ko invite kar rahe hain. Bidding jald shuru hogi.',
        'bidding'     => 'Sellers bidding kar rahe hain. Best price dekhne ke liye enquiry khuli rakhein.',
        'best_found'  => 'Best price aa gaya hai aur sellers ise abhi bhi kam kar sakte hain. Timer ka intezaar karein ya abhi aage badhein.',
        'offer_ready' => 'Bidding band ho gayi hai aur best price lock ho gaya hai. Offer dekhne aur order place karne ke liye enquiry kholein.',
        'closed'      => 'Bidding bina offer ke band ho gayi. Nayi enquiry daalein ya support se baat karein, hum sourcing mein madad karenge.',
        'ordered'     => 'Is enquiry ka order ban chuka hai. Apne orders mein ise track karein.',
        'cancelled'   => 'Ye enquiry band hai. Aap kabhi bhi nayi enquiry daal sakte hain.',
        'awaiting_offers' => 'Aapki enquiry sellers ko bhej di gayi hai. Offer aate hi hum aapko yahan update karenge.',
        'offer_received'  => 'Sellers ne offers bheje hain. Unhe dekhne aur order place karne ke liye enquiry kholein.',
        'team_contact'    => 'Hamari team ne is enquiry ke liye seller tay kar liya hai aur deal confirm karne ke liye aapse sampark karegi.',
    ],

];
