<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends OTP SMS through MSG91's flow API.
 *
 * Credentials come from config('services.msg91') — MSG91_AUTH_KEY and
 * MSG91_OTP_TEMPLATE_ID — not from source (they used to be hard-coded here).
 * When sending is off (any non-production environment by default) nothing is
 * sent and the OTP is written to the log, so local and test runs never text a
 * real number.
 */
class Msg91OtpService
{
    private const API_URL = 'https://control.msg91.com/api/v5/flow';

    /**
     * @param string $mobile with country code, e.g. 919876543210
     * @param array{type?: string, otp?: string|int} $vars
     */
    public static function sendSms($mobile, $vars = [])
    {
        $config = config('services.msg91');

        if (! ($config['send_sms'] ?? false)) {
            Log::info('MSG91 sending disabled; OTP not sent', ['mobile' => $mobile, 'otp' => $vars['otp'] ?? null]);

            return ['type' => 'logged'];
        }

        if (empty($config['auth_key']) || empty($config['otp_template_id'])) {
            Log::error('MSG91 is not configured: set MSG91_AUTH_KEY and MSG91_OTP_TEMPLATE_ID');

            return null;
        }

        try {
            $response = Http::withHeaders([
                'accept'       => 'application/json',
                'authkey'      => $config['auth_key'],
                'content-type' => 'application/json',
            ])->post(self::API_URL, self::buildPayload($config['otp_template_id'], $mobile, $vars));

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('MSG91 SMS API Error', [
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('MSG91 SMS Service Exception', [
                'message' => $e->getMessage(),
                'mobile'  => $mobile,
            ]);

            return null;
        }
    }

    private static function buildPayload(string $templateId, $mobile, array $vars = []): array
    {
        return [
            'template_id'      => $templateId,
            'short_url'        => '0',
            'realTimeResponse' => '1',
            'recipients'       => [
                [
                    'mobiles' => $mobile,
                    'type'    => $vars['type'] ?? 'Login',
                    'otp'     => (string) ($vars['otp'] ?? ''),
                ],
            ],
        ];
    }
}
