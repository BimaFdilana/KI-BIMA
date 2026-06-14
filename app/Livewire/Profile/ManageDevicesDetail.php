<?php

namespace App\Livewire\Profile;

use App\Services\Message\VerificationService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ManageDevicesDetail extends Component
{
    use WithPagination;

    public $user;
    public $devices = [];
    public $page = 1;
    public $isLoading = false;

    protected $listeners = ['refresh' => 'loadDevices'];

    public function mount()
    {
        $this->loadDevices();
    }

    public function render()
    {
        return view('livewire.profile.manage-devices-detail');
    }

    public function loadDevices()
    {
        $this->isLoading = true;
        $verificationService = app(VerificationService::class);
        $this->devices = $verificationService->getUserDevices($this->user);
        $this->isLoading = false;
    }


    public function gotoPage($page)
    {
        $this->page = $page;
    }
}
