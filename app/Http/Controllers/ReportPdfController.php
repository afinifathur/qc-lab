<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportPdfController extends Controller
{
    public function show(Request $request, Sample $sample) { return $this->render($request, $sample, true); }
    public function download(Request $request, Sample $sample) { return $this->render($request, $sample, false); }

    private function render(Request $request, Sample $sample, bool $isPreview)
    {
        $user      = $request->user();
        $stampId   = (string) Str::uuid();
        $tz        = 'Asia/Jakarta';
        $stampTime = now()->setTimezone($tz)->format('Y-m-d H:i:s');

        // Audit trail (opsional, jika spatie/activitylog tersedia)
        if (function_exists('activity')) {
            activity('reports')->performedOn($sample)->causedBy($user)->withProperties([
                'action'     => $isPreview ? 'preview_pdf' : 'download_pdf',
                'stamp_id'   => $stampId,
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
                'tz'         => $tz,
            ])->log(($isPreview ? 'Preview' : 'Download') . " PDF Sample #{$sample->id}");
        }

        $sample->load(['spectroResult','tensileTest','hardnessTest']);

        // === Hindari bug "dot-notation" untuk key grade bertitik (1.4308/1.4408) ===
        $materialsAll = config('qc.materials', []);
        $paramsAll    = config('qc.params', []);

        $material = $materialsAll[$sample->grade] ?? [];
        $limits   = $paramsAll[$sample->grade]    ?? ['chem' => [], 'mech' => []];

        // Standard: utamakan nilai di DB; jika kosong, ambil dari config; fallback aman
        $standard = $sample->standard
            ?: ($material['standard'] ?? (in_array($sample->grade, ['1.4308','1.4408'], true) ? 'BS EN 10213' : 'ASTM A351'));

        // Evaluasi PASS/FAIL berdasarkan $limits dari config
        $eval = $this->evaluate($sample, $limits);

        // === base64 image helper ===
        $img = function (string $relPath): ?string {
            $path = public_path($relPath);
            if (!is_file($path)) return null;
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            return 'data:image/'.$ext.';base64,'.base64_encode(file_get_contents($path));
        };

        // folder: public/storage/assets/*.png
        $logoData      = $img('storage/assets/logo.png');
        $signatureData = $img('storage/assets/signature.png');
        $stampImgData  = $img('storage/assets/stamp.png');

        $pdf = Pdf::loadView('reports.pdf', [
            'sample'        => $sample,
            'isPreview'     => $isPreview,
            'stampUser'     => $user?->name ?? 'guest',
            'stampTime'     => $stampTime,
            'stampId'       => $stampId,
            'logoData'      => $logoData,
            'signatureData' => $signatureData,
            'stampImgData'  => $stampImgData,

            // === data tambahan untuk PDF ===
            'standard'      => $standard, // tampilkan standard dari DB/config (bukan config() langsung di Blade)
            'limits'        => $limits,   // ['chem'=>[el=>[min,max]], 'mech'=>['ys_mpa'=>[min,max],...]]
            'eval'          => $eval,     // hasil evaluasi OK/FAIL kimia & mekanik + overall_pass
        ])->setPaper('A4');

        return $isPreview
            ? $pdf->stream("QC-Report-{$sample->id}.pdf")
            : $pdf->download("QC-Report-{$sample->id}.pdf");
    }

    /**
     * Evaluasi PASS/FAIL untuk komposisi & mekanik.
     * $limits mengikuti struktur qc.params[GRADE]:
     *   - chem: ['c'=>[min,max], ...]
     *   - mech: ['ys_mpa'=>[min,max], 'uts_mpa'=>[min,max], 'elong_pct'=>[min,max], 'hb'=>[min,max]]
     */
    private function evaluate(Sample $sample, array $limits): array
    {
        $chemLimits = $limits['chem'] ?? [];
        $mechLimits = $limits['mech'] ?? [];

        $chemChecks = [];
        $chemPass   = true;

        foreach ($chemLimits as $el => $range) {
            // Normalisasi [min,max]
            $min = $range[0] ?? null;
            $max = $range[1] ?? null;

            $val = optional($sample->spectroResult)->{$el};

            $ok = true;
            if ($val === null && ($min !== null || $max !== null)) {
                $ok = false; // anggap gagal jika ada limit tapi nilai tidak diinput
            } else {
                if ($min !== null && $val < $min) $ok = false;
                if ($max !== null && $val > $max) $ok = false;
            }

            $chemChecks[$el] = [
                'value' => $val,
                'min'   => $min,
                'max'   => $max,
                'pass'  => $ok,
            ];
            $chemPass = $chemPass && $ok;
        }

        // Mekanik
        $tt = $sample->tensileTest;
        $hd = $sample->hardnessTest;

        $mk = function (?float $val, ?float $min, ?float $max): bool {
            if ($val === null && ($min !== null || $max !== null)) return false;
            if ($min !== null && $val !== null && $val < $min) return false;
            if ($max !== null && $val !== null && $val > $max) return false;
            return true;
        };

        $mechChecks = [
            'ys_mpa' => [
                'value' => $tt->ys_mpa ?? null,
                'min'   => $mechLimits['ys_mpa'][0] ?? null,
                'max'   => $mechLimits['ys_mpa'][1] ?? null,
            ],
            'uts_mpa' => [
                'value' => $tt->uts_mpa ?? null,
                'min'   => $mechLimits['uts_mpa'][0] ?? null,
                'max'   => $mechLimits['uts_mpa'][1] ?? null,
            ],
            'elong_pct' => [
                'value' => $tt->elong_pct ?? null,
                'min'   => $mechLimits['elong_pct'][0] ?? null,
                'max'   => $mechLimits['elong_pct'][1] ?? null,
            ],
            'hb' => [
                'value' => $hd->avg_value ?? null,
                'min'   => $mechLimits['hb'][0] ?? null,
                'max'   => $mechLimits['hb'][1] ?? null,
            ],
        ];

        foreach ($mechChecks as $k => $c) {
            $mechChecks[$k]['pass'] = $mk($c['value'], $c['min'], $c['max']);
        }

        $mechPass = collect($mechChecks)->every(fn($c) => $c['pass']);

        return [
            'chemical'     => $chemChecks,
            'mechanical'   => $mechChecks,
            'overall_pass' => $chemPass && $mechPass,
        ];
    }
}
