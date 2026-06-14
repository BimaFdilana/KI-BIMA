<?php

namespace App\Services\Message;

use App\Models\Auth\OtpModel;
use App\Models\Auth\RecoveryCodeModel;
use App\Models\Auth\UserDeviceModel;
use App\Models\Auth\UserModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VerificationService
{
    // Service dependencies
    protected TwilioService $twilioService;
    protected FonnteService $fonnteService;
    protected EmailService $emailService;

    // Configuration constants
    const OTP_EXPIRATION_MINUTES = 10;
    const DEVICE_REMEMBER_DAYS = 30;
    const COOLDOWN_MINUTES = 1;
    const OTP_LENGTH = 6;
    const MAX_ATTEMPTS = 5;
    const LOCKOUT_MINUTES = 15;

    // Verification types
    const TYPE_PHONE = 'phone';
    const TYPE_EMAIL = 'email';
    const TYPE_DEVICE = 'device';
    const TYPE_PASSWORD = 'password';
    const TYPE_RECOVERY = 'recovery-code';
    const TYPE_NEWPW = 'new-password';
    const TYPE_ACCOUNT = 'account';

    // Channels
    const CHANNEL_WHATSAPP = 'whatsapp';
    const CHANNEL_SMS = 'sms';
    const CHANNEL_EMAIL = 'email';

    public function __construct(
        TwilioService $twilioService,
        FonnteService $fonnteService,
        EmailService $emailService
    ) {
        $this->twilioService = $twilioService;
        $this->fonnteService = $fonnteService;
        $this->emailService = $emailService;
    }

    /**
     * Generate a secure OTP code with validation patterns
     */
    private function generateOtp(): string
    {
        // Avoid sequential numbers and common patterns
        do {
            $otp = '';
            for ($i = 0; $i < self::OTP_LENGTH; $i++) {
                $otp .= random_int(0, 9);
            }
        } while ($this->isWeakOtp($otp));

        return $otp;
    }

    /**
     * Check if OTP is weak (sequential, repeated digits, etc.)
     */
    private function isWeakOtp(string $otp): bool
    {
        // Check for repeated digits (111111, 000000)
        if (count(array_unique(str_split($otp))) <= 1) {
            return true;
        }

        // Check for sequential patterns (123456, 654321)
        $isAscending = true;
        $isDescending = true;

        for ($i = 1; $i < strlen($otp); $i++) {
            if ((int)$otp[$i] !== (int)$otp[$i - 1] + 1) {
                $isAscending = false;
            }
            if ((int)$otp[$i] !== (int)$otp[$i - 1] - 1) {
                $isDescending = false;
            }
        }

        return $isAscending || $isDescending;
    }

    /**
     * Check if user is in cooldown period for verification
     */
    public function isInCooldown(UserModel $user, string $type): bool
    {
        if (!$this->isValidType($type)) {
            Log::warning('Invalid verification type in cooldown check', ['type' => $type, 'user_id' => $user->id]);
            return true; // Fail safe
        }

        $cooldownKey = $this->getCooldownKey($user, $type);
        return Cache::has($cooldownKey);
    }

    /**
     * Set cooldown period for user verification
     */
    public function setCooldown(UserModel $user, string $type): void
    {
        if (!$this->isValidType($type)) {
            Log::error('Invalid verification type for cooldown', ['type' => $type, 'user_id' => $user->id]);
            return;
        }

        $cooldownKey = $this->getCooldownKey($user, $type);
        $expiryTime = now()->addMinutes(self::COOLDOWN_MINUTES);
        Cache::put($cooldownKey, $expiryTime, $expiryTime);

        Log::info('Cooldown set for user', [
            'user_id' => $user->id,
            'type' => $type,
            'expires_at' => $expiryTime
        ]);
    }

    /**
     * Get remaining cooldown seconds
     */
    public function getRemainingCooldownSeconds(UserModel $user, string $type): int
    {
        if (!$this->isValidType($type)) {
            return 0;
        }

        $cooldownKey = $this->getCooldownKey($user, $type);

        if (Cache::has($cooldownKey)) {
            $expiryTime = Cache::get($cooldownKey);
            if ($expiryTime instanceof Carbon) {
                return max(0, now()->diffInSeconds($expiryTime, false));
            }
        }

        return 0;
    }

    /**
     * Check and increment failed attempts
     */
    public function checkFailedAttempts(UserModel $user, string $type): bool
    {
        $attemptsKey = $this->getAttemptsKey($user, $type);
        $attempts = Cache::get($attemptsKey, 0);

        if ($attempts >= self::MAX_ATTEMPTS) {
            $this->lockoutUser($user, $type);
            return false;
        }

        return true;
    }

    /**
     * Increment failed attempts
     */
    public function incrementFailedAttempts(UserModel $user, string $type): void
    {
        $attemptsKey = $this->getAttemptsKey($user, $type);
        $attempts = Cache::get($attemptsKey, 0) + 1;
        Cache::put($attemptsKey, $attempts, now()->addMinutes(self::LOCKOUT_MINUTES));

        Log::warning('Failed verification attempt', [
            'user_id' => $user->id,
            'type' => $type,
            'attempts' => $attempts
        ]);
    }

    /**
     * Clear failed attempts after successful verification
     */
    public function clearFailedAttempts(UserModel $user, string $type): void
    {
        $attemptsKey = $this->getAttemptsKey($user, $type);
        Cache::forget($attemptsKey);
    }

    /**
     * Lockout user for too many failed attempts
     */
    private function lockoutUser(UserModel $user, string $type): void
    {
        $lockoutKey = $this->getLockoutKey($user, $type);
        Cache::put($lockoutKey, true, now()->addMinutes(self::LOCKOUT_MINUTES));

        Log::error('User locked out for too many attempts', [
            'user_id' => $user->id,
            'type' => $type,
            'lockout_until' => now()->addMinutes(self::LOCKOUT_MINUTES)
        ]);
    }

    /**
     * Check if user is locked out
     */
    public function isLockedOut(UserModel $user, string $type): bool
    {
        $lockoutKey = $this->getLockoutKey($user, $type);
        return Cache::has($lockoutKey);
    }

    /**
     * Generate verification code and store in database
     */
    public function generateVerificationCode(UserModel $user, string $type, ?string $identifier = null): string
    {
        if (!$this->isValidType($type)) {
            throw new \InvalidArgumentException("Invalid verification type: {$type}");
        }

        // Check if user is locked out
        if ($this->isLockedOut($user, $type)) {
            throw new \Exception('Too many failed attempts. Please try again later.', 429);
        }

        $code = $this->generateOtp();
        $identifier = $this->resolveIdentifier($user, $type, $identifier);

        try {
            // Delete existing OTP for the same user, type and identifier
            OtpModel::where('user_id', $user->id)
                ->where('type', $type)
                ->where('identifier', $identifier)
                ->delete();

            // Save new OTP in database
            OtpModel::create([
                'user_id' => $user->id,
                'type' => $type,
                'identifier' => $identifier,
                'code' => $code,
                'expires_at' => now()->addMinutes(self::OTP_EXPIRATION_MINUTES),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Clean up expired OTPs
            $this->cleanupExpiredOtps();

            Log::info('Verification code generated', [
                'user_id' => $user->id,
                'type' => $type,
                'identifier' => $this->maskIdentifier($identifier),
                'expires_at' => now()->addMinutes(self::OTP_EXPIRATION_MINUTES)
            ]);

            return $code;
        } catch (\Exception $e) {
            Log::error('Failed to generate verification code', [
                'user_id' => $user->id,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Peek/verify code without consuming it (does not delete the OTP)
     * Used for two-step verification where OTP is verified first, then used again later
     */
    public function peekCode(UserModel $user, string $type, string $code, ?string $identifier = null): bool
    {
        if (!$this->isValidType($type)) {
            throw new \InvalidArgumentException("Invalid verification type: {$type}");
        }

        // Check if user is locked out
        if ($this->isLockedOut($user, $type)) {
            throw new \Exception('Too many failed attempts. Please try again later.', 429);
        }

        // Check failed attempts
        if (!$this->checkFailedAttempts($user, $type)) {
            throw new \Exception('Too many failed attempts. Please try again later.', 429);
        }

        $identifier = $this->resolveIdentifier($user, $type, $identifier);

        // First check if there's an expired code that matches
        $expiredOtp = OtpModel::where('user_id', $user->id)
            ->where('type', $type)
            ->where('identifier', $identifier)
            ->where('code', $code)
            ->where('expires_at', '<=', now())
            ->first();

        if ($expiredOtp) {
            // Delete expired OTP
            $expiredOtp->delete();
            throw new \Exception('Verification code has expired. Please request a new code.', 1001);
        }

        // Find valid OTP in database (but don't delete it)
        $otpRecord = OtpModel::where('user_id', $user->id)
            ->where('type', $type)
            ->where('identifier', $identifier)
            ->where('code', $code)
            ->where('expires_at', '>', now())
            ->first();

        if ($otpRecord) {
            // Don't delete OTP - just verify it exists and is valid
            // Clear failed attempts since code is valid
            $this->clearFailedAttempts($user, $type);

            Log::info('Verification code peeked successfully', [
                'user_id' => $user->id,
                'type' => $type,
                'identifier' => $this->maskIdentifier($identifier)
            ]);

            return true;
        }

        // Increment failed attempts
        $this->incrementFailedAttempts($user, $type);

        Log::warning('Invalid verification code peek attempt', [
            'user_id' => $user->id,
            'type' => $type,
            'identifier' => $this->maskIdentifier($identifier)
        ]);

        return false;
    }

    /**
     * Verify code for any verification type using database
     */
    public function verifyCode(UserModel $user, string $type, string $code, ?string $identifier = null): bool
    {
        if (!$this->isValidType($type)) {
            throw new \InvalidArgumentException("Invalid verification type: {$type}");
        }

        // Check if user is locked out
        if ($this->isLockedOut($user, $type)) {
            throw new \Exception('Too many failed attempts. Please try again later.', 429);
        }

        // Check failed attempts
        if (!$this->checkFailedAttempts($user, $type)) {
            throw new \Exception('Too many failed attempts. Please try again later.', 429);
        }

        $identifier = $this->resolveIdentifier($user, $type, $identifier);

        // First check if there's an expired code that matches
        $expiredOtp = OtpModel::where('user_id', $user->id)
            ->where('type', $type)
            ->where('identifier', $identifier)
            ->where('code', $code)
            ->where('expires_at', '<=', now())
            ->first();

        if ($expiredOtp) {
            // Delete expired OTP
            $expiredOtp->delete();
            throw new \Exception('Verification code has expired. Please request a new code.', 1001);
        }

        // Find valid OTP in database
        $otpRecord = OtpModel::where('user_id', $user->id)
            ->where('type', $type)
            ->where('identifier', $identifier)
            ->where('code', $code)
            ->where('expires_at', '>', now())
            ->first();

        if ($otpRecord) {
            // Delete OTP after verification
            $otpRecord->delete();

            // Clear failed attempts
            $this->clearFailedAttempts($user, $type);

            // Mark appropriate field as verified based on type
            $this->markAsVerified($user, $type, $identifier);

            Log::info('Verification code verified successfully', [
                'user_id' => $user->id,
                'type' => $type,
                'identifier' => $this->maskIdentifier($identifier)
            ]);

            return true;
        }

        // Increment failed attempts
        $this->incrementFailedAttempts($user, $type);

        Log::warning('Invalid verification code attempt', [
            'user_id' => $user->id,
            'type' => $type,
            'identifier' => $this->maskIdentifier($identifier)
        ]);

        return false;
    }

    /**
     * Mark user field as verified based on type
     */
    private function markAsVerified(UserModel $user, string $type, string $identifier): void
    {
        switch ($type) {
            case self::TYPE_PHONE:
                $user->phone_verified_at = now();
                $user->save();
                break;

            case self::TYPE_EMAIL:
                $user->email_verified_at = now();
                $user->save();
                break;

            case self::TYPE_ACCOUNT:
                $user->email_verified_at = now();
                $user->phone_verified_at = now();
                $user->account_verified_at = now();
                $user->save();
                break;

            case self::TYPE_DEVICE:
                $this->verifyDevice($user, $identifier);
                break;
        }
    }

    /**
     * Delete expired OTPs
     */
    private function cleanupExpiredOtps(): void
    {
        $deleted = OtpModel::where('expires_at', '<', now())->delete();

        if ($deleted > 0) {
            Log::info('Cleaned up expired OTPs', ['count' => $deleted]);
        }
    }

    /**
     * Verify device and mark it as trusted
     */
    private function verifyDevice(UserModel $user, string $deviceId): bool
    {
        $device = UserDeviceModel::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->first();

        if ($device) {
            $device->verified_at = now();
            $device->save();

            Log::info('Device verified', [
                'user_id' => $user->id,
                'device_id' => $deviceId
            ]);

            return true;
        }

        Log::warning('Device not found for verification', [
            'user_id' => $user->id,
            'device_id' => $deviceId
        ]);

        return false;
    }

    /**
     * Remember a device for a user
     */
    public function rememberDevice(
        UserModel $user,
        string $deviceId,
        ?string $deviceName = null,
        ?string $userAgent = null,
        ?Carbon $expiresAt = null,
        ?string $fcmToken = null,
        ?string $ipAddress = null
    ): UserDeviceModel {
        $expiresAt = $expiresAt ?? now()->addDays(self::DEVICE_REMEMBER_DAYS);

        $device = UserDeviceModel::updateOrCreate(
            [
                'device_id' => $deviceId
            ],
            [
                'user_id' => $user->id,
                'device_name' => $deviceName,
                'user_agent' => $userAgent,
                'ip_address' => $ipAddress,
                'fcm_token' => $fcmToken,
                'last_active_at' => now(),
                'expires_at' => $expiresAt
            ]
        );
        return $device;
    }

    /**
     * Check if device is remembered and still valid
     */
    public function isDeviceRemembered(UserModel $user, string $deviceId): bool
    {
        return UserDeviceModel::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Forget a specific device
     */
    public function forgetDevice(UserModel $user, string $deviceId): bool
    {
        $deleted = UserDeviceModel::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->delete();

        if ($deleted > 0) {
            Log::info('Device forgotten', [
                'user_id' => $user->id,
                'device_id' => $deviceId
            ]);
        }

        return $deleted > 0;
    }

    /**
     * Forget all devices except current
     */
    public function forgetOtherDevices(UserModel $user, string $currentDeviceId): int
    {
        $deleted = UserDeviceModel::where('user_id', $user->id)
            ->where('device_id', '!=', $currentDeviceId)
            ->delete();

        Log::info('Other devices forgotten', [
            'user_id' => $user->id,
            'current_device_id' => $currentDeviceId,
            'deleted_count' => $deleted
        ]);

        return $deleted;
    }

    /**
     * List all user devices
     */
    public function getUserDevices(UserModel $user): \Illuminate\Database\Eloquent\Collection
    {
        return UserDeviceModel::where('user_id', $user->id)
            ->orderBy('last_active_at', 'desc')
            ->get();
    }

    /**
     * Verify a recovery code
     */
    public function verifyRecoveryCode(UserModel $user, string $recoveryCode, ?string $deviceId = null, ?string $deviceName = null): bool
    {
        $recoveryRecord = RecoveryCodeModel::where('user_id', $user->id)
            ->where('code', hash('sha256', $recoveryCode))
            ->where('hasUsed', 0)
            ->first();

        if ($recoveryRecord) {
            // Mark as used
            $recoveryRecord->hasUsed = 1;
            $recoveryRecord->last_used = now();
            $recoveryRecord->last_used_device = $deviceName ?? 'Unknown Device';
            $recoveryRecord->save();

            Log::info('Recovery code used', [
                'user_id' => $user->id,
                'device_id' => $deviceId,
                'device_name' => $deviceName
            ]);

            return true;
        }

        Log::warning('Invalid recovery code attempt', [
            'user_id' => $user->id,
            'device_id' => $deviceId
        ]);

        return false;
    }

    /**
     * Send verification code via preferred channel
     */
    public function sendVerificationCode(UserModel $user, string $type, string $channel = self::CHANNEL_WHATSAPP, ?string $identifier = null): array
    {
        if (!$this->isValidType($type)) {
            return [
                'success' => false,
                'message' => 'Tipe verifikasi tidak valid'
            ];
        }

        if (!$this->isValidChannel($channel)) {
            return [
                'success' => false,
                'message' => 'Saluran verifikasi tidak valid'
            ];
        }

        // Check if user is in cooldown period
        if ($this->isInCooldown($user, $type)) {
            return [
                'success' => false,
                'message' => 'Harap menunggu sebelum meminta kode verifikasi lagi',
                'cooldown' => $this->getRemainingCooldownSeconds($user, $type)
            ];
        }

        // Validate channel availability for user
        $channelValidation = $this->validateChannelForUser($user, $channel, $type);
        if (!$channelValidation['valid']) {
            return [
                'success' => false,
                'message' => $channelValidation['message']
            ];
        }

        try {
            $code = $this->generateVerificationCode($user, $type, $identifier);
            $result = $this->sendCodeViaChannel($user, $channel, $code, $type);

            // Set cooldown after successful send
            if ($result) {
                $this->setCooldown($user, $type);
            }

            return [
                'success' => (bool)$result,
                'message' => $result ? 'Kode verifikasi berhasil dikirim' : 'Gagal mengirim kode verifikasi',
                'channel' => $channel
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send verification code', [
                'user_id' => $user->id,
                'type' => $type,
                'channel' => $channel,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengirim kode verifikasi'
            ];
        }
    }

    /**
     * Send password reset code
     */
    public function sendPasswordResetCode(UserModel $user, string $channel = self::CHANNEL_WHATSAPP, ?string $identifier = null): array
    {
        return $this->sendVerificationCode($user, self::TYPE_PASSWORD, $channel, $identifier);
    }

    /**
     * Send account verification code
     */
    public function sendAccountVerificationCode(UserModel $user, string $channel = self::CHANNEL_EMAIL, ?string $identifier = null): array
    {
        return $this->sendVerificationCode($user, self::TYPE_ACCOUNT, $channel, $identifier);
    }

    /**
     * Send phone verification code
     */
    public function sendPhoneVerificationCode(UserModel $user, string $channel = self::CHANNEL_WHATSAPP): array
    {
        return $this->sendVerificationCode($user, self::TYPE_PHONE, $channel, $user->phone_number);
    }

    /**
     * Send email verification code
     */
    public function sendEmailVerificationCode(UserModel $user): array
    {
        return $this->sendVerificationCode($user, self::TYPE_EMAIL, self::CHANNEL_EMAIL, $user->email);
    }

    /**
     * Send new password to user
     */

    private function getNewpasswordMessage($user, $password)
    {
        return <<<EOT
        Ini adalah password baru kamu:

        *Credential* :
        Username : $user->name
        Password : $password

        *Perhatian!*
        Tolong ganti password setelah login untuk keamanan akun anda

        Terimakasih!
        EOT;
    }
    public function sendNewPassword(UserModel $user, string $password, string $channel = self::CHANNEL_WHATSAPP): array
    {
        $message = $this->getNewPasswordMessage($user, $password);

        try {
            $result = $this->sendMessageViaChannel($user, $channel, $message, 'Password Baru');

            return [
                'success' => (bool)$result,
                'message' => $result ? 'Password baru berhasil dikirim' : 'Gagal mengirim password baru',
                'channel' => $channel
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send new password', [
                'user_id' => $user->id,
                'channel' => $channel,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengirim password baru'
            ];
        }
    }

    /**
     * Verify password reset code
     */
    public function verifyPasswordResetCode(UserModel $user, string $code, ?string $identifier = null): bool
    {
        return $this->verifyCode($user, self::TYPE_PASSWORD, $code, $identifier);
    }

    /**
     * Verify account verification code
     */
    public function verifyAccountCode(UserModel $user, string $code, ?string $identifier = null): bool
    {
        return $this->verifyCode($user, self::TYPE_ACCOUNT, $code, $identifier);
    }

    /**
     * Verify phone verification code
     */
    public function verifyPhoneCode(UserModel $user, string $code): bool
    {
        return $this->verifyCode($user, self::TYPE_PHONE, $code, $user->phone_number);
    }

    /**
     * Verify email verification code
     */
    public function verifyEmailCode(UserModel $user, string $code): bool
    {
        return $this->verifyCode($user, self::TYPE_EMAIL, $code, $user->email);
    }

    // Private helper methods

    private function isValidType(string $type): bool
    {
        return in_array($type, [
            self::TYPE_PHONE,
            self::TYPE_EMAIL,
            self::TYPE_DEVICE,
            self::TYPE_PASSWORD,
            self::TYPE_RECOVERY,
            self::TYPE_NEWPW,
            self::TYPE_ACCOUNT
        ]);
    }

    private function isValidChannel(string $channel): bool
    {
        return in_array($channel, [
            self::CHANNEL_WHATSAPP,
            self::CHANNEL_SMS,
            self::CHANNEL_EMAIL
        ]);
    }

    private function resolveIdentifier(UserModel $user, string $type, ?string $identifier): string
    {
        if ($identifier !== null) {
            return $identifier;
        }

        return match ($type) {
            self::TYPE_PHONE => $user->phone_number,
            self::TYPE_EMAIL => $user->email,
            self::TYPE_PASSWORD => $user->username ?? $user->email,
            self::TYPE_ACCOUNT => $user->email,
            self::TYPE_RECOVERY => (string)$user->id,
            self::TYPE_NEWPW => $user->username ?? $user->email,
            default => (string)$user->id
        };
    }

    private function getCooldownKey(UserModel $user, string $type): string
    {
        return "verification_cooldown_{$type}_{$user->id}";
    }

    private function getAttemptsKey(UserModel $user, string $type): string
    {
        return "verification_attempts_{$type}_{$user->id}";
    }

    private function getLockoutKey(UserModel $user, string $type): string
    {
        return "verification_lockout_{$type}_{$user->id}";
    }

    private function maskIdentifier(string $identifier): string
    {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            // Mask email
            $parts = explode('@', $identifier);
            $name = $parts[0];
            $domain = $parts[1];
            $maskedName = substr($name, 0, 2) . str_repeat('*', max(0, strlen($name) - 4)) . substr($name, -2);
            return $maskedName . '@' . $domain;
        }

        // Mask phone or other identifiers
        return substr($identifier, 0, 3) . str_repeat('*', max(0, strlen($identifier) - 6)) . substr($identifier, -3);
    }

    private function validateChannelForUser(UserModel $user, string $channel, string $type = ''): array
    {
        switch ($channel) {
            case self::CHANNEL_EMAIL:
                if (!$user->email) {
                    return [
                        'valid' => false,
                        'message' => 'Email tidak tersedia. Harap pilih metode lain.'
                    ];
                }
                // Allow email verification even if not verified yet
                if ($type !== self::TYPE_EMAIL && $type !== self::TYPE_ACCOUNT && !$user->email_verified_at) {
                    return [
                        'valid' => false,
                        'message' => 'Email belum terverifikasi. Harap pilih metode lain.'
                    ];
                }
                break;

            case self::CHANNEL_WHATSAPP:
            case self::CHANNEL_SMS:
                if (!$user->phone_number) {
                    return [
                        'valid' => false,
                        'message' => 'Nomor telepon tidak tersedia. Harap pilih metode lain.'
                    ];
                }
                // Allow phone verification via WhatsApp even if not verified yet
                if ($type !== self::TYPE_PHONE && $channel === self::CHANNEL_SMS && !$user->phone_verified_at) {
                    return [
                        'valid' => false,
                        'message' => 'Nomor telepon belum terverifikasi untuk SMS. Gunakan WhatsApp atau verifikasi nomor terlebih dahulu.'
                    ];
                }
                break;
        }

        return ['valid' => true, 'message' => ''];
    }

    private function sendCodeViaChannel(UserModel $user, string $channel, string $code, string $type): bool
    {
        try {
            $result = match ($channel) {
                self::CHANNEL_WHATSAPP => $this->sendWhatsAppCode($user, $code, $type),
                self::CHANNEL_SMS => $this->sendSmsCode($user, $code, $type),
                self::CHANNEL_EMAIL => $this->sendEmailCode($user, $code, $type),
                default => false
            };

            Log::debug('Verification code sent via channel', [
                'user_id' => $user->id,
                'channel' => $channel,
                'type' => $type,
                'success' => $result
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to send verification code via channel', [
                'user_id' => $user->id,
                'channel' => $channel,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function sendWhatsAppCode(UserModel $user, string $code, string $type): bool
    {
        $message = $this->getWhatsAppMessage($code, $type);
        $result = $this->fonnteService->sendWhatsAppMessage($user->phone_number, $message);

        if (!is_array($result) || !isset($result['status'])) {
            Log::error('Invalid response from FonnteService', [
                'response' => $result
            ]);
            return false;
        }

        if (!$result['status']) {
            Log::error('Failed to send WhatsApp message', [
                'error' => $result['error'] ?? 'Unknown error',
                'response' => $result
            ]);
        }

        return $result['status'] ?? false;
    }

    private function sendSmsCode(UserModel $user, string $code, string $type): bool
    {
        $message = $this->getSMSMessage($code, $type);
        return $this->twilioService->sendSMS($user->phone_number, $message);
    }

    private function sendEmailCode(UserModel $user, string $code, string $type): bool
    {
        return match ($type) {
            self::TYPE_PASSWORD => $this->emailService->sendPasswordResetCode($user->email, $code, $user->name ?? 'User'),
            self::TYPE_ACCOUNT => $this->emailService->sendAccountVerificationCode($user->email, $code, $user->name ?? 'User'),
            self::TYPE_EMAIL => $this->emailService->sendEmailVerificationCode($user->email, $code, $user->name ?? 'User'),
            default => $this->emailService->sendVerificationCode($user->email, $code, $user->name ?? 'User', $type)
        };
    }

    public function sendNewCustomMessage(UserModel $user, string $channel, string $message, string $subject = ''): bool
    {
        return $this->sendMessageViaChannel($user, $channel, $message, $subject);
    }

    private function sendMessageViaChannel(UserModel $user, string $channel, string $message, string $subject = ''): bool
    {
        $result = match ($channel) {
            self::CHANNEL_WHATSAPP => $this->fonnteService->sendWhatsAppMessage($user->phone_number, $message),
            self::CHANNEL_SMS => $this->twilioService->sendSMS($user->phone_number, $message),
            self::CHANNEL_EMAIL => $this->emailService->sendCustomEmail($user->email, $subject, $message, $user->name ?? 'User'),
            default => ['status' => false, 'message' => 'Invalid channel']
        };

        return $result['status'] ?? false;
    }

    private function getWhatsAppMessage(string $code, string $type): string
    {
        $otpExpirationMinutes = self::OTP_EXPIRATION_MINUTES;
        $message = match ($type) {
            self::TYPE_PASSWORD => <<<EOT
    Anda telah meminta pengaturan ulang kata sandi.

    *Kode pengaturan ulang kata sandi* :
    $code

    *Perhatian!*
    Kode berlaku selama $otpExpirationMinutes menit
    Jangan berikan kode ini kepada siapapun

    Terimakasih!
    EOT,
            self::TYPE_EMAIL => <<<EOT
    Anda telah meminta verifikasi email.

    *Kode verifikasi email* :
    $code

    *Perhatian!*
    Kode berlaku selama $otpExpirationMinutes menit
    Jangan berikan kode ini kepada siapapun

    Terimakasih!
    EOT,
            self::TYPE_ACCOUNT => <<<EOT
    Anda telah meminta verifikasi akun.

    *Kode verifikasi akun* :
    $code

    *Perhatian!*
    Kode berlaku selama $otpExpirationMinutes menit
    Jangan berikan kode ini kepada siapapun

    Terimakasih!
    EOT,
            default => <<<EOT
    Anda telah meminta verifikasi.

    *Kode verifikasi* :
    $code

    *Perhatian!*
    Kode berlaku selama $otpExpirationMinutes menit
    Jangan berikan kode ini kepada siapapun

    Terimakasih!
    EOT
        };

        return str_replace('*Kode verifikasi*', "Kode verifikasi $type", $message);
    }

    private function getSMSMessage(string $code, string $type): string
    {
        $otpExpirationMinutes = self::OTP_EXPIRATION_MINUTES;
        return "Kode verifikasi Anda: $code. Berlaku $otpExpirationMinutes menit. Jangan berikan kepada siapapun.";
    }
}
