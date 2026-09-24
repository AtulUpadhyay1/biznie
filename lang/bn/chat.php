<?php

/*
| Biznie AI chat copy — Bengali (common English business words kept, as in the
| rest of the app). Keep the key set identical to lang/en/chat.php.
*/

return [

    'greeting_guest'     => "নমস্কার! আমি Biznie AI। আমি আপনাকে price check করতে, best quote-এর জন্য enquiry দিতে এবং order track করতে সাহায্য করতে পারি।\nআপনি কী করতে চান?",
    'greeting_user'      => 'নমস্কার :name! আপনাকে দেখে ভালো লাগল।',
    'greeting_user_anon' => 'নমস্কার! আপনাকে দেখে ভালো লাগল।',
    'summary_orders'     => '{1} আপনার 1টি order active আছে।|[2,*] আপনার :countটি order active আছে।',
    'summary_enquiries'  => '{1} আপনার 1টি enquiry-তে এখন live bidding চলছে।|[2,*] আপনার :countটি enquiry-তে এখন live bidding চলছে।',
    'menu_prompt'        => 'আপনি কী করতে চান?',

    'login_required' => 'এগিয়ে যেতে দয়া করে login করুন — যেখানে থেমেছিলাম সেখান থেকেই আবার শুরু করব।',
    'login_prompt'   => 'আপনার order track করতে, enquiry-র live bidding দেখতে এবং personalised update পেতে login করুন।',

    'menu' => [
        'track_order'    => 'আমার order track করুন',
        'my_enquiries'   => 'আমার enquiry',
        'create_enquiry' => 'নতুন enquiry দিন',
        'check_prices'   => 'Price দেখুন',
        'support'        => 'Support-এর সাথে কথা বলুন',
        'callback'       => 'Callback চান',
        'login'          => 'Login করুন',
        'main_menu'      => 'Main menu',
        'all_orders'     => 'সব order',
        'all_enquiries'  => 'সব enquiry',
        'show_more'      => 'আরও দেখুন',
        'get_quote'      => 'Quote নিন: :name',
        'track_enquiry'  => 'এই enquiry track করুন',
    ],

    'orders_active_intro' => 'এই হলো আপনার active order:',
    'orders_all_intro'    => 'এই হলো আপনার সাম্প্রতিক order:',
    'orders_none_active'  => 'এই মুহূর্তে আপনার কোনো active order নেই।',
    'orders_none'         => 'আপনি এখনও কোনো order দেননি। Verified seller-দের থেকে live quote পেতে একটি enquiry দিন।',
    'order_found'         => 'Order :ref-এর সর্বশেষ আপডেট:',
    'order_not_found'     => 'এই order আপনার account-এ পাওয়া যায়নি। দয়া করে order ID মিলিয়ে দেখুন — এটি OID-20260901-1234-এর মতো দেখতে।',

    'enquiries_open_intro' => 'এই হলো আপনার open enquiry:',
    'enquiries_all_intro'  => 'এই হলো আপনার সাম্প্রতিক enquiry:',
    'enquiries_none_open'  => 'এই মুহূর্তে আপনার কোনো open enquiry নেই।',
    'enquiries_none'       => 'আপনি এখনও কোনো enquiry দেননি।',
    'enquiry_found'        => 'Enquiry :ref-এর সর্বশেষ আপডেট:',
    'enquiry_not_found'    => 'এই enquiry আপনার account-এ পাওয়া যায়নি। দয়া করে ID মিলিয়ে দেখুন — এটি BZN-RFQ-260901-0001-এর মতো দেখতে।',

    'enquiry_form_intro' => 'নিচের details দেখে submit করুন — আপনার enquiry সঙ্গে সঙ্গে verified seller-দের কাছে live হয়ে যাবে।',
    'enquiry_created'    => 'আপনার enquiry :ref এখন live! Seller-রা bidding করছেন, best price এলেই এখানে দেখতে পাবেন।',

    'products_prompt' => 'Price দেখতে product-এর নাম লিখুন (যেমন "TMT bar" বা "HR coil")। জনপ্রিয় product:',
    'products_intro'  => '":query"-এর জন্য এগুলো পাওয়া গেছে। Verified seller-দের live price পেতে "Quote নিন" চাপুন।',
    'products_none'   => '":query"-এর সাথে মেলে এমন কোনো product পাওয়া যায়নি। একটি enquiry দিন, seller-রা quote পাঠাবেন।',

    'lead_form_intro' => 'আপনার details দিন, আমাদের team আপনাকে call back করবে।',
    'lead_created'    => 'ধন্যবাদ! আমাদের team শীঘ্রই আপনাকে call করবে। আপনার reference :ref।',

    'contact_intro' => 'Biznie team-এর সাথে এখানে যোগাযোগ করুন:',
    'credit_info'   => 'Biznie যোগ্য business-দের জন্য credit wallet দেয়, যাতে আপনি এখন কিনে পরে payment করতে পারেন। Dashboard-এর Credit Request page থেকে apply করুন। Eligibility ও documents নিয়ে আমাদের support team সাহায্য করবে।',
    'thanks'        => 'আপনাকেও ধন্যবাদ! আর কিছুতে সাহায্য করতে পারি?',
    'fallback'      => 'দুঃখিত, ঠিক বুঝতে পারিনি। নিচ থেকে একটি option বেছে নিন, অথবা লিখুন যেমন "OID-20260901-1234 track", "আমার enquiry" বা "TMT bar price"।',

    'error_unknown_action' => 'দুঃখিত, এটি এখনও করা যাচ্ছে না। দয়া করে নিচ থেকে একটি option বেছে নিন।',
    'error_in_progress'    => 'আপনার আগের message এখনও process হচ্ছে। একটু পরে আবার চেষ্টা করুন।',
    'rate_limited'         => 'আপনি খুব তাড়াতাড়ি message পাঠাচ্ছেন। একটু অপেক্ষা করে আবার চেষ্টা করুন।',
    'rate_limited_writes'  => 'গত এক ঘণ্টায় অনেক বেশি request submit হয়েছে। পরে আবার চেষ্টা করুন বা support-এর সাথে যোগাযোগ করুন।',
    'validation_one_of'    => 'হয় একটি message পাঠান, নয়তো একটি action।',
    'validation_payload'   => 'Action payload অনেক বড়।',
    'validation_encoding'  => 'লেখায় এমন কিছু অক্ষর আছে যা পড়া যায়নি। অনুগ্রহ করে আবার টাইপ করুন।',

    'order_stage' => [
        'placed'     => 'Order দেওয়া হয়েছে',
        'confirmed'  => 'Confirm হয়েছে',
        'loading'    => 'Loading চলছে',
        'dispatched' => 'Dispatch হয়েছে',
        'delivered'  => 'Deliver হয়েছে',
        'completed'  => 'সম্পূর্ণ',
        'cancelled'  => 'Cancel হয়েছে',
    ],

    'enquiry_stage' => [
        'sent'        => 'Seller-দের পাঠানো হয়েছে',
        'bidding'     => 'Bidding live',
        'best_found'  => 'Best price পাওয়া গেছে',
        'offer_ready' => 'Offer তৈরি',
        'closed'      => 'বন্ধ',
        'ordered'     => 'Order হয়েছে',
        'cancelled'   => 'Cancel হয়েছে',
    ],

    'enquiry_next_step' => [
        'sent'        => 'আমরা এখন verified seller-দের invite করছি। শীঘ্রই bidding শুরু হবে।',
        'bidding'     => 'Seller-রা bidding করছেন। Best price দেখতে enquiry খোলা রাখুন।',
        'best_found'  => 'একটি best price এসেছে এবং seller-রা এখনও দাম কমাতে পারেন। Timer শেষ হওয়া পর্যন্ত অপেক্ষা করুন বা এখনই এগিয়ে যান।',
        'offer_ready' => 'Bidding শেষ, best price lock হয়ে গেছে। Offer দেখে order দিতে enquiry খুলুন।',
        'closed'      => 'কোনো offer ছাড়াই bidding শেষ হয়েছে। নতুন enquiry দিন বা support-এর সাথে কথা বলুন, আমরা sourcing-এ সাহায্য করব।',
        'ordered'     => 'এই enquiry থেকে order তৈরি হয়েছে। আপনার order থেকে এটি track করুন।',
        'cancelled'   => 'এই enquiry বন্ধ। আপনি যেকোনো সময় নতুন enquiry দিতে পারেন।',
        'awaiting_offers' => 'আপনার enquiry seller-দের কাছে পাঠানো হয়েছে। Offer এলেই আমরা এখানে আপনাকে জানাব।',
        'offer_received'  => 'Seller-রা offer পাঠিয়েছেন। সেগুলি দেখে order দিতে enquiry খুলুন।',
        'team_contact'    => 'আমাদের team এই enquiry-র জন্য একজন seller ঠিক করেছে এবং deal confirm করতে আপনার সাথে যোগাযোগ করবে।',
    ],

];
