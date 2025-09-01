<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Msg91OtpService
{
    private static $apiUrl = 'https://control.msg91.com/api/v5/flow';
    private static $authKey = '465477AlEkKvjdS68b15084P1';
    private static $templateId = '68b1479a8bd2b60fd26c3e94';

    /**
     * Send SMS using MSG91 API
     *
     * @param string $mobile
     * @param array $vars
     * @return array|null
     */
    public static function sendSms($mobile, $vars = [])
    {
        try {
            $payload = self::buildPayload($mobile, $vars);

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'authkey' => self::$authKey,
                'content-type' => 'application/json'
            ])->post(self::$apiUrl, $payload);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('MSG91 SMS API Error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('MSG91 SMS Service Exception', [
                'message' => $e->getMessage(),
                'mobile' => $mobile
            ]);
            return null;
        }
    }

    /**
     * Build the payload for MSG91 API
     *
     * @param string $mobile
     * @param array $vars
     * @return array
     */
    private static function buildPayload($mobile, $vars = [])
    {
        return [
            'template_id' => self::$templateId,
            'short_url' => '0',
            'realTimeResponse' => '1',
            'recipients' => [
                [
                    'mobiles' => $mobile,
                    'type' => $vars['type'] ?? 'Login',
                    'otp' => $vars['otp'] ?? '1234'
                ]
            ]
        ];
    }
}
