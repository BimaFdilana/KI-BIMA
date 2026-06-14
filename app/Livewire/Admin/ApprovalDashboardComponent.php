<?php

namespace App\Livewire\Admin;

use App\Models\Auth\UserModel;
use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoPayment;
use App\Events\KtpVerificationUpdated;
use App\Events\TokoVerificationUpdated;
use App\Events\PaymentVerificationUpdated;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Session;
use App\Services\Message\NotificationService;
use App\Services\Toko\TokoPaymentProgressService;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;

class ApprovalDashboardComponent extends Component
{
    use WithPagination;

    #[Validate('string')]
    public string $tab = 'overview';

    #[Validate('string')]
    public string $search = '';

    public ?int $processingId = null;
    public ?string $processingType = null;
    public ?string $processingAction = null;

    public ?UserModel $selectedKtp = null;
    public ?array $selectedToko = null;
    public ?array $selectedPayment = null;
    public ?string $modalAction = null;
    public ?string $detailModalType = null;

    #[Validate('nullable|string|min:10')]
    public string $actionReason = '';

    #[Validate('nullable|string')]
    public string $actionNote = '';

    private ?array $cachedSummary = null;

    private const TAB_SESSION_KEY = 'approval_dashboard_tab';
    private const SEARCH_SESSION_KEY = 'approval_dashboard_search';

    public function mount(): void
    {
        $this->tab = Session::get(self::TAB_SESSION_KEY, 'overview');
        $this->search = Session::get(self::SEARCH_SESSION_KEY, '');
    }

    #[On('refresh-dashboard')]
    public function refreshComponent(): void
    {
        $this->resetPage();
        $this->cachedSummary = null;
    }

    #[On('refresh-ktp-data')]
    public function refreshKtpData(): void
    {
        // Reset cached summary to update counts
        $this->cachedSummary = null;
        // Livewire 3 will automatically re-render the component
    }

    #[On('refresh-toko-data')]
    public function refreshTokoData(): void
    {
        // Reset cached summary to update counts
        $this->cachedSummary = null;
        // Livewire 3 will automatically re-render the component
    }

    #[On('refresh-payment-data')]
    public function refreshPaymentData(): void
    {
        // Reset cached summary to update counts
        $this->cachedSummary = null;
        // Livewire 3 will automatically re-render the component
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->search = '';
        $this->resetPage();
        Session::put(self::TAB_SESSION_KEY, $tab);
        Session::forget(self::SEARCH_SESSION_KEY);
    }

    public function updatedSearch(string $value): void
    {
        $this->resetPage();
        Session::put(self::SEARCH_SESSION_KEY, $value);
    }

    public function setupAction(string $type, string $action, int $id): void
    {
        $this->processingId = $id;
        $this->processingType = $type;
        $this->processingAction = $action;

        match ($type) {
            'ktp' => $this->selectedKtp = UserModel::find($id),
            'toko' => $this->selectedToko = $this->formatTokoData(
                TokoModel::with('owner', 'users', 'barangs')->find($id)
            ),
            'payment' => $this->selectedPayment = $this->formatPaymentData(
                TokoPayment::with('user', 'toko', 'progress', 'pesanan.barangki.barang')->find($id)
            ),
        };

        $this->modalAction = match ($action) {
            'verify' => 'verify',
            'approve' => 'approve',
            'confirm' => 'confirm',
            'reject' => 'reject',
            default => null,
        };

        $this->detailModalType = '';
        $this->actionReason = '';
        $this->actionNote = '';
    }

    public function showDetailModal(string $type, int $id): void
    {
        $this->detailModalType = $type;

        match ($type) {
            'ktp' => $this->selectedKtp = UserModel::find($id),
            'toko' => $this->selectedToko = $this->formatTokoData(
                TokoModel::with('owner', 'users.jabatan', 'barangs')->find($id)
            ),
            'payment' => $this->selectedPayment = $this->formatPaymentData(
                TokoPayment::with('user', 'toko', 'progress', 'pesanan.barangki.barang')->find($id)
            ),
        };
    }

    public function closeModal(): void
    {
        $this->selectedKtp = null;
        $this->selectedToko = null;
        $this->selectedPayment = null;
        $this->modalAction = null;
        $this->detailModalType = null;
        $this->processingId = null;
        $this->processingType = null;
        $this->processingAction = null;
        $this->actionReason = '';
        $this->actionNote = '';
    }

    public function verifyKtp(): void
    {
        try {
            if (!$this->selectedKtp) {
                throw new \Exception('User tidak ditemukan');
            }

            $user = $this->selectedKtp;
            $user->update([
                'ktp_verified' => true,
                'ktp_verified_at' => now(),
                'ktp_verified_by' => auth()->id(),
            ]);

            app(NotificationService::class)->sendToUserFromSystem(
                $user,
                'ktp_verified',
                [
                    'message' => 'KTP Anda telah diverifikasi dan disetujui.',
                    'status' => 'approved',
                    'note' => $this->actionNote ?: null
                ]
            );

            // Broadcast event for realtime update
            event(new KtpVerificationUpdated($user, 'verified'));

            $this->successNotification('KTP berhasil diverifikasi!');
            $this->closeModal();
            $this->dispatch('refresh-dashboard');
        } catch (\Exception $e) {
            $this->errorNotification('Gagal memverifikasi KTP: ' . $e->getMessage());
        }
    }

    public function rejectKtp(): void
    {
        $this->validate([
            'actionReason' => 'required|min:10',
        ]);

        try {
            if (!$this->selectedKtp) {
                throw new \Exception('User tidak ditemukan');
            }

            $user = $this->selectedKtp;
            $user->update([
                'ktp_verified' => false,
                'ktp_rejection_reason' => $this->actionReason,
            ]);

            app(NotificationService::class)->sendToUserFromSystem(
                $user,
                'ktp_rejected',
                [
                    'message' => 'KTP Anda ditolak. Silakan periksa alasan penolakan.',
                    'status' => 'rejected',
                    'reason' => $this->actionReason
                ]
            );

            // Broadcast event for realtime update
            event(new KtpVerificationUpdated($user, 'rejected'));

            $this->successNotification('KTP berhasil ditolak!');
            $this->closeModal();
            $this->dispatch('refresh-dashboard');
        } catch (\Exception $e) {
            $this->errorNotification('Gagal menolak KTP: ' . $e->getMessage());
        }
    }

    public function approveToko(): void
    {
        try {
            if (!$this->selectedToko) {
                throw new \Exception('Toko tidak ditemukan');
            }

            $toko = TokoModel::find($this->selectedToko['id']);
            if (!$toko) {
                throw new \Exception('Toko tidak ditemukan di database');
            }

            $toko->update([
                'verified_at' => now(),
                'verified_by' => auth()->id(),
                'status' => 'active',
            ]);

            foreach ($toko->users as $user) {
                $user->syncRoles([]);
                $user->syncPermissions([]);
                $user->assignRole('shop');
            }

            $tokoTypeName = match (strtolower($toko->type ?? '')) {
                'ki' => 'Kedai Indonesia',
                'kmp' => 'Koperasi Merah Putih',
                'pro' => 'PRO',
                default => 'Mitra',
            };

            app(NotificationService::class)->sendToUserFromSystem(
                $toko->owner,
                'toko_approved',
                [
                    'message' => "Selamat! Toko '{$toko->name}' Anda telah disetujui sebagai mitra {$tokoTypeName}.",
                    'toko_name' => $toko->name,
                    'toko_id' => $toko->id,
                    'toko_type' => $tokoTypeName
                ]
            );

            // Broadcast event for realtime update
            event(new TokoVerificationUpdated($toko, 'approved'));

            $this->successNotification('Toko berhasil disetujui!');
            $this->closeModal();
            $this->dispatch('refresh-dashboard');
        } catch (\Exception $e) {
            $this->errorNotification('Gagal menyetujui toko: ' . $e->getMessage());
        }
    }

    public function rejectToko(): void
    {
        $this->validate([
            'actionReason' => 'required|min:10',
        ]);

        try {
            if (!$this->selectedToko) {
                throw new \Exception('Toko tidak ditemukan');
            }

            $toko = TokoModel::find($this->selectedToko['id']);
            if (!$toko) {
                throw new \Exception('Toko tidak ditemukan di database');
            }

            $toko->update([
                'verified_at' => null,
                'verified_by' => auth()->id(),
                'status' => 'rejected',
                'rejection_reason' => $this->actionReason,
            ]);

            app(NotificationService::class)->sendToUserFromSystem(
                $toko->owner,
                'toko_rejected',
                [
                    'message' => "Toko '{$toko->name}' ditolak.",
                    'toko_name' => $toko->name,
                    'status' => 'rejected',
                    'reason' => $this->actionReason
                ]
            );

            // Broadcast event for realtime update
            event(new TokoVerificationUpdated($toko, 'rejected'));

            $this->successNotification('Toko berhasil ditolak!');
            $this->closeModal();
            $this->dispatch('refresh-dashboard');
        } catch (\Exception $e) {
            $this->errorNotification('Gagal menolak toko: ' . $e->getMessage());
        }
    }

    public function kirimPesanan(): void
    {
        try {
            if (!$this->selectedPayment) {
                throw new \Exception('Pembayaran tidak ditemukan');
            }

            $payment = TokoPayment::find($this->selectedPayment['id']);
            if (!$payment) {
                throw new \Exception('Pembayaran tidak ditemukan di database');
            }

            // Use service to update payment status to delivery
            $progressService = app(TokoPaymentProgressService::class);
            $progressService->updatePaymentStatus(
                paymentId: $payment->id,
                status: 'delivery',
                keterangan: $this->actionNote ?: '',
                userId: auth()->id()
            );

            // Send notification to user
            app(NotificationService::class)->sendToUserFromSystem(
                $payment->user,
                'payment_delivery',
                [
                    'message' => "Pesanan {$payment->transaction_id} sedang dikirim.",
                    'transaction_id' => $payment->transaction_id,
                    'status' => 'delivery',
                ]
            );

            // Broadcast event for realtime update
            event(new PaymentVerificationUpdated($payment->fresh(), 'delivery'));

            $this->successNotification('Pesanan berhasil dikirim!');
            $this->closeModal();
            $this->dispatch('refresh-dashboard');
        } catch (\Exception $e) {
            $this->errorNotification('Gagal mengirim pesanan: ' . $e->getMessage());
        }
    }

    public function approveRefund(): void
    {
        try {
            if (!$this->selectedPayment) {
                throw new \Exception('Pembayaran tidak ditemukan');
            }

            $payment = TokoPayment::find($this->selectedPayment['id']);
            if (!$payment) {
                throw new \Exception('Pembayaran tidak ditemukan di database');
            }

            // Verify current status is refund_requested
            if ($payment->status !== 'refund_requested') {
                throw new \Exception('Status pembayaran tidak valid untuk refund');
            }

            // Use service to update payment status to refunded
            $progressService = app(TokoPaymentProgressService::class);
            $progressService->updatePaymentStatus(
                paymentId: $payment->id,
                status: 'refunded',
                keterangan: $this->actionNote ?: 'Dana telah dikembalikan ke pelanggan',
                userId: auth()->id()
            );

            // Send notification to user
            app(NotificationService::class)->sendToUserFromSystem(
                $payment->user,
                'payment_refunded',
                [
                    'message' => "Refund untuk transaksi {$payment->transaction_id} telah diproses. Dana akan dikembalikan dalam 1-3 hari kerja.",
                    'transaction_id' => $payment->transaction_id,
                    'status' => 'refunded',
                    'amount' => $payment->total,
                ]
            );

            // Broadcast event for realtime update
            event(new PaymentVerificationUpdated($payment->fresh(), 'refunded'));

            $this->successNotification('Refund berhasil diproses!');
            $this->closeModal();
            $this->dispatch('refresh-dashboard');
        } catch (\Exception $e) {
            $this->errorNotification('Gagal memproses refund: ' . $e->getMessage());
        }
    }

    private function successNotification(string $message): void
    {
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => $message,
            'title' => 'Berhasil'
        ]);
    }

    private function errorNotification(string $message): void
    {
        $this->dispatch('show-toast', [
            'type' => 'error',
            'message' => $message,
            'title' => 'Gagal'
        ]);
    }

    private function formatTokoData($toko): array
    {
        if (!$toko) return [];

        return [
            'id' => $toko->id,
            'name' => $toko->name,
            'slug' => $toko->slug,
            'description' => $toko->description,
            'address' => $toko->address,
            'latitude' => $toko->latitude,
            'longitude' => $toko->longitude,
            'owner' => [
                'id' => $toko->owner?->id,
                'name' => $toko->owner?->name,
                'email' => $toko->owner?->email,
                'username' => $toko->owner?->username,
                'phone_number' => $toko->owner?->phone_number,
            ],
            'employees' => $toko->users->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'phone_number' => $user->phone_number,
                'jabatan' => $user->pivot->jabatan_id,
                'jabatan_name' => $user->jabatan?->name ?? 'Karyawan',
            ])->toArray(),
            'type' => $toko->type,
            'products_count' => $toko->barangs->count(),
            'employees_count' => $toko->users->count(),
            'created_at' => $toko->created_at,
            'image' => $toko->image,
        ];
    }

    private function formatPaymentData($payment): array
    {
        if (!$payment) return [];

        return [
            'id' => $payment->id,
            'transaction_id' => $payment->transaction_id,
            'total' => $payment->total,
            'payment_method' => $payment->payment_method,
            'payment_type' => $payment->payment_type,
            'status' => $payment->status,
            'snap_token' => $payment->snap_token,
            'created_at' => $payment->created_at,
            'user' => [
                'id' => $payment->user?->id,
                'name' => $payment->user?->name,
                'email' => $payment->user?->email,
                'phone_number' => $payment->user?->phone_number,
            ],
            'toko' => $payment->toko,
            'progress' => $payment->progress->map(fn($p) => [
                'id' => $p->id,
                'status' => $p->status,
                'keterangan' => $p->keterangan,
                'created_at' => $p->created_at,
                'updated_at' => $p->updated_at,
            ])->toArray(),
            'pesanan' => $payment->pesanan->map(fn($p) => [
                'id' => $p->id,
                'barangki' => $p->barangki,
                'quantity' => $p->quantity,
                'total' => $p->total,
                'status' => $p->status,
                'created_at' => $p->created_at,
            ])->toArray(),
        ];
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

    public function render()
    {
        return view('livewire.admin.admin-approval', [
            'summary' => $this->getSummary(),
            'pendingKtps' => $this->getPendingKtps(),
            'pendingTokos' => $this->getPendingTokos(),
            'pendingPayments' => $this->getPendingPayments(),
        ]);
    }

    private function getSummary(): array
    {
        return [
            'pending_ktp' => UserModel::where('ktp_verified', false)
                ->whereNotNull('ktp_image')
                ->count(),
            'pending_toko' => TokoModel::where('verified_at', null)
                ->where('status', 'pending')
                ->count(),
            'pending_payments' => TokoPayment::where('status', 'pending')
                ->count(),
            'active_toko' => TokoModel::where('status', 'active')
                ->where('verified_at', '!=', null)
                ->count(),
            'verified_users' => UserModel::where('ktp_verified', true)
                ->count(),
        ];
    }

    private function getPendingKtps()
    {
        $query = UserModel::where('ktp_verified', false)
            ->whereNotNull('ktp_image');

        if ($this->search !== '') {
            $query->where(
                fn($q) => $q
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('ktp_number', 'like', "%{$this->search}%")
                    ->orWhere('username', 'like', "%{$this->search}%")
                    ->orWhere('phone_number', 'like', "%{$this->search}%")
            );
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    private function getPendingTokos()
    {
        $query = TokoModel::with('owner', 'users')
            ->where('verified_at', null)
            ->where('status', 'pending');

        if ($this->search !== '') {
            $query->where(
                fn($q) => $q
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhereHas(
                        'owner',
                        fn($q) => $q
                            ->where('name', 'like', "%{$this->search}%")
                            ->orWhere('email', 'like', "%{$this->search}%")
                            ->orWhere('username', 'like', "%{$this->search}%")
                            ->orWhere('phone_number', 'like', "%{$this->search}%")
                    )
            );
        }

        return $query->orderBy('created_at', 'desc')->get()->map(
            fn($toko) => $this->formatTokoData($toko)
        );
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

    public function getPaymentStatusClass($status)
    {
        return match ($status) {
            'pending' => 'text-yellow-800',
            'paid' => 'text-blue-800',
            'delivery' => 'text-purple-800',
            'success' => 'text-green-800',
            'failed' => 'text-red-800',
            'refund_requested' => 'text-yellow-800',
            'refunded' => 'text-green-800',
            default => 'text-gray-800',
        };
    }

    public function getPaymentStatusIcon($status)
    {
        return match ($status) {
            'pending' => 'fas fa-clock',
            'paid' => 'fas fa-credit-card',
            'delivery' => 'fas fa-truck',
            'success' => 'fas fa-check-circle',
            'failed' => 'fas fa-times-circle',
            'refund_requested' => 'fas fa-money-bill-transfer',
            'refunded' => 'fas fa-money-bill',
            default => 'fas fa-question-circle',
        };
    }

    public function getTokoTypeClass($type)
    {
        return match (strtolower($type ?? '')) {
            'ki' => 'bg-gradient-to-r from-red-600 to-rose-600 text-white shadow-sm',
            'kmp' => 'bg-gradient-to-r from-red-500 via-white to-slate-100 text-red-900 border border-red-200 shadow-sm',
            'pro' => 'bg-gradient-to-r from-amber-500 to-yellow-400 text-white shadow-sm',
            default => 'bg-gray-100 text-gray-800',
        };
    }
    private function getPendingPayments()
    {
        $query = TokoPayment::with('user', 'toko')
            ->whereIn('status', ['paid', 'refund_requested']);

        if ($this->search !== '') {
            $query->where(
                fn($q) => $q
                    ->where('transaction_id', 'like', "%{$this->search}%")
                    ->orWhereHas(
                        'user',
                        fn($q) => $q
                            ->where('name', 'like', "%{$this->search}%")
                            ->orWhere('email', 'like', "%{$this->search}%")
                            ->orWhere('username', 'like', "%{$this->search}%")
                            ->orWhere('phone_number', 'like', "%{$this->search}%")
                    )
            );
        }



        return $query->orderBy('created_at', 'desc')->get()->map(
            fn($payment) => $this->formatPaymentData($payment)
        );
    }
}
