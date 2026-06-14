<?php

namespace App\Livewire\Analytics;

use App\Models\Auth\UserModel;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class UserAnalytics extends Component
{
    use WithPagination;

    public $search = '';
    public $verificationFilter = 'all';
    public $period = 'month';

    public $totalUsers = 0;
    public $newUsersThisMonth = 0;
    public $ktpVerified = 0;
    public $phoneVerified = 0;
    public $userGrowthData = [];
    public $genderDistribution = [];

    protected $queryString = ['search', 'verificationFilter'];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        \Carbon\Carbon::setLocale('id');

        $this->totalUsers = UserModel::count();

        $this->newUsersThisMonth = UserModel::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $this->ktpVerified = UserModel::where('ktp_verified', true)->count();
        $this->phoneVerified = UserModel::whereNotNull('phone_verified_at')->count();

        // Gender distribution
        $this->genderDistribution = UserModel::select('gender', DB::raw('count(*) as count'))
            ->whereNotNull('gender')
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();

        // User growth data (last 30 days)
        $days = match ($this->period) {
            'week' => 7,
            'month' => 30,
            'quarter' => 90,
            default => 30
        };

        $growthData = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = UserModel::whereDate('created_at', $date->toDateString())->count();
            $growthData->push([
                'date' => $date->translatedFormat($days <= 7 ? 'D' : 'd M'),
                'count' => $count
            ]);
        }
        $this->userGrowthData = $growthData->toArray();

        $this->dispatch(
            'statsUpdated',
            growthData: $this->userGrowthData,
            genderData: $this->genderDistribution
        );
    }

    public function updatedPeriod()
    {
        $this->loadStats();
    }

    public function getUsersProperty()
    {
        return UserModel::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('username', 'like', '%' . $this->search . '%')
                    ->orWhere('phone_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->verificationFilter === 'ktp_verified', function ($query) {
                $query->where('ktp_verified', true);
            })
            ->when($this->verificationFilter === 'ktp_pending', function ($query) {
                $query->where('ktp_verified', false)->whereNotNull('ktp_number');
            })
            ->when($this->verificationFilter === 'phone_verified', function ($query) {
                $query->whereNotNull('phone_verified_at');
            })
            ->latest()
            ->paginate(10);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.analytics.user-analytics', [
            'users' => $this->users
        ]);
    }
}
