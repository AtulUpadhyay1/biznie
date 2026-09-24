<?php

/*
| Biznie AI chat copy. Plain text only — the client never renders HTML.
| Keep the key set identical across en, hi, bn, mr, ta and te.
*/

return [

    'greeting_guest'     => "Namaste! I'm Biznie AI. I can help you check prices, raise an enquiry for the best quote, and track your orders.\nWhat would you like to do?",
    'greeting_user'      => 'Namaste :name! Good to see you.',
    'greeting_user_anon' => 'Namaste! Good to see you.',
    'summary_orders'     => '{1} You have 1 active order.|[2,*] You have :count active orders.',
    'summary_enquiries'  => '{1} 1 enquiry is live for bidding right now.|[2,*] :count enquiries are live for bidding right now.',
    'menu_prompt'        => 'What would you like to do?',

    'login_required' => "Please log in to continue — I'll pick up right where we left off.",
    'login_prompt'   => 'Log in to track your orders, follow live bidding on your enquiries and get personalised updates.',

    'menu' => [
        'track_order'    => 'Track my order',
        'my_enquiries'   => 'My enquiries',
        'create_enquiry' => 'Create enquiry',
        'check_prices'   => 'Check prices',
        'support'        => 'Talk to support',
        'callback'       => 'Request a callback',
        'login'          => 'Log in',
        'main_menu'      => 'Main menu',
        'all_orders'     => 'All orders',
        'all_enquiries'  => 'All enquiries',
        'show_more'      => 'Show more',
        'get_quote'      => 'Get quote: :name',
        'track_enquiry'  => 'Track this enquiry',
    ],

    'orders_active_intro' => 'Here are your active orders:',
    'orders_all_intro'    => 'Here are your recent orders:',
    'orders_none_active'  => 'You have no active orders right now.',
    'orders_none'         => "You haven't placed any orders yet. Raise an enquiry to get live quotes from verified sellers.",
    'order_found'         => "Here's the latest on order :ref:",
    'order_not_found'     => "I couldn't find that order in your account. Please check the order ID — it looks like OID-20260901-1234.",

    'enquiries_open_intro' => 'Here are your open enquiries:',
    'enquiries_all_intro'  => 'Here are your recent enquiries:',
    'enquiries_none_open'  => 'You have no open enquiries right now.',
    'enquiries_none'       => "You haven't raised any enquiries yet.",
    'enquiry_found'        => "Here's the latest on enquiry :ref:",
    'enquiry_not_found'    => "I couldn't find that enquiry in your account. Please check the ID — it looks like BZN-RFQ-260901-0001.",

    'enquiry_form_intro' => 'Check the details below and submit — your enquiry goes live to verified sellers instantly.',
    'enquiry_created'    => "Your enquiry :ref is live! Sellers are bidding now, and you'll see the best price as it comes in.",

    'products_prompt' => 'Type a product name (for example "TMT bar" or "HR coil") to check prices. Popular products:',
    'products_intro'  => 'Here\'s what I found for ":query". Tap "Get quote" for live prices from verified sellers.',
    'products_none'   => 'I couldn\'t find products matching ":query". Raise an enquiry and sellers will quote for it.',

    'lead_form_intro' => 'Share your details and our team will call you back.',
    'lead_created'    => 'Thank you! Our team will call you back shortly. Your reference is :ref.',

    'contact_intro' => 'You can reach the Biznie team here:',
    'credit_info'   => 'Biznie offers a credit wallet for eligible businesses, so you can buy now and pay later. You can apply from the Credit Request page in your dashboard. Our support team can help with eligibility and documents.',
    'thanks'        => "You're welcome! Anything else I can help with?",
    'fallback'      => 'Sorry, I didn\'t quite get that. Pick an option below, or try things like "track OID-20260901-1234", "my enquiries" or "TMT bar price".',

    'error_unknown_action' => "Sorry, I can't do that yet. Please choose an option below.",
    'error_in_progress'    => 'Your previous message is still being processed. Please try again in a moment.',
    'rate_limited'         => "You're sending messages too quickly. Please wait a moment and try again.",
    'rate_limited_writes'  => "You've submitted too many requests in the last hour. Please try again later or contact support.",
    'validation_one_of'    => 'Send either a message or an action.',
    'validation_payload'   => 'The action payload is too large.',
    'validation_encoding'  => 'The text contains characters that could not be read. Please retype it.',

    'order_stage' => [
        'placed'     => 'Order placed',
        'confirmed'  => 'Confirmed',
        'loading'    => 'Loading',
        'dispatched' => 'Dispatched',
        'delivered'  => 'Delivered',
        'completed'  => 'Completed',
        'cancelled'  => 'Cancelled',
    ],

    'enquiry_stage' => [
        'sent'        => 'Sent to sellers',
        'bidding'     => 'Bidding live',
        'best_found'  => 'Best price found',
        'offer_ready' => 'Offer ready',
        'closed'      => 'Closed',
        'ordered'     => 'Ordered',
        'cancelled'   => 'Cancelled',
    ],

    'enquiry_next_step' => [
        'sent'        => "We're inviting verified sellers now. Bidding will start shortly.",
        'bidding'     => 'Sellers are bidding. Keep the enquiry open to see the best price as it comes in.',
        'best_found'  => 'A best price is in and sellers can still undercut it. You can wait for the timer or proceed now.',
        'offer_ready' => 'Bidding has closed with a best price locked in. Open the enquiry to review the offer and place your order.',
        'closed'      => 'Bidding closed without an offer. Raise a new enquiry or talk to support and we will help you source it.',
        'ordered'     => 'This enquiry has been converted to an order. Track it from your orders.',
        'cancelled'   => 'This enquiry is closed. You can raise a new one anytime.',
        'awaiting_offers' => 'Your enquiry has been shared with sellers. We\'ll update you here as soon as an offer comes in.',
        'offer_received'  => 'Sellers have replied with offers. Open the enquiry to review them and place your order.',
        'team_contact'    => 'Our team has lined up a seller for this enquiry and will contact you to confirm the deal.',
    ],

];
