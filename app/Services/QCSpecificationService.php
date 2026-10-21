<?php

namespace App\Services;

use App\Models\Sample;

class QCSpecificationService
{
    /**
     * Dapatkan daftar seluruh grade yang terdaftar di konfigurasi.
     *
     * @return string[]
     */
    public static function getGrades(): array
    {
        return array_keys(config('qc.materials', []));
    }

    /**
     * Dapatkan seluruh data materials (mapping grade, standard, alias, label).
     */
    public static function getMaterials(): array
    {
        return config('qc.materials', []);
    }

    /**
     * Ambil metadata satu material berdasarkan grade (aman dari dot-notation).
     */
    public static function getMaterial(?string $grade): ?array
    {
        if (empty($grade)) {
            return null;
        }
        $materials = config('qc.materials', []);
        return $materials[$grade] ?? null;
    }

    /**
     * Ambil standard material berdasarkan grade.
     */
    public static function getStandardForGrade(?string $grade): string
    {
        $mat = self::getMaterial($grade);
        return $mat['standard'] ?? 'ASTM A351';
    }

    /**
     * Periksa apakah grade terdaftar di konfigurasi.
     */
    public static function isValidGrade(?string $grade): bool
    {
        if (empty($grade)) {
            return false;
        }
        return array_key_exists($grade, config('qc.materials', []));
    }

    /**
     * Ambil urutan baku parameter kimia untuk tabel (12 elemen).
     *
     * @return string[]
     */
    public static function getChemDisplayOrder(): array
    {
        return config('qc.chem_display_order', [
            'c', 'si', 'mn', 'p', 's', 'cr', 'ni', 'mo', 'cu', 'co', 'al', 'v'
        ]);
    }

    /**
     * Ambil label elemen kimia (misal: 'Carbon (C)').
     */
    public static function getChemLabel(string $element): string
    {
        $labels = config('qc.labels.chem', []);
        return $labels[$element] ?? strtoupper($element);
    }

    /**
     * Ambil satuan elemen kimia (default: '% wt').
     */
    public static function getChemUnit(): string
    {
        return config('qc.units.chem', '% wt');
    }

    /**
     * Ambil label parameter mekanik (misal: 'Yield Strength').
     */
    public static function getMechLabel(string $param): string
    {
        $labels = config('qc.labels.mech', []);
        return $labels[$param] ?? $param;
    }

    /**
     * Ambil satuan parameter mekanik (misal: 'MPa', '%', 'HB').
     */
    public static function getMechUnit(string $param): string
    {
        $units = config('qc.units.mech', []);
        return $units[$param] ?? '';
    }

    /**
     * Ambil seluruh material dikelompokkan berdasarkan Standard (untuk Standard Reference view).
     */
    public static function getMaterialsGroupedByStandard(): array
    {
        $materials = config('qc.materials', []);
        $grouped = [];
        foreach ($materials as $grade => $meta) {
            $std = $meta['standard'] ?? 'Other';
            if (!isset($grouped[$std])) {
                $grouped[$std] = [];
            }
            $grouped[$std][$grade] = array_merge($meta, [
                'limits' => self::getLimitsForGrade($grade)
            ]);
        }
        return $grouped;
    }

    /**
     * Ambil spesifikasi limits (chem & mech) untuk grade tertentu.
     * Aman dari dot-notation untuk key seperti '1.4308'.
     */
    public static function getLimitsForGrade(?string $grade): array
    {
        if (empty($grade)) {
            return ['chem' => [], 'mech' => []];
        }
        $paramsAll = config('qc.params', []);
        return $paramsAll[$grade] ?? ['chem' => [], 'mech' => []];
    }

    /**
     * Logika evaluasi tunggal PASS / FAIL / —.
     *
     * Aturan evaluasi:
     * - Jika nilai kosong (null) ATAU (min dan max keduanya null/tidak berlaku) => '—'
     * - Jika nilai < min (di bawah batas minimum) => 'FAIL'
     * - Jika nilai > max (di atas batas maksimum) => 'FAIL'
     * - Jika nilai tepat pada min ATAU tepat pada max ATAU di antaranya => 'PASS'
     *
     * @param float|int|string|null $val
     * @param float|int|null $min
     * @param float|int|null $max
     * @return string 'PASS'|'FAIL'|'—'
     */
    public static function judge($val, $min = null, $max = null): string
    {
        if ($val === null || $val === '' || ($min === null && $max === null)) {
            return '—';
        }

        $v = (float) $val;

        if ($min !== null && $v < (float) $min) {
            return 'FAIL';
        }

        if ($max !== null && $v > (float) $max) {
            return 'FAIL';
        }

        return 'PASS';
    }

    /**
     * Evaluasi seluruh parameter sample (kimia dan mekanik).
     * Digunakan oleh Controller, Preview, dan Workflow Approval.
     */
    public static function evaluateSample(Sample $sample): array
    {
        $limits = self::getLimitsForGrade($sample->grade);
        $chemLimits = $limits['chem'] ?? [];
        $mechLimits = $limits['mech'] ?? [];

        $s = $sample->spectroResult;
        $t = $sample->tensileTest;
        $h = $sample->hardnessTest;

        $chemChecks = [];
        $hasFail = false;

        foreach ($chemLimits as $el => $range) {
            $min = $range[0] ?? null;
            $max = $range[1] ?? null;
            $val = $s ? ($s->{$el} ?? null) : null;
            $status = self::judge($val, $min, $max);

            if ($status === 'FAIL') {
                $hasFail = true;
            }

            $chemChecks[$el] = [
                'value'  => $val,
                'min'    => $min,
                'max'    => $max,
                'status' => $status,
                'pass'   => $status === 'PASS',
            ];
        }

        $mechChecks = [
            'ys_mpa' => [
                'value' => $t ? ($t->ys_mpa ?? null) : null,
                'min'   => $mechLimits['ys_mpa'][0] ?? null,
                'max'   => $mechLimits['ys_mpa'][1] ?? null,
            ],
            'uts_mpa' => [
                'value' => $t ? ($t->uts_mpa ?? null) : null,
                'min'   => $mechLimits['uts_mpa'][0] ?? null,
                'max'   => $mechLimits['uts_mpa'][1] ?? null,
            ],
            'elong_pct' => [
                'value' => $t ? ($t->elong_pct ?? null) : null,
                'min'   => $mechLimits['elong_pct'][0] ?? null,
                'max'   => $mechLimits['elong_pct'][1] ?? null,
            ],
            'hb' => [
                'value' => $h ? ($h->avg_value ?? null) : null,
                'min'   => $mechLimits['hb'][0] ?? null,
                'max'   => $mechLimits['hb'][1] ?? null,
            ],
        ];

        foreach ($mechChecks as $k => $c) {
            $status = self::judge($c['value'], $c['min'], $c['max']);
            if ($status === 'FAIL') {
                $hasFail = true;
            }
            $mechChecks[$k]['status'] = $status;
            $mechChecks[$k]['pass']   = ($status === 'PASS');
        }

        return [
            'chemical'     => $chemChecks,
            'mechanical'   => $mechChecks,
            'overall_pass' => !$hasFail,
            'has_failures' => $hasFail,
        ];
    }
}
