<?php

namespace App\Livewire\Auth;

use App\Models\Auth\RecoveryCodeModel;
use App\Models\Auth\UserModel;
use App\Services\Message\VerificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class RecoveryCodeManager extends Component
{
    public $recoveryCodes = [];
    public $lastGeneratedDate = null;
    public $canGenerateNewCodes = false;
    public $showCodes = false;
    public $showPasswordConfirmation = false;
    public $showOtpVerification = false;
    public $password = '';
    public $otpCode = '';
    public $otpSent = false;
    public $otpChannel = 'whatsapp';
    public $passwordError = '';
    public $otpError = '';
    public $generationMessage = '';
    public $cooldown = 0;
    public $showCooldown = false;
    public $showInputOtp = false;

    protected $verificationService;

    public function boot(VerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    public function mount()
    {
        $this->loadRecoveryCodes();
        $this->checkGenerationEligibility();
        // Initialize showCodes to false explicitly
        $this->showCodes = false;
    }


    public function checkGenerationEligibility()
    {
        // If no codes exist or last generated date is more than a month ago, allow generation with password
        if (!$this->lastGeneratedDate || $this->lastGeneratedDate->addMonth()->isPast()) {
            $this->canGenerateNewCodes = true;
            $this->showPasswordConfirmation = false;
            $this->showOtpVerification = false;
        }
        // If less than a month, require OTP verification only
        elseif ($this->lastGeneratedDate && $this->lastGeneratedDate->addMonth()->isFuture()) {
            $this->canGenerateNewCodes = true;
            $this->showPasswordConfirmation = false;
            $this->showOtpVerification = false;
        }
    }

    public function toggleShowCodes()
    {
        $this->showCodes = !$this->showCodes;
        $this->loadRecoveryCodes();
    }

    // Add this method to handle the event
    public function handleShowCodesToggled()
    {
        // This will force Livewire to update the UI
        $this->showCodes = $this->showCodes;
    }

    public function initiateCodeGeneration()
    {
        if (!$this->lastGeneratedDate || $this->lastGeneratedDate->addMonth()->isPast()) {
            $this->showPasswordConfirmation = true;
            $this->showOtpVerification = false;
        } else {
            $this->showPasswordConfirmation = false;
            $this->showOtpVerification = true;
        }
    }
    public function sendOtp()
    {
        $user = Auth::user();

        // Check if cooldown is active
        if ($this->showCooldown) {
            return;
        }

        $this->showInputOtp = true;

        $result = $this->verificationService->sendVerificationCode($user, 'recovery-code', $this->otpChannel);

        if ($result['success']) {
            $this->dispatch('notify', [
                'message' => "Kode verifikasi berhasil dikirim via {$this->otpChannel}",
                'type' => 'success'
            ]);

            // Get cooldown from VerificationService
            $this->cooldown = $this->verificationService->getRemainingCooldownSeconds($user, 'recovery-code');
            $this->showCooldown = true;

            // Update the UI directly
            $this->startCooldownCountdown();
        } else {
            $this->otpError = $result['message'];

            // Get cooldown from VerificationService for error cases
            $this->cooldown = $this->verificationService->getRemainingCooldownSeconds($user, 'recovery-code');
            if ($this->cooldown > 0) {
                $this->showCooldown = true;
            }

            $this->dispatch('notify', [
                'message' => $result['message'],
                'type' => 'error'
            ]);
        }
    }

    public function verifyPassword()
    {
        $user = Auth::user();

        if (!Hash::check($this->password, $user->password)) {
            $this->passwordError = 'Incorrect password';
            return;
        }

        $this->passwordError = '';
        $this->showPasswordConfirmation = false;
        $this->generateRecoveryCodes();
    }

    public function verifyOtp()
    {
        $user = Auth::user();
        $result = $this->verificationService->verifyCode($user, 'recovery-code', $this->otpCode);

        if ($result) {
            $this->otpError = '';
            $this->showOtpVerification = false;
            $this->generateRecoveryCodes();
        } else {
            $this->otpError = 'Invalid verification code';
        }
    }

    public function generateRecoveryCodes()
    {
        $user = Auth::user();

        // First, delete all existing recovery codes
        RecoveryCodeModel::where('user_id', $user->id)->delete();

        $generatedCodes = [];

        // Generate 5 new recovery codes with new format
        for ($i = 0; $i < 5; $i++) {
            // Generate a code with format like 2123-5232-12312
            $code = implode('-', [
                mt_rand(1000, 9999),
                mt_rand(1000, 9999),
                mt_rand(10000, 99999)
            ]);

            // Store the hashed version in the database
            $recoveryCode = RecoveryCodeModel::create([
                'user_id' => $user->id,
                'code' => $code, // Hash the code for security
                'hasUsed' => 0
            ]);

            // Add to the visible codes (not hashed) for display only during generation
            $generatedCodes[] = (object)[
                'id' => $recoveryCode->id,
                'code' => $code,
                'hasUsed' => 0,
                'created_at' => $recoveryCode->created_at
            ];
        }

        // For newly generated codes, show the plaintext versions
        $this->recoveryCodes = collect($generatedCodes);
        $this->lastGeneratedDate = now();
        $this->showCodes = true; // Automatically show the new codes

        $this->generationMessage = 'New recovery codes have been generated successfully. Please save them in a secure location.';

        // Clear form fields
        $this->password = '';
        $this->otpCode = '';
        $this->otpSent = false;
        $this->passwordError = '';
        $this->otpError = '';
    }

    public function loadRecoveryCodes()
    {
        $user = Auth::user();

        // Get all non-used recovery codes
        $codes = RecoveryCodeModel::where('user_id', $user->id)
            ->where('hasUsed', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        // For existing codes, show either the actual code or masked version based on showCodes state
        $this->recoveryCodes = $codes->map(function ($code) {
            return (object)[
                'id' => $code->id,
                'code' => $this->showCodes ? $code->code : '••••-••••-•••••',
                'hasUsed' => $code->hasUsed,
                'created_at' => $code->created_at
            ];
        });

        // Get the date of the most recently generated code
        $latestCode = RecoveryCodeModel::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $this->lastGeneratedDate = $latestCode ? $latestCode->created_at : null;
    }

    public function changeOtpChannel($channel)
    {
        $this->otpChannel = $channel;
        $this->otpSent = false;
        $this->otpError = '';
    }

    public function cancelPasswordConfirmation()
    {
        $this->showPasswordConfirmation = false;
        $this->password = '';
        $this->passwordError = '';
    }

    public function cancelOtpVerification()
    {
        $this->showOtpVerification = false;
        $this->otpCode = '';
        $this->otpError = '';
        $this->otpSent = false;
        $this->showCooldown = false;
    }



    public function startCooldownCountdown()
    {
        // Update the UI directly
        $this->cooldown = $this->verificationService->getRemainingCooldownSeconds(Auth::user(), 'recovery-code');

        if ($this->cooldown > 0) {
            $this->showCooldown = true;
        } else {
            $this->showCooldown = false;
        }
    }

    public function render()
    {
        return view('livewire.auth.recovery-code-manager');
    }
}
