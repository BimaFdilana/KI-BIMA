<?php

namespace App\Helpers;

class CurrencyHelper
{
    public static function formatNumber($number, $type = 'stock')
    {
        if ($type === 'currency') {
            // Format as currency with Rp prefix
            return 'Rp ' . number_format($number, 0, ',', '.');
        }

        // Format as stock with K/M suffix
        if ($number >= 1000000000) {
            return number_format($number / 1000000000, 1) . 'M';
        } elseif ($number >= 1000000) {
            return number_format($number / 1000000, 1) . 'Jt';
        } elseif ($number >= 1000) {
            return number_format($number / 1000, 1) . 'Rb';
        }
        return number_format($number);
    }

    public static function formatCurrency($number)
    {
        return self::formatNumber($number, 'currency');
    }

    public static function formatStock($number)
    {
        return self::formatNumber($number, 'stock');
    }
}
