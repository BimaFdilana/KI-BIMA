<?php

namespace App\Livewire\Auth;

use App\Models\Auth\UserModel;
use App\Services\Message\VerificationService;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class ResetPassword extends Component
{
    public $step = 1;
    public $username = '';
    public $otp = ['', '', '', '', ''];
    public $channel = 'whatsapp';
    public $cooldown = 0;
    public $showCooldown = false;
    public $user = null;
    public $password = '';
    public $password_confirmation = '';
    public $recovery_code = '';
    public $useRecoveryCode = false;

    protected $rules = [
        'username' => 'required|string',
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required',
        'recovery_code' => 'required_if:useRecoveryCode,true'
    ];

    protected $listeners = [
        'cooldownFinished' => 'cooldownFinished'
    ];

    public function mount()
    {
        // Check if username was passed from ForgotPassword
        if (session()->has('username')) {
            $this->username = session('username');
            $this->findUser();
        }
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }

    public function findUser()
    {
        $this->validate(['username' => 'required|string']);

        $this->user = UserModel::where('username', $this->username)
            ->orWhere('email', $this->username)
            ->orWhere('phone_number', $this->username)
            ->first();

        if (!$this->user) {
            session()->flash('error', 'User not found');
            return;
        }

        $this->step = 2;
    }

    public function toggleRecoveryCode()
    {
        $this->useRecoveryCode = !$this->useRecoveryCode;

        if (!$this->useRecoveryCode) {
            $this->sendVerificationCode();
        }
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
        if (!$this->user) {
            return redirect()->route('password.request');
        }

        $verificationService = app(VerificationService::class);

        if ($verificationService->isInCooldown($this->user, 'password')) {
            $this->cooldown = $verificationService->getRemainingCooldownSeconds($this->user, 'password');
            $this->showCooldown = true;
            $this->dispatch('start-countdown', seconds: $this->cooldown);

            session()->flash('message', 'Please wait before requesting another code.');
            return;
        }

        // Check if email channel is available
        if ($this->channel === 'email' && !$this->user->email_verified_at) {
            session()->flash('error', 'Email is not verified. Please choose another method.');
            $this->channel = 'whatsapp';
            return;
        }

        $result = $verificationService->sendVerificationCode(
            $this->user,
            'password',
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
        // Check if email is verified for email channel
        if ($channel === 'email' && !$this->user->email_verified_at) {
            session()->flash('error', 'Email is not verified');
            return;
        }

        $this->channel = $channel;
        $this->sendVerificationCode();
    }

    public function verifyOtp()
    {
        $code = implode('', $this->otp);
        $verificationService = app(VerificationService::class);

        if ($verificationService->verifyCode($this->user, 'password', $code)) {
            $this->step = 3;
        } else {
            session()->flash('error', 'Invalid verification code. Please try again.');
            $this->otp = ['', '', '', '', ''];
        }
    }

    public function verifyRecoveryCode()
    {
        $this->validate(['recovery_code' => 'required|string']);

        $verificationService = app(VerificationService::class);

        if ($verificationService->verifyRecoveryCode($this->user, $this->recovery_code)) {
            $this->step = 3;
        } else {
            session()->flash('error', 'Invalid recovery code');
        }
    }

    public function resetPassword()
    {
        $this->validate([
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $this->user->password = Hash::make($this->password);

        // Generate a new recovery code
        $verificationService = app(VerificationService::class);
        $recoveryCode = $verificationService->generateRecoveryCode($this->user);

        $this->user->save();

        session()->flash('success', 'Password reset successfully! Your new recovery code is: ' . $recoveryCode);
        return redirect()->route('login');
    }

    public function cooldownFinished()
    {
        $this->showCooldown = false;
    }
}
