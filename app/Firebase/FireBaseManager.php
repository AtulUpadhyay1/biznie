<?php
namespace App\Firebase;

class FireBaseManager
{
    private static function getGoogleAccessToken(){

        $credentialsFilePath = 'biznie-b2952-firebase-adminsdk-86p12-f51654347f.json'; //replace this with your actual path and file name
        $client = new \Google_Client();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        return $token['access_token'];
    }

    public static function sendMessage($notification_tray, $in_app_module, $token)
    {
        $apiurl = 'https://fcm.googleapis.com/v1/projects/biznie-b2952/messages:send';   //replace "your-project-id" with...your project ID

        $headers = [
                'Authorization: Bearer ' . FireBaseManager::getGoogleAccessToken(),
                'Content-Type: application/json'
        ];

        // $notification_tray = [
        //         'title'             => "Some title",
        //         'body'              => "Some content",
        //     ];

        // $in_app_module = [
        //         "title"          => "Some data title (optional)",
        //         "body"           => "Some data body (optional)",
        //         "type"           => "call",
        //     ];
        //The $in_app_module array above can be empty - I use this to send variables in to my app when it is opened, so the user sees a popup module with the message additional to the generic task tray notification.

        $message = [
                'message' => [
                    'token'             => $token,
                    'notification'      => $notification_tray,
                    'data'              => $in_app_module,
                ],
        ];

        // return $message;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiurl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

        $result = curl_exec($ch);

        if ($result === FALSE) {
            //Failed
            die('Curl failed: ' . curl_error($ch));
        }

        curl_close($ch);
    }
}
