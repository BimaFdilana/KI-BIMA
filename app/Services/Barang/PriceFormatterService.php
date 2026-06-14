<?php

namespace App\Services\Barang;

class PriceFormatterService
{
    public function formatPriceToRupiah($price)
    {
        return 'Rp' . number_format($price, 0, ',', '.');
    }
}
