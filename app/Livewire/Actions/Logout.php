<?php

namespace App\Livewire\Actions;

use App\Models\Auth\UserDeviceModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Agent;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        $agent = new Agent();
        $deviceId = md5($agent->device() . $agent->platform() . request()->ip());


        // Delete all user devices
        UserDeviceModel::where('user_id', Auth::user()->id)->where('device_id', $deviceId)->delete();

        Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();

        return redirect('/');
    }
}
