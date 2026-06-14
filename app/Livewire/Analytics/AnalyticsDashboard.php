<?php

namespace App\Livewire\Analytics;

use Livewire\Component;

class AnalyticsDashboard extends Component
{
    public $activeTab = 'overview';

    protected $queryString = ['activeTab'];

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.analytics.analytics-dashboard');
    }
}
