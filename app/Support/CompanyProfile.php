<?php

namespace App\Support;

use App\Models\WebsiteSetup;

/**
 * Biznie's own contact and legal identity, read from the one settings table
 * (`website_setups`, edited under Admin → Website Setup → General).
 *
 * The footer, contact page, logistics page, FAQ, chat assistant and the policy
 * pages used to carry their own copies — a Delhi number in one place, a Gmail
 * address and a different Varanasi address inside the Terms (B21/B22). Every
 * surface now reads from here; policy HTML refers to these values through
 * tokens such as {{company_email}} so the text can never drift from the
 * settings again.
 */
final class CompanyProfile
{
    /** Tokens a policy page may contain, mapped to their settings key. */
    public const TOKENS = [
        '{{company_name}}'    => 'legal_name',
        '{{company_email}}'   => 'email',
        '{{company_phone}}'   => 'phone1',
        '{{company_address}}' => 'address',
        '{{company_gstin}}'   => 'company_gstin',
        '{{company_llpin}}'   => 'company_llpin',
        '{{grievance_officer}}' => 'grievance_officer_name',
        '{{grievance_email}}' => 'grievance_officer_email',
        '{{grievance_phone}}' => 'grievance_officer_phone',
        '{{jurisdiction}}'    => 'jurisdiction',
        // The one booking / cancellation rule (4.1): Terms, Return Policy and
        // checkout all read this, so they cannot disagree again.
        '{{booking_policy}}'  => 'booking_policy',
    ];

    /** Used only when a key has never been saved. */
    private const DEFAULTS = [
        'legal_name'   => 'Biznie TradeTech LLP',
        'jurisdiction' => 'Varanasi, Uttar Pradesh, India',
        'grievance_response_time' => 'within 48 hours',
        // Mirrors the published Terms until Admin -> General says otherwise.
        'booking_policy' => 'The booking (token) amount confirms your order with the seller and is non-refundable. If you cancel after booking, the booking amount is forfeited.',
    ];

    /** @var array<string, string>|null */
    private static ?array $cache = null;

    public static function get(string $key): string
    {
        self::$cache ??= WebsiteSetup::pluck('value', 'key')
            ->map(fn ($v) => trim((string) $v))
            ->all();

        $value = self::$cache[$key] ?? '';

        return $value !== '' ? $value : (self::DEFAULTS[$key] ?? '');
    }

    /** Forget the per-request cache (after the admin saves settings). */
    public static function flush(): void
    {
        self::$cache = null;
    }

    /** Address on one line, for policy text and notifications. */
    public static function addressLine(): string
    {
        return trim(preg_replace('/\s*\R\s*/', ', ', self::get('address')) ?? '');
    }

    /**
     * A social link is shown only when it points at a real Biznie profile —
     * not an empty value, a bare domain (`https://www.youtube.com/`) or a
     * login screen (`https://x.com/i/flow/login`), which is what the footer
     * linked to (B04).
     */
    public static function socialUrl(string $key): ?string
    {
        $url = self::get($key);
        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        if ($path === '' || preg_match('#(^|/)(login|signin|sign-in|flow)(/|$)#i', $path)) {
            return null;
        }

        return $url;
    }

    /** Replaces {{company_*}} tokens in admin-authored HTML. */
    public static function render(?string $html): ?string
    {
        if ($html === null || $html === '' || ! str_contains($html, '{{')) {
            return $html;
        }

        $replacements = [];
        foreach (self::TOKENS as $token => $key) {
            $replacements[$token] = e($key === 'address' ? self::addressLine() : self::get($key));
        }

        return strtr($html, $replacements);
    }
}
