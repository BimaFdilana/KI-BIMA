<?php

namespace App\Services\Message;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use Exception;

class FonnteService
{
    protected ?string $apiKey;
    protected string $baseUrl;
    protected ?string $accountToken;

    // API Endpoints
    const ENDPOINTS = [
        'send_message'          => '/send',
        'send_template'         => '/send-template',
        'add_device'            => '/add-device',
        'get_devices'           => '/get-devices',
        'device_profile'        => '/device',
        'device_info'           => '/device/info',
        'qr_activation'         => '/qr',
        'delete_device'         => '/delete-device',
        'disconnect'            => '/disconnect',
        'check_device_status'   => '/device/status',
    ];

    // Rate limiting and retry configuration
    const RATE_LIMIT_PER_MINUTE = 30;
    const RETRY_ATTEMPTS = 3;
    const RETRY_DELAY_SECONDS = 2;
    const REQUEST_TIMEOUT = 30;
    const WHATSAPP_MESSAGE_LIMIT = 4096;

    public function __construct()
    {
        $this->apiKey = config('services.fonnte.api_key');
        $this->baseUrl = config('services.fonnte.base_url', 'https://api.fonnte.com');
        $this->accountToken = config('services.fonnte.account_token');

        // Note: kredensial Fonnte tidak diwajibkan saat instansiasi agar service
        // bisa di-resolve oleh container meski 2FA/WhatsApp tidak digunakan
        // (mis. saat user login tanpa 2FA). Validasi dipindah ke ensureConfigured()
        // yang dipanggil oleh metode yang benar-benar memerlukan API call.
        if (empty($this->apiKey)) {
            Log::warning('Fonnte API key not configured');
        }

        if (empty($this->accountToken)) {
            Log::warning('Fonnte account token not configured');
        }
    }

    /**
     * Pastikan kredensial Fonnte tersedia sebelum melakukan API call.
     *
     * @throws InvalidArgumentException
     */
    protected function ensureConfigured(bool $requireAccountToken = true): void
    {
        if (empty($this->apiKey)) {
            throw new InvalidArgumentException('Fonnte API key is required');
        }

        if ($requireAccountToken && empty($this->accountToken)) {
            throw new InvalidArgumentException('Fonnte account token is required');
        }
    }

    /**
     * Make HTTP request to Fonnte API
     *
     * @param string $endpoint
     * @param array $params
     * @param bool $useAccountToken
     * @param string|null $deviceToken
     * @return array
     */
    protected function makeRequest(string $endpoint, array $params = [], bool $useAccountToken = true, ?string $deviceToken = null): array
    {
        $token = $useAccountToken ? $this->accountToken : ($deviceToken ?? $this->apiKey);

        if (empty($token)) {
            return [
                'status' => false,
                'error' => 'API token is required'
            ];
        }

        try {
            $url = $this->baseUrl . $endpoint;
            $request = Http::timeout(self::REQUEST_TIMEOUT)
                ->retry(self::RETRY_ATTEMPTS, self::RETRY_DELAY_SECONDS * 1000)
                ->withHeaders([
                    'Authorization' => $token,
                    'Accept' => 'application/json',
                ]);

            $response = $request->asForm()->post($url, $params);

            $result = $response->json();

            // Log response for debugging
            Log::info('Fonnte API Response', [
                'endpoint' => $endpoint,
                'status_code' => $response->status(),
                'success' => $response->successful(),
                'response' => $result
            ]);

            if ($response->failed()) {
                return [
                    'status' => false,
                    'error' => $result['reason'] ?? 'HTTP ' . $response->status(),
                    'http_code' => $response->status()
                ];
            }

            return [
                'status' => true,
                'data' => $result
            ];
        } catch (Exception $e) {
            Log::error('Fonnte API Exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send WhatsApp message
     *
     * @param $phoneNumber
     * @param string $message
     * @param string|null $deviceToken
     * @return array
     */
    public function sendWhatsAppMessage($phoneNumber, string $message, ?string $deviceToken = null): array
    {
        Log::debug('FonnteService - Starting to send WhatsApp message', [
            'phone' => $this->maskPhoneNumber($phoneNumber),
            'message_length' => strlen($message),
            'has_device_token' => !empty($deviceToken)
        ]);

        // Validate inputs
        if (!$this->validatePhoneNumber($phoneNumber)) {
            $error = 'Invalid phone number format';
            Log::error('FonnteService - Validation failed', [
                'phone' => $this->maskPhoneNumber($phoneNumber),
                'error' => $error
            ]);
            return [
                'status' => false,
                'error' => $error
            ];
        }

        if (empty(trim($message))) {
            $error = 'Message cannot be empty';
            Log::error('FonnteService - Empty message');
            return [
                'status' => false,
                'error' => $error
            ];
        }

        // Check rate limiting
        if (!$this->checkRateLimit()) {
            Log::warning('Fonnte rate limit exceeded');
            return [
                'status' => false,
                'error' => 'Rate limit exceeded'
            ];
        }

        // If no device token provided, try to find an available one
        if (empty($deviceToken)) {
            $devices = $this->getAllDevices();

            $connected = $devices['data']['connected'];
            if ($connected == 0) {
                return [
                    'status' => false,
                    'error' => 'No WhatsApp devices available'
                ];
            }

            // Find first connected device
            foreach ($devices['data']['data'] as $device) {
                if (empty($device['token'])) {
                    Log::debug('Skipping device - missing token', [
                        'device_data' => $device
                    ]);
                    continue;
                }

                // Check if device is connected based on the get-devices response
                if (
                    isset($device['status']) &&
                    in_array(strtolower($device['status']), ['connected', 'connect'])
                ) {
                    $deviceToken = $device['token'];
                    Log::debug('Found connected device', [
                        'device_token' => $this->maskToken($deviceToken),
                        'status' => $device['status'],
                        'device_name' => $device['name'] ?? 'unknown'
                    ]);
                    break;
                }

                Log::debug('Skipping device', [
                    'device_name' => $device['name'] ?? 'unknown',
                    'status' => $device['status'] ?? 'unknown'
                ]);
            }

            if (empty($deviceToken)) {
                return [
                    'status' => false,
                    'error' => 'No connected WhatsApp devices available'
                ];
            }
        }

        Log::debug('FonnteService - Using device token', [
            'device_token' => $this->maskToken($deviceToken),
            'has_device_token' => !empty($deviceToken)
        ]);

        // Format and sanitize
        $formattedPhone = $this->formatPhoneNumber($phoneNumber);
        $sanitizedMessage = $this->sanitizeMessage($message);

        // Prepare payload
        $payload = [
            'target' => $formattedPhone,
            'message' => $sanitizedMessage,
        ];

        // Add country code for Indonesian numbers if needed
        if (substr($formattedPhone, 0, 2) !== '62') {
            $payload['countryCode'] = '62';
        }
        Log::info('Fonnte sendWhatsAppMessage', [
            'payload' => $payload,
            'deviceToken' => $this->maskToken($deviceToken)
        ]);
        // Attempt to send with retry
        for ($attempt = 1; $attempt <= self::RETRY_ATTEMPTS; $attempt++) {
            Log::info('Fonnte send attempt', [
                'attempt' => $attempt,
                'to' => $this->maskPhoneNumber($formattedPhone),
                'message_length' => strlen($sanitizedMessage),
                'device_token' => $this->maskToken($deviceToken)
            ]);
            $result = $this->makeRequest(
                self::ENDPOINTS['send_message'],
                $payload,
                false, // Don't use account token since we're using device token
                $deviceToken
            );

            Log::debug('FonnteService - API Response', [
                'status' => $result['status'] ?? null,
                'error' => $result['error'] ?? null,
                'data' => $result['data'] ?? null
            ]);

            if ($result['status'] && $this->isSuccessResponse($result['data'] ?? [])) {
                Log::info('Fonnte WhatsApp message sent successfully', [
                    'to' => $this->maskPhoneNumber($formattedPhone),
                    'attempt' => $attempt,
                    'device_token' => $this->maskToken($deviceToken)
                ]);

                $this->incrementRateLimit();
                return [
                    'status' => true,
                    'message' => 'Message sent successfully',
                    'data' => $result['data']
                ];
            }

            // If we have more attempts left, wait before retrying
            if ($attempt < self::RETRY_ATTEMPTS) {
                sleep(self::RETRY_DELAY_SECONDS);
            }
        }

        Log::error('Failed to send WhatsApp message after all attempts', [
            'to' => $this->maskPhoneNumber($formattedPhone),
            'device_token' => $this->maskToken($deviceToken),
            'error' => $result['error'] ?? 'Unknown error'
        ]);

        return [
            'status' => false,
            'error' => $result['error'] ?? 'Failed to send message after ' . self::RETRY_ATTEMPTS . ' attempts',
            'device_token' => $this->maskToken($deviceToken)
        ];
    }

    /**
     * Send WhatsApp template message
     *
     * @param string $phoneNumber
     * @param string $templateName
     * @param array $params
     * @param string|null $deviceToken
     * @return array
     */
    public function sendTemplateMessage(string $phoneNumber, string $templateName, array $params = [], ?string $deviceToken = null): array
    {
        // Validate inputs
        if (!$this->validatePhoneNumber($phoneNumber)) {
            return [
                'status' => false,
                'error' => 'Invalid phone number format'
            ];
        }

        if (empty(trim($templateName))) {
            return [
                'status' => false,
                'error' => 'Template name cannot be empty'
            ];
        }

        // Check rate limiting
        if (!$this->checkRateLimit()) {
            return [
                'status' => false,
                'error' => 'Rate limit exceeded'
            ];
        }

        $formattedPhone = $this->formatPhoneNumber($phoneNumber);

        $payload = [
            'target' => $formattedPhone,
            'template_name' => $templateName,
            'delay' => 0
        ];

        if (!empty($params)) {
            $payload['parameters'] = $this->sanitizeTemplateParams($params);
        }

        $result = $this->makeRequest(
            self::ENDPOINTS['send_template'],
            $payload,
            $deviceToken ? false : true,
            $deviceToken
        );

        if ($result['status']) {
            $this->incrementRateLimit();
            Log::info('Fonnte template message sent successfully', [
                'to' => $this->maskPhoneNumber($formattedPhone),
                'template' => $templateName
            ]);
        }

        return $result;
    }

    /**
     * Get all devices
     *
     * @return array
     */
    public function getAllDevices(): array
    {
        return $this->makeRequest(self::ENDPOINTS['get_devices'], [], true);
    }

    /**
     * Add new device
     *
     * @param string $name
     * @param string $phoneNumber
     * @return array
     */
    public function addDevice(string $name, string $phoneNumber): array
    {
        if (!$this->validatePhoneNumber($phoneNumber)) {
            return [
                'status' => false,
                'error' => 'Invalid phone number format'
            ];
        }

        $params = [
            'name' => trim($name),
            'device' => $this->formatPhoneNumber($phoneNumber),
            'autoread' => 'false',
            'personal' => 'true',
            'group' => 'false',
        ];

        Log::info('Fonnte Add Device Request', ['params' => $params]);

        $result = $this->makeRequest(self::ENDPOINTS['add_device'], $params, true);

        if (!$result['status']) {
            Log::error('Failed to add device', ['response' => $result]);
        }

        return $result;
    }

    /**
     * Request QR code for device activation
     *
     * @param string $phoneNumber
     * @param string $deviceToken
     * @return array
     */
    public function requestQRActivation(string $phoneNumber, string $deviceToken): array
    {
        if (!$this->validatePhoneNumber($phoneNumber)) {
            return [
                'status' => false,
                'error' => 'Invalid phone number format'
            ];
        }

        $params = [
            'type' => 'qr',
            'whatsapp' => $this->formatPhoneNumber($phoneNumber)
        ];

        return $this->makeRequest(self::ENDPOINTS['qr_activation'], $params, false, $deviceToken);
    }
    /**
     * Mengecek status QR Code untuk device tertentu.
     * 
     * @param string $deviceToken
     * @return array
     */
    public function checkQRStatus(string $deviceToken): array
    {
        try {
            $response = Http::timeout(30)->post($this->baseUrl . '/qr-status', [
                'token' => $this->token,
                'device' => $deviceToken,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'status' => $data['status'] ?? false,
                    'data' => $data,
                    'error' => null
                ];
            }

            return [
                'status' => false,
                'data' => null,
                'error' => 'HTTP Error: ' . $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Fonnte QR Status Check Error', [
                'device_token' => $deviceToken,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => false,
                'data' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Mengecek status koneksi device secara real-time.
     * Alternative method yang lebih spesifik untuk monitoring.
     * 
     * @param string $deviceToken
     * @return array
     */
    public function checkDeviceStatus(string $deviceToken): array
    {
        try {
            $response = Http::timeout(30)->post($this->baseUrl . '/check-device', [
                'token' => $this->token,
                'device' => $deviceToken,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'status' => true,
                    'data' => [
                        'connected' => $data['connected'] ?? false,
                        'device_status' => $data['device_status'] ?? 'unknown',
                        'last_seen' => $data['last_seen'] ?? null,
                        'qr_expired' => $data['qr_expired'] ?? false
                    ],
                    'error' => null
                ];
            }

            return [
                'status' => false,
                'data' => null,
                'error' => 'HTTP Error: ' . $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Fonnte Device Status Check Error', [
                'device_token' => $deviceToken,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => false,
                'data' => null,
                'error' => $e->getMessage()
            ];
        }
    }
    /**
     * Get device profile information
     *
     * @param string $deviceToken
     * @return array
     */
    public function getDeviceProfile(string $deviceToken): array
    {
        return $this->makeRequest(self::ENDPOINTS['device_profile'], [], false, $deviceToken);
    }

    /**
     * Get device status information
     *
     * @param string|null $deviceToken
     * @return array
     */
    public function getDeviceStatus(?string $deviceToken = null): array
    {
        return $this->makeRequest(
            self::ENDPOINTS['device_info'],
            [],
            $deviceToken ? false : true,
            $deviceToken
        );
    }

    /**
     * Disconnect device
     *
     * @param string $deviceToken
     * @return array
     */
    public function disconnectDevice(string $deviceToken): array
    {
        return $this->makeRequest(self::ENDPOINTS['disconnect'], [], false, $deviceToken);
    }

    /**
     * Request OTP for device deletion
     *
     * @param string $deviceToken
     * @return array
     */
    public function requestOTPForDeleteDevice(string $deviceToken): array
    {
        return $this->makeRequest(self::ENDPOINTS['delete_device'], ['otp' => ''], false, $deviceToken);
    }

    /**
     * Submit OTP to delete device
     *
     * @param string $otp
     * @param string $deviceToken
     * @return array
     */
    public function submitOTPForDeleteDevice(string $otp, string $deviceToken): array
    {
        Log::info('Deleting device with OTP', [
            'otp' => $otp,
            'device_token' => substr($deviceToken, 0, 10) . '...'
        ]);

        return $this->makeRequest(
            self::ENDPOINTS['delete_device'],
            ['otp' => (int) $otp],
            false,
            $deviceToken
        );
    }

    // Private helper methods

    /**
     * Validate phone number format
     *
     * @param string $phoneNumber
     * @return bool
     */
    private function validatePhoneNumber(string $phoneNumber): bool
    {
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);
        return preg_match('/^(\+62|62|0)[0-9]{9,13}$/', $cleaned);
    }

    /**
     * Format phone number for API
     *
     * @param string $phoneNumber
     * @return string
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);

        // Convert to 62 format
        if (substr($cleaned, 0, 1) === '0') {
            return '62' . substr($cleaned, 1);
        } elseif (substr($cleaned, 0, 3) === '+62') {
            return substr($cleaned, 1);
        } elseif (substr($cleaned, 0, 2) !== '62') {
            return '62' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Mask phone number for logging
     *
     * @param string $phoneNumber
     * @return string
     */
    private function maskPhoneNumber(string $phoneNumber): string
    {
        if (strlen($phoneNumber) <= 6) {
            return str_repeat('*', strlen($phoneNumber));
        }

        return substr($phoneNumber, 0, 3) . str_repeat('*', strlen($phoneNumber) - 6) . substr($phoneNumber, -3);
    }

    /**
     * Sanitize message content
     *
     * @param string $message
     * @return string
     */
    private function sanitizeMessage(string $message): string
    {
        $sanitized = strip_tags(trim($message));
        return substr($sanitized, 0, self::WHATSAPP_MESSAGE_LIMIT);
    }

    /**
     * Sanitize template parameters
     *
     * @param array $params
     * @return array
     */
    private function sanitizeTemplateParams(array $params): array
    {
        return array_map(function ($param) {
            if (is_string($param)) {
                return strip_tags(trim($param));
            }
            return $param;
        }, $params);
    }

    /**
     * Check if response indicates success
     *
     * @param array $result
     * @return bool
     */
    private function isSuccessResponse(array $result): bool
    {
        return isset($result['status']) && $result['status'] === true;
    }

    /**
     * Check if error is retryable
     *
     * @param array $result
     * @return bool
     */
    private function isRetryableError(array $result): bool
    {
        if (!isset($result['http_code'])) {
            return true; // Network errors are generally retryable
        }

        $statusCode = $result['http_code'];
        return in_array($statusCode, [429, 500, 502, 503, 504]);
    }

    /**
     * Check rate limiting
     *
     * @return bool
     */
    private function checkRateLimit(): bool
    {
        $key = 'fonnte_rate_limit_' . now()->format('Y-m-d-H-i');
        $current = Cache::get($key, 0);
        return $current < self::RATE_LIMIT_PER_MINUTE;
    }

    /**
     * Increment rate limit counter
     *
     * @return void
     */
    private function incrementRateLimit(): void
    {
        $key = 'fonnte_rate_limit_' . now()->format('Y-m-d-H-i');
        $current = Cache::get($key, 0);
        Cache::put($key, $current + 1, now()->addMinute());
    }

    /**
     * Get rate limit status
     *
     * @return array
     */
    public function getRateLimitStatus(): array
    {
        $key = 'fonnte_rate_limit_' . now()->format('Y-m-d-H-i');
        $current = Cache::get($key, 0);

        return [
            'current' => $current,
            'limit' => self::RATE_LIMIT_PER_MINUTE,
            'remaining' => self::RATE_LIMIT_PER_MINUTE - $current,
            'reset_at' => now()->startOfMinute()->addMinute()->toDateTimeString()
        ];
    }

    /**
     * Reset rate limit (for testing purposes)
     *
     * @return void
     */
    public function resetRateLimit(): void
    {
        $key = 'fonnte_rate_limit_' . now()->format('Y-m-d-H-i');
        Cache::forget($key);
    }

    private function maskToken(string $token): string
    {
        return substr($token, 0, 10) . '...';
    }
}
