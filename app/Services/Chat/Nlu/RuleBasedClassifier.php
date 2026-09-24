<?php

namespace App\Services\Chat\Nlu;

use App\Services\Chat\ChatContext;

/**
 * The deterministic brain: multilingual keywords plus regex entities.
 *
 * It always runs and must be good enough on its own — the LLM layer is
 * optional. Dictionaries cover English, Hinglish / Hindi (Roman and
 * Devanagari), Bengali, Marathi, Tamil and Telugu. ASCII keywords match on
 * word boundaries; Indic keywords match as substrings, because suffixes attach
 * directly to the stem ("ऑर्डरची", "ஆர்டரை").
 */
class RuleBasedClassifier implements IntentClassifier
{
    /** A rule result at or above this is trusted without asking the LLM. */
    public const CONFIDENT = 0.8;

    private const ORDER_WORDS = [
        'order', 'orders', 'shipment', 'consignment', 'delivery', 'dispatch', 'truck', 'gaadi', 'gadi', 'vehicle',
        'ऑर्डर', 'आर्डर', 'डिलीवरी', 'गाड़ी', 'অর্ডার', 'ডেলিভারি', 'ஆர்டர்', 'டெலிவரி', 'ఆర్డర్', 'డెలివరీ',
    ];

    private const ENQUIRY_WORDS = [
        'enquiry', 'enquiries', 'inquiry', 'inquiries', 'enquiry\'s', 'rfq', 'rfqs', 'quote', 'quotes', 'quotation',
        'quotations', 'bid', 'bids', 'bidding', 'offer', 'offers',
        'एन्क्वायरी', 'इन्क्वायरी', 'पूछताछ', 'कोटेशन', 'बोली', 'এনকোয়ারি', 'কোটেশন', 'चौकशी',
        'விசாரணை', 'கொட்டேஷன்', 'ఎంక్వైరీ', 'కొటేషన్',
    ];

    private const STATUS_WORDS = [
        'where', 'when', 'status', 'track', 'tracking', 'update', 'updates', 'arrive', 'reach', 'reached', 'kahan', 'kaha',
        'kahaan', 'kab', 'kya hua', 'aayega', 'aaega', 'ayega', 'milega', 'pahuncha', 'pahunchega', 'pohcha', 'dispatched',
        'कहाँ', 'कहां', 'कब', 'स्टेटस', 'स्थिति', 'কোথায়', 'কবে', 'স্ট্যাটাস', 'कुठे', 'कधी',
        'எங்கே', 'எப்போது', 'நிலை', 'ஸ்டேட்டஸ்', 'ఎక్కడ', 'ఎప్పుడు', 'స్టేటస్',
    ];

    private const LIST_WORDS = [
        'my', 'all', 'list', 'show', 'recent', 'previous', 'past', 'history', 'mera', 'mere', 'meri', 'dikhao', 'dikhaiye',
        'मेरा', 'मेरे', 'मेरी', 'सभी', 'সব', 'আমার', 'माझे', 'माझी', 'माझा', 'सर्व', 'என்', 'எனது', 'அனைத்து', 'నా', 'అన్ని',
    ];

    /** "Where is my stuff" phrasing that implies an order without saying so. */
    private const TRACK_WORDS = [
        'track', 'tracking', 'kab aayega', 'kab aaega', 'kab milega', 'kab tak aayega', 'delivery kab',
        'ट्रैक', 'कब आएगा', 'कब मिलेगा', 'ট্র্যাক', 'কবে আসবে', 'ट्रॅक', 'कधी येईल', 'ட்ராக்', 'எப்போது வரும்',
        'ట్రాక్', 'ఎప్పుడు వస్తుంది',
    ];

    private const CREATE_STRONG = [
        'new enquiry', 'create enquiry', 'raise enquiry', 'send enquiry', 'new inquiry', 'create inquiry', 'new rfq',
        'create rfq', 'raise rfq', 'raise an rfq', 'post requirement', 'post my requirement', 'get quote', 'get a quote',
        'get quotes', 'quote chahiye', 'quotation chahiye', 'rate chahiye', 'enquiry banao', 'enquiry karni',
        'नई एन्क्वायरी', 'कोटेशन चाहिए', 'নতুন এনকোয়ারি', 'নতুন কোটেশন', 'नवीन चौकशी', 'புதிய விசாரணை', 'కొత్త ఎంక్వైరీ',
    ];

    private const CREATE_WORDS = [
        'need', 'needed', 'want', 'require', 'required', 'requirement', 'buy', 'purchase', 'procure', 'chahiye', 'chaiye',
        'chahie', 'kharidna', 'kharid', 'lena hai', 'mangwana',
        'चाहिए', 'खरीदना', 'खरीद', 'দরকার', 'লাগবে', 'কিনতে', 'हवे', 'हवा', 'पाहिजे', 'खरेदी', 'வேண்டும்', 'வாங்க',
        'కావాలి', 'కొనాలి',
    ];

    private const PRICE_WORDS = [
        'price', 'prices', 'rate', 'rates', 'cost', 'pricing', 'bhav', 'bhaav', 'bhao', 'daam', 'dam', 'kimat', 'keemat',
        'qimat', 'bhav kya', 'rate kya',
        'भाव', 'रेट', 'दाम', 'कीमत', 'মূল্য', 'দাম', 'দর', 'রেট', 'किंमत', 'दर', 'விலை', 'ரேட்', 'ధర', 'రేట్',
    ];

    private const SUPPORT_STRONG = [
        'support', 'customer care', 'customer support', 'contact', 'contact us', 'call me', 'talk to', 'speak to',
        'helpline', 'phone number', 'whatsapp', 'email', 'human', 'agent', 'executive', 'baat karni', 'baat karna',
        'सपोर्ट', 'संपर्क', 'बात करनी', 'সাপোর্ট', 'যোগাযোগ', 'संपर्क साधा', 'ஆதரவு', 'தொடர்பு', 'సపోర్ట్', 'సంప్రదించ',
    ];

    private const SUPPORT_WEAK = [
        'help', 'madad', 'sahayata', 'मदद', 'सहायता', 'সাহায্য', 'मदत', 'உதவி', 'సహాయం',
    ];

    private const CREDIT_WORDS = [
        'credit', 'credit limit', 'credit wallet', 'udhar', 'udhaar', 'pay later', 'emi', 'loan', 'finance',
        'उधार', 'क्रेडिट', 'ক্রেডিট', 'ধার', 'উধার', 'கிரெடிட்', 'கடன்', 'క్రెడిట్', 'అప్పు',
    ];

    private const GREETING_WORDS = [
        'hi', 'hii', 'hiii', 'hello', 'helo', 'hey', 'namaste', 'namaskar', 'namaskaram', 'vanakkam', 'good morning',
        'good afternoon', 'good evening', 'नमस्ते', 'नमस्कार', 'নমস্কার', 'হ্যালো', 'வணக்கம்', 'నమస్కారం', 'హలో',
    ];

    private const THANKS_WORDS = [
        'thanks', 'thank you', 'thankyou', 'thx', 'dhanyavad', 'dhanyawad', 'shukriya', 'धन्यवाद', 'शुक्रिया',
        'ধন্যবাদ', 'आभार', 'நன்றி', 'ధన్యవాదాలు', 'ధన్యవాదం',
    ];

    private const MENU_WORDS = [
        'menu', 'main menu', 'options', 'start over', 'restart', 'what can you do', 'मेनू', 'মেনু', 'मेन्यू', 'மெனு', 'మెనూ',
    ];

    /** Unit spellings (regex fragments, longest first) → the label we prefill. */
    private const UNITS = [
        'metric\s*tonnes?' => 'MT',
        'metric\s*tons?'   => 'MT',
        'mts'              => 'MT',
        'mt'               => 'MT',
        'tonnes?'          => 'MT',
        'tons?'            => 'MT',
        'टन'               => 'MT',
        'টন'               => 'MT',
        'டன்'              => 'MT',
        'టన్నులు'          => 'MT',
        'టన్ను'            => 'MT',
        'quintals?'        => 'Quintal',
        'qtls?'            => 'Quintal',
        'क्विंटल'           => 'Quintal',
        'kgs?'             => 'Kg',
        'kilograms?'       => 'Kg',
        'kilos?'           => 'Kg',
        'किलो'             => 'Kg',
        'কেজি'             => 'Kg',
        'கிலோ'             => 'Kg',
        'కిలో'             => 'Kg',
        'pcs'              => 'Pcs',
        'pieces?'          => 'Pcs',
        'nos'              => 'Nos',
        'bags?'            => 'Bags',
        'bundles?'         => 'Bundles',
        'coils?'           => 'Coils',
        'sheets?'          => 'Sheets',
        'rolls?'           => 'Rolls',
        'litres?'          => 'Litre',
        'liters?'          => 'Litre',
        'ltrs?'            => 'Litre',
        'metres?'          => 'Meter',
        'meters?'          => 'Meter',
        'mtrs?'            => 'Meter',
        'feet'             => 'Feet',
        'ft'               => 'Feet',
    ];

    /** Checked in this order, so "2 weeks" lands on 15 days before "week" can claim it. */
    private const REQUIRED_BY = [
        'Within 15 days' => [
            '15 days', '15 day', 'fifteen days', '15 din', 'pandrah din', '2 weeks', 'two weeks', '15 दिन', 'पंद्रह दिन',
            '15 দিন', '১৫ দিন', '15 दिवस', '15 நாள்', '15 நாட்கள்', '15 రోజుల', '15 రోజులు',
        ],
        'Within 3 days' => [
            '3 days', '3 day', 'three days', 'teen din', '3 din', '3 दिन', 'तीन दिन', '3 দিন', '৩ দিন', 'তিন দিন',
            '3 दिवस', 'तीन दिवस', '3 நாள்', '3 நாட்கள்', '3 రోజుల', '3 రోజులు',
        ],
        'Within a month' => [
            'month', 'a month', '1 month', 'one month', 'mahina', 'mahine', 'महीना', 'महीने', 'মাস', 'महिना', 'महिन्यात',
            'மாதம்', 'నెల',
        ],
        'Within 1 week' => [
            'week', '1 week', 'one week', 'a week', '7 days', 'hafta', 'hafte', 'saptah', 'हफ्ते', 'हफ्ता', 'सप्ताह',
            'সপ্তাহ', 'आठवडा', 'आठवड्यात', 'வாரம்', 'వారం',
        ],
        'As soon as possible' => [
            'asap', 'urgent', 'urgently', 'immediately', 'immediate', 'jaldi', 'turant', 'as soon as possible',
            'right away', 'तुरंत', 'जल्दी', 'অবিলম্বে', 'তাড়াতাড়ি', 'জরুরি', 'लवकर', 'तातडीने', 'உடனே', 'அவசரம்',
            'వెంటనే', 'అత్యవసరం',
        ],
    ];

    /** Delivery cities we can recognise without capitalisation cues (multi-word first). */
    private const CITIES = [
        'new delhi' => 'New Delhi', 'navi mumbai' => 'Navi Mumbai', 'greater noida' => 'Greater Noida',
        'mumbai' => 'Mumbai', 'delhi' => 'Delhi', 'pune' => 'Pune', 'bangalore' => 'Bengaluru', 'bengaluru' => 'Bengaluru',
        'hyderabad' => 'Hyderabad', 'chennai' => 'Chennai', 'kolkata' => 'Kolkata', 'calcutta' => 'Kolkata',
        'ahmedabad' => 'Ahmedabad', 'surat' => 'Surat', 'jaipur' => 'Jaipur', 'lucknow' => 'Lucknow', 'kanpur' => 'Kanpur',
        'nagpur' => 'Nagpur', 'indore' => 'Indore', 'thane' => 'Thane', 'bhopal' => 'Bhopal',
        'visakhapatnam' => 'Visakhapatnam', 'vizag' => 'Visakhapatnam', 'patna' => 'Patna', 'vadodara' => 'Vadodara',
        'baroda' => 'Vadodara', 'ghaziabad' => 'Ghaziabad', 'ludhiana' => 'Ludhiana', 'agra' => 'Agra', 'nashik' => 'Nashik',
        'faridabad' => 'Faridabad', 'meerut' => 'Meerut', 'rajkot' => 'Rajkot', 'varanasi' => 'Varanasi',
        'srinagar' => 'Srinagar', 'aurangabad' => 'Aurangabad', 'dhanbad' => 'Dhanbad', 'amritsar' => 'Amritsar',
        'allahabad' => 'Prayagraj', 'prayagraj' => 'Prayagraj', 'ranchi' => 'Ranchi', 'howrah' => 'Howrah',
        'coimbatore' => 'Coimbatore', 'jabalpur' => 'Jabalpur', 'gwalior' => 'Gwalior', 'vijayawada' => 'Vijayawada',
        'jodhpur' => 'Jodhpur', 'madurai' => 'Madurai', 'raipur' => 'Raipur', 'kota' => 'Kota', 'guwahati' => 'Guwahati',
        'chandigarh' => 'Chandigarh', 'solapur' => 'Solapur', 'hubli' => 'Hubballi', 'bareilly' => 'Bareilly',
        'moradabad' => 'Moradabad', 'mysore' => 'Mysuru', 'mysuru' => 'Mysuru', 'gurgaon' => 'Gurugram',
        'gurugram' => 'Gurugram', 'noida' => 'Noida', 'aligarh' => 'Aligarh', 'jalandhar' => 'Jalandhar',
        'bhubaneswar' => 'Bhubaneswar', 'salem' => 'Salem', 'warangal' => 'Warangal', 'guntur' => 'Guntur',
        'bhiwandi' => 'Bhiwandi', 'gorakhpur' => 'Gorakhpur', 'bikaner' => 'Bikaner', 'jamshedpur' => 'Jamshedpur',
        'bhilai' => 'Bhilai', 'cuttack' => 'Cuttack', 'kochi' => 'Kochi', 'cochin' => 'Kochi', 'dehradun' => 'Dehradun',
        'durgapur' => 'Durgapur', 'asansol' => 'Asansol', 'kolhapur' => 'Kolhapur', 'ajmer' => 'Ajmer', 'jammu' => 'Jammu',
        'udaipur' => 'Udaipur', 'siliguri' => 'Siliguri', 'rourkela' => 'Rourkela', 'belgaum' => 'Belagavi',
        'mangalore' => 'Mangaluru', 'tirupati' => 'Tirupati', 'trichy' => 'Tiruchirappalli', 'nellore' => 'Nellore',
        'kozhikode' => 'Kozhikode', 'trivandrum' => 'Thiruvananthapuram', 'thiruvananthapuram' => 'Thiruvananthapuram',
        'bokaro' => 'Bokaro', 'bilaspur' => 'Bilaspur', 'korba' => 'Korba', 'raigarh' => 'Raigarh', 'durg' => 'Durg',
        'sambalpur' => 'Sambalpur', 'jharsuguda' => 'Jharsuguda', 'haridwar' => 'Haridwar', 'rudrapur' => 'Rudrapur',
        'panipat' => 'Panipat', 'sonipat' => 'Sonipat', 'karnal' => 'Karnal', 'hisar' => 'Hisar', 'rohtak' => 'Rohtak',
        'bhiwadi' => 'Bhiwadi', 'vapi' => 'Vapi', 'silvassa' => 'Silvassa', 'goa' => 'Goa', 'hosur' => 'Hosur',
        'puducherry' => 'Puducherry', 'pondicherry' => 'Puducherry', 'jalna' => 'Jalna', 'gandhidham' => 'Gandhidham',
        'दिल्ली' => 'Delhi', 'मुंबई' => 'Mumbai', 'पुणे' => 'Pune', 'कोलकाता' => 'Kolkata', 'चेन्नई' => 'Chennai',
        'हैदराबाद' => 'Hyderabad', 'बेंगलुरु' => 'Bengaluru', 'अहमदाबाद' => 'Ahmedabad', 'जयपुर' => 'Jaipur',
        'लखनऊ' => 'Lucknow', 'कानपुर' => 'Kanpur', 'नागपुर' => 'Nagpur', 'इंदौर' => 'Indore', 'भोपाल' => 'Bhopal',
        'पटना' => 'Patna', 'वाराणसी' => 'Varanasi', 'रायपुर' => 'Raipur', 'रांची' => 'Ranchi', 'कलकत्ता' => 'Kolkata',
        'কলকাতা' => 'Kolkata', 'হাওড়া' => 'Howrah', 'শিলিগুড়ি' => 'Siliguri', 'दुर्गापुर' => 'Durgapur',
        'சென்னை' => 'Chennai', 'கோயம்புத்தூர்' => 'Coimbatore', 'மதுரை' => 'Madurai',
        'హైదరాబాద్' => 'Hyderabad', 'విజయవాడ' => 'Vijayawada', 'విశాఖపట్నం' => 'Visakhapatnam',
    ];

    /** Capitalised words that are never a delivery city, whatever cue precedes them. */
    private const NOT_CITIES = [
        'january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november',
        'december', 'jan', 'feb', 'mar', 'apr', 'jun', 'jul', 'aug', 'sep', 'sept', 'oct', 'nov', 'dec',
        'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'mon', 'tue', 'tues', 'wed',
        'thu', 'thur', 'thurs', 'fri', 'sat', 'sun', 'today', 'tomorrow', 'tonight', 'morning', 'evening', 'night',
        'week', 'weekend', 'month', 'year', 'time', 'next', 'this', 'that', 'the', 'our', 'your', 'same', 'site',
        'sales', 'support', 'buy', 'order', 'orders', 'enquiry', 'enquiries', 'rfq', 'quote', 'price', 'rate', 'team',
        'office', 'factory', 'warehouse', 'godown', 'plant', 'shop', 'store', 'stock', 'bulk', 'cash', 'credit',
        'payment', 'advance', 'delivery', 'customer', 'care', 'help', 'account', 'biznie', 'please', 'address', 'home',
        'mujhe', 'humein', 'hamein', 'isme', 'usme', 'jaldi', 'abhi', 'kal', 'aaj',
    ];

    /** Filler that never belongs in a product search. */
    private const STOPWORDS = [
        'i', 'me', 'we', 'us', 'our', 'you', 'u', 'a', 'an', 'the', 'of', 'for', 'in', 'to', 'at', 'on', 'with', 'and',
        'or', 'is', 'are', 'am', 'be', 'it', 'this', 'that', 'some', 'any', 'what', 'whats', 'what\'s', 'how', 'much',
        'many', 'today', 'todays', 'today\'s', 'current', 'latest', 'live', 'best', 'lowest', 'cheapest', 'market', 'please',
        'pls', 'plz', 'kindly', 'can', 'could', 'would', 'do', 'does', 'have', 'has', 'get', 'give', 'send', 'check',
        'tell', 'about', 'per', 'by', 'within', 'from', 'days', 'day', 'weeks', 'sir', 'ji', 'bhai', 'hai', 'hain', 'h',
        'ka', 'ki', 'ke', 'ko', 'kya', 'kitna', 'kitne', 'kitni', 'mein', 'me', 'mai', 'main', 'se', 'tak', 'aaj', 'aj',
        'abhi', 'din', 'mujhe', 'muje', 'mujhko', 'hame', 'humein', 'hamein', 'hume', 'bata', 'batao', 'bataiye', 'batayein',
        'dijiye', 'dena', 'lena', 'wala', 'wali', 'wale', 'chahta', 'chahti', 'hun', 'hoon', 'ho', 'tha', 'new', 'create',
        'raise', 'deliver', 'delivered', 'deliveries', 'kaise', 'kaisa', 'kaun', 'kab', 'kyun', 'hoga', 'hogi',
        'milega', 'milegi', 'ho', 'gaya', 'gya', 'something', 'anything', 'everything', 'thing', 'things', 'stuff',
        'someone', 'somebody', 'anyone', 'kuch', 'kuchh',
        'मुझे', 'का', 'की', 'के', 'को', 'क्या', 'है', 'हैं', 'आज', 'में', 'तक', 'से', 'कितना', 'आमार', 'এর', 'কত', 'আজ',
        'কি', 'मला', 'आहे', 'चा', 'ची', 'चे', 'आज', 'काय', 'எனக்கு', 'என்ன', 'இன்று', 'ஒரு', 'నాకు', 'ఎంత',
        'ఈరోజు', 'ఏమిటి', 'ఉంది', 'உள்ளது', 'আছে', 'कैसे', 'কীভাবে', 'कसे',
    ];

    public function classify(string $text, ChatContext $ctx, array $history = []): NluResult
    {
        return $this->analyse($text);
    }

    /** Context-free analysis of one line of text. */
    public function analyse(string $text): NluResult
    {
        $text = trim($text);
        if ($text === '') {
            return NluResult::fallback();
        }

        $entities = $this->extractEntities($text);
        $haystack = $this->normalise($text);
        $words    = count(preg_split('/\s+/u', $haystack, -1, PREG_SPLIT_NO_EMPTY));

        $scores = [];
        $score  = function (string $intent, float $value) use (&$scores): void {
            $scores[$intent] = max($scores[$intent] ?? 0.0, $value);
        };

        if (! empty($entities['order_ref'])) {
            $score('order.status', 1.0);
        }
        if (! empty($entities['enquiry_ref'])) {
            $score('enquiry.status', 1.0);
        }

        $asksStatus = $this->has($haystack, self::STATUS_WORDS);
        $asksList   = $this->has($haystack, self::LIST_WORDS);

        if ($this->has($haystack, self::ORDER_WORDS)) {
            $asksStatus
                ? $score('order.status', 0.9)
                : $score('order.list', $asksList ? 0.85 : 0.6);
        } elseif ($this->has($haystack, self::TRACK_WORDS)) {
            $score('order.status', 0.85);
        }

        if ($this->has($haystack, self::ENQUIRY_WORDS)) {
            $asksStatus
                ? $score('enquiry.status', 0.9)
                : $score('enquiry.list', $asksList ? 0.85 : 0.65);
        }

        $hasQuantity   = isset($entities['quantity']);
        $hasProduct    = ! empty($entities['product_query']);
        $supportStrong = $this->has($haystack, self::SUPPORT_STRONG);
        $supportWeak   = ! $supportStrong && $this->has($haystack, self::SUPPORT_WEAK);

        if ($this->has($haystack, self::CREATE_STRONG)) {
            $score('enquiry.create', 0.92);
        } elseif ($this->has($haystack, self::CREATE_WORDS)) {
            // "need" / "want" / "chahiye" on their own say little: only a
            // quantity or a product makes them a requirement. Without a
            // quantity, any ask for help or a person ("I need help", "want
            // to talk to someone") is a support question, not an RFQ.
            if ($hasQuantity) {
                $score('enquiry.create', 0.93);
            } elseif (! $supportStrong && ! $supportWeak) {
                $score('enquiry.create', $hasProduct ? 0.85 : 0.55);
            }
        } elseif ($hasQuantity && $hasProduct) {
            // "20 mt tmt pune" is a requirement even without a verb.
            $score('enquiry.create', 0.8);
        }

        if ($this->has($haystack, self::PRICE_WORDS)) {
            $score('product.search', 0.85);
        }

        if ($supportStrong) {
            $score('support.contact', 0.88);
        } elseif ($supportWeak) {
            $score('support.contact', 0.6);
        }

        if ($this->has($haystack, self::CREDIT_WORDS)) {
            $score('credit.info', 0.88);
        }

        if ($this->has($haystack, self::MENU_WORDS)) {
            $score('menu', 0.9);
        }

        // Pleasantries only win when nothing substantive was asked, so
        // "hi, where is my order" is an order question.
        if (($scores === [] || max($scores) < 0.6)) {
            if ($this->has($haystack, self::THANKS_WORDS)) {
                $score('thanks', $words <= 6 ? 0.9 : 0.6);
            } elseif ($this->has($haystack, self::GREETING_WORDS)) {
                $score('greeting', $words <= 4 ? 0.9 : 0.6);
            }
        }

        if ($scores === []) {
            return new NluResult('fallback', 0.0, $entities);
        }

        $intent = $this->pick($scores);
        if ($scores[$intent] < 0.5) {
            return new NluResult('fallback', $scores[$intent], $entities);
        }

        return new NluResult($intent, $scores[$intent], $entities);
    }

    /** Highest score wins; ties go to the more specific intent. */
    private function pick(array $scores): string
    {
        $priority = [
            'order.status', 'enquiry.status', 'order.list', 'enquiry.list', 'product.search', 'enquiry.create',
            'credit.info', 'support.contact', 'menu', 'thanks', 'greeting',
        ];

        $best = max($scores);
        foreach ($priority as $intent) {
            if (isset($scores[$intent]) && $scores[$intent] === $best) {
                return $intent;
            }
        }

        return (string) array_key_first($scores);
    }

    /* ------------------------------------------------------------------ */
    /* Entities                                                            */
    /* ------------------------------------------------------------------ */

    /** @return array<string, mixed> */
    public function extractEntities(string $text): array
    {
        $entities = [];

        if (preg_match(NluResult::ORDER_REF_REGEX, $text, $m)) {
            $entities['order_ref'] = strtoupper($m[0]);
        }
        if (preg_match(NluResult::ENQUIRY_REF_REGEX, $text, $m)) {
            $entities['enquiry_ref'] = strtoupper($m[0]);
        }

        // "1,000 kg" / "1,00,000 kg" → "1000 kg" / "100000 kg". On the raw
        // text: normalise() turns the comma into a space, after which the
        // number would read as "1" and "000".
        $text = preg_replace('/(?<=\d),(?=(?:\d{2},)*\d{3}(?!\d))/u', '', $text) ?? $text;

        $haystack = $this->normalise($text);
        // References are digits too; take them out before looking for quantities.
        $haystack = preg_replace([NluResult::ORDER_REF_REGEX, NluResult::ENQUIRY_REF_REGEX], ' ', $haystack);

        $unitPattern = '/(\d+(?:\.\d+)?)\s*(' . implode('|', array_keys(self::UNITS)) . ')(?![\p{L}\p{M}])/u';
        if (preg_match($unitPattern, $haystack, $m)) {
            $quantity = (float) $m[1];
            if ($quantity > 0 && $quantity < 10_000_000) {
                $entities['quantity'] = $quantity;
                $entities['unit']     = $this->canonicalUnit($m[2]);
            }
            $haystack = str_replace($m[0], ' ', $haystack);
        }

        foreach (self::REQUIRED_BY as $option => $phrases) {
            if ($this->has($haystack, $phrases)) {
                $entities['required_by'] = $option;
                break;
            }
        }

        foreach (self::CITIES as $needle => $city) {
            if ($this->matches($haystack, $needle)) {
                $entities['delivery_city'] = $city;
                $haystack = $this->remove($haystack, $needle);
                break;
            }
        }
        if (! isset($entities['delivery_city']) && ($city = $this->cityFromCue($text)) !== null) {
            $entities['delivery_city'] = $city;
            $haystack = $this->remove($haystack, mb_strtolower($city));
        }

        $productQuery = $this->productQuery($haystack);
        if ($productQuery !== null) {
            $entities['product_query'] = $productQuery;
        }

        return $entities;
    }

    /**
     * A city the dictionary does not know, read off a capitalised word — but
     * only right after a delivery cue ("deliver to X", "ship to X") or before
     * the Hinglish postposition ("X me / mein"). A bare "to / in / at X" is
     * far too loose: "talk to Sales", "in March".
     */
    private function cityFromCue(string $text): ?string
    {
        $name     = '([A-Z][a-z]{2,}(?:\s+[A-Z][a-z]{2,})?)';
        $patterns = [
            '/(?i:\b(?:deliver(?:y|ed|ing)?|ship(?:ped|ping|ment)?)\s+(?:it\s+)?(?:to|at|in))\s+' . $name . '\b/u',
            '/\b' . $name . '\s+(?i:me|mein)\b/u',
        ];

        foreach ($patterns as $pattern) {
            if (! preg_match($pattern, $text, $m)) {
                continue;
            }

            // Keep leading words up to the first one that is not a place.
            $words = [];
            foreach (preg_split('/\s+/u', $m[1]) as $word) {
                if (in_array(mb_strtolower($word), self::NOT_CITIES, true)) {
                    break;
                }
                $words[] = $word;
            }

            if ($words !== []) {
                return implode(' ', $words);
            }
        }

        return null;
    }

    private function canonicalUnit(string $raw): string
    {
        foreach (self::UNITS as $pattern => $label) {
            if (preg_match('/^(?:' . $pattern . ')$/u', $raw)) {
                return $label;
            }
        }

        return strtoupper($raw);
    }

    /** Whatever is left once every keyword, stopword and number is removed. */
    private function productQuery(string $haystack): ?string
    {
        $vocabulary = array_merge(
            self::ORDER_WORDS, self::ENQUIRY_WORDS, self::STATUS_WORDS, self::LIST_WORDS, self::TRACK_WORDS,
            self::CREATE_STRONG, self::CREATE_WORDS, self::PRICE_WORDS, self::SUPPORT_STRONG, self::SUPPORT_WEAK,
            self::CREDIT_WORDS, self::GREETING_WORDS, self::THANKS_WORDS, self::MENU_WORDS,
            ...array_values(self::REQUIRED_BY),
        );

        // Longest phrases first, so "as soon as possible" goes before "as".
        usort($vocabulary, fn (string $a, string $b) => mb_strlen($b) <=> mb_strlen($a));
        foreach ($vocabulary as $phrase) {
            $haystack = $this->remove($haystack, $phrase);
        }

        $stop   = array_flip(self::STOPWORDS);
        $tokens = array_filter(
            preg_split('/\s+/u', $haystack, -1, PREG_SPLIT_NO_EMPTY),
            fn (string $t) => ! isset($stop[$t])
                && ! preg_match('/^[\d.\-]+$/', $t)
                && mb_strlen($t) >= 2
        );

        $query = trim(mb_substr(implode(' ', $tokens), 0, 80));

        return $query === '' ? null : $query;
    }

    /* ------------------------------------------------------------------ */
    /* Matching                                                            */
    /* ------------------------------------------------------------------ */

    /** Lower-cased, with punctuation (but not Indic vowel signs) turned into spaces. */
    private function normalise(string $text): string
    {
        $lower = mb_strtolower($text);
        $lower = preg_replace('/[^\p{L}\p{M}\p{N}\s\-\.\']+/u', ' ', $lower);
        $lower = preg_replace('/(?<!\d)\.|\.(?!\d)/u', ' ', $lower);

        return trim(preg_replace('/\s+/u', ' ', $lower));
    }

    private function has(string $haystack, array $words): bool
    {
        foreach ($words as $word) {
            if ($this->matches($haystack, $word)) {
                return true;
            }
        }

        return false;
    }

    private function matches(string $haystack, string $word): bool
    {
        if ($this->isAscii($word)) {
            return (bool) preg_match($this->boundaryPattern($word), $haystack);
        }

        return mb_strpos($haystack, $word) !== false;
    }

    private function remove(string $haystack, string $word): string
    {
        $result = $this->isAscii($word)
            ? preg_replace($this->boundaryPattern($word), ' ', $haystack)
            : str_replace($word, ' ', $haystack);

        return trim(preg_replace('/\s+/u', ' ', (string) $result));
    }

    private function boundaryPattern(string $word): string
    {
        return '/(?<![\p{L}\p{N}])' . preg_quote($word, '/') . '(?![\p{L}\p{N}])/u';
    }

    private function isAscii(string $word): bool
    {
        return (bool) preg_match('/^[\x20-\x7e]+$/', $word);
    }
}
