<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * B21/B22: the Terms and Privacy pages carried their own contact block — a
 * Gmail address, a phone number and an address spelt "chetganj" that matched
 * nothing else on the site — plus an unfilled "[Varanasi, India]" placeholder.
 *
 * The block is replaced with {{company_*}} tokens that ConfigApiController
 * fills from Website Setup → General, so the policies always show the same
 * phone, email and address as the footer and contact page.
 *
 * The original HTML is kept under `<key>__backup_b21` and restored on rollback.
 */
return new class extends Migration
{
    private const KEYS = ['terms_condition', 'privacy_policy', 'returns_policy', 'logistics', 'payment_method', 'about_us'];

    public function up(): void
    {
        foreach (self::KEYS as $key) {
            $row = DB::table('website_setups')->where('key', $key)->first();
            if (! $row || ! is_string($row->value) || $row->value === '') {
                continue;
            }

            $html = $row->value;
            $fixed = $this->tokenise($html);
            if ($fixed === $html) {
                continue;
            }

            DB::table('website_setups')->updateOrInsert(
                ['key' => $key . '__backup_b21'],
                ['value' => $html, 'updated_at' => now(), 'created_at' => now()]
            );
            DB::table('website_setups')->where('key', $key)->update(['value' => $fixed, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        foreach (self::KEYS as $key) {
            $backup = DB::table('website_setups')->where('key', $key . '__backup_b21')->first();
            if ($backup) {
                DB::table('website_setups')->where('key', $key)->update(['value' => $backup->value]);
                DB::table('website_setups')->where('key', $key . '__backup_b21')->delete();
            }
        }
    }

    private function tokenise(string $html): string
    {
        // Any personal-mail contact address, not just the one seen in QA.
        $html = preg_replace('/[A-Za-z0-9._%+-]+@(gmail|yahoo|hotmail|outlook|rediffmail)\.com/i', '{{company_email}}', $html);

        // "C27/277 A-1 M, Maldahiya, chetganj, Varanasi, India – 221002" in its
        // plain, &ndash; and en-dash spellings.
        $html = preg_replace(
            '/C\s*27\s*\/\s*277\s*A-1\s*M,?\s*(?:Mandal\s+Plaza,?\s*)?Maldahiya,?\s*(?:chetganj|jagatganj),?\s*Varanasi,?\s*(?:\(U\.?P\.?\)\s*)?India\s*(?:&ndash;|–|-)?\s*221002/i',
            '{{company_address}}',
            $html
        );

        // "Phone: 9807489290" / "Phone: +91 98074 89290" next to the block.
        $html = preg_replace('/(Phone|Mobile|Contact(?: No\.?)?)\s*:\s*(?:\+?91[\s-]*)?[6-9](?:[\s-]?\d){9}/i', '$1: {{company_phone}}', $html);

        $html = str_replace('[Varanasi, India]', '{{jurisdiction}}', $html);
        $html = preg_replace('/\bchetganj\b/i', 'Jagatganj', $html);

        return $html;
    }
};
