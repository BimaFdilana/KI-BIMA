<?php

namespace App\Livewire\Auth;

use App\Models\Auth\UserModel;
use App\Services\Message\VerificationService;
use Livewire\Component;

class ForgotPassword extends Component
{
    public $username = '';
    public $step = 1;
    public $user = null;
    public $shouldRedirect = false;

    protected $rules = [
        'username' => 'required|string',
    ];

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }

    public function findUser()
    {
        $this->validate();

        // Try to find user by username, email, or phone number
        $this->user = UserModel::where('username', $this->username)
            ->orWhere('email', $this->username)
            ->orWhere('phone_number', $this->username)
            ->first();

        if (!$this->user) {
            session()->flash('error', 'User not found');
            return;
        }

        // Redirect to password reset
        $this->shouldRedirect = true;
        return redirect()->route('password.reset')->with([
            'username' => $this->username
        ]);
    }
}
