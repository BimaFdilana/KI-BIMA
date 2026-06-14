<?php

namespace App\Livewire\Analytics;

use App\Models\PakDul\PaylatterAccount;
use App\Models\PakDul\PaylatterTransaction;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PaylatterAnalytics extends Component
{
    public $period = 'month';
    public $totalAccounts = 0;
    public $activeAccounts = 0;
    public $totalCreditLimit = 0;
    public $totalCreditUsed = 0;
    public $overdueCount = 0;
    public $repaymentRate = 0;
    public $creditUtilization = [];
    public $accountStats = [];
    public $recentTransactions = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        try {
            // Total accounts
            $this->totalAccounts = PaylatterAccount::count();
            $this->activeAccounts = PaylatterAccount::where('status', 'active')->count();

            // Credit limits
            $this->totalCreditLimit = PaylatterAccount::where('status', 'active')->sum('credit_limit');
            $this->totalCreditUsed = PaylatterAccount::where('status', 'active')->sum('credit_used');

            // Overdue transactions
            $this->overdueCount = PaylatterTransaction::where('status', 'pending')
                ->where('due_date', '<', now())
                ->count();

            // Repayment rate (paid transactions / total completed transactions)
            $totalTransactions = PaylatterTransaction::whereIn('status', ['paid', 'overdue'])->count();
            $paidTransactions = PaylatterTransaction::where('status', 'paid')->count();
            $this->repaymentRate = $totalTransactions > 0
                ? round(($paidTransactions / $totalTransactions) * 100, 1)
                : 100;

            // Credit utilization distribution
            $accounts = PaylatterAccount::where('status', 'active')
                ->select('credit_limit', 'credit_used')
                ->get();

            $utilizationRanges = [
                '0-25%' => 0,
                '26-50%' => 0,
                '51-75%' => 0,
                '76-100%' => 0
            ];

            foreach ($accounts as $account) {
                if ($account->credit_limit > 0) {
                    $utilization = ($account->credit_used / $account->credit_limit) * 100;
                    if ($utilization <= 25) {
                        $utilizationRanges['0-25%']++;
                    } elseif ($utilization <= 50) {
                        $utilizationRanges['26-50%']++;
                    } elseif ($utilization <= 75) {
                        $utilizationRanges['51-75%']++;
                    } else {
                        $utilizationRanges['76-100%']++;
                    }
                }
            }
            $this->creditUtilization = $utilizationRanges;

            // Account status breakdown
            $this->accountStats = PaylatterAccount::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // Recent transactions
            $this->recentTransactions = PaylatterTransaction::with('paylatterAccount.user:id,name', 'paylatterAccount.toko:id,name')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($tx) {
                    return [
                        'id' => $tx->id,
                        'user' => optional(optional($tx->paylatterAccount)->user)->name ?? 'Unknown',
                        'toko' => optional(optional($tx->paylatterAccount)->toko)->name ?? 'Unknown',
                        'amount' => $tx->amount,
                        'status' => $tx->status,
                        'due_date' => $tx->due_date ? $tx->due_date->format('d M Y') : '-',
                        'is_overdue' => $tx->status === 'pending' && $tx->due_date && $tx->due_date < now()
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            // Tables might not exist
            $this->totalAccounts = 0;
            $this->activeAccounts = 0;
            $this->totalCreditLimit = 0;
            $this->totalCreditUsed = 0;
            $this->overdueCount = 0;
            $this->repaymentRate = 100;
            $this->creditUtilization = [];
            $this->accountStats = [];
            $this->recentTransactions = [];
        }
    }

    public function render()
    {
        return view('livewire.analytics.paylatter-analytics');
    }
}
