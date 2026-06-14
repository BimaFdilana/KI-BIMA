<?php

namespace App\Services\Message;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Models\Auth\UserModel;
use Illuminate\Support\Facades\Log;

class FirebaseCloudMessagingService
{
    protected $messaging;

    public function __construct()
    {
        try {
            // Initialize Firebase with service account
            $serviceAccountPath = config('services.firebase.credentials_file');

            if (!$serviceAccountPath || !file_exists($serviceAccountPath)) {
                Log::warning('Firebase service account file not found at: ' . $serviceAccountPath);
                return;
            }

            $factory = (new Factory)->withServiceAccount($serviceAccountPath);
            $this->messaging = $factory->createMessaging();
        } catch (\Exception $e) {
            Log::error('Failed to initialize Firebase: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to a specific user
     */
    public function sendToUser($userId, string $title, string $body, array $data = [])
    {
        if (!$this->messaging) {
            Log::warning('Firebase messaging not initialized');
            return false;
        }

        try {
            $user = UserModel::find($userId);

            if (!$user) {
                Log::info("User {$userId} not found");
                return false;
            }

            // Get FCM tokens from device_verifications table (user can have multiple devices)
            // fcmTokens() returns array, not collection
            $fcmTokens = $user->fcmTokens();

            if (empty($fcmTokens)) {
                Log::info("User {$userId} does not have any FCM tokens");
                return false;
            }

            // Send to all user's devices
            $successCount = 0;
            foreach ($fcmTokens as $token) {
                if ($this->sendToToken($token, $title, $body, $data)) {
                    $successCount++;
                }
            }

            Log::info("FCM sent to {$successCount} of " . count($fcmTokens) . " devices for user {$userId}");
            return $successCount > 0;
        } catch (\Exception $e) {
            Log::error('Error sending FCM to user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to a specific FCM token
     */
    public function sendToToken(string $token, string $title, string $body, array $data = [])
    {
        if (!$this->messaging) {
            Log::warning('Firebase messaging not initialized');
            return false;
        }

        try {
            $notification = Notification::create($title, $body);

            // Kreait Firebase v8.x API
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data);

            $this->messaging->sendMulticast($message, [$token]);

            Log::info("FCM sent successfully to token: {$token}");
            return true;
        } catch (\Exception $e) {
            Log::error('Error sending FCM: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to multiple tokens
     */
    public function sendToMultipleTokens(array $tokens, string $title, string $body, array $data = [])
    {
        if (!$this->messaging) {
            Log::warning('Firebase messaging not initialized');
            return false;
        }

        try {
            $notification = Notification::create($title, $body);

            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data);

            $this->messaging->sendMulticast($message, $tokens);

            Log::info('FCM sent to ' . count($tokens) . ' tokens');
            return true;
        } catch (\Exception $e) {
            Log::error('Error sending multicast FCM: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to a topic
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = [])
    {
        if (!$this->messaging) {
            Log::warning('Firebase messaging not initialized');
            return false;
        }

        try {
            $notification = Notification::create($title, $body);

            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification($notification)
                ->withData($data);

            $this->messaging->send($message);

            Log::info("FCM sent to topic: {$topic}");
            return true;
        } catch (\Exception $e) {
            Log::error('Error sending FCM to topic: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Subscribe user to a topic
     */
    public function subscribeToTopic(string $token, string $topic)
    {
        if (!$this->messaging) {
            return false;
        }

        try {
            $this->messaging->subscribeToTopic($topic, $token);
            return true;
        } catch (\Exception $e) {
            Log::error('Error subscribing to topic: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Unsubscribe user from a topic
     */
    public function unsubscribeFromTopic(string $token, string $topic)
    {
        if (!$this->messaging) {
            return false;
        }

        try {
            $this->messaging->unsubscribeFromTopic($topic, $token);
            return true;
        } catch (\Exception $e) {
            Log::error('Error unsubscribing from topic: ' . $e->getMessage());
            return false;
        }
    }
}
