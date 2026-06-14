<?php

namespace App\Livewire\Analytics;

use App\Models\Barang\BarangIOModel;
use App\Models\Barang\BarangKI;
use App\Models\Auth\UserModel;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class BarangIOAnalytics extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'all';
    public string $period = 'monthly';
    public ?string $startDate = null;
    public ?string $endDate = null;

    public int $totalIn = 0;
    public int $totalOut = 0;
    public int $netChange = 0;
    public float $totalValueIn = 0;
    public float $totalValueOut = 0;
    public array $topProducts = [];
    public array $topUsers = [];
    public array $flowData = [];

    protected array $queryString = ['search', 'filter', 'period'];

    public function mount(): void
    {
        $this->setDefaultDates();
        $this->loadStats();
    }

    private function setDefaultDates(): void
    {
        $dates = $this->getDateRange();
        $this->startDate = $dates['start_date']->format('Y-m-d');
        $this->endDate = $dates['end_date']->format('Y-m-d');
    }

    public function updatedPeriod(): void
    {
        $this->setDefaultDates();
        $this->loadStats();
    }

    public function updatedStartDate(): void
    {
        $this->loadStats();
    }

    public function updatedEndDate(): void
    {
        $this->loadStats();
    }

    public function loadStats(): void
    {
        $dates = $this->getDateRange();
        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];

        $this->calculateTotals($startDate, $endDate);
        $this->topProducts = $this->calculateTopProducts($startDate, $endDate);
        $this->topUsers = $this->calculateTopUsers($startDate, $endDate);
        $this->flowData = $this->calculateFlowData($startDate, $endDate);
    }

    private function getDateRange(): array
    {
        if ($this->period === 'custom' && $this->startDate && $this->endDate) {
            return [
                'start_date' => Carbon::parse($this->startDate)->startOfDay(),
                'end_date' => Carbon::parse($this->endDate)->endOfDay()
            ];
        }

        $now = now();
        switch ($this->period) {
            case 'daily':
                $startDate = $now->copy()->startOfDay();
                break;
            case 'weekly':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                break;
            case 'monthly':
                $startDate = $now->copy()->startOfMonth();
                break;
            case 'yearly':
                $startDate = $now->copy()->startOfYear();
                break;
            default:
                $startDate = $now->copy()->startOfMonth();
        }

        return [
            'start_date' => $startDate,
            'end_date' => $now->endOfDay()
        ];
    }

    private function calculateTotals($startDate, $endDate): void
    {
        // Total IN
        $inData = BarangIOModel::where('type', 'in')
            ->where('status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('SUM(quantity) as total_qty, SUM(quantity * price) as total_value')
            ->first();

        $this->totalIn = $inData->total_qty ?? 0;
        $this->totalValueIn = $inData->total_value ?? 0;

        // Total OUT
        $outData = BarangIOModel::where('type', 'out')
            ->where('status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('SUM(ABS(quantity)) as total_qty, SUM(ABS(quantity) * price) as total_value')
            ->first();

        $this->totalOut = $outData->total_qty ?? 0;
        $this->totalValueOut = $outData->total_value ?? 0;

        // Net Change
        $this->netChange = $this->totalIn - $this->totalOut;
    }

    private function calculateTopProducts($startDate, $endDate, $limit = 10): array
    {
        return BarangIOModel::selectRaw('
                barangki_id,
                SUM(ABS(quantity)) as total_movement,
                SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) as total_in,
                SUM(CASE WHEN type = "out" THEN ABS(quantity) ELSE 0 END) as total_out,
                SUM(ABS(quantity) * price) as total_value
            ')
            ->where('status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('barangKI.barang', 'barangKI.satuan')
            ->groupBy('barangki_id')
            ->orderByDesc('total_movement')
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'id' => $item->barangki_id,
                'name' => $item->barangKI?->barang?->name ?? 'Unknown',
                'sku' => $item->barangKI?->barang?->sku ?? 'N/A',
                'satuan' => $item->barangKI?->satuan?->name ?? '-',
                'total_movement' => $item->total_movement,
                'total_in' => $item->total_in,
                'total_out' => $item->total_out,
                'total_value' => $item->total_value
            ])
            ->toArray();
    }

    private function calculateTopUsers($startDate, $endDate, $limit = 10): array
    {
        return BarangIOModel::selectRaw('
                user_id,
                COUNT(*) as transaction_count,
                SUM(ABS(quantity)) as total_movement,
                SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) as total_in,
                SUM(CASE WHEN type = "out" THEN ABS(quantity) ELSE 0 END) as total_out
            ')
            ->where('status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('user')
            ->groupBy('user_id')
            ->orderByDesc('transaction_count')
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'id' => $item->user_id,
                'name' => $item->user?->name ?? 'Unknown',
                'email' => $item->user?->email ?? '-',
                'transaction_count' => $item->transaction_count,
                'total_movement' => $item->total_movement,
                'total_in' => $item->total_in,
                'total_out' => $item->total_out
            ])
            ->toArray();
    }

    private function calculateFlowData($startDate, $endDate): array
    {
        $days = $endDate->diffInDays($startDate);

        // Determine grouping format
        if ($days <= 31) {
            $groupFormat = 'daily';
        } elseif ($days <= 90) {
            $groupFormat = 'weekly';
        } else {
            $groupFormat = 'monthly';
        }

        $data = BarangIOModel::selectRaw("
                DATE(created_at) as date,
                SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END) as in_qty,
                SUM(CASE WHEN type = 'out' THEN ABS(quantity) ELSE 0 END) as out_qty
            ")
            ->where('status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return $data->map(function ($item) {
            return [
                'date' => Carbon::parse($item->date)->format('d M'),
                'in' => (int)$item->in_qty,
                'out' => (int)$item->out_qty,
                'net' => (int)($item->in_qty - $item->out_qty)
            ];
        })->toArray();
    }

    public function getTransactionsProperty()
    {
        return BarangIOModel::query()
            ->where('status', 'success')
            ->when($this->search, function ($q) {
                $q->whereHas('barangKI.barang', function ($query) {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('sku', 'like', "%{$this->search}%");
                })->orWhereHas('user', function ($query) {
                    $query->where('name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filter !== 'all', function ($q) {
                $q->where('type', $this->filter);
            })
            ->with('barangKI.barang', 'barangKI.satuan', 'user')
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.analytics.barang-io-analytics', [
            'transactions' => $this->transactions
        ]);
    }
}
