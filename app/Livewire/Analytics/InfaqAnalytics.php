<?php

namespace App\Livewire\Analytics;

use App\Models\Infaq\InfaqHistory;
use App\Models\Infaq\InfaqList;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InfaqAnalytics extends Component
{
    public $period = 'month';
    public $totalInfaq = 0;
    public $totalDonors = 0;
    public $activeCampaigns = 0;
    public $avgDonation = 0;
    public $infaqTrend = [];
    public $topCampaigns = [];
    public $recentDonations = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $days = match ($this->period) {
            'week' => 7,
            'month' => 30,
            'quarter' => 90,
            default => 30
        };

        try {
            // Total infaq collected
            $this->totalInfaq = InfaqHistory::where('status', 'success')
                ->where('created_at', '>=', now()->subDays($days))
                ->sum('amount');

            // Total unique donors
            $this->totalDonors = InfaqHistory::where('status', 'success')
                ->where('created_at', '>=', now()->subDays($days))
                ->distinct('user_id')
                ->count('user_id');

            // Active campaigns
            $this->activeCampaigns = InfaqList::where('is_active', true)->count();

            // Average donation
            $donationCount = InfaqHistory::where('status', 'success')
                ->where('created_at', '>=', now()->subDays($days))
                ->count();
            $this->avgDonation = $donationCount > 0 ? round($this->totalInfaq / $donationCount) : 0;

            // Infaq trend
            $trendData = collect();
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $amount = InfaqHistory::whereDate('created_at', $date->toDateString())
                    ->where('status', 'success')
                    ->sum('amount');
                $trendData->push([
                    'date' => $date->format($days <= 7 ? 'D' : 'd M'),
                    'amount' => $amount
                ]);
            }
            $this->infaqTrend = $trendData->toArray();

            // Top campaigns
            $this->topCampaigns = InfaqList::withSum(['histories as total_collected' => function ($query) {
                $query->where('status', 'success');
            }], 'amount')
                ->withCount(['histories as donor_count' => function ($query) {
                    $query->where('status', 'success');
                }])
                ->orderByDesc('total_collected')
                ->limit(5)
                ->get()
                ->map(function ($campaign) {
                    $progress = $campaign->amount_needed > 0
                        ? min(100, round(($campaign->total_collected / $campaign->amount_needed) * 100))
                        : 0;
                    return [
                        'id' => $campaign->id,
                        'title' => $campaign->title,
                        'collected' => $campaign->total_collected ?? 0,
                        'target' => $campaign->amount_needed,
                        'donors' => $campaign->donor_count ?? 0,
                        'progress' => $progress,
                        'is_active' => $campaign->is_active
                    ];
                })
                ->toArray();

            // Recent donations
            $this->recentDonations = InfaqHistory::with('user:id,name', 'infaqList:id,title')
                ->where('status', 'success')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($donation) {
                    return [
                        'id' => $donation->id,
                        'user' => optional($donation->user)->name ?? 'Anonim',
                        'campaign' => optional($donation->infaqList)->title ?? 'Unknown',
                        'amount' => $donation->amount,
                        'created_at' => $donation->created_at->diffForHumans()
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            // Tables might not exist or have different structure
            $this->totalInfaq = 0;
            $this->totalDonors = 0;
            $this->activeCampaigns = 0;
            $this->avgDonation = 0;
            $this->infaqTrend = [];
            $this->topCampaigns = [];
            $this->recentDonations = [];
        }
    }

    public function updatedPeriod()
    {
        $this->loadStats();
    }

    public function render()
    {
        return view('livewire.analytics.infaq-analytics');
    }
}
