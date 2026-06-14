<?php

namespace App\Services\Barang;

use App\Models\Barang\BarangKI;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BarangKIService
{
    // Konstanta untuk status diskon
    const DISCOUNT_STATUS_NONE = 'none';
    const DISCOUNT_STATUS_COMING = 'coming';
    const DISCOUNT_STATUS_ACTIVE = 'active';
    const DISCOUNT_STATUS_EXPIRED = 'expired';

    public function getBarangKIPagination($search = null, $category = null)
    {
        $today = Carbon::today();
        $barangKIQuery = BarangKI::with(['barang.images']);

        if (!empty($search)) {
            $barangKIQuery->where(function ($query) use ($search) {
                $searchWords = explode(' ', $search);
                foreach ($searchWords as $word) {
                    $query->orWhereHas('barang', function ($query) use ($word) {
                        $query->where('name', 'like', "%$word%")
                            ->orWhereHas('brand', function ($query) use ($word) {
                                $query->where('name', 'like', "%$word%");
                            });
                    })
                        ->orWhereHas('satuan', function ($query) use ($word) {
                            $query->where('name', 'like', "%$word%");
                        });
                }
            });
        }

        if (!empty($category)) {
            $barangKIQuery->whereHas('barang.subcategory.category', function ($query) use ($category) {
                $query->where('name', 'like', "%$category%");
            });
        }

        $barangKIList = $barangKIQuery->get();
        $filteredBarangKI = [];
        foreach ($barangKIList as $barangKI) {
            $midExpiryDate = Carbon::parse($barangKI->expired_time)->subDays($barangKI->barang->mid_expiry_days);
            if ($today->lessThanOrEqualTo($midExpiryDate)) {
                $filteredBarangKI[] = $barangKI;
            }
        }

        $groupedBarangKI = collect($filteredBarangKI)->groupBy('barang_id')->map(function ($items) {
            return $items->unique('satuan_id');
        })->flatten();

        // Apply discount information to each item
        $groupedBarangKI = $this->applyDiscountsToCollection($groupedBarangKI);

        $perPage = 24;
        $currentPage = request()->query('page', 1);
        $paginatedBarangKI = new \Illuminate\Pagination\LengthAwarePaginator(
            $groupedBarangKI->forPage($currentPage, $perPage),
            $groupedBarangKI->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginatedBarangKI;
    }

    public function getBarangKIForShop()
    {
        $today = Carbon::today();

        $barangKIList = BarangKI::where('status', 'active')
            ->whereHas('barang', function ($query) {
                $query->where('status', 'active');
            })
            ->whereHas('satuan', function ($query) {
                $query->where('selling', 'true');
            })
            ->where('expired_time', '>=', $today)
            ->get();

        $filteredBarangKI = [];

        foreach ($barangKIList as $barangKI) {
            $midExpiryDate = Carbon::parse($barangKI->expired_time)->subDays($barangKI->barang->mid_expiry_days);
            if ($today->lessThanOrEqualTo($midExpiryDate)) {
                $filteredBarangKI[] = $barangKI;
            }
        }

        $groupedBarangKI = collect($filteredBarangKI)->groupBy('barang_id')->map(function ($items) {
            return $items->unique('satuan_id');
        })->flatten();

        // Apply discount information to each item
        $groupedBarangKI = $this->applyDiscountsToCollection($groupedBarangKI);

        return $groupedBarangKI;
    }

    public function getBarangValid($barangId, $satuanId)
    {
        $today = Carbon::today();

        $barang = BarangKI::where('barang_id', $barangId)
            ->where('satuan_id', $satuanId)
            ->where('status', 'active')
            ->whereHas('barang', function ($query) use ($today) {
                $query->whereRaw('DATE_ADD(expired_time, INTERVAL -mid_expiry_days DAY) >= ?', [$today]);
            })
            ->whereHas('satuan', function ($query) {
                $query->where('selling', 'true');
            })
            ->whereHas('barang', function ($query) {
                $query->where('status', 'active');
            })
            ->orderBy('expired_time', 'asc')
            ->first();

        // Apply discount information if barang found
        if ($barang) {
            $discountStatus = $this->getDiscountStatus($barang);
            $discountInfo = $this->calculateAdvancedDiscount($barang);

            $barang->discount_status = $discountStatus;
            $barang->discount_info = $discountInfo;
            $barang->final_price = $discountInfo['current_price'];
            $barang->is_discounted = $discountStatus === self::DISCOUNT_STATUS_ACTIVE;
        }

        return $barang;
    }

    public function getBarangKosong()
    {
        return BarangKI::join('barang', 'barang.id', '=', 'barang_ki.barang_id')
            ->join('satuan_items', 'satuan_items.id', '=', 'barang_ki.satuan_id')
            ->where('barang_ki.quantity', '<=', 0)
            ->where('barang.status', 'active')
            ->where('satuan_items.selling', true)
            ->groupBy('barang_ki.barang_id', 'barang_ki.satuan_id')
            ->get(['barang_ki.barang_id', 'barang_ki.satuan_id', DB::raw('COUNT(*) as total_barang')]);
    }

    public function getBarangExpired()
    {
        return BarangKI::join('barang', 'barang.id', '=', 'barang_ki.barang_id')
            ->join('satuan_items', 'satuan_items.id', '=', 'barang_ki.satuan_id')
            ->where('barang_ki.expired_time', '<=', Carbon::today())
            ->where('barang.status', 'active')
            ->where('satuan_items.selling', true)
            ->groupBy('barang_ki.barang_id', 'barang_ki.satuan_id')
            ->get(['barang_ki.barang_id', 'barang_ki.satuan_id', DB::raw('COUNT(*) as total_barang')]);
    }

    public function getBarangNonactive()
    {
        return BarangKI::where('expired_time', '>=', Carbon::today())
            ->where('status', '!=', 'active')
            ->whereHas('barang', function ($query) {
                $query->where('status', 'active');
            })
            ->whereHas('satuan', function ($query) {
                $query->where('selling', true);
            })
            ->distinct(['barang_id', 'satuan_id'])
            ->get();
    }

    /**
     * Calculate original price (harga jual + markup)
     * Priority: price_up > subcategory margin
     */
    private function calculateOriginalPrice(BarangKI $barang): float
    {
        $basePrice = $barang->price_sell;
        if ($basePrice <= 0) {
            return 0;
        }

        $marginValue = 0;

        if (!empty($barang->price_up) && $barang->price_up > 0) {
            $marginValue = $barang->price_up;
        } elseif (isset($barang->barang->subcategory) && !empty($barang->barang->subcategory->margin) && $barang->barang->subcategory->margin > 0) {
            $marginValue = $barang->barang->subcategory->margin;
        } else {
            return $basePrice; // No margin to apply
        }

        // Heuristik: Jika marginValue terlihat seperti harga (misal > 1000) bukan persentase.
        // Ini untuk menangani kemungkinan input '39000' padahal maksudnya margin dari harga 39000.
        if ($marginValue > 1000 && $marginValue > $basePrice) {
            // Hitung persentase margin dari nilai absolut
            $marginPercentage = (($marginValue / $basePrice) - 1) * 100;
        } else {
            $marginPercentage = $marginValue;
        }

        // Safety check: pastikan margin tidak lebih dari 100%
        if ($marginPercentage > 100) {
            $marginPercentage = 100;
        }

        $margin = $marginPercentage / 100;

        return $basePrice * (1 + $margin);
    }

   
    /**
     * Get discount status for a product
     */
    public function getDiscountStatus(BarangKI $barang): string
    {
        $now = Carbon::now();

        // Check if discount is configured
        if (!$this->isDiscountConfigured($barang)) {
            return self::DISCOUNT_STATUS_NONE;
        }

        $discountStart = Carbon::parse($barang->discount_start);
        $discountEnd = Carbon::parse($barang->discount_end);

        if ($now->lt($discountStart)) {
            return self::DISCOUNT_STATUS_COMING;
        } elseif ($now->gte($discountStart) && $now->lte($discountEnd)) {
            return self::DISCOUNT_STATUS_ACTIVE;
        } else {
            return self::DISCOUNT_STATUS_EXPIRED;
        }
    }

    /**
     * Advanced discount checker with comprehensive information
     */
    public function cekDiskonBarang(string $barcode): array
    {
        $barang = BarangKI::where('id_barcode', $barcode)->first();

        if (!$barang) {
            return [
                'success' => false,
                'status' => 'not_found',
                'message' => 'Barang tidak ditemukan',
                'data' => [
                    'barcode' => $barcode,
                    'product_name' => null,
                    'discount_status' => self::DISCOUNT_STATUS_NONE,
                    'current_price' => null,
                    'original_price' => null,
                    'discount_info' => null,
                    'schedule' => null,
                ]
            ];
        }

        $discountStatus = $this->getDiscountStatus($barang);
        $discountInfo = $this->calculateAdvancedDiscount($barang);

        return [
            'success' => true,
            'status' => 'found',
            'message' => $this->getDiscountStatusMessage($discountStatus),
            'data' => [
                'barcode' => $barcode,
                'product_name' => $barang->barang->name ?? null,
                'discount_status' => $discountStatus,
                'current_price' => $discountInfo['current_price'],
                'original_price' => $discountInfo['original_price'],
                'discount_info' => $discountInfo['discount_info'],
                'schedule' => $discountInfo['schedule'],
                'countdown' => $discountInfo['countdown'],
            ]
        ];
    }

    /**
     * Calculate advanced discount information
     */
    private function calculateAdvancedDiscount(BarangKI $barang): array
    {
        $now = Carbon::now();
        $originalPrice = $this->calculateOriginalPrice($barang);
        $discountStatus = $this->getDiscountStatus($barang);

        $result = [
            'current_price' => (int) round($originalPrice),
            'original_price' => (int) round($originalPrice),
            'price_awal' => (int) round($barang->price_sell),
            'discount_info' => null,
            'schedule' => null,
            'countdown' => null,
        ];

        if ($discountStatus === self::DISCOUNT_STATUS_NONE) {
            return $result;
        }

        // Discount schedule information
        $discountStart = Carbon::parse($barang->discount_start);
        $discountEnd = Carbon::parse($barang->discount_end);

        $result['schedule'] = [
            'start_date' => $discountStart->format('Y-m-d'),
            'start_time' => $discountStart->format('H:i:s'),
            'start_datetime' => $discountStart->format('Y-m-d H:i:s'),
            'start_formatted' => $discountStart->translatedFormat('d F Y H:i'),
            'end_date' => $discountEnd->format('Y-m-d'),
            'end_time' => $discountEnd->format('H:i:s'),
            'end_datetime' => $discountEnd->format('Y-m-d H:i:s'),
            'end_formatted' => $discountEnd->translatedFormat('d F Y H:i'),
            'duration_days' => $discountStart->diffInDays($discountEnd),
            'duration_hours' => $discountStart->diffInHours($discountEnd),
        ];

        // Calculate discounted price
        $discountedPrice = $this->calculateDiscountedPrice($barang);
        $discountPercentage = $this->calculateDiscountPercentage($barang, $discountedPrice);

        $result['discount_info'] = [
            'type' => $barang->discount_amount > 0 ? 'fixed_price' : 'percentage',
            'discount_amount' => $barang->discount_amount,
            'discount_percentage' => $barang->discount_percentage,
            'calculated_percentage' => (float) str_replace('%', '', $discountPercentage),
            'discounted_price' => (int) round($discountedPrice),
            'savings_amount' => (int) round($originalPrice - $discountedPrice),
            'savings_percentage' => $discountPercentage,
        ];

        // Set current price based on status
        if ($discountStatus === self::DISCOUNT_STATUS_ACTIVE) {
            $result['current_price'] = (int) round($discountedPrice);
        }

        // Countdown information
        $result['countdown'] = $this->getAdvancedCountdown($barang, $discountStatus);

        return $result;
    }

    /**
     * Get advanced countdown information
     */
    private function getAdvancedCountdown(BarangKI $barang, string $discountStatus): array
    {
        $now = Carbon::now();
        $discountStart = Carbon::parse($barang->discount_start);
        $discountEnd = Carbon::parse($barang->discount_end);

        $countdown = [
            'status' => $discountStatus,
            'target_date' => null,
            'target_timestamp' => null,
            'remaining_seconds' => null,
            'remaining_formatted' => null,
            'progress_percentage' => null,
        ];

        switch ($discountStatus) {
            case self::DISCOUNT_STATUS_COMING:
                $remainingSeconds = $now->diffInSeconds($discountStart);
                $countdown['target_date'] = $discountStart->format('Y-m-d H:i:s');
                $countdown['target_timestamp'] = $discountStart->timestamp;
                $countdown['remaining_seconds'] = $remainingSeconds;
                $countdown['remaining_formatted'] = $this->formatCountdownTime($remainingSeconds);
                break;

            case self::DISCOUNT_STATUS_ACTIVE:
                $remainingSeconds = $now->diffInSeconds($discountEnd);
                $totalSeconds = $discountStart->diffInSeconds($discountEnd);
                $elapsedSeconds = $discountStart->diffInSeconds($now);

                $countdown['target_date'] = $discountEnd->format('Y-m-d H:i:s');
                $countdown['target_timestamp'] = $discountEnd->timestamp;
                $countdown['remaining_seconds'] = $remainingSeconds;
                $countdown['remaining_formatted'] = $this->formatCountdownTime($remainingSeconds);
                $countdown['progress_percentage'] = $totalSeconds > 0 ? round(($elapsedSeconds / $totalSeconds) * 100, 2) : 0;
                break;

            case self::DISCOUNT_STATUS_EXPIRED:
                $expiredSeconds = $discountEnd->diffInSeconds($now);
                $countdown['remaining_seconds'] = 0;
                $countdown['remaining_formatted'] = 'Expired';
                $countdown['expired_since'] = $this->formatCountdownTime($expiredSeconds) . ' ago';
                break;
        }

        return $countdown;
    }

    /**
     * Format countdown time to human readable format
     */
    private function formatCountdownTime(int $seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        $parts = [];
        if ($days > 0) $parts[] = $days . ' hari';
        if ($hours > 0) $parts[] = $hours . ' jam';
        if ($minutes > 0) $parts[] = $minutes . ' menit';
        if ($secs > 0 && $days == 0) $parts[] = $secs . ' detik';

        return empty($parts) ? '0 detik' : implode(' ', $parts);
    }

    /**
     * Get discount status message
     */
    private function getDiscountStatusMessage(string $status): string
    {
        return match ($status) {
            self::DISCOUNT_STATUS_NONE => 'Tidak ada diskon',
            self::DISCOUNT_STATUS_COMING => 'Diskon akan segera dimulai',
            self::DISCOUNT_STATUS_ACTIVE => 'Sedang diskon',
            self::DISCOUNT_STATUS_EXPIRED => 'Diskon telah berakhir',
            default => 'Status tidak dikenali',
        };
    }

    /**
     * Apply discounts to all items in a collection of BarangKI
     */
    public function applyDiscountsToCollection($barangKICollection)
    {
        return $barangKICollection->map(function ($barang) {
            $discountStatus = $this->getDiscountStatus($barang);
            $discountInfo = $this->calculateAdvancedDiscount($barang);

            $barang->discount_status = $discountStatus;
            $barang->discount_info = $discountInfo;
            $barang->final_price = $discountInfo['current_price'];
            $barang->is_discounted = $discountStatus === self::DISCOUNT_STATUS_ACTIVE;

            return $barang;
        });
    }

    public function applyDiscountsToBarang(BarangKI $barang)
    {
        $discountStatus = $this->getDiscountStatus($barang);
        $discountInfo = $this->calculateAdvancedDiscount($barang);

        $barang->discount_status = $discountStatus;
        $barang->discount_info = $discountInfo;
        $barang->final_price = $discountInfo['current_price'];
        $barang->is_discounted = $discountStatus === self::DISCOUNT_STATUS_ACTIVE;

        return $barang;
    }
    /**
     * Get discount badge HTML based on status
     */
    public function getDiscountBadge(string $discountStatus): string
    {
        return match ($discountStatus) {
            self::DISCOUNT_STATUS_NONE => '',
            self::DISCOUNT_STATUS_COMING => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Diskon Segera</span>',
            self::DISCOUNT_STATUS_ACTIVE => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Sedang Diskon</span>',
            self::DISCOUNT_STATUS_EXPIRED => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Diskon Berakhir</span>',
            default => '',
        };
    }

    /**
     * Check if discount is properly configured
     */
    private function isDiscountConfigured(BarangKI $barang): bool
    {
        return ($barang->discount_start && $barang->discount_end) &&
            ($barang->discount_amount > 0 || $barang->discount_percentage > 0);
    }

    /**
     * Calculate the discounted price
     */
    private function calculateDiscountedPrice(BarangKI $barang): float
    {
        $originalPrice = $this->calculateOriginalPrice($barang);

        if ($barang->discount_amount > 0) {
            return $barang->discount_amount;
        } elseif ($barang->discount_percentage > 0) {
            return $originalPrice - ($originalPrice * ($barang->discount_percentage / 100));
        }

        return $originalPrice;
    }

    /**
     * Calculate the discount percentage for display
     */
    private function calculateDiscountPercentage(BarangKI $barang, float $discountedPrice): string
    {
        $originalPrice = $this->calculateOriginalPrice($barang);

        if ($discountedPrice > 0) {
            $calculatedPercentage = round((($originalPrice - $discountedPrice) / $originalPrice) * 100);
            return $calculatedPercentage . '%';
        } else {
            return round($barang->discount_percentage) . '%';
        }
    }

    /**
     * Get all products with active discounts
     */
    public function getDiscountedProducts()
    {
        $now = Carbon::now();

        $discountedProducts = BarangKI::where('status', 'active')
            ->whereNotNull('discount_start')
            ->whereNotNull('discount_end')
            ->where('discount_start', '<=', $now)
            ->where('discount_end', '>=', $now)
            ->where(function ($query) {
                $query->where('discount_amount', '>', 0)
                    ->orWhere('discount_percentage', '>', 0);
            })
            ->whereHas('barang', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        return $this->applyDiscountsToCollection($discountedProducts);
    }

    /**
     * Get products with upcoming discounts
     */
    public function getUpcomingDiscountProducts()
    {
        $now = Carbon::now();

        $upcomingProducts = BarangKI::where('status', 'active')
            ->whereNotNull('discount_start')
            ->whereNotNull('discount_end')
            ->where('discount_start', '>', $now)
            ->where(function ($query) {
                $query->where('discount_amount', '>', 0)
                    ->orWhere('discount_percentage', '>', 0);
            })
            ->whereHas('barang', function ($query) {
                $query->where('status', 'active');
            })
            ->orderBy('discount_start', 'asc')
            ->get();

        return $this->applyDiscountsToCollection($upcomingProducts);
    }

    // Keep existing methods for backward compatibility
    public function getExpiryStatus($expiredTime, $early_expiry_days, $mid_expiry_days, $late_expiry_days)
    {
        if (!$expiredTime) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">No Expiry</span>';
        }
        $now = Carbon::now();
        $expiry = Carbon::parse($expiredTime);
        $diffInDays = $now->diffInDays($expiry, false);

        if ($diffInDays > $early_expiry_days) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full justify-center text-center text-xs font-medium bg-green-100 text-green-800">Fresh</span>';
        }

        if ($diffInDays > $mid_expiry_days && $diffInDays <= $early_expiry_days) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full justify-center text-center text-xs font-medium bg-green-100 text-green-800">Early Expiry</span>';
        } elseif ($diffInDays > $late_expiry_days && $diffInDays <= $mid_expiry_days) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full justify-center text-center text-xs font-medium bg-blue-100 text-blue-800">Mid Expiry</span>';
        } elseif ($diffInDays > 0 && $diffInDays <= $late_expiry_days) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full justify-center text-center text-xs font-medium bg-yellow-100 text-yellow-800">Late Expiry</span>';
        } else {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full justify-center text-center text-xs font-medium bg-red-100 text-red-800">Expired</span>';
        }
    }

    public function getExpiryDate($expiredTime, $early_expiry_days = 365, $mid_expiry_days = 60, $late_expiry_days = 3)
    {
        if (!$expiredTime) {
            return '<span class="px-2 py-1 text-white rounded bg-gray-600">No Expiry</span>';
        }

        $now = Carbon::now();
        $expiry = Carbon::parse($expiredTime);
        $diff = $now->diff($expiry);
        $diffInDays = $now->diffInDays($expiry, false);

        $label = $diffInDays > 0 ? $this->formatTimeDifference($diff) : 'Expired';

        $color = match (true) {
            $diffInDays > $early_expiry_days => 'bg-green-600',
            $diffInDays > $mid_expiry_days => 'bg-green-600',
            $diffInDays > $late_expiry_days => 'bg-blue-600',
            $diffInDays > 0 => 'bg-yellow-600',
            default => 'bg-red-600'
        };

        return "<span class='px-2 py-1 text-white rounded $color'>$label</span>";
    }

    private function formatTimeDifference($diff)
    {
        $periods = [
            'years' => ['Tahun', 'Tahun'],
            'months' => ['Bulan', 'Bulan'],
            'weeks' => ['Minggu', 'Minggu'],
            'days' => ['Hari', 'Hari']
        ];

        foreach ($periods as $period => $labels) {
            $value = $diff->$period;
            if ($value > 0) {
                return "{$value} {$labels[0]}";
            }
        }

        $hours = $diff->h;
        if ($hours > 0) {
            return "{$hours} Jam";
        }

        if ($diff->invert) {
            return 'Expired';
        }

        return '0 Hari';
    }

    public function getStatusBadge($status)
    {
        $badges = [
            'active' => '<span class="rounded bg-blue-100 text-blue-600 hover:text-blue-600 hover:bg-blue-200 text-sm font-medium px-2 py-1.5 transition-all inline-flex space-x-1 items-center">Active</span>',
            'nonactive' => '<span class="rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 transition-all inline-flex space-x-1 items-center">Nonactive</span>',
            'sold_out' => '<span class="rounded bg-gray-100 text-gray-600 hover:text-gray-600 hover:bg-gray-200 text-sm font-medium px-2 py-1.5 transition-all inline-flex space-x-1 items-center">Sold Out</span>',
            'expired' => '<span class="rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 transition-all inline-flex space-x-1 items-center">Expired</span>',
            'discount' => '<span class="rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 transition-all inline-flex space-x-1 items-center">Discount</span>',
            'discount_ends' => '<span class="rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 transition-all inline-flex space-x-1 items-center">Discount Ends</span>',
            'deleted' => '<span class="rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 transition-all inline-flex space-x-1 items-center">Delete</span>',
            'waiting' => '<span class="rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 transition-all inline-flex space-x-1 items-center">Waiting</span>',
            'pending' => '<span class="rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 transition-all inline-flex space-x-1 items-center">Pending</span>',
        ];

        return $badges[$status] ?? $badges['nonactive'];
    }
}