<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use App\Models\SpectroResult;
use App\Models\TensileTest;
use App\Models\HardnessTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SampleController extends Controller
{
    /** Daftar sample */
    public function index()
    {
        $samples = Sample::orderByDesc('id')->get();
        return view('samples.index', compact('samples'));
    }

    /** Form input sample baru (Operator/Approver) */
    public function create()
    {
        abort_unless(auth()->user()->hasRole(['Operator', 'Approver']), 403);

        // Opsi dropdown
        $grades = ['CF8', 'CF8M', 'SCS13A', 'SCS14A', '1.4308', '1.4408'];
        $productTypes = ['Flange', 'Fitting'];

        return view('samples.create', compact('grades', 'productTypes'));
    }

    /** Simpan sample baru sebagai DRAFT */
    public function store(Request $r)
    {
        abort_unless(auth()->user()->hasRole(['Operator', 'Approver']), 403);

        $rules = [
            // Identitas
            'grade'        => 'required|in:CF8,CF8M,SCS13A,SCS14A,1.4308,1.4408',
            'standard'     => 'nullable|string',
            'product_type' => 'nullable|string',
            'heat_no'      => 'nullable|string',
            'batch_no'     => 'nullable|string',
            'po_customer'  => 'nullable|string|max:100',
            'test_date'    => 'nullable|date',

            // Spektro
            'c'  => 'nullable|numeric',
            'si' => 'nullable|numeric',
            'mn' => 'nullable|numeric',
            'p'  => 'nullable|numeric',
            's'  => 'nullable|numeric',
            'cr' => 'nullable|numeric',
            'ni' => 'nullable|numeric',
            'mo' => 'nullable|numeric',
            'cu' => 'nullable|numeric',
            'co' => 'nullable|numeric',
            'al' => 'nullable|numeric',
            'v'  => 'nullable|numeric',
            'n'  => 'nullable|numeric',

            // Tarik
            'ys_mpa'    => 'nullable|numeric',
            'uts_mpa'   => 'nullable|numeric',
            'elong_pct' => 'nullable|numeric',

            // Kekerasan
            'hb' => 'nullable|numeric',
        ];

        $data = $r->validate($rules);

        // Map grade → standard (hindari dot-notation untuk key 1.4308/1.4408)
        $standard = $this->resolveStandard($data['grade']);

        DB::transaction(function () use ($data, $standard) {
            $s = Sample::create([
                'report_no'    => null,
                'grade'        => $data['grade'],
                'standard'     => $standard,
                'product_type' => $data['product_type'] ?? 'Flange',
                'heat_no'      => $data['heat_no'] ?? null,
                'batch_no'     => $data['batch_no'] ?? null,
                'po_customer'  => $data['po_customer'] ?? null,
                'test_date'    => $data['test_date'] ?? now(),
                'status'       => 'DRAFT',
            ]);

            SpectroResult::create([
                'sample_id' => $s->id,
                'c'  => $data['c']  ?? null,
                'si' => $data['si'] ?? null,
                'mn' => $data['mn'] ?? null,
                'p'  => $data['p']  ?? null,
                's'  => $data['s']  ?? null,
                'cr' => $data['cr'] ?? null,
                'ni' => $data['ni'] ?? null,
                'mo' => $data['mo'] ?? null,
                'cu' => $data['cu'] ?? null,
                'co' => $data['co'] ?? null,
                'al' => $data['al'] ?? null,
                'v'  => $data['v']  ?? null,
                'n'  => $data['n']  ?? null,
            ]);

            TensileTest::create([
                'sample_id' => $s->id,
                'ys_mpa'    => $data['ys_mpa']    ?? null,
                'uts_mpa'   => $data['uts_mpa']   ?? null,
                'elong_pct' => $data['elong_pct'] ?? null,
            ]);

            HardnessTest::create([
                'sample_id' => $s->id,
                'method'    => 'HB',
                'avg_value' => $data['hb'] ?? null,
            ]);
        });

        return redirect()->route('samples.index')->with('ok', 'Draft tersimpan. Klik Submit bila siap approve.');
    }

    /** Edit DRAFT/REJECTED */
    public function edit(Sample $sample)
    {
        abort_unless(auth()->user()->hasRole(['Operator','Approver']), 403);

        if (!in_array($sample->status, ['DRAFT', 'REJECTED'], true)) {
            return redirect()->route('samples.index')
                ->with('err', 'Hanya DRAFT/REVISI yang bisa diedit.');
        }

        $sample->load(['spectroResult','tensileTest','hardnessTest']);

        // Opsi dropdown untuk halaman Edit
        $grades = ['CF8', 'CF8M', 'SCS13A', 'SCS14A', '1.4308', '1.4408'];
        $productTypes = ['Flange', 'Fitting'];

        return view('samples.edit', compact('sample', 'grades', 'productTypes'));
    }

    /** Update DRAFT/REJECTED */
    public function update(Request $r, Sample $sample)
    {
        abort_unless(auth()->user()->hasRole(['Operator','Approver']), 403);

        if (!in_array($sample->status, ['DRAFT', 'REJECTED'], true)) {
            return redirect()->route('samples.index')
                ->with('err', 'Hanya DRAFT/REVISI yang bisa diupdate.');
        }

        $rules = [
            // Identitas
            'grade'        => 'required|in:CF8,CF8M,SCS13A,SCS14A,1.4308,1.4408',
            'standard'     => 'nullable|string',
            'product_type' => 'nullable|string',
            'heat_no'      => 'nullable|string',
            'batch_no'     => 'nullable|string',
            'po_customer'  => 'nullable|string|max:100',
            'test_date'    => 'nullable|date',

            // Spektro
            'c'  => 'nullable|numeric',
            'si' => 'nullable|numeric',
            'mn' => 'nullable|numeric',
            'p'  => 'nullable|numeric',
            's'  => 'nullable|numeric',
            'cr' => 'nullable|numeric',
            'ni' => 'nullable|numeric',
            'mo' => 'nullable|numeric',
            'cu' => 'nullable|numeric',
            'co' => 'nullable|numeric',
            'al' => 'nullable|numeric',
            'v'  => 'nullable|numeric',
            'n'  => 'nullable|numeric',

            // Tarik
            'ys_mpa'    => 'nullable|numeric',
            'uts_mpa'   => 'nullable|numeric',
            'elong_pct' => 'nullable|numeric',

            // Kekerasan
            'hb' => 'nullable|numeric',
        ];

        $data = $r->validate($rules);

        // Map grade → standard (hindari dot-notation untuk key 1.4308/1.4408)
        $standard = $this->resolveStandard($data['grade']);

        DB::transaction(function () use ($sample, $data, $standard) {
            $sample->update([
                'grade'        => $data['grade'],
                'standard'     => $standard,
                'product_type' => $data['product_type'] ?? $sample->product_type,
                'heat_no'      => $data['heat_no'] ?? null,
                'batch_no'     => $data['batch_no'] ?? null,
                'po_customer'  => $data['po_customer'] ?? $sample->po_customer,
                'test_date'    => $data['test_date'] ?? $sample->test_date,
            ]);

            $sample->spectroResult()->updateOrCreate(['sample_id' => $sample->id], [
                'c'  => $data['c']  ?? null,
                'si' => $data['si'] ?? null,
                'mn' => $data['mn'] ?? null,
                'p'  => $data['p']  ?? null,
                's'  => $data['s']  ?? null,
                'cr' => $data['cr'] ?? null,
                'ni' => $data['ni'] ?? null,
                'mo' => $data['mo'] ?? null,
                'cu' => $data['cu'] ?? null,
                'co' => $data['co'] ?? null,
                'al' => $data['al'] ?? null,
                'v'  => $data['v']  ?? null,
                'n'  => $data['n']  ?? null,
            ]);

            $sample->tensileTest()->updateOrCreate(['sample_id' => $sample->id], [
                'ys_mpa'    => $data['ys_mpa']    ?? null,
                'uts_mpa'   => $data['uts_mpa']   ?? null,
                'elong_pct' => $data['elong_pct'] ?? null,
            ]);

            $sample->hardnessTest()->updateOrCreate(['sample_id' => $sample->id], [
                'method'    => 'HB',
                'avg_value' => $data['hb'] ?? null,
            ]);
        });

        return redirect()->route('samples.index')->with('ok', 'Perubahan disimpan.');
    }

    /** Soft delete sample (pindah ke Recycle Bin) */
    public function destroy(Sample $sample)
    {
        abort_unless(auth()->user()->hasRole('Approver'), 403);

        DB::transaction(function () use ($sample) {
            $sample->spectroResult()?->delete();
            $sample->tensileTest()?->delete();
            $sample->hardnessTest()?->delete();
            $sample->delete();
        });

        return back()->with('ok','Sample dipindah ke Recycle Bin (soft delete).');
    }

    /** Lihat isi Recycle Bin */
    public function recycleBin()
    {
        abort_unless(auth()->user()->hasRole('Approver'), 403);

        $samples = Sample::onlyTrashed()->orderByDesc('id')->get();
        return view('samples.recycle-bin', compact('samples'));
    }

    /** Pulihkan dari Recycle Bin */
    public function restore($id)
    {
        abort_unless(auth()->user()->hasRole('Approver'), 403);

        DB::transaction(function () use ($id) {
            $s = Sample::onlyTrashed()->findOrFail($id);
            $s->restore();
            $s->spectroResult()->withTrashed()->restore();
            $s->tensileTest()->withTrashed()->restore();
            $s->hardnessTest()->withTrashed()->restore();
        });

        return back()->with('ok','Sample berhasil dipulihkan.');
    }

    /** Hapus permanen (beserta PDF jika ada) */
    public function forceDelete($id)
    {
        abort_unless(auth()->user()->hasRole('Approver'), 403);

        DB::transaction(function () use ($id) {
            $s = Sample::onlyTrashed()->findOrFail($id);

            // Hapus file PDF terkait (jika ada pola penamaan report_no)
            if ($s->report_no) {
                $safeReport = preg_replace('/[\/\\\\]+/', '-', (string) $s->report_no);
                $yearMonth  = $s->approved_at ? $s->approved_at->format('Y/m') : null;

                if ($yearMonth) {
                    $dir = 'qc-pdf/'.$yearMonth;
                    foreach (Storage::files($dir) as $f) {
                        if (str_contains($f, 'QC-'.$safeReport)) {
                            Storage::delete($f);
                        }
                    }
                } else {
                    foreach (Storage::allFiles('qc-pdf') as $f) {
                        if (str_contains($f, 'QC-'.$safeReport)) {
                            Storage::delete($f);
                        }
                    }
                }
            }

            // Hapus permanen data terkait
            $s->spectroResult()->withTrashed()->forceDelete();
            $s->tensileTest()->withTrashed()->forceDelete();
            $s->hardnessTest()->withTrashed()->forceDelete();

            $s->forceDelete();
        });

        return back()->with('ok','Sample & PDF terkait dihapus permanen.');
    }

    /**
     * Resolve standard berdasarkan grade tanpa terjebak "dot notation"
     */
    private function resolveStandard(string $grade): string
    {
        $materials = config('qc.materials', []);           // ambil seluruh array
        $mat       = $materials[$grade] ?? null;           // index manual (aman untuk key bertitik)

        return $mat['standard'] ?? match ($grade) {
            '1.4308', '1.4408' => 'BS EN 10213',
            default            => 'ASTM A351',
        };
    }
}
