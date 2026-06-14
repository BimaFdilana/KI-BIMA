<?php

namespace App\Livewire\Auth;

use App\Models\Auth\UserModel;
use App\Services\Message\VerificationService;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Register extends Component
{
    public $username = '';
    public $email = '';
    public $phone_number = '';
    public $password = '';
    public $password_confirmation = '';

    // Phone verification
    public $showPhoneVerificationModal = false;
    public $otp = ['', '', '', '', ''];
    public $channel = 'whatsapp';
    public $cooldown = 0;
    public $showCooldown = false;

    protected $rules = [
        'username' => 'required|string|min:3|max:50|unique:users',
        'email' => 'required|email|unique:users',
        'phone_number' => 'required|unique:users|regex:/^\+[0-9]{10,15}$/',
        'password' => 'required|min:8|confirmed',
        'password_confirmation' => 'required',
    ];

    protected $listeners = [
        'cooldownFinished' => 'cooldownFinished'
    ];

    public function render()
    {
        return view('livewire.auth.register-component');
    }

    public function register()
    {
        $this->validate();

        // Create user
        $user = UserModel::create([
            'username' => $this->username,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'password' => Hash::make($this->password),
            'two_factor_enabled' => true, // Enable 2FA by default
        ]);

        // Generate recovery code
        $verificationService = app(VerificationService::class);
        $recoveryCode = $verificationService->generateRecoveryCode($user);

        // Log the user in
        Auth::login($user);
        $user->assignRole('guest');
        // Show phone verification modal
        $this->showPhoneVerificationModal = true;
        $this->sendVerificationCode();

        session()->flash('success', 'Account created successfully! Your recovery code is: ' . $recoveryCode . '. Please save it somewhere safe.');
    }

    public function updatedOtp($value, $key)
    {
        // Auto advance to next input
        if ($value && $key < 4) {
            $this->dispatch('focus-next', position: $key + 1);
        }

        // Auto verify when all digits are filled
        if (!in_array('', $this->otp)) {
            $this->verifyOtp();
        }
    }

    public function sendVerificationCode()
    {
        $user = Auth::user();
        $verificationService = app(VerificationService::class);

        if ($verificationService->isInCooldown($user, 'phone')) {
            $this->cooldown = $verificationService->getRemainingCooldownSeconds($user, 'phone');
            $this->showCooldown = true;
            $this->dispatch('start-countdown', seconds: $this->cooldown);
            session()->flash('message', 'Please wait before requesting another code.');
            return;
        }

        $result = $verificationService->sendVerificationCode(
            $user,
            'phone',
            $this->channel
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
        $this->channel = $channel;
        $this->sendVerificationCode();
    }

    public function verifyOtp()
    {
        $code = implode('', $this->otp);
        $user = Auth::user();
        $verificationService = app(VerificationService::class);

        if ($verificationService->verifyCode($user, 'phone', $code)) {
            $this->showPhoneVerificationModal = false;

            // Generate a device ID for the current device
            $deviceId = (string) Str::uuid();
            session()->put('device_id', $deviceId);

            // Get device info
            $agent = new \Jenssegers\Agent\Agent();
            $deviceName = $agent->device() . ' (' . $agent->platform() . ')';

            // Remember this device
            $verificationService->rememberDevice(
                $user,
                $deviceId,
                $deviceName,
                request()->userAgent(),
                null,
                request()->ip()
            );

            session()->flash('success', 'Phone number verified successfully!');
            return redirect()->route('dashboard');
        } else {
            session()->flash('error', 'Invalid verification code. Please try again.');
            $this->otp = ['', '', '', '', ''];
        }
    }

    public function cooldownFinished()
    {
        $this->showCooldown = false;
    }
}
