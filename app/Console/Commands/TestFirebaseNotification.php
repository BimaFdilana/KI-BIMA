<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Message\FirebaseCloudMessagingService;
use App\Services\Message\NotificationService;
use App\Models\Auth\UserModel;
use Illuminate\Support\Facades\Log;

class TestFirebaseNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:test-notification
                            {--user-id= : User ID to send test notification}
                            {--title=Test Notification : Notification title}
                            {--body=This is a test from Laravel backend : Notification body}
                            {--type=test : Notification type}
                            {--token= : Send to specific FCM token instead of user}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Test Firebase Cloud Messaging (FCM) notification service';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔥 Firebase Cloud Messaging Test');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Get parameters
        $userId = $this->option('user-id');
        $title = $this->option('title');
        $body = $this->option('body');
        $type = $this->option('type');
        $token = $this->option('token');

        // Validate
        if (!$token && !$userId) {
            $this->error('❌ Either --user-id or --token must be provided');
            $this->line('Examples:');
            $this->line('  php artisan firebase:test-notification --user-id=1');
            $this->line('  php artisan firebase:test-notification --token=YOUR_FCM_TOKEN');
            return 1;
        }

        try {
            $this->line('📋 Parameters:');
            $this->line('  Title: ' . $title);
            $this->line('  Body: ' . $body);
            $this->line('  Type: ' . $type);

            if ($token) {
                $this->line('  Token: ' . substr($token, 0, 20) . '...');
                $this->testSingleToken($token, $title, $body, $type);
            } elseif ($userId) {
                $this->line('  User ID: ' . $userId);
                $this->testUserNotification($userId, $title, $body, $type);
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            Log::error('Firebase test notification failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function testSingleToken(string $token, string $title, string $body, string $type): void
    {
        $this->line('');
        $this->info('📤 Sending notification to single token...');

        $service = new FirebaseCloudMessagingService();
        $data = [
            'type' => $type,
            'timestamp' => now()->toIso8601String(),
            'test' => 'true',
        ];

        $started = microtime(true);
        $result = $service->sendToToken($token, $title, $body, $data);
        $duration = round((microtime(true) - $started) * 1000, 2);

        if ($result) {
            $this->line('');
            $this->info('✅ SUCCESS!');
            $this->line('  Response time: ' . $duration . 'ms');
            $this->line('');
            $this->line('📬 Notification Details:');
            $this->line('  Title: ' . $title);
            $this->line('  Body: ' . $body);
            $this->line('  Type: ' . $type);
            $this->line('  Token: ' . substr($token, 0, 30) . '...');
            $this->line('');
            $this->line('💡 Tip: Check your device for the notification (usually appears in 1-5 seconds)');
        } else {
            $this->line('');
            $this->error('❌ FAILED! Check logs for details.');
            $this->line('');
            $this->line('🔍 Troubleshooting:');
            $this->line('  1. Verify FCM token is valid and not expired');
            $this->line('  2. Check Firebase credentials in storage/app/firebase/');
            $this->line('  3. Verify network connection to Firebase servers');
            $this->line('  4. Check Laravel logs: storage/logs/');
        }
    }

    private function testUserNotification(string $userId, string $title, string $body, string $type): void
    {
        $this->line('');
        $this->info('🔍 Looking up user...');

        $user = UserModel::find($userId);
        if (!$user) {
            $this->error('❌ User not found: ' . $userId);
            return;
        }

        $this->line('  ✓ User found: ' . $user->username . ' (' . $user->phone_number . ')');

        // Get FCM tokens
        $this->line('');
        $this->info('📱 Getting FCM tokens...');

        $fcmTokens = $user->fcmTokens();
        if (empty($fcmTokens)) {
            $this->warn('⚠️  No FCM tokens found for this user');
            $this->line('');
            $this->line('Steps to register device:');
            $this->line('  1. Open the mobile app');
            $this->line('  2. Register/Login');
            $this->line('  3. Device should auto-register FCM token');
            $this->line('  4. Expected in: user_devices table, fcm_token column');
            return;
        }

        $this->line('  ✓ Found ' . count($fcmTokens) . ' device(s)');

        // Send to each token
        $this->line('');
        $this->info('📤 Sending notification to all devices...');
        $this->newLine();

        $service = new FirebaseCloudMessagingService();
        $data = [
            'type' => $type,
            'timestamp' => now()->toIso8601String(),
            'test' => 'true',
            'user_id' => $userId,
        ];

        $successCount = 0;
        $failedCount = 0;

        foreach ($fcmTokens as $index => $token) {
            $this->line('  Device ' . ($index + 1) . '/' . count($fcmTokens) . ': ', false);

            $started = microtime(true);
            $result = $service->sendToToken($token, $title, $body, $data);
            $duration = round((microtime(true) - $started) * 1000, 2);

            if ($result) {
                $this->line('✅ OK (' . $duration . 'ms) - ' . substr($token, 0, 20) . '...');
                $successCount++;
            } else {
                $this->line('❌ FAILED (' . $duration . 'ms)');
                $failedCount++;
            }
        }

        // Summary
        $this->line('');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($successCount > 0) {
            $this->info('✅ SUCCESS: ' . $successCount . ' of ' . count($fcmTokens) . ' notifications sent');
        } else {
            $this->error('❌ FAILED: 0 of ' . count($fcmTokens) . ' notifications sent');
        }

        if ($failedCount > 0) {
            $this->warn('⚠️  ' . $failedCount . ' devices failed');
        }

        $this->line('');
        $this->line('📬 Notification Details:');
        $this->line('  Title: ' . $title);
        $this->line('  Body: ' . $body);
        $this->line('  Type: ' . $type);
        $this->line('  User: ' . $user->username);
        $this->line('');
        $this->line('💡 Tip: Notifications should appear on device within 1-5 seconds');
    }
}
