<?php

namespace App\Services\Firebase;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
class FirebaseNotificationService
{
    /**
     * Create a new class instance.
     */
   protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $this->messaging = $factory->createMessaging();
    }

    public function sendToDevice(string $deviceToken, string $title, string $body, array $data = [])
    {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        return $this->messaging->send($message);
    }
}
