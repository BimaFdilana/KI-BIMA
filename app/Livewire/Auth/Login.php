<?php

namespace App\Livewire\Auth;

use App\Models\Auth\UserModel;
use App\Services\Message\VerificationService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class Login extends Component
{
    public $username = '';
    public $password = '';
    public $remember = false;

    // Modal states
    public $showVerificationModal = false;
    public $showRecoveryCodeModal = false;

    // Device verification
    public $otp = ['', '', '', '', '', ''];
    public $channel = 'whatsapp';
    public $cooldown = 0;
    public $showCooldown = false;
    public $deviceId;
    public $deviceName;

    // Recovery code
    public $recoveryCode = '';

    protected $rules = [
        'username' => 'required|string',
        'password' => 'required|string',
    ];

    protected $listeners = [
        'cooldownFinished' => 'cooldownFinished'
    ];

    public function mount()
    {
        if (auth()->check()) {
            // Redirect ke dashboard dengan paksa tanpa fallback ke back()
            return redirect()->intended(route('dashboard'));
        }

        // Get device info
        $agent = new Agent();

        // Generate deterministic device ID based on device characteristics
        $deviceId = md5($agent->device() . $agent->platform() . request()->ip());

        // Store in session
        $this->deviceId = session()->get('device_id', $deviceId);
        session()->put('device_id', $this->deviceId);

        // Improved device name generation
        $this->deviceName = $this->generateReadableDeviceName($agent);
    }

    /**
     * Generate a more readable device name
     * 
     * @param Agent $agent
     * @return string
     */
    protected function generateReadableDeviceName(Agent $agent)
    {
        $deviceType = 'Unknown Device';
        $browser = $agent->browser();
        $platform = $agent->platform();
        $device = $agent->device();

        // Determine device type
        if ($agent->isDesktop()) {
            $deviceType = 'Computer';
        } elseif ($agent->isTablet()) {
            $deviceType = 'Tablet';
        } elseif ($agent->isPhone()) {
            $deviceType = 'Mobile Phone';
        }

        // Check if device name is available and valid
        if (!empty($device) && $device !== 'WebKit' && $device !== 'Unknown') {
            $deviceType = $device;
        }

        // Format the device name
        $deviceName = $deviceType;

        // Add platform info if available
        if (!empty($platform) && $platform !== 'Unknown') {
            $deviceName .= ' on ' . $platform;
        }

        // Add browser info
        if (!empty($browser) && $browser !== 'Unknown') {
            $deviceName .= ' (' . $browser . ')';
        }

        return $deviceName;
    }

    public function render()
    {
        return view('livewire.auth.login-component');
    }

    public function login()
    {
        $this->validate();

        // Try to find user
        $loginField = filter_var($this->username, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : (is_numeric($this->username) ? 'phone_number' : 'username');

        $user = UserModel::withTrashed()->where($loginField, $this->username)->first();

        if (!$user) {
            $this->addError('username', 'User not found');
            return;
        }

        if ($user->trashed()) {
            $this->addError('username', 'User is banned');
            return;
        }

        if ($user->status !== 'active') {
            $this->addError('username', 'User is suspended');
            return;
        }

        if (!$user->hasPermissionTo('access.dashboard')) {
            session()->flash('error', 'You do not have permission to access the dashboard');
            return redirect()->route('home');
        }

        // Attempt to authenticate
        $credentials = [
            $loginField => $this->username,
            'password' => $this->password,
        ];

        if (!Auth::attempt($credentials, $this->remember)) {
            $this->addError('password', 'Invalid credentials');
            return;
        }

        // Make sure deviceId is not null
        if (!$this->deviceId) {
            // Generate device ID if null
            $agent = new Agent();
            $this->deviceId = md5($agent->device() . $agent->platform() . request()->ip());
            session()->put('device_id', $this->deviceId);

            // Also regenerate device name
            $this->deviceName = $this->generateReadableDeviceName($agent);
        }

        $verificationService = app(VerificationService::class);

        // Check if 2FA is enabled and device verification is required
        if (Auth::user()->two_factor_enabled) {
            // Check if device is already remembered
            if ($verificationService->isDeviceRemembered(Auth::user(), $this->deviceId)) {
                // Device is already verified, proceed to dashboard
                session()->flash('success', 'Login successful');
                return redirect()->intended(route('dashboard'));
            }

            // Device not remembered, show verification modal
            $this->showVerificationModal = true;
        } else {
            // 2FA not enabled, but still remember device if requested
            if ($this->remember) {
                $verificationService->rememberDevice(
                    Auth::user(),
                    $this->deviceId,
                    $this->deviceName,
                    request()->userAgent(),
                    null,  // Use default expiration
                    null,
                    request()->ip()  // Store user's IP address
                );
            } else {
                // Still track this device but only for today
                $verificationService->rememberDevice(
                    Auth::user(),
                    $this->deviceId,
                    $this->deviceName,
                    request()->userAgent(),
                    now()->endOfDay(), // Expires at end of day
                    null,
                    request()->ip()  // Store user's IP address
                );
            }

            // Proceed to dashboard
            session()->flash('success', 'Login successful');
            return redirect()->intended(route('dashboard'));
        }
    }

    public function toggleRecoveryCodeModal()
    {
        $this->showRecoveryCodeModal = !$this->showRecoveryCodeModal;
        $this->showVerificationModal = !$this->showRecoveryCodeModal;
    }

    public function updatedOtp($value, $key)
    {
        // Auto advance to next input
        if ($value && $key < 5) {
            $this->dispatch('focus-next', position: $key + 1);
        }

        // Auto verify when all digits are filled
        if (!in_array('', $this->otp)) {
            $this->verifyCode();
        }
    }

    public function sendVerificationCode()
    {
        $user = Auth::user();
        $verificationService = app(VerificationService::class);

        if ($verificationService->isInCooldown($user, 'device')) {
            $this->cooldown = $verificationService->getRemainingCooldownSeconds($user, 'device');
            $this->showCooldown = true;
            $this->dispatch('start-countdown', seconds: $this->cooldown);
            session()->flash('message', 'Please wait before requesting another code.');
            return;
        }

        // Check if email channel is available
        if ($this->channel === 'email' && !$user->email_verified_at) {
            session()->flash('error', 'Email is not verified. Please choose another method.');
            $this->channel = 'whatsapp';
            return;
        }

        $result = $verificationService->sendVerificationCode(
            $user,
            'device',
            $this->channel,
            $this->deviceId
        );

        if ($result['success']) {
            $this->cooldown = 120; // 2 minutes
            $this->showCooldown = true;
            $this->dispatch('start-countdown', seconds: $this->cooldown);
            session()->flash('message', 'Verification code sent via ' . ucfirst($this->channel));
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function switchChannel($channel)
    {
        // Check if email is verified for email channel
        if ($channel === 'email' && !Auth::user()->email_verified_at) {
            session()->flash('error', 'Email is not verified');
            return;
        }

        $this->channel = $channel;
        $this->sendVerificationCode();
    }

    public function verifyCode()
    {
        $code = implode('', $this->otp);
        $user = Auth::user();
        $verificationService = app(VerificationService::class);

        if ($verificationService->verifyCode($user, 'device', $code, $this->deviceId)) {
            // Remember this device
            $expiresAt = $this->remember
                ? now()->addDays(30)
                : now()->endOfDay();

            $verificationService->rememberDevice(
                $user,
                $this->deviceId,
                $this->deviceName,
                request()->userAgent(),
                $expiresAt,
                null,
                request()->ip() // Store user's IP address
            );

            $this->showVerificationModal = false;
            session()->flash('success', 'Device verified successfully!');
            return redirect()->intended(route('dashboard'));
        } else {
            session()->flash('error', 'Invalid verification code. Please try again.');
            $this->otp = ['', '', '', '', '', ''];
        }
    }

    public function verifyRecoveryCode()
    {
        $this->validate(['recoveryCode' => 'required|string']);

        $user = Auth::user();
        $verificationService = app(VerificationService::class);

        if ($verificationService->verifyRecoveryCode($user, $this->recoveryCode, $this->deviceId, $this->deviceName)) {
            $this->showRecoveryCodeModal = false;

            // Remember this device
            $expiresAt = $this->remember
                ? now()->addDays(30)
                : now()->endOfDay();

            $verificationService->rememberDevice(
                $user,
                $this->deviceId,
                $this->deviceName,
                request()->userAgent(),
                $expiresAt,
                null,
                request()->ip() // Store user's IP address
            );

            session()->flash('success', 'Recovery code verified successfully!');
            return redirect()->intended(route('dashboard'));
        } else {
            session()->flash('error', 'Invalid recovery code');
        }
    }

    public function cooldownFinished()
    {
        $this->showCooldown = false;
    }
}
