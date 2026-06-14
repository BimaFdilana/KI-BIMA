<?php

namespace App\Services\Message;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Mail\VerificationCode;
use App\Mail\PasswordReset;

class EmailService
{
    // Rate limiting
    const RATE_LIMIT_PER_HOUR = 100;
    const RETRY_ATTEMPTS = 2;
    const RETRY_DELAY_SECONDS = 3;

    /**
     * Send email verification code
     *
     * @param string $email Recipient email
     * @param string $code Verification code
     * @param string|null $name User name (optional)
     * @return bool Success status
     */
    public function sendVerificationCode(string $email, string $code, ?string $name = null): bool
    {
        if (!$this->validateEmail($email)) {
            Log::error('Invalid email address', ['email' => $email]);
            return false;
        }

        if (!$this->validateVerificationCode($code)) {
            Log::error('Invalid verification code format', ['code' => $code]);
            return false;
        }

        // Check rate limiting
        if (!$this->checkRateLimit($email)) {
            Log::warning('Email rate limit exceeded', ['email' => $this->maskEmail($email)]);
            return false;
        }

        for ($attempt = 1; $attempt <= self::RETRY_ATTEMPTS; $attempt++) {
            try {
                Mail::to($email)->send(new VerificationCode($code, $name));

                Log::info('Email verification code sent successfully', [
                    'email' => $this->maskEmail($email),
                    'name' => $name,
                    'attempt' => $attempt
                ]);

                $this->incrementRateLimit($email);
                return true;
            } catch (\Exception $e) {
                Log::error('Email verification code failed', [
                    'email' => $this->maskEmail($email),
                    'code' => $code,
                    'name' => $name,
                    'attempt' => $attempt,
                    'error' => $e->getMessage()
                ]);

                if ($attempt === self::RETRY_ATTEMPTS) {
                    break;
                }

                sleep(self::RETRY_DELAY_SECONDS * $attempt);
            }
        }

        return false;
    }

    /**
     * Send password reset code
     *
     * @param string $email Recipient email
     * @param string $code Reset code
     * @param string|null $name User name (optional)
     * @return bool Success status
     */
    public function sendPasswordResetCode(string $email, string $code, ?string $name = null): bool
    {
        if (!$this->validateEmail($email)) {
            Log::error('Invalid email address for password reset', ['email' => $email]);
            return false;
        }

        if (!$this->validateVerificationCode($code)) {
            Log::error('Invalid password reset code format', ['code' => $code]);
            return false;
        }

        // Check rate limiting
        if (!$this->checkRateLimit($email)) {
            Log::warning('Email rate limit exceeded for password reset', ['email' => $this->maskEmail($email)]);
            return false;
        }

        for ($attempt = 1; $attempt <= self::RETRY_ATTEMPTS; $attempt++) {
            try {
                Mail::to($email)->send(new PasswordReset($code, $name));

                Log::info('Email password reset code sent successfully', [
                    'email' => $this->maskEmail($email),
                    'name' => $name,
                    'attempt' => $attempt
                ]);

                $this->incrementRateLimit($email);
                return true;
            } catch (\Exception $e) {
                Log::error('Email password reset code failed', [
                    'email' => $this->maskEmail($email),
                    'code' => $code,
                    'name' => $name,
                    'attempt' => $attempt,
                    'error' => $e->getMessage()
                ]);

                if ($attempt === self::RETRY_ATTEMPTS) {
                    break;
                }

                sleep(self::RETRY_DELAY_SECONDS * $attempt);
            }
        }

        return false;
    }

    /**
     * Send custom notification email
     *
     * @param string $email Recipient email
     * @param string $subject Email subject
     * @param string $content Email content
     * @param array $data Additional data
     * @return bool Success status
     */
    public function sendCustomEmail(string $email, string $subject, string $content, array $data = []): bool
    {
        if (!$this->validateEmail($email)) {
            Log::error('Invalid email address for custom email', ['email' => $email]);
            return false;
        }

        if (empty(trim($subject))) {
            Log::error('Empty subject for custom email');
            return false;
        }

        if (empty(trim($content))) {
            Log::error('Empty content for custom email');
            return false;
        }

        // Check rate limiting
        if (!$this->checkRateLimit($email)) {
            Log::warning('Email rate limit exceeded for custom email', ['email' => $this->maskEmail($email)]);
            return false;
        }

        // Sanitize content
        $sanitizedContent = $this->sanitizeContent($content);
        $sanitizedSubject = $this->sanitizeSubject($subject);

        for ($attempt = 1; $attempt <= self::RETRY_ATTEMPTS; $attempt++) {
            try {
                Mail::send([], [], function ($message) use ($email, $sanitizedSubject, $sanitizedContent) {
                    $message->to($email)
                        ->subject($sanitizedSubject)
                        ->setBody($sanitizedContent, 'text/html');
                });

                Log::info('Custom email sent successfully', [
                    'email' => $this->maskEmail($email),
                    'subject' => $sanitizedSubject,
                    'content_length' => strlen($sanitizedContent),
                    'attempt' => $attempt
                ]);

                $this->incrementRateLimit($email);
                return true;
            } catch (\Exception $e) {
                Log::error('Custom email failed', [
                    'email' => $this->maskEmail($email),
                    'subject' => $sanitizedSubject,
                    'content_length' => strlen($sanitizedContent),
                    'data' => $data,
                    'attempt' => $attempt,
                    'error' => $e->getMessage()
                ]);

                if ($attempt === self::RETRY_ATTEMPTS) {
                    break;
                }

                sleep(self::RETRY_DELAY_SECONDS * $attempt);
            }
        }

        return false;
    }

    /**
     * Send bulk emails (with rate limiting consideration)
     *
     * @param array $recipients Array of email addresses
     * @param string $subject Email subject
     * @param string $content Email content
     * @return array Results array with success/failure status
     */
    public function sendBulkEmail(array $recipients, string $subject, string $content): array
    {
        $results = [];
        $validRecipients = [];

        // Validate all recipients first
        foreach ($recipients as $email) {
            if ($this->validateEmail($email)) {
                $validRecipients[] = $email;
            } else {
                $results[$email] = [
                    'success' => false,
                    'error' => 'Invalid email address'
                ];
            }
        }

        // Check if we have valid recipients
        if (empty($validRecipients)) {
            Log::error('No valid email recipients for bulk send');
            return $results;
        }

        // Send to valid recipients
        foreach ($validRecipients as $email) {
            $results[$email] = [
                'success' => $this->sendCustomEmail($email, $subject, $content),
                'error' => $results[$email]['success'] ?? false ? null : 'Failed to send email'
            ];

            // Add small delay between sends to avoid overwhelming the mail server
            usleep(100000); // 0.1 second delay
        }

        Log::info('Bulk email completed', [
            'total_recipients' => count($recipients),
            'valid_recipients' => count($validRecipients),
            'successful_sends' => count(array_filter($results, fn($r) => $r['success']))
        ]);

        return $results;
    }

    // Private helper methods

    /**
     * Validate email address
     */
    private function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false &&
            strlen($email) <= 254 && // RFC 5321 limit
            !preg_match('/[<>"\']/', $email); // Additional security check
    }

    /**
     * Validate verification code format
     */
    private function validateVerificationCode(string $code): bool
    {
        return preg_match('/^\d{4,8}$/', $code); // 4-8 digit codes only
    }

    /**
     * Mask email for logging
     */
    private function maskEmail(string $email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return str_repeat('*', strlen($email));
        }

        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1];

        if (strlen($name) <= 2) {
            $maskedName = str_repeat('*', strlen($name));
        } else {
            $maskedName = substr($name, 0, 2) . str_repeat('*', max(0, strlen($name) - 4)) . substr($name, -2);
        }

        return $maskedName . '@' . $domain;
    }

    /**
     * Sanitize email content to prevent injection attacks
     */
    private function sanitizeContent(string $content): string
    {
        // Remove script tags and other potentially dangerous elements
        $content = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $content);
        $content = preg_replace('/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi', '', $content);
        $content = preg_replace('/on\w+="[^"]*"/i', '', $content);

        // Limit content length
        return substr($content, 0, 50000); // 50KB limit
    }

    /**
     * Sanitize email subject
     */
    private function sanitizeSubject(string $subject): string
    {
        // Remove line breaks and limit length
        $subject = str_replace(["\r", "\n", "\t"], ' ', $subject);
        return substr(trim($subject), 0, 998); // RFC 5322 limit
    }

    /**
     * Check rate limiting per email address
     */
    private function checkRateLimit(string $email): bool
    {
        $key = 'email_rate_limit_' . hash('sha256', $email) . '_' . now()->format('Y-m-d-H');
        $current = Cache::get($key, 0);

        return $current < self::RATE_LIMIT_PER_HOUR;
    }

    /**
     * Increment rate limit counter for email address
     */
    private function incrementRateLimit(string $email): void
    {
        $key = 'email_rate_limit_' . hash('sha256', $email) . '_' . now()->format('Y-m-d-H');
        $current = Cache::get($key, 0);
        Cache::put($key, $current + 1, now()->addHour());
    }

    /**
     * Get current rate limit count for email
     */
    public function getRateLimitCount(string $email): int
    {
        $key = 'email_rate_limit_' . hash('sha256', $email) . '_' . now()->format('Y-m-d-H');
        return Cache::get($key, 0);
    }

    /**
     * Check if email service is properly configured
     */
    public function isConfigured(): bool
    {
        try {
            $mailer = config('mail.default');
            $host = config("mail.mailers.{$mailer}.host");
            $username = config("mail.mailers.{$mailer}.username");

            return !empty($mailer) && !empty($host) && !empty($username);
        } catch (\Exception $e) {
            Log::error('Email configuration check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
