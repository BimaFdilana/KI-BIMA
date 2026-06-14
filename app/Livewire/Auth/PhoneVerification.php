<?php

namespace App\Livewire\Auth;

use App\Services\Message\VerificationService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class PhoneVerification extends Component
{
    public $otp = ['', '', '', '', ''];
    public $channel = 'whatsapp';
    public $cooldown = 0;
    public $showCooldown = false;

    public function mount()
    {
        $this->sendVerificationCode();
    }

    public function render()
    {
        return view('livewire.auth.phone-verification');
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

        if ($verificationService->isInCooldown($user, 'phone')) {
            $this->cooldown = $verificationService->getRemainingCooldownSeconds($user, 'phone');
            $this->showCooldown = true;
            $this->dispatch('start-countdown', seconds: $this->cooldown);

            session()->flash('message', 'Please wait before requesting another code.');
            return;
        }

        $result = $verificationService->sendVerificationCode($user, 'phone', $this->channel);

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

    public function verify()
    {
        $code = implode('', $this->otp);
        $user = Auth::user();
        $verificationService = app(VerificationService::class);

        if ($verificationService->verifyCode($user, 'phone', $code)) {
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
