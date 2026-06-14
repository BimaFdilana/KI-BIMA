<?php

namespace App\Livewire\Auth;

use App\Services\Message\VerificationService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class EmailVerification extends Component
{
    public $otp = ['', '', '', '', ''];
    public $cooldown = 0;
    public $showCooldown = false;

    public function mount()
    {
        $this->sendVerificationCode();
    }

    public function render()
    {
        return view('livewire.auth.email-verification');
    }

    public function updatedOtp($value, $key)
    {
        // Auto advance to next input
        if ($value && $key < 4) {
            $this->dispatch('focus-next', position: $key + 1);
        }

        // Auto verify when all digits are filled
        if (!in_array('', $this->otp)) {
            $this->verify();
        }
    }

    public function sendVerificationCode()
    {
        $user = Auth::user();
        $verificationService = app(VerificationService::class);

        if ($verificationService->isInCooldown($user, 'email')) {
            $this->cooldown = $verificationService->getRemainingCooldownSeconds($user, 'email');
            $this->showCooldown = true;
            $this->dispatch('start-countdown', seconds: $this->cooldown);

            session()->flash('message', 'Please wait before requesting another code.');
            return;
        }

        $result = $verificationService->sendVerificationCode($user, 'email', 'email');

        if ($result['success']) {
            $this->cooldown = 120; // 2 minutes
            $this->showCooldown = true;
            $this->dispatch('start-countdown', seconds: $this->cooldown);

            session()->flash('message', 'Verification code sent to your email');
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function verify()
    {
        $code = implode('', $this->otp);
        $user = Auth::user();
        $verificationService = app(VerificationService::class);

        if ($verificationService->verifyCode($user, 'email', $code)) {
            session()->flash('success', 'Email verified successfully!');
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
