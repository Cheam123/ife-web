<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationController extends Controller
{
    public function sendPushNotification()
    {
        $firebase = (new Factory)->withServiceAccount(storage_path('app/google-services-eciatto-life-dev.json'));

        $messaging = $firebase->createMessaging();

        $message = CloudMessage::fromArray([
            'notification' => [
                'title' => 'Test',
                'body' => 'This is a test notification.'
            ],
            'topic' => 'global'
        ]);

        $messaging->send($message);

        return response()->json(['message' => 'Push notification sent successfully']);
    }
}
