<?php

namespace App\Models\Infaq;

use App\Models\Auth\UserModel;
use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoSelling;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfaqHistory extends Model
{
    use HasFactory;

    protected $table = 'infaq_histories';

    protected $fillable = [
        'toko_id',
        'user_id',
        'infaq_list_id',
        'amount',
        'status',
        'donor_name',
        'note',
        'payment_method',
        'selling_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constants untuk status
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    // Constants untuk payment method
    const PAYMENT_CASH = 'cash';
    const PAYMENT_TRANSFER = 'transfer';
    const PAYMENT_DIGITAL_WALLET = 'digital_wallet';
    const PAYMENT_QRIS = 'qris';

    // Method untuk mendapatkan semua status
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_FAILED => 'Gagal',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }

    // Method untuk mendapatkan payment methods
    public static function getPaymentMethods()
    {
        return [
            self::PAYMENT_CASH => 'Tunai',
            self::PAYMENT_TRANSFER => 'Transfer Bank',
            self::PAYMENT_DIGITAL_WALLET => 'Dompet Digital',
            self::PAYMENT_QRIS => 'QRIS',
        ];
    }

    // Relasi ke toko
    public function toko()
    {
        return $this->belongsTo(TokoModel::class);
    }

    // Relasi ke user (donatur)
    public function user()
    {
        return $this->belongsTo(UserModel::class);
    }

    // Relasi ke infaq list (pos infaq)
    public function infaqList()
    {
        return $this->belongsTo(InfaqList::class);
    }

    // Relasi ke selling (transaksi penjualan)
    public function selling()
    {
        return $this->belongsTo(TokoSelling::class, 'selling_id');
    }

    // Scope untuk status tertentu
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk donasi yang sudah selesai
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    // Scope untuk donasi pending
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // Scope berdasarkan rentang tanggal
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Scope berdasarkan bulan dan tahun
    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth('created_at', $month)
            ->whereYear('created_at', $year);
    }

    // Scope berdasarkan toko
    public function scopeByToko($query, $tokoId)
    {
        return $query->where('toko_id', $tokoId);
    }

    // Method untuk mendapatkan formatted amount
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // Method untuk mendapatkan status label
    public function getStatusLabelAttribute()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    // Method untuk mendapatkan payment method label
    public function getPaymentMethodLabelAttribute()
    {
        $methods = self::getPaymentMethods();
        return $methods[$this->payment_method] ?? ucfirst(str_replace('_', ' ', $this->payment_method));
    }

    // Method untuk mengubah status donasi
    public function markAsCompleted()
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    public function markAsFailed()
    {
        $this->update(['status' => self::STATUS_FAILED]);
    }

    public function markAsCancelled()
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    // Method untuk validasi apakah bisa diubah statusnya
    public function canChangeStatus()
    {
        return $this->status === self::STATUS_PENDING;
    }

    // Boot method untuk event handling
    protected static function boot()
    {
        parent::boot();

        // Event saat creating - set default donor name jika kosong
        static::creating(function ($infaqHistory) {
            if (empty($infaqHistory->donor_name)) {
                $infaqHistory->donor_name = 'Hamba Allah';
            }
        });
    }
}
