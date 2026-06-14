<?php

namespace App\Livewire\Analytics;

use App\Models\Toko\TokoPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RevenueAnalytics extends Component
{
    public $period = 'week';
    public $revenueData = [];
    public $paymentMethodData = [];
    public $totalRevenue = 0;
    public $avgOrderValue = 0;
    public $topPaymentMethod = '';

    public function mount()
    {
        $this->loadRevenueData();
    }

    public function updatedPeriod()
    {
        $this->loadRevenueData();
    }

    /**
     * Format tanggal sesuai dengan tipe period
     */
    private function formatDate(Carbon $date, string $type): string
    {
        // Mapping bulan Indonesia
        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Ags',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des'
        ];

        // Mapping hari Indonesia
        $days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        return match ($type) {
            'week' => $days[$date->dayOfWeek],                    // Sen, Sel, Rab
            'month' => $date->day . ' ' . $months[$date->month],  // 11 Nov
            'quarter' => $date->day . ' ' . $months[$date->month], // 11 Nov
            'year' => $months[$date->month],                      // Nov
            default => $days[$date->dayOfWeek]
        };
    }

    public function loadRevenueData()
    {
        Carbon::setLocale('id');

        $config = match ($this->period) {
            'week' => ['days' => 7, 'interval' => 'day'],
            'month' => ['days' => 30, 'interval' => 'day'],
            'quarter' => ['days' => 90, 'interval' => 'week'],
            'year' => ['days' => 365, 'interval' => 'month'],
            default => ['days' => 7, 'interval' => 'day']
        };

        $revenueByDay = collect();

        if ($config['interval'] === 'day') {
            for ($i = $config['days'] - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $revenue = TokoPayment::whereDate('created_at', $date->toDateString())
                    ->where('status', 'success')
                    ->sum('total');
                $revenueByDay->push([
                    'date' => $this->formatDate($date, $this->period),
                    'revenue' => (int)$revenue
                ]);
            }
        } elseif ($config['interval'] === 'week') {
            for ($i = 12; $i >= 0; $i--) {
                $endDate = now()->subWeeks($i);
                $startDate = $endDate->copy()->subDays(6);
                $revenue = TokoPayment::whereBetween('created_at', [
                    $startDate->startOfDay(),
                    $endDate->endOfDay()
                ])
                    ->where('status', 'success')
                    ->sum('total');
                $revenueByDay->push([
                    'date' => $this->formatDate($startDate, $this->period),
                    'revenue' => (int)$revenue
                ]);
            }
        } elseif ($config['interval'] === 'month') {
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $revenue = TokoPayment::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', 'success')
                    ->sum('total');
                $revenueByDay->push([
                    'date' => $this->formatDate($date, $this->period),
                    'revenue' => (int)$revenue
                ]);
            }
        }

        $this->revenueData = $revenueByDay->toArray();

        // Payment method distribution
        $this->paymentMethodData = TokoPayment::select('payment_type', DB::raw('SUM(total) as total'), DB::raw('COUNT(*) as count'))
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays($config['days']))
            ->groupBy('payment_type')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'method' => $item->payment_type ?? 'Unknown',
                    'total' => (int)$item->total,
                    'count' => (int)$item->count
                ];
            })
            ->toArray();

        $this->totalRevenue = TokoPayment::where('status', 'success')
            ->where('created_at', '>=', now()->subDays($config['days']))
            ->sum('total');

        $orderCount = TokoPayment::where('status', 'success')
            ->where('created_at', '>=', now()->subDays($config['days']))
            ->count();

        $this->avgOrderValue = $orderCount > 0 ? round($this->totalRevenue / $orderCount) : 0;
        $this->topPaymentMethod = !empty($this->paymentMethodData) ? $this->paymentMethodData[0]['method'] : 'N/A';

        // Dispatch event untuk refresh chart
        $this->dispatch('revenueDataUpdated', data: $this->revenueData);
    }

    public function render()
    {
        return view('livewire.analytics.revenue-analytics');
    }
}
