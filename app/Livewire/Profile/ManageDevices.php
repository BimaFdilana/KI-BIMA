<?php

namespace App\Livewire\Profile;

use App\Services\Message\VerificationService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ManageDevices extends Component
{
    use WithPagination;

    public $devices = [];
    public $currentDeviceId;
    public $password = '';
    public $deviceToRemove = null;
    public $showConfirmationModal = false;
    public $page = 1;
    public $isLoading = false;

    protected $rules = [
        'password' => 'required|string',
    ];

    protected $listeners = ['refresh' => 'loadDevices'];

    public function mount()
    {
        $this->currentDeviceId = session()->get('device_id');
        $this->loadDevices();
    }

    public function render()
    {
        return view('livewire.profile.manage-devices');
    }

    public function loadDevices()
    {
        $this->isLoading = true;
        $verificationService = app(VerificationService::class);
        $this->devices = $verificationService->getUserDevices(Auth::user());
        $this->isLoading = false;
    }

    public function confirmRemoveDevice($deviceId)
    {
        $this->resetErrorBag();
        $this->password = '';
        $this->deviceToRemove = $deviceId;
        $this->showConfirmationModal = true;
    }

    public function confirmRemoveAllDevices()
    {
        $this->resetErrorBag();
        $this->password = '';
        $this->deviceToRemove = 'all';
        $this->showConfirmationModal = true;
    }

    public function removeDevice()
    {
        $this->validate();

        // Check password
        if (!Hash::check($this->password, Auth::user()->password)) {
            $this->addError('password', 'The password is incorrect.');
            return;
        }

        $verificationService = app(VerificationService::class);

        if ($this->deviceToRemove === 'all') {
            $verificationService->forgetOtherDevices(Auth::user(), $this->currentDeviceId);
            session()->flash('success', 'All other devices have been successfully logged out.');
        } else {
            // Do not allow removing the current device
            if ($this->deviceToRemove === $this->currentDeviceId) {
                session()->flash('error', 'You cannot log out your current device.');
                $this->showConfirmationModal = false;
                $this->deviceToRemove = null;
                return;
            }

            $verificationService->forgetDevice(Auth::user(), $this->deviceToRemove);
            session()->flash('success', 'Device has been successfully logged out.');
        }

        $this->password = '';
        $this->showConfirmationModal = false;
        $this->deviceToRemove = null;

        // Reload devices after successful operation
        $this->loadDevices();
    }

    public function cancelRemove()
    {
        $this->resetErrorBag();
        $this->password = '';
        $this->showConfirmationModal = false;
        $this->deviceToRemove = null;
    }

    public function gotoPage($page)
    {
        $this->page = $page;
    }
}
