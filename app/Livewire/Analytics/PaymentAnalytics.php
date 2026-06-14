<?php

namespace App\Livewire\Analytics;

use App\Models\Toko\TokoPayment;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentAnalytics extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $period = 'month';

    public $totalTransactions = 0;
    public $successRate = 0;
    public $avgProcessingTime = 0;
    public $statusDistribution = [];
    public $paymentTrend = [];
    public $recentTransactions = [];

    protected $queryString = ['search', 'statusFilter'];

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

        // Total transactions
        $this->totalTransactions = TokoPayment::where('created_at', '>=', now()->subDays($days))->count();

        // Success rate
        $successCount = TokoPayment::where('created_at', '>=', now()->subDays($days))
            ->where('status', 'success')
            ->count();
        $this->successRate = $this->totalTransactions > 0
            ? round(($successCount / $this->totalTransactions) * 100, 1)
            : 0;

        // Status distribution
        $this->statusDistribution = TokoPayment::select('status', DB::raw('count(*) as count'), DB::raw('SUM(total) as total'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $item->status,
                    'count' => $item->count,
                    'total' => $item->total
                ];
            })
            ->toArray();

        // Payment trend
        $trendData = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = TokoPayment::whereDate('created_at', $date->toDateString())->count();
            $trendData->push([
                'date' => $date->format($days <= 7 ? 'D' : 'd M'),
                'count' => $count
            ]);
        }
        $this->paymentTrend = $trendData->toArray();

        // Recent transactions
        $this->recentTransactions = TokoPayment::with('toko:id,name', 'user:id,name')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'toko' => optional($payment->toko)->name ?? 'Unknown',
                    'user' => optional($payment->user)->name ?? 'Guest',
                    'total' => $payment->total,
                    'status' => $payment->status,
                    'payment_method' => $payment->payment_method,
                    'created_at' => $payment->created_at->diffForHumans()
                ];
            })
            ->toArray();
    }

    public function updatedPeriod()
    {
        $this->loadStats();
    }

    public function getPaymentsProperty()
    {
        return TokoPayment::query()
            ->when($this->search, function ($query) {
                $query->where('transaction_id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('toko', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->with('toko:id,name', 'user:id,name')
            ->latest()
            ->paginate(10);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.analytics.payment-analytics', [
            'payments' => $this->payments
        ]);
    }
}
