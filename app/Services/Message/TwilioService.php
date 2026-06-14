<?php

namespace App\Services\Message;

use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TwilioService
{
    protected ?Client $client;
    protected ?string $fromNumber;
    protected ?string $whatsappFromNumber;

    // Rate limiting
    const RATE_LIMIT_PER_MINUTE = 20;
    const RETRY_ATTEMPTS = 2;
    const RETRY_DELAY_SECONDS = 2;

    public function __construct()
    {
        try {
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.token');

            if (empty($sid) || empty($token)) {
                Log::error('Twilio credentials not configured');
                $this->client = null;
                return;
            }

            $this->client = new Client($sid, $token);
            $this->fromNumber = config('services.twilio.from_number');
            $this->whatsappFromNumber = config('services.twilio.whatsapp_from_number', $this->fromNumber);
        } catch (\Exception $e) {
            Log::error('Failed to initialize Twilio client', ['error' => $e->getMessage()]);
            $this->client = null;
        }
    }

    /**
     * Send WhatsApp message using Twilio API
     *
     * @param string $to Phone number
     * @param string $message Message content
     * @return bool Success status
     */
    public function sendWhatsAppMessage(string $to, string $message): bool
    {
        if (!$this->isConfigured()) {
            Log::error('Twilio not configured for WhatsApp');
            return false;
        }

        if (!$this->validatePhoneNumber($to)) {
            Log::error('Invalid phone number for Twilio WhatsApp', ['phone' => $to]);
            return false;
        }

        if (empty(trim($message))) {
            Log::error('Empty message for Twilio WhatsApp');
            return false;
        }

        // Check rate limiting
        if (!$this->checkRateLimit('whatsapp')) {
            Log::warning('Twilio WhatsApp rate limit exceeded');
            return false;
        }

        // Format numbers for WhatsApp
        $toFormatted = 'whatsapp:' . $this->formatPhoneNumber($to);
        $fromFormatted = 'whatsapp:' . $this->whatsappFromNumber;

        for ($attempt = 1; $attempt <= self::RETRY_ATTEMPTS; $attempt++) {
            try {
                $this->client->messages->create($toFormatted, [
                    'from' => $fromFormatted,
                    'body' => $this->sanitizeMessage($message)
                ]);

                Log::info('Twilio WhatsApp message sent successfully', [
                    'to' => $this->maskPhoneNumber($to),
                    'message_length' => strlen($message),
                    'attempt' => $attempt
                ]);

                $this->incrementRateLimit('whatsapp');
                return true;
            } catch (TwilioException $e) {
                Log::error('Twilio WhatsApp exception', [
                    'to' => $this->maskPhoneNumber($to),
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                    'code' => $e->getCode()
                ]);

                // Don't retry on certain errors
                if (!$this->isRetryableError($e)) {
                    break;
                }

                if ($attempt < self::RETRY_ATTEMPTS) {
                    sleep(self::RETRY_DELAY_SECONDS * $attempt);
                }
            } catch (\Exception $e) {
                Log::error('Twilio WhatsApp general exception', [
                    'to' => $this->maskPhoneNumber($to),
                    'attempt' => $attempt,
                    'error' => $e->getMessage()
                ]);

                if ($attempt < self::RETRY_ATTEMPTS) {
                    sleep(self::RETRY_DELAY_SECONDS * $attempt);
                }
            }
        }

        Log::error('Twilio WhatsApp message failed after all attempts', [
            'to' => $this->maskPhoneNumber($to),
            'attempts' => self::RETRY_ATTEMPTS
        ]);

        return false;
    }

    /**
     * Send SMS using Twilio API
     *
     * @param string $to Phone number
     * @param string $message Message content
     * @return bool Success status
     */
    public function sendSMS(string $to, string $message): bool
    {
        if (!$this->isConfigured()) {
            Log::error('Twilio not configured for SMS');
            return false;
        }

        if (!$this->validatePhoneNumber($to)) {
            Log::error('Invalid phone number for Twilio SMS', ['phone' => $to]);
            return false;
        }

        if (empty(trim($message))) {
            Log::error('Empty message for Twilio SMS');
            return false;
        }

        // Check rate limiting
        if (!$this->checkRateLimit('sms')) {
            Log::warning('Twilio SMS rate limit exceeded');
            return false;
        }

        // Format phone number
        $toFormatted = $this->formatPhoneNumber($to);

        for ($attempt = 1; $attempt <= self::RETRY_ATTEMPTS; $attempt++) {
            try {
                $this->client->messages->create($toFormatted, [
                    'from' => $this->fromNumber,
                    'body' => $this->sanitizeMessage($message)
                ]);

                Log::info('Twilio SMS sent successfully', [
                    'to' => $this->maskPhoneNumber($to),
                    'message_length' => strlen($message),
                    'attempt' => $attempt
                ]);

                $this->incrementRateLimit('sms');
                return true;
            } catch (TwilioException $e) {
                Log::error('Twilio SMS exception', [
                    'to' => $this->maskPhoneNumber($to),
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                    'code' => $e->getCode()
                ]);

                // Don't retry on certain errors
                if (!$this->isRetryableError($e)) {
                    break;
                }

                if ($attempt < self::RETRY_ATTEMPTS) {
                    sleep(self::RETRY_DELAY_SECONDS * $attempt);
                }
            } catch (\Exception $e) {
                Log::error('Twilio SMS general exception', [
                    'to' => $this->maskPhoneNumber($to),
                    'attempt' => $attempt,
                    'error' => $e->getMessage()
                ]);

                if ($attempt < self::RETRY_ATTEMPTS) {
                    sleep(self::RETRY_DELAY_SECONDS * $attempt);
                }
            }
        }

        Log::error('Twilio SMS failed after all attempts', [
            'to' => $this->maskPhoneNumber($to),
            'attempts' => self::RETRY_ATTEMPTS
        ]);

        return false;
    }

    /**
     * Send verification code via SMS
     *
     * @param string $to Phone number
     * @param string $code Verification code
     * @param string|null $appName Application name
     * @return bool Success status
     */
    public function sendVerificationCode(string $to, string $code, ?string $appName = null): bool
    {
        $appName = $appName ?? config('app.name', 'App');
        $message = "Your {$appName} verification code is: {$code}. Do not share this code with anyone.";

        return $this->sendSMS($to, $message);
    }

    /**
     * Check account balance and status
     *
     * @return array Account information
     */
    public function getAccountInfo(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Twilio not configured'
            ];
        }

        try {
            $account = $this->client->api->accounts($this->client->getAccountSid())->fetch();

            return [
                'success' => true,
                'status' => $account->status,
                'balance' => $account->balance,
                'currency' => 'USD' // Twilio uses USD
            ];
        } catch (TwilioException $e) {
            Log::error('Failed to get Twilio account info', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('General exception getting Twilio account info', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to retrieve account information'
            ];
        }
    }

    /**
     * Get message delivery status
     *
     * @param string $messageSid Message SID from Twilio
     * @return array Message status information
     */
    public function getMessageStatus(string $messageSid): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Twilio not configured'
            ];
        }

        try {
            $message = $this->client->messages($messageSid)->fetch();

            return [
                'success' => true,
                'status' => $message->status,
                'error_code' => $message->errorCode,
                'error_message' => $message->errorMessage,
                'price' => $message->price,
                'date_sent' => $message->dateSent?->format('Y-m-d H:i:s'),
                'date_updated' => $message->dateUpdated?->format('Y-m-d H:i:s')
            ];
        } catch (TwilioException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    // Private helper methods

    /**
     * Check if Twilio is properly configured
     */
    private function isConfigured(): bool
    {
        return $this->client !== null && !empty($this->fromNumber);
    }

    /**
     * Validate phone number format
     */
    private function validatePhoneNumber(string $phoneNumber): bool
    {
        // Remove all non-numeric characters except +
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);

        // Check if it's a valid international phone number format
        return preg_match('/^\+[1-9]\d{1,14}$/', $cleaned) ||
            preg_match('/^(\+62|62|0)[0-9]{9,13}$/', $cleaned); // Indonesian numbers
    }

    /**
     * Format phone number for Twilio API
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-numeric characters except +
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);

        // Add + prefix if not present and handle Indonesian numbers
        if (!str_starts_with($cleaned, '+')) {
            if (str_starts_with($cleaned, '62')) {
                $cleaned = '+' . $cleaned;
            } elseif (str_starts_with($cleaned, '0')) {
                $cleaned = '+62' . substr($cleaned, 1);
            } else {
                $cleaned = '+' . $cleaned;
            }
        }

        return $cleaned;
    }

    /**
     * Mask phone number for logging
     */
    private function maskPhoneNumber(string $phoneNumber): string
    {
        $formatted = $this->formatPhoneNumber($phoneNumber);

        if (strlen($formatted) <= 6) {
            return str_repeat('*', strlen($formatted));
        }

        return substr($formatted, 0, 4) . str_repeat('*', strlen($formatted) - 7) . substr($formatted, -3);
    }

    /**
     * Sanitize message content
     */
    private function sanitizeMessage(string $message): string
    {
        // Remove any potentially harmful content and limit length
        $sanitized = strip_tags($message);

        // SMS has a 160 character limit for single message
        // Extended messages can go up to 1600 characters
        return substr($sanitized, 0, 1600);
    }

    /**
     * Check if Twilio error is retryable
     */
    private function isRetryableError(TwilioException $e): bool
    {
        $retryableCodes = [
            20003, // Authentication Error (temporary)
            20429, // Too Many Requests
            30001, // Message queue full
            30003, // Unreachable destination handset
            30006, // Landline or unreachable carrier
        ];

        return in_array($e->getCode(), $retryableCodes) ||
            $e->getStatusCode() >= 500; // Server errors
    }

    /**
     * Check rate limiting by service type
     */
    private function checkRateLimit(string $serviceType): bool
    {
        $key = "twilio_rate_limit_{$serviceType}_" . now()->format('Y-m-d-H-i');
        $current = Cache::get($key, 0);

        return $current < self::RATE_LIMIT_PER_MINUTE;
    }

    /**
     * Increment rate limit counter
     */
    private function incrementRateLimit(string $serviceType): void
    {
        $key = "twilio_rate_limit_{$serviceType}_" . now()->format('Y-m-d-H-i');
        $current = Cache::get($key, 0);
        Cache::put($key, $current + 1, now()->addMinute());
    }

    /**
     * Generate OTP (kept for backward compatibility)
     *
     * @deprecated Use VerificationService::generateOtp() instead
     * @return string
     */
    public function generateOtp(): string
    {
        return (string) random_int(100000, 999999);
    }

    /**
     * Get current rate limit count for service type
     */
    public function getRateLimitCount(string $serviceType): int
    {
        $key = "twilio_rate_limit_{$serviceType}_" . now()->format('Y-m-d-H-i');
        return Cache::get($key, 0);
    }

    /**
     * Test Twilio connection
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Twilio not configured'
            ];
        }

        try {
            // Try to fetch account info as a connection test
            $account = $this->client->api->accounts($this->client->getAccountSid())->fetch();

            return [
                'success' => true,
                'status' => $account->status,
                'message' => 'Connection successful'
            ];
        } catch (TwilioException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Connection test failed: ' . $e->getMessage()
            ];
        }
    }
}
