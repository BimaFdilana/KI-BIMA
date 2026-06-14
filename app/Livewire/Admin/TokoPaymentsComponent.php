<?php

namespace App\Livewire\Admin;

use App\Models\Toko\TokoPayment;
use App\Models\Toko\TokoPaymentProgress;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use App\Services\Toko\KeranjangTokoService;

class TokoPaymentsComponent extends Component
{
    // Remove the service property - we'll resolve it when needed

    // Url parameters for maintaining state
    #[Url]
    public string $status = 'all';

    #[Url]
    public string $search = '';

    public int $page = 1;
    public int $perPage = 20;
    public bool $loading = false;
    public bool $hasMorePages = true;
    public $payments = [];

    public $selectedPayment = null;
    public $selectedEdit = null;
    public $selectedStatusAction = '';
    public $statusNote = '';
    public $statusCounts = [];

    // Properties for item selection in modal
    public $selectedItems = [];
    public $selectedItemsTotal = 0;
    public $itemSearch = '';


    // Status tabs
    public $statusTabs = [
        'all' => 'Semua',
        'pending' => 'Menunggu',
        'paid' => 'Dibayar',
        'delivery' => 'Pengiriman',
        'success' => 'Selesai',
        'failed' => 'Gagal/Batal',
    ];


    // Status groups with actions
    public $statusGroups = [
        'payment' => [
            'label' => 'Status Pembayaran',
            'actions' => [
                'pending' => 'Menunggu Pembayaran',
                'paid' => 'Sudah Dibayar',
            ]
        ],
        'order' => [
            'label' => 'Status Pesanan',
            'actions' => [
                'delivery' => 'Dalam Pengiriman',
                'success' => 'Pesanan Selesai',
                'partial_success' => 'Berhasil Sebagian',
            ]
        ],
        'cancellation' => [
            'label' => 'Pembatalan & Refund',
            'actions' => [
                'cancelled' => 'Dibatalkan',
                'refund_requested' => 'Permintaan Refund',
                'refunded' => 'Refund Selesai',
                'failed' => 'Transaksi Gagal',
            ]
        ]
    ];


    // Status mapping to help determine which group a status belongs to
    private $statusToGroupMap = [
        'pending' => 'pending',
        'paid' => 'paid',
        'delivery' => 'delivery',
        'success' => 'success',
        'partial_success' => 'success',
        'failed' => 'failed',
        'cancelled' => 'failed',
        'refund_requested' => 'failed',
        'refunded' => 'failed',
        'unknown' => 'failed',
    ];



    private $statusToGroupLabel = [
        'pending' => 'Pesanan menunggu pembayaran',
        'paid' => 'Pesanan telah dibayar',
        'delivery' => 'Pesanan dalam pengiriman',
        'success' => 'Pesanan telah selesai',
        'partial_success' => 'Pesanan berhasil sebagian',
        'failed' => 'Transaksi gagal',
        'cancelled' => 'Pesanan dibatalkan',
        'refund_requested' => 'Permintaan pengembalian dana',
        'refunded' => 'Pengembalian dana berhasil',
        'unknown' => 'Status tidak diketahui',
    ];


    public function mount()
    {
        $this->loadStatusCounts();
        $this->loadInitialPayments();
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->resetPagination();
        $this->loadInitialPayments();
    }

    public function updatedSearch()
    {
        $this->resetPagination();
        $this->loadInitialPayments();
    }

    private function resetPagination()
    {
        $this->page = 1;
        $this->payments = [];
        $this->hasMorePages = true;
    }

    private function loadInitialPayments()
    {
        $this->resetPagination();
        $this->loadPayments();
    }

    #[On('loadMore')]
    public function loadMore()
    {
        if ($this->loading || !$this->hasMorePages) {
            return;
        }

        $this->page++;
        $this->loadPayments();
    }

    private function loadPayments()
    {
        $this->loading = true;

        $query = $this->buildQuery();

        $newPayments = $query
            ->skip(($this->page - 1) * $this->perPage)
            ->take($this->perPage)
            ->get();

        if ($newPayments->isEmpty() || $newPayments->count() < $this->perPage) {
            $this->hasMorePages = false;
        }

        if ($this->page === 1) {
            $this->payments = $newPayments;
        } else {
            $this->payments = collect($this->payments)->merge($newPayments);
        }

        $this->loading = false;
    }

    private function buildQuery()
    {
        $query = TokoPayment::with([
            'user',
            'toko.owner',
            'pesanan.barangki.barang',
            'progress' => function ($q) {
                $q->with('user')->latest();
            }
        ]);

        // Filter by status if not 'all'
        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        // Apply search filter - search across all relevant fields
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('transaction_id', 'like', $searchTerm)
                    ->orWhere('total', 'like', $searchTerm)
                    ->orWhere('payment_method', 'like', $searchTerm)
                    ->orWhere('status', 'like', $searchTerm)
                    ->orWhereHas('user', function ($subq) use ($searchTerm) {
                        $subq->where('name', 'like', $searchTerm)
                            ->orWhere('username', 'like', $searchTerm)
                            ->orWhere('email', 'like', $searchTerm)
                            ->orWhere('phone_number', 'like', $searchTerm);
                    })
                    ->orWhereHas('toko', function ($subq) use ($searchTerm) {
                        $subq->where('name', 'like', $searchTerm)
                            ->orWhere('address', 'like', $searchTerm);
                    })
                    ->orWhereHas('toko.owner', function ($subq) use ($searchTerm) {
                        $subq->where('name', 'like', $searchTerm);
                    })
                    ->orWhereHas('pesanan.barangki.barang', function ($subq) use ($searchTerm) {
                        $subq->where('name', 'like', $searchTerm);
                    });
            });
        }
        $this->loadStatusCounts();
        return $query->latest('created_at');
    }

    private function loadStatusCounts()
    {
        $this->statusCounts = [
            'all' => TokoPayment::count(),
            'pending' => TokoPayment::where('status', 'pending')->count(),
            'paid' => TokoPayment::where('status', 'paid')->count(),
            'delivery' => TokoPayment::where('status', 'delivery')->count(),
            'success' => TokoPayment::where('status', 'success')->count(),
            'failed' => TokoPayment::where('status', 'failed')->count(),
        ];
    }

    public function getPaymentStatusClass($status)
    {
        return match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-blue-100 text-blue-800',
            'delivery' => 'bg-purple-100 text-purple-800',
            'success' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'partial_success' => 'bg-indigo-100 text-indigo-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            'refund_requested' => 'bg-orange-100 text-orange-800',
            'refunded' => 'bg-teal-100 text-teal-800',
            'unknown' => 'bg-gray-200 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPaymentTypeClass($payment_type)
    {
        return match ($payment_type) {
            'Cash' => 'bg-green-100 text-green-800',
            'Pakdul' => 'bg-red-100 text-red-800',
            'Virtual' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusActions($statusType)
    {
        return $this->statusGroups[$statusType]['actions'] ?? [];
    }

    public function printInvoice($paymentId)
    {
        try {
            $payment = TokoPayment::with([
                'user',
                'toko.owner',
                'pesanan.barangki.barang',
                'progress' => function ($query) {
                    $query->with('user')->latest();
                }
            ])->findOrFail($paymentId);

            // Generate PDF using DomPDF
            $pdf = DomPDF::loadView('livewire.admin.payment-invoice', compact('payment'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'Arial',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isPhpEnabled' => true,
                    'isJavascriptEnabled' => true,
                    'isFontSubsettingEnabled' => true,
                    'chroot' => base_path(),
                    'tempDir' => sys_get_temp_dir(),
                ]);

            // Return the PDF response
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->stream();
            }, 'invoice_' . $payment->transaction_id . '.pdf');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Gagal membuat invoice: ' . $e->getMessage(),
                'title' => 'Gagal!'
            ]);
        }
    }

    public function showDetail($paymentId)
    {
        try {
            $this->selectedPayment = TokoPayment::with([
                'user',
                'toko.owner',
                'pesanan.barangki.barang',
                'progress' => function ($query) {
                    $query->with('user')->latest();
                },
                'pakdulTransaksi',
                'pakdulPayments'
            ])->findOrFail($paymentId);
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Gagal memuat detail transaksi: ' . $e->getMessage(),
                'title' => 'Gagal!'
            ]);
        }
    }

    public function showEdit($paymentId)
    {
        try {
            $this->closeDetail();
            $this->selectedEdit = TokoPayment::with([
                'user',
                'toko.owner',
                'pesanan.barangki.barang',
                'progress' => function ($query) {
                    $query->with('user')->latest();
                },
                'pakdulTransaksi',
                'pakdulPayments'
            ])->findOrFail($paymentId);

            // Clear previous selections
            $this->selectedStatusAction = '';
            $this->statusNote = '';
            $this->selectedItems = [];
            $this->selectedItemsTotal = 0;
            $this->itemSearch = '';

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Gagal memuat detail transaksi: ' . $e->getMessage(),
                'title' => 'Gagal!'
            ]);
        }
    }

    public function closeDetail()
    {
        $this->dispatch('modalClosed');
        $this->selectedEdit = null;
        $this->selectedPayment = null;
        $this->selectedStatusAction = '';
        $this->statusNote = '';
        $this->selectedItems = [];
        $this->selectedItemsTotal = 0;
        $this->itemSearch = '';
    }

    public function closeEdit()
    {
        $this->selectedEdit = null;
        $this->selectedStatusAction = '';
        $this->statusNote = '';
        $this->selectedItems = [];
        $this->selectedItemsTotal = 0;
        $this->itemSearch = '';
    }

    public function toggleSelectItem($itemId)
    {
        if (in_array($itemId, $this->selectedItems)) {
            $this->selectedItems = array_diff($this->selectedItems, [$itemId]);
        } else {
            $this->selectedItems[] = $itemId;
        }
        $this->calculateSelectedTotal();
    }

    public function toggleSelectAll()
    {
        $filteredItems = $this->getFilteredItems();
        $filteredItemIds = collect($filteredItems)->pluck('id')->toArray();

        if (count(array_intersect($filteredItemIds, $this->selectedItems)) === count($filteredItemIds)) {
            $this->selectedItems = array_diff($this->selectedItems, $filteredItemIds);
        } else {
            $this->selectedItems = array_unique(array_merge($this->selectedItems, $filteredItemIds));
        }
        $this->calculateSelectedTotal();
    }

    public function calculateSelectedTotal()
    {
        if (!$this->selectedEdit) {
            $this->selectedItemsTotal = 0;
            return;
        }

        $this->selectedItemsTotal = $this->selectedEdit->pesanan
            ->whereIn('id', $this->selectedItems)
            ->sum('total');
    }

    public function getFilteredItems()
    {
        if (!$this->selectedEdit) return [];

        if (empty($this->itemSearch)) {
            return $this->selectedEdit->pesanan;
        }

        $searchTerm = strtolower($this->itemSearch);
        return $this->selectedEdit->pesanan->filter(function ($item) use ($searchTerm) {
            $name = strtolower($item->barangki->barang->name ?? '');
            $barcode = strtolower($item->barangki->id_barcode ?? '');
            return str_contains($name, $searchTerm) || str_contains($barcode, $searchTerm);
        });
    }


    public function updateStatus()
    {
        // Validate inputs
        $this->validate([
            'selectedStatusAction' => 'required|string',
            'statusNote' => 'nullable|string|max:500'
        ]);

        try {
            // Begin transaction
            \DB::beginTransaction();

            // Map the action status to TokoPayment status
            $paymentStatus = $this->getPaymentStatusFromAction($this->selectedStatusAction);
            $paymentLabel = $this->statusToGroupLabel[$this->selectedStatusAction] ?? $this->selectedStatusAction;

            $keranjangTokoService = app(KeranjangTokoService::class);
            
            // If we have selected items, we update those items
            if (!empty($this->selectedItems)) {
                $totalItems = $this->selectedEdit->pesanan->count();
                $selectedCount = count($this->selectedItems);

                // Update selected items
                foreach ($this->selectedEdit->pesanan as $item) {
                    if (in_array($item->id, $this->selectedItems)) {
                        $item->status = $paymentStatus;
                        $item->save();

                        // Log progress for individual item if needed (optional based on your schema)
                    }
                }

                // Determine overall payment status
                if ($selectedCount === $totalItems) {
                    // All items updated, update main payment status
                    $this->selectedEdit->status = $paymentStatus;
                } else {
                    // Only some items updated, set to partial_success if it's not already something else
                    $this->selectedEdit->status = 'partial_success';
                }
            } else {
                // No specific items selected, update the entire payment status
                $this->selectedEdit->status = $paymentStatus;
                
                // Update all items too for consistency
                foreach ($this->selectedEdit->pesanan as $item) {
                    $item->status = $paymentStatus;
                    $item->save();
                }
            }

            $this->selectedEdit->save();

            // Create new progress record in TokoPaymentProgress
            TokoPaymentProgress::create([
                'payment_id' => $this->selectedEdit->id,
                'status' => $this->selectedStatusAction,
                'keterangan' => $this->statusNote ?: $paymentLabel,
                'user_id' => Auth::id()
            ]);

            // Commit transaction
            \DB::commit();

            // Send notification to user about the status update
            try {
                $notificationService = app(\App\Services\Message\NotificationService::class);
                $notifLabel = $keranjangTokoService->notifLabel($this->selectedEdit->status);
                
                if ($this->selectedEdit->user) {
                    $notificationService->sendOrderStatusNotification(
                        $this->selectedEdit->user,
                        $this->selectedEdit->transaction_id,
                        $this->selectedEdit->status,
                        $notifLabel,
                        $this->selectedEdit->total,
                        Auth::user(),
                        "/historyShopping"
                    );
                }
            } catch (\Exception $notifEx) {
                // Log notification error but don't fail the transaction
                \Log::error('Failed to send status notification from admin panel: ' . $notifEx->getMessage());
            }

            // Refresh the payment data and show detail modal
            $this->showDetail($this->selectedEdit->id);
            $this->selectedEdit = null;

            // Refresh the payments list
            $this->loadInitialPayments();
            $this->loadStatusCounts();
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Status berhasil diperbarui.',
                'title' => 'Berhasil!'
            ]);
        } catch (\Exception $e) {
            // Rollback transaction on error
            \DB::rollBack();

            // Show error notification
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Gagal memperbarui status: ' . $e->getMessage(),
                'title' => 'Gagal!'
            ]);
        }
    }

    // Helper method to map action status to payment status using the status map
    private function getPaymentStatusFromAction($action)
    {
        return $this->statusToGroupMap[$action] ?? 'pending';
    }

    public function render()
    {
        return view('livewire.admin.payment-management');
    }
}
