<?php

namespace App\Livewire\Analytics;

use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoPayment;
use App\Models\Toko\TokoSelling;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class TokoAnalytics extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'revenue';
    public $sortDirection = 'desc';
    public $statusFilter = 'all';
    public $typeFilter = 'all';

    public $tokoStats = [];
    public $tokoByType = [];
    public $tokoByStatus = [];
    public $topBuyers = [];
    public $topSellers = [];

    protected $queryString = ['search', 'statusFilter', 'typeFilter'];

    public function mount()
    {
        $this->loadTokoStats();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function loadTokoStats()
    {
        // Toko by type
        $this->tokoByType = TokoModel::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Toko by status
        $this->tokoByStatus = TokoModel::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Top Buyers (stores that buy the most from KI)
        $this->topBuyers = TokoPayment::select('toko_id', DB::raw('SUM(total) as total_revenue'), DB::raw('COUNT(*) as total_orders'))
            ->where('status', 'success')
            ->whereMonth('created_at', now()->month)
            ->groupBy('toko_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->with('toko:id,name,type')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->toko_id,
                    'name' => optional($item->toko)->name ?? 'Unknown',
                    'type' => optional($item->toko)->type ?? 'N/A',
                    'revenue' => $item->total_revenue,
                    'orders' => $item->total_orders
                ];
            })
            ->toArray();

        // Top Sellers (stores that sell the most)
        $this->topSellers = TokoSelling::select('toko_id', DB::raw('SUM(total_harga) as total_revenue'), DB::raw('COUNT(*) as total_orders'))
            ->where('status', 'success')
            ->whereMonth('created_at', now()->month)
            ->groupBy('toko_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->with('toko:id,name,type')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->toko_id,
                    'name' => optional($item->toko)->name ?? 'Unknown',
                    'type' => optional($item->toko)->type ?? 'N/A',
                    'revenue' => $item->total_revenue,
                    'orders' => $item->total_orders
                ];
            })
            ->toArray();
    }

    public function getTokosProperty()
    {
        return TokoModel::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->typeFilter !== 'all', function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->withCount(['payments as total_orders' => function ($query) {
                $query->where('status', 'success');
            }])
            ->withSum(['payments as total_revenue' => function ($query) {
                $query->where('status', 'success');
            }], 'total')
            ->orderBy($this->sortBy === 'revenue' ? 'total_revenue' : ($this->sortBy === 'orders' ? 'total_orders' : 'name'), $this->sortDirection)
            ->paginate(10);
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'desc';
        }
    }

    public function render()
    {
        return view('livewire.analytics.toko-analytics', [
            'tokos' => $this->tokos
        ]);
    }
}
