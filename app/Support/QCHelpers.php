<?php

namespace App\Support;

class QCHelpers
{
    /**
     * Format angka dengan 2 desimal (tanpa pemisah ribuan).
     * Contoh: 1234 -> "1234.00"
     */
    public static function fmt2($v): string
    {
        if ($v === null || $v === '') return '';
        return number_format((float)$v, 2, '.', '');
    }

    /**
     * Format angka hingga 4 desimal, lalu pangkas nol buntut.
     * Contoh: 17.9000 -> "17.9", 17 -> "17"
     */
    public static function fmt4($v): string
    {
        if ($v === null || $v === '') return '';
        $s = number_format((float)$v, 4, '.', '');
        $s = rtrim($s, '0');
        $s = rtrim($s, '.');
        return $s;
    }

    /**
     * Cek apakah nilai di luar rentang min/max.
     * Nilai kosong (null/'') dianggap TIDAK gagal.
     */
    public static function outOfRange($val, $min = null, $max = null): bool
    {
        if ($val === null || $val === '') return false;
        $v = (float) $val;
        if ($min !== null && $v < (float)$min) return true;
        if ($max !== null && $v > (float)$max) return true;
        return false;
    }
}
