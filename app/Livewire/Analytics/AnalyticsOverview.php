<?php

namespace App\Livewire\Analytics;

use App\Models\Auth\UserModel;
use App\Models\Barang\Barang;
use App\Models\Barang\BarangKI;
use App\Models\Infaq\InfaqHistory;
use App\Models\PakDul\PaylatterAccount;
use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoPayment;
use Carbon\Carbon;
use Livewire\Component;

class AnalyticsOverview extends Component
{
    public $period = 'month';
    public $revenueThisMonth = 0;
    public $revenueLastMonth = 0;
    public $revenueGrowth = 0;
    public $totalOrders = 0;
    public $orderGrowth = 0;
    public $activeStores = 0;
    public $totalUsers = 0;
    public $lowStockCount = 0;
    public $expiringSoonCount = 0;
    public $pendingApprovals = 0;

    protected $listeners = ['refreshAnalytics' => '$refresh'];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Revenue calculations
        $this->revenueThisMonth = TokoPayment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'success')
            ->sum('total');

        $this->revenueLastMonth = TokoPayment::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->where('status', 'success')
            ->sum('total');

        $this->revenueGrowth = $this->revenueLastMonth > 0
            ? round((($this->revenueThisMonth - $this->revenueLastMonth) / $this->revenueLastMonth) * 100, 1)
            : ($this->revenueThisMonth > 0 ? 100 : 0);

        // Orders
        $ordersThisMonth = TokoPayment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'success')
            ->count();

        $ordersLastMonth = TokoPayment::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->where('status', 'success')
            ->count();

        $this->totalOrders = $ordersThisMonth;
        $this->orderGrowth = $ordersLastMonth > 0
            ? round((($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100, 1)
            : ($ordersThisMonth > 0 ? 100 : 0);

        // Stores & Users
        $this->activeStores = TokoModel::where('status', 'active')->count();
        $this->totalUsers = UserModel::count();

        $this->lowStockCount = BarangKI::where('status', 'active')
            ->whereRaw('quantity - sold_quantity < quantity * 0.2')
            ->where('quantity', '>', 0)
            ->whereNotExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('barang_ki as bk2')
                    ->whereColumn('bk2.barang_id', 'barang_ki.barang_id')
                    ->whereColumn('bk2.satuan_id', 'barang_ki.satuan_id')
                    ->where('bk2.status', 'active')
                    ->where('bk2.quantity', '>', 0)
                    ->whereRaw('bk2.quantity - bk2.sold_quantity >= bk2.quantity * 0.2')
                    ->where(function ($q) {
                        $q->whereNull('bk2.expired_time')
                            ->orWhere('bk2.expired_time', '>', now());
                    })
                    ->whereNull('bk2.deleted_at');
            })
            ->count();

        $this->expiringSoonCount = BarangKI::where('status', 'active')
            ->where('expired_time', '<=', now()->addDays(30))
            ->where('expired_time', '>', now())
            ->whereNotExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('barang_ki as bk2')
                    ->whereColumn('bk2.barang_id', 'barang_ki.barang_id')
                    ->whereColumn('bk2.satuan_id', 'barang_ki.satuan_id')
                    ->where('bk2.status', 'active')
                    ->where('bk2.quantity', '>', 0)
                    ->where('bk2.expired_time', '>', now()->addDays(30))
                    ->whereNull('bk2.deleted_at');
            })
            ->count();

        $this->pendingApprovals = TokoModel::where('status', 'pending')->count()
            + TokoPayment::where('status', 'paid')->count()
            + UserModel::where('ktp_verified', false)->whereNotNull('ktp_number')->count();
    }

    public function render()
    {
        return view('livewire.analytics.analytics-overview');
    }
}
