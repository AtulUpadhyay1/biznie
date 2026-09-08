<?php
namespace App\Firebase;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FireBaseManager
{
    /** How long a minted Google access token is reused for (it lives an hour). */
    private const TOKEN_TTL = 3300;

    private static function getGoogleAccessToken(): ?string
    {
        // One token per run of sends instead of one per recipient: minting it is
        // a network round trip to Google for every admin in the loop otherwise.
        $cached = Cache::get('firebase.access_token');
        if ($cached) {
            return $cached;
        }

        $credentialsFilePath = 'biznie-60aff-firebase-adminsdk-fbsvc-68588dd59e.json'; //replace this with your actual path and file name
        $client = new \Google_Client();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        $accessToken = $token['access_token'] ?? null;

        // Only a real token is worth keeping — caching a null would sit on the
        // failure for the whole TTL.
        if ($accessToken) {
            Cache::put('firebase.access_token', $accessToken, self::TOKEN_TTL);
        }

        return $accessToken;
    }

    /**
     * Fire a push notification, and never let a push failure break the request
     * that triggered it.
     *
     * A dead service-account key made Google answer the token call with
     * "invalid_grant / Invalid JWT Signature"; that exception escaped all the
     * way to the visitor, so sending an enquiry showed a red "Client error"
     * even though the enquiry had already been saved. Push is a side effect —
     * it is logged and swallowed.
     */
    public static function sendMessage($notification_tray, $in_app_module, $token): bool
    {
        $apiurl = 'https://fcm.googleapis.com/v1/projects/biznie-60aff/messages:send';   //replace "your-project-id" with...your project ID

        try {
            $accessToken = self::getGoogleAccessToken();
        } catch (\Throwable $e) {
            Cache::forget('firebase.access_token');
            Log::warning('Firebase token request failed: '.$e->getMessage());

            return false;
        }

        if (! $accessToken) {
            Log::warning('Firebase token request returned no access token.');

            return false;
        }

        $headers = [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
        ];

        $message = [
                'message' => [
                    'token'             => $token,
                    'notification'      => $notification_tray,
                    'data'              => $in_app_module,
                ],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiurl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $result = curl_exec($ch);
        $failed = $result === false;
        $error  = $failed ? curl_error($ch) : null;
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($failed) {
            // This used to be a die(), which took the whole response with it.
            Log::warning('Firebase push failed: '.$error);

            return false;
        }

        if ($status >= 400) {
            Log::warning('Firebase push rejected ('.$status.'): '.(is_string($result) ? $result : ''));

            return false;
        }

        return true;
    }
}
