@extends('layouts.app', ['title' => 'Edit QC Sample'])

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">Edit QC Sample (ID #{{ $sample->id }})</h4>
    <a href="{{ route('samples.index') }}" class="btn btn-outline-secondary">← Kembali</a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Periksa kembali isian berikut:</div>
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @php
    $sr = $sample->spectroResult;
    $tt = $sample->tensileTest;
    $hd = $sample->hardnessTest;

    // Fallback jika controller belum mengirim variabel opsi
    $grades = $grades ?? ['CF8','CF8M','SCS13A','SCS14A','1.4308','1.4408'];
    $productTypes = $productTypes ?? ['Flange','Fitting'];

    // Hindari error format() bila test_date berupa string/null
    $testDate = old('test_date');
    if (!$testDate && !empty($sample->test_date)) {
        try {
            $testDate = \Illuminate\Support\Carbon::parse($sample->test_date)->format('Y-m-d');
        } catch (\Throwable $e) {
            $testDate = '';
        }
    }
  @endphp

  <form method="post" action="{{ route('samples.update',$sample) }}" class="needs-validation" novalidate>
    @csrf @method('PUT')

    {{-- Identitas --}}
    <div class="card mb-3">
      <div class="card-header fw-semibold">Identitas Sampel</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Grade <span class="text-danger">*</span></label>
            <select name="grade" id="grade" class="form-select" required>
              <option value="">— Pilih —</option>
              @foreach($grades as $g)
                <option value="{{ $g }}" {{ old('grade',$sample->grade) === $g ? 'selected' : '' }}>
                  {{ $g }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Standard</label>
            <input type="text" name="standard" id="standard" class="form-control"
                   value="{{ old('standard',$sample->standard) }}" placeholder="Otomatis dari grade">
          </div>

          <div class="col-md-3">
            <label class="form-label">Product Type</label>
            <select name="product_type" class="form-select" required>
              <option value="">— Pilih —</option>
              @foreach($productTypes as $pt)
                <option value="{{ $pt }}" {{ old('product_type',$sample->product_type) === $pt ? 'selected' : '' }}>
                  {{ $pt }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Test Date</label>
            <input type="date" name="test_date" class="form-control" value="{{ $testDate }}">
          </div>

          <div class="col-md-3">
            <label class="form-label">Heat No</label>
            <input type="text" name="heat_no" class="form-control" value="{{ old('heat_no',$sample->heat_no) }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Batch No</label>
            <input type="text" name="batch_no" class="form-control" value="{{ old('batch_no',$sample->batch_no) }}">
          </div>
        </div>
      </div>
    </div>

    {{-- Spektro: urut sesuai alat --}}
    <div class="card mb-3">
      <div class="card-header fw-semibold">Uji Komposisi (Spektro) — %wt</div>
      <div class="card-body">
        @php
          $order  = ['c','si','mn','p','s','cr','ni','mo','cu','co','al','v','n'];
          $labels = [
            'c'=>'Carbon (C)','si'=>'Silicon (Si)','mn'=>'Manganese (Mn)','p'=>'Phosphorus (P)',
            's'=>'Sulfur (S)','cr'=>'Chromium (Cr)','ni'=>'Nickel (Ni)','mo'=>'Molybdenum (Mo)',
            'cu'=>'Copper (Cu)','co'=>'Cobalt (Co)','al'=>'Aluminium (Al)','v'=>'Vanadium (V)','n'=>'Nitrogen (N)'
          ];
        @endphp
        <div class="row g-3">
          @foreach($order as $name)
            <div class="col-6 col-md-3 col-lg-2">
              <label class="form-label">{{ $labels[$name] }}</label>
              <div class="input-group">
                <input type="number" step="0.0001" min="0" max="100" name="{{ $name }}"
                       value="{{ old($name, optional($sr)->{$name}) }}" class="form-control" placeholder="0.0000">
                <span class="input-group-text">%</span>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Uji Tarik --}}
    <div class="card mb-3">
      <div class="card-header fw-semibold">Uji Tarik</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Yield Strength (MPa)</label>
            <input type="number" step="0.01" name="ys_mpa" value="{{ old('ys_mpa', optional($tt)->ys_mpa) }}" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">UTS (MPa)</label>
            <input type="number" step="0.01" name="uts_mpa" value="{{ old('uts_mpa', optional($tt)->uts_mpa) }}" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Elongation (%)</label>
            <div class="input-group">
              <input type="number" step="0.01" name="elong_pct" value="{{ old('elong_pct', optional($tt)->elong_pct) }}" class="form-control">
              <span class="input-group-text">%</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Kekerasan --}}
    <div class="card mb-3">
      <div class="card-header fw-semibold">Uji Kekerasan</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">HB</label>
            <input type="number" step="0.01" name="hb" value="{{ old('hb', optional($hd)->avg_value) }}" class="form-control">
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between gap-2">
      <a href="{{ route('reports.pdf',$sample) }}?inline=1" target="_blank" rel="noopener" class="btn btn-outline-secondary">
        <i class="bi bi-eye"></i> Preview Dokumen
      </a>
      <div class="d-flex gap-2">
        <a href="{{ route('samples.index') }}" class="btn btn-outline-secondary">Batal</a>
        <button class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </div>
  </form>

  {{-- Auto-isi Standard berdasarkan Grade --}}
  <script>
  document.addEventListener('DOMContentLoaded', function () {
      const gradeEl = document.getElementById('grade');
      const stdEl   = document.getElementById('standard');

      const map = {
          'CF8'   : 'ASTM A351 ',
          'CF8M'  : 'ASTM A351 ',
          'SCS13A': 'JIS G 5121 ',
          'SCS14A': 'JIS G 5121 ',
          '1.4308': 'BS EN 10213',
          '1.4408': 'BS EN 10213',
      };

      function applyStandard(force=false) {
          const g = gradeEl.value;
          if (map[g] && (force || !stdEl.value)) {
              stdEl.value = map[g];
          }
      }

      gradeEl.addEventListener('change', function(){ applyStandard(true); });
      applyStandard(false); // saat halaman dibuka
  });
  </script>
@endsection
