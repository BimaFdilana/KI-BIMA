<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\UserModel;
use App\Models\Toko\JabatanModel;
use App\Models\Toko\TokoUserModel;
use App\Services\Message\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Jenssegers\Agent\Agent;

class AuthController extends Controller
{
    protected VerificationService $verificationService;

    /**
     * Constructor to ensure all requests accept JSON and inject dependencies
     */
    public function __construct(VerificationService $verificationService)
    {
        $this->verificationService = $verificationService;

        // Apply middleware to force JSON responses
        $this->middleware(function ($request, $next) {
            // Force Accept header to application/json
            $request->headers->set('Accept', 'application/json');
            return $next($request);
        });
    }

    /**
     * Global method to handle exceptions with JSON response
     *
     * @param \Exception $e
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function handleException(\Exception $e, int $statusCode = 500): JsonResponse
    {
        Log::error('Auth Controller Exception', [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ], $statusCode);
    }

    /**
     * Register a new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => [
                    'required',
                    'string',
                    'min:3',
                    'max:50',
                    'regex:/^[a-zA-Z0-9._-]+$/',
                    Rule::unique('users', 'username')
                ],
                'phone_number' => [
                    'required',
                    'string',
                    'regex:/^(\+62|62|0)[0-9]{9,13}$/',
                    Rule::unique('users', 'phone_number')
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:255',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
                ],
                'email' => [
                    'nullable',
                    'email:rfc,dns',
                    'max:255',
                    Rule::unique('users', 'email')
                ],
                'fcm_token' => 'nullable|string',

            ], [
                'username.required' => 'Username wajib diisi',
                'username.min' => 'Username minimal 3 karakter',
                'username.max' => 'Username maksimal 50 karakter',
                'username.regex' => 'Username hanya boleh mengandung huruf, angka, titik, underscore, dan dash',
                'username.unique' => 'Username sudah digunakan',
                'phone_number.required' => 'Nomor HP wajib diisi',
                'phone_number.regex' => 'Format nomor HP tidak valid',
                'phone_number.unique' => 'Nomor HP sudah digunakan',
                'password.required' => 'Password wajib diisi',
                'password.min' => 'Password minimal 8 karakter',
                'password.max' => 'Password maksimal 255 karakter',
                'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create user in transaction
            $user = DB::transaction(function () use ($request) {
                $userData = [
                    'name' => $request->username,
                    'username' => $request->username,
                    'phone_number' => $request->phone_number,
                    'password' => Hash::make($request->password),
                ];

                // Add email if provided
                if ($request->filled('email')) {
                    $userData['email'] = $request->email;
                }

                $user = UserModel::create($userData);

                // Assign default role
                $defaultRole = config('auth.default_user_role', 'guest');
                $user->assignRole($defaultRole);

                return $user;
            });

            // Get device info
            $agent = new Agent();
            $deviceId = $this->generateDeviceId($request, $agent);
            $deviceName = $this->generateDeviceName($agent);

            // Remember device
            $this->verificationService->rememberDevice(
                $user,
                $deviceId,
                $deviceName,
                $request->userAgent(),
                now()->endOfDay(),
                $request->fcm_token,
                $request->ip()
            );

            // Send phone verification code via WhatsApp
            $verificationResult = $this->verificationService->sendVerificationCode(
                $user,
                VerificationService::TYPE_PHONE,
                VerificationService::CHANNEL_WHATSAPP,
                $deviceId
            );

            // Generate temporary token for phone verification
            $tempToken = $user->createToken('temp_phone_verification', ['verify-phone'], now()->addMinutes(10))->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => $verificationResult['success'] ? 'Pendaftaran berhasil. Silahkan verifikasi nomor anda.' : 'Pendaftaran berhasil. Gagal mengirim kode verifikasi.',
                'requires_phone_verification' => true,
                'temp_token' => $tempToken,
                'device_id' => $deviceId,
                'device_name' => $deviceName,
                'cooldown' => 120
            ], 201);
        } catch (\Exception $e) {
            $user->delete();
            return $this->handleException($e);
        }
    }

    /**
     * Verify phone number after registration
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyPhone(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|size:6|regex:/^\d{6}$/',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Check if already verified
            if ($user->phone_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor anda sudah terverifikasi'
                ], 400);
            }
            $agent = new Agent();
            $deviceId = $this->generateDeviceId($request, $agent);

            if ($this->verificationService->verifyCode($user, VerificationService::TYPE_PHONE, $request->code, $deviceId)) {
                // Revoke temporary token
                $user->tokens()->where('name', 'temp_phone_verification')->delete();

                // Generate authenticated token
                return $this->generateSuccessResponse($user, $deviceId, $request->header('User-Agent'), false);
            }

            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi tidak valid'
            ], 401);
        } catch (\Exception $e) {
            if ($e->getCode() == 1001) {
                return response()->json([
                    'success' => false,
                    'status' => 'Verification code has expired',
                    'message' => 'Kami mengirimkan code baru ke nomor telepon Anda. Silakan coba lagi.',
                    'requires_verification' => 'device',
                    'code' => 'expired'
                ], 401);
            }

            if ($e->getCode() == 429) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'requires_verification' => 'device'
                ], 429);
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'requires_verification' => 'device'
            ], 401);
        }
    }

    /**
     * Resend phone verification code
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resendPhoneVerification(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'channel' => 'required|string|in:whatsapp,sms'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Check if already verified
            if ($user->phone_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor anda sudah terverifikasi'
                ], 400);
            }

            // Check if in cooldown
            if ($this->verificationService->isInCooldown($user, VerificationService::TYPE_PHONE)) {
                $cooldown = $this->verificationService->getRemainingCooldownSeconds($user, VerificationService::TYPE_PHONE);

                return response()->json([
                    'success' => false,
                    'message' => 'Silahkan tunggu sebelum mengirim kode verifikasi lagi',
                    'cooldown' => $cooldown
                ], 429);
            }
            $agent = new Agent();
            $deviceId = $this->generateDeviceId($request, $agent);
            $result = $this->verificationService->sendVerificationCode(
                $user,
                VerificationService::TYPE_PHONE,
                $request->channel,
                $deviceId
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kode verifikasi berhasil dikirim via ' . ucfirst($request->channel),
                    'cooldown' => 120
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 500);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Attempt to login a user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'auth' => 'required|string|max:255',
                'password' => 'required|string|max:255',
                'remember' => 'boolean',
                'fcm_token' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Determine login field (email, phone, or username)
            $loginField = $this->determineLoginField($request->auth);

            // Find user
            $user = UserModel::withTrashed()->where($loginField, $request->auth)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            if ($user->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun anda ditolak'
                ], 403);
            }

            if ($user->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun anda tidak aktif'
                ], 403);
            }

            // Check if phone is verified (required for all login methods)
            if (!$user->phone_verified_at) {
                return $this->handleUnverifiedPhone($user, $request);
            }

            // Check if email login is used but email not verified
            if ($loginField === 'email' && !$user->email_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email anda tidak terverifikasi'
                ], 401);
            }

            // Attempt authentication
            $credentials = [
                $loginField => $request->auth,
                'password' => $request->password,
            ];

            if (!Auth::attempt($credentials, $request->boolean('remember', false))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kredensial tidak valid'
                ], 401);
            }

            // Get device info
            $agent = new Agent();
            $deviceId = $this->generateDeviceId($request, $agent);
            $deviceName = $this->generateDeviceName($agent);

            // Check if 2FA is required
            if ($user->two_factor_enabled) {
                return $this->handle2FARequired($user, $request, $deviceId, $deviceName);
            }

            // No 2FA required, still store device info
            $expiresAt = $request->boolean('remember', false)
                ? now()->addDays(30)
                : now()->endOfDay();

            // Save device information
            $this->verificationService->rememberDevice(
                $user,
                $deviceId,
                $deviceName,
                $request->userAgent(),
                $expiresAt,
                $request->fcm_token,
                $request->ip()
            );

            // Generate API token and return
            return $this->generateSuccessResponse($user, $deviceId, $deviceName, $request->boolean('remember', false));
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Verify 2FA code
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyDevice(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|size:6|regex:/^\d{6}$/',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terotentikasi'
                ], 401);
            }
            $agent = new Agent();
            $deviceId = $this->generateDeviceId($request, $agent);

            if ($this->verificationService->verifyCode($user, VerificationService::TYPE_DEVICE, $request->code, $deviceId)) {
                // Revoke temporary token
                $user->tokens()->where('name', 'temp_verification_token')->delete();

                // Generate authenticated token
                return $this->generateSuccessResponse($user, $deviceId, $agent->device(), false);
            }

            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi tidak valid'
            ], 401);
        } catch (\Exception $e) {
            if ($e->getCode() == 1001) {
                return response()->json([
                    'success' => false,
                    'status' => 'Verification code has expired',
                    'message' => 'Kami mengirimkan code baru ke nomor telepon Anda. Silakan coba lagi.',
                    'requires_verification' => 'device',
                    'code' => 'expired'
                ], 401);
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'requires_verification' => 'device'
            ], 401);
        }
    }

    /**
     * Verify recovery code for 2FA
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyRecoveryCode(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'recovery_code' => 'required|string|min:8|max:50',
                'device_id' => 'required|string|max:255',
                'device_name' => 'required|string|max:255',
                'remember' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terotentikasi'
                ], 401);
            }

            if ($this->verificationService->verifyRecoveryCode(
                $user,
                $request->recovery_code,
                $request->device_id,
                $request->device_name
            )) {
                // Remember device
                $expiresAt = $request->boolean('remember', false)
                    ? now()->addDays(30)
                    : now()->endOfDay();

                $this->verificationService->rememberDevice(
                    $user,
                    $request->device_id,
                    $request->device_name,
                    $request->userAgent(),
                    $expiresAt,
                    null,
                    $request->ip()
                );

                // Revoke temporary token
                $user->tokens()->where('name', 'temp_verification_token')->delete();

                // Generate authenticated token
                return $this->generateSuccessResponse(
                    $user,
                    $request->device_id,
                    $request->device_name,
                    $request->boolean('remember', false)
                );
            }

            return response()->json([
                'success' => false,
                'message' => 'Kode recovery tidak valid'
            ], 401);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Logout user (revoke token)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user && $request->user()->currentAccessToken()) {
                // Log the logout
                Log::info('User logged out', [
                    'user_id' => $user->id,
                    'token_name' => $request->user()->currentAccessToken()->name
                ]);

                // Revoke current token
                $request->user()->currentAccessToken()->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil logout'
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    // Private helper methods

    /**
     * Generate device ID
     */
    private function generateDeviceId(Request $request, Agent $agent): string
    {
        $components = [
            $agent->device() ?: 'unknown',
            $agent->platform() ?: 'unknown',
            $request->ip() ?: 'unknown',
            $request->userAgent() ?: 'unknown'
        ];

        return hash('sha256', implode('|', $components));
    }

    /**
     * Generate human-readable device name
     */
    private function generateDeviceName(Agent $agent): string
    {
        $device = $agent->device() ?: 'Unknown Device';
        $platform = $agent->platform() ?: 'Unknown OS';
        $browser = $agent->browser() ?: 'Unknown Browser';

        return "{$device} ({$platform} - {$browser})";
    }

    /**
     * Determine login field based on input format
     */
    private function determineLoginField(string $auth): string
    {
        if (filter_var($auth, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        if (preg_match('/^(\+62|62|0)[0-9]{9,13}$/', $auth)) {
            return 'phone_number';
        }

        return 'username';
    }

    /**
     * Handle unverified phone during login
     */
    private function handleUnverifiedPhone(UserModel $user, Request $request): JsonResponse
    {
        // Get device info for verification process
        $agent = new Agent();
        $deviceId = $this->generateDeviceId($request, $agent);
        $deviceName = $this->generateDeviceName($agent);

        // Generate temporary token for phone verification
        $tempToken = $user->createToken('temp_phone_verification', ['verify-phone'], now()->addMinutes(10))->plainTextToken;

        // Send verification code via WhatsApp
        $verificationResult = $this->verificationService->sendVerificationCode(
            $user,
            VerificationService::TYPE_PHONE,
            VerificationService::CHANNEL_WHATSAPP,
            $deviceId
        );

        return response()->json([
            'success' => false,
            'status' => 'Phone number is not verified',
            'message' => 'Silahkan verifikasi nomor anda terlebih dahulu.',
            'requires_phone_verification' => true,
            'temp_token' => $tempToken,
            'device_id' => $deviceId,
            'device_name' => $deviceName,
            'cooldown' => 120
        ], 401);
    }

    /**
     * Request password reset OTP
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function requestPasswordReset(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'auth' => 'required|string|max:255',
                'channel' => 'required|string|in:email,whatsapp,sms'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Determine if auth is email or phone
            $loginField = $this->determineLoginField($request->auth);

            // Find user but don't reveal if they exist or not
            $user = UserModel::withTrashed()->where($loginField, $request->auth)->first();

            // Always return success message regardless of user existence
            // This prevents user enumeration attacks
            $successMessage = match ($request->channel) {
                'email' => 'Jika akun dengan alamat email ini ada dan email sudah terverifikasi, kode reset password akan dikirim.',
                'whatsapp' => 'Jika akun dengan nomor telepon ini ada dan nomor telepon sudah terverifikasi, kode reset password akan dikirim via WhatsApp.',
                'sms' => 'Jika akun dengan nomor telepon ini ada dan nomor telepon sudah terverifikasi, kode reset password akan dikirim via SMS.',
                default => 'Jika akun dengan nomor telepon ini ada dan nomor telepon sudah terverifikasi, kode reset password akan dikirim.'
            };

            // If user exists and meets criteria, send OTP
            if ($user && !$user->trashed() && $user->status === 'active') {
                // Check verification status based on channel
                $canSendOtp = $this->canSendPasswordResetOtp($user, $request->channel, $loginField);

                if ($canSendOtp) {
                    // Check cooldown
                    if (!$this->verificationService->isInCooldown($user, VerificationService::TYPE_PASSWORD)) {
                        $result = $this->verificationService->sendPasswordResetCode($user, $request->channel, $request->auth);

                        Log::info('Password reset OTP requested', [
                            'user_id' => $user->id,
                            'channel' => $request->channel,
                            'login_field' => $loginField,
                            'success' => $result
                        ]);
                    } else {
                        // User is in cooldown, but still return success message
                        return response()->json([
                            'success' => false,
                            'message' => 'Silahkan tunggu beberapa saat sebelum mengirim kode verifikasi lagi',
                            'cooldown' => $this->verificationService->getRemainingCooldownSeconds($user, VerificationService::TYPE_PASSWORD)
                        ], 401);
                    }
                } else {
                    // User exists but channel not verified, log but don't reveal
                    return response()->json([
                        'success' => false,
                        'message' => 'Channel tidak terverifikasi',
                    ], 401);
                }
            } else {
                // User doesn't exist or is banned/suspended, log but don't reveal
                Log::info('Password reset OTP requested for non-existent/invalid user', [
                    'auth' => $this->maskIdentifier($request->auth),
                    'channel' => $request->channel,
                    'login_field' => $loginField
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'cooldown' => 120
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Verify password reset OTP only (without resetting password)
     * This is used to verify OTP before allowing user to enter new password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyPasswordResetOtp(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'auth' => 'required|string|max:255',
                'code' => 'required|string|size:6|regex:/^\d{6}$/',
            ], [
                'auth.required' => 'Email atau nomor HP wajib diisi',
                'code.required' => 'Kode OTP wajib diisi',
                'code.size' => 'Kode OTP harus 6 digit',
                'code.regex' => 'Kode OTP harus berupa angka',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Determine login field
            $loginField = $this->determineLoginField($request->auth);

            // Find user
            $user = UserModel::where($loginField, $request->auth)
                ->where('status', 'active')
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode verifikasi tidak valid'
                ], 401);
            }

            // Verify OTP using the user's identifier (username/email/phone)
            $identifier = $this->getPasswordResetIdentifier($user, $loginField);

            // Use peekCode to verify without consuming the OTP
            $verify = $this->verificationService->peekCode($user, VerificationService::TYPE_PASSWORD, $request->code, $identifier);

            if ($verify) {
                Log::info('Password reset OTP verified', [
                    'user_id' => $user->id,
                    'login_field' => $loginField
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Kode OTP valid. Silahkan masukkan password baru.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi tidak valid',
            ], 401);
        } catch (\Exception $e) {
            if ($e->getCode() == 1001) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode verifikasi sudah expired. Silahkan minta kode verifikasi baru.',
                    'code' => 'expired'
                ], 401);
            }

            if ($e->getCode() == 429) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak percobaan gagal. Silahkan coba lagi nanti.'
                ], 429);
            }

            return $this->handleException($e);
        }
    }

    /**
     * Verify password reset OTP and reset password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPasswordWithOtp(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'auth' => 'required|string|max:255',
                'code' => 'required|string|size:6|regex:/^\d{6}$/',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:255',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                    'confirmed'
                ],
            ], [
                'code.required' => 'Kode OTP wajib diisi',
                'code.size' => 'Kode OTP harus 6 digit',
                'code.regex' => 'Kode OTP harus berupa angka',
                'password.required' => 'Password baru wajib diisi',
                'password.min' => 'Password minimal 8 karakter',
                'password.max' => 'Password maksimal 255 karakter',
                'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka',
                'password.confirmed' => 'Konfirmasi password tidak cocok'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Determine login field
            $loginField = $this->determineLoginField($request->auth);

            // Find user
            $user = UserModel::where($loginField, $request->auth)
                ->where('status', 'active')
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kredensial atau kode verifikasi tidak valid'
                ], 401);
            }

            // Verify OTP using the user's identifier (username/email/phone)
            $identifier = $this->getPasswordResetIdentifier($user, $loginField);
            $verify = $this->verificationService->verifyCode($user, VerificationService::TYPE_PASSWORD, $request->code, $identifier);
            if ($verify) {
                // Update password
                $user->password = Hash::make($request->password);
                $user->save();

                // Revoke all existing tokens for security
                $user->tokens()->delete();

                // Log password reset
                Log::info('Password reset successful', [
                    'user_id' => $user->id,
                    'login_field' => $loginField
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Password berhasil direset. Silahkan login dengan password baru.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi tidak valid',
            ], 401);
        } catch (\Exception $e) {
            if ($e->getCode() == 1001) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode verifikasi sudah expired. Silahkan minta kode verifikasi baru.',
                    'code' => 'expired'
                ], 401);
            }

            if ($e->getCode() == 429) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak percobaan gagal. Silahkan coba lagi nanti.'
                ], 429);
            }

            return $this->handleException($e);
        }
    }

    /**
     * Check if password reset OTP can be sent for the given channel
     */
    private function canSendPasswordResetOtp(UserModel $user, string $channel, string $loginField): bool
    {
        switch ($channel) {
            case VerificationService::CHANNEL_EMAIL:
                // Can only send to email if:
                // 1. Login was via email AND email is verified
                // 2. OR user has verified email (regardless of login method)
                return $user->email_verified_at !== null &&
                    (($loginField === 'email' && $user->email) || $user->email);

            case VerificationService::CHANNEL_WHATSAPP:
            case VerificationService::CHANNEL_SMS:
                // Can only send to phone if:
                // 1. Login was via phone AND phone is verified
                // 2. OR user has verified phone (regardless of login method)
                return $user->phone_verified_at !== null &&
                    (($loginField === 'phone_number' && $user->phone_number) || $user->phone_number);

            default:
                return false;
        }
    }

    /**
     * Get identifier for password reset verification
     */
    private function getPasswordResetIdentifier(UserModel $user, string $loginField): string
    {
        // Use the actual verified contact method as identifier
        return match ($loginField) {
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'username' => $user->username,
            default => $user->username
        };
    }

    /**
     * Mask identifier for logging (already exists, but ensuring it's available)
     */
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

    /**
     * Handle 2FA requirement during login
     */
    private function handle2FARequired(UserModel $user, Request $request, string $deviceId, string $deviceName): JsonResponse
    {
        // Check if device is already verified
        if ($this->verificationService->isDeviceRemembered($user, $deviceId)) {
            // Update last active time for the device
            $this->verificationService->rememberDevice(
                $user,
                $deviceId,
                $deviceName,
                $request->userAgent(),
                null,
                null, // Use default expiry
                $request->ip()
            );

            // Device verified, generate API token and return
            return $this->generateSuccessResponse($user, $deviceId, $deviceName, $request->boolean('remember', false));
        }

        // Device needs verification, generate a temporary token
        $tempToken = $user->createToken('temp_verification_token', ['verify-device'], now()->addMinutes(10))->plainTextToken;

        $verificationResult = $this->verificationService->sendVerificationCode(
            $user,
            VerificationService::TYPE_DEVICE,
            VerificationService::CHANNEL_WHATSAPP,  // Default to WhatsApp channel
            $deviceId
        );

        return response()->json([
            'success' => false,
            'message' => "Kami baru saja mengirim OTP ke nomor anda",
            'requires_verification' => true,
            'temp_token' => $tempToken,
            'device_id' => $deviceId,
            'device_name' => $deviceName,
        ]);
    }

    /**
     * Validate channel availability for user
     */
    private function validateChannelForUser(UserModel $user, string $channel): array
    {
        switch ($channel) {
            case VerificationService::CHANNEL_EMAIL:
                if (!$user->email_verified_at) {
                    return [
                        'valid' => false,
                        'message' => 'Email belum terverifikasi. Silahkan pilih metode lain.'
                    ];
                }
                break;

            case VerificationService::CHANNEL_WHATSAPP:
            case VerificationService::CHANNEL_SMS:
                if (!$user->phone_verified_at) {
                    return [
                        'valid' => false,
                        'message' => 'Nomor telepon belum terverifikasi. Silahkan pilih metode lain.'
                    ];
                }
                break;
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Generate success response with token
     */
    private function generateSuccessResponse(UserModel $user, string $deviceId, string $deviceName, bool $remember): JsonResponse
    {
        try {
            // Determine token expiry
            $expiresAt = $remember ? now()->addDays(30) : now()->addDay();

            // Create token
            $token = $user->createToken(
                $deviceName,
                ['*'], // Default scopes
                $expiresAt
            )->plainTextToken;

            // Get user roles and permissions
            $roles = $user->getRoleNames();
            $permissions = $user->getAllPermissions()->pluck('name');

            // Get all shops and their positions for the user
            $shops = TokoUserModel::where('user_id', $user->id)
                ->with([
                    'toko:id,name,slug,owner_id',
                    'jabatan:id,name,level,can_invite_users,can_manage_inventory,can_view_reports,can_manage_orders',
                    'user:id,name,email,phone_number'
                ])
                ->get()
                ->map(function ($tokoUser) use ($user) {
                    return [
                        'id' => $tokoUser->toko->id,
                        'name' => $tokoUser->toko->name,
                        'slug' => $tokoUser->toko->slug,
                        'position' => [
                            'id' => $tokoUser->jabatan->id,
                            'name' => $tokoUser->jabatan->name,
                            'level' => $tokoUser->jabatan->level
                        ],
                        'access' => [
                            'can_invite_users' => $tokoUser->jabatan->can_invite_users,
                            'can_manage_inventory' => $tokoUser->jabatan->can_manage_inventory,
                            'can_view_reports' => $tokoUser->jabatan->can_view_reports,
                            'can_manage_orders' => $tokoUser->jabatan->can_manage_orders,
                        ],
                        'is_owner' => $tokoUser->toko->owner_id === $user->id,
                        'pending_invitation' => false,
                    ];
                })->toArray();

            // Get pending invitations
            $pendingInvitations = DB::table('toko_invitations')
                ->where('invited_id', $user->id)
                ->where('toko_invitations.status', 'pending')
                ->join('toko', 'toko_invitations.toko_id', '=', 'toko.id')
                ->join('jabatan', 'toko_invitations.jabatan_id', '=', 'jabatan.id')
                ->select(
                    'toko.id',
                    'toko.name',
                    'toko.slug',
                    'jabatan.id as jabatan_id',
                    'jabatan.name as jabatan_name',
                    'jabatan.level as jabatan_level',
                    'toko_invitations.id as invitation_id',
                    'jabatan.can_invite_users',
                    'jabatan.can_manage_inventory',
                    'jabatan.can_view_reports',
                    'jabatan.can_manage_orders',
                )
                ->get();

            $jumlahPendingInvitations = $pendingInvitations->count();

            foreach ($pendingInvitations as $invitation) {
                $shops[] = [
                    'id' => $invitation->id,
                    'name' => $invitation->name,
                    'slug' => $invitation->slug,
                    'position' => [
                        'id' => $invitation->jabatan_id,
                        'name' => $invitation->jabatan_name,
                        'level' => $invitation->jabatan_level
                    ],
                    'access' => [
                        'can_invite_users' => $invitation->can_invite_users,
                        'can_manage_inventory' => $invitation->can_manage_inventory,
                        'can_view_reports' => $invitation->can_view_reports,
                        'can_manage_orders' => $invitation->can_manage_orders,
                    ],
                    'is_owner' => false,
                    'pending_invitation' => true,
                    'invitation_id' => $invitation->invitation_id
                ];
            }

            $shopRequestPending = \App\Models\Toko\TokoModel::where('owner_id', $user->id)
                ->where('status', 'pending')
                ->exists();

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'token' => $token,
                'token_type' => 'Bearer',
                'device_id' => $deviceId,
                'device_name' => $deviceName,
                'expires_at' => $expiresAt->toIso8601String(),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toIso8601String() : null,
                    'phone_number' => $user->phone_number,
                    'phone_verified_at' => $user->phone_verified_at ? $user->phone_verified_at->toIso8601String() : null,
                    'two_factor_enabled' => $user->two_factor_enabled,
                    'profile_photo_url' => $user->getUserPhoto(),
                    'roles' => $roles->first(),
                    'shops' => $shops,
                    'permissions' => $permissions,
                    'shop_request_pending' => $shopRequestPending,
                    'has_pending_invitation' => $jumlahPendingInvitations > 0,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
