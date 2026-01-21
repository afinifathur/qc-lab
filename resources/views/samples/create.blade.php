@extends('layouts.app', ['title' => 'Input QC Sample'])

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">Input QC Sample</h4>
    <a href="{{ route('samples.index') }}" class="btn btn-outline-secondary">
      ← Kembali ke Daftar
    </a>
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

  <form method="post" action="{{ route('samples.store') }}" class="needs-validation" novalidate>
    @csrf

    {{-- Identitas --}}
   <div class="card mb-4">
  <div class="card-header fw-semibold">Identitas Sampel</div>
  <div class="card-body">

    <div class="row g-3 align-items-end">
      {{-- Grade / Material --}}
      <div class="col-12 col-md-6 col-lg-3">
        <label class="form-label">Grade / Material <span class="text-danger">*</span></label>
        <select name="grade" id="grade" class="form-select" required>
          <option value="CF8"    @selected(old('grade', $sample->grade ?? '')==='CF8')>CF8 ( 304)</option>
          <option value="CF8M"   @selected(old('grade', $sample->grade ?? '')==='CF8M')>CF8M ( 316)</option>
          <option value="SCS13A" @selected(old('grade', $sample->grade ?? '')==='SCS13A')>SCS13A (304)</option>
          <option value="SCS14A" @selected(old('grade', $sample->grade ?? '')==='SCS14A')>SCS14A (316)</option>
          <option value="1.4308" @selected(old('grade', $sample->grade ?? '')==='1.4308')>GX5CrNi19-10 (1.4308)</option>
          <option value="1.4408" @selected(old('grade', $sample->grade ?? '')==='1.4408')>GX5CrNiMo19-11-2 (1.4408)</option>
        </select>
      </div>

      {{-- Standard (auto) --}}
      <div class="col-12 col-md-6 col-lg-3">
        <label class="form-label">Standard</label>
        <input type="text" id="standard" name="standard"
               value="{{ old('standard', $sample->standard ?? '') }}"
               class="form-control" readonly>
      </div>

      {{-- Product Type --}}
      <div class="col-12 col-md-6 col-lg-3">
        <label class="form-label">Product Type</label>
<select name="product_type" class="form-control" required>
    <option value="">— Pilih —</option>
    <option value="Flange"  {{ old('product_type', $sample->product_type ?? '') === 'Flange' ? 'selected' : '' }}>Flange</option>
    <option value="Fitting" {{ old('product_type', $sample->product_type ?? '') === 'Fitting' ? 'selected' : '' }}>Fitting</option>
</select>
@error('product_type') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      {{-- Test Date --}}
      <div class="col-12 col-md-6 col-lg-3">
        <label class="form-label">Test Date</label>
        <input type="date" name="test_date"
               value="{{ old('test_date', optional($sample->test_date ?? now())->format('Y-m-d')) }}"
               class="form-control">
      </div>

      {{-- Heat No --}}
      <div class="col-12 col-md-6 col-lg-3">
        <label class="form-label">Heat No</label>
        <input type="text" name="heat_no" value="{{ old('heat_no', $sample->heat_no ?? '') }}" class="form-control">
      </div>

      {{-- Batch No --}}
      <div class="col-12 col-md-6 col-lg-3">
        <label class="form-label">Batch No</label>
        <input type="text" name="batch_no" value="{{ old('batch_no', $sample->batch_no ?? '') }}" class="form-control">
      </div>

      {{-- PO / Customer --}}
      <div class="col-12 col-md-6 col-lg-3">
        <label class="form-label">P.O / Customer</label>
        <input type="text" name="po_customer" value="{{ old('po_customer', $sample->po_customer ?? '') }}" class="form-control">
      </div>
    </div>

  </div>
</div>
<script>
const MAP_STD = {
  "CF8":"ASTM A351","CF8M":"ASTM A351",
  "SCS13A":"JIS G 5121","SCS14A":"JIS G 5121",
  "1.4308":"BS EN 10213","1.4408":"BS EN 10213",
};
function applyStandard(){ document.getElementById('standard').value = MAP_STD[document.getElementById('grade').value] || ''; }
document.getElementById('grade').addEventListener('change', applyStandard);
window.addEventListener('DOMContentLoaded', applyStandard);
</script>


   {{-- Spektro: urut sesuai alat --}}
<div class="card mb-3">
  <div class="card-header fw-semibold">Uji Komposisi (Spektro) — %wt</div>
  <div class="card-body">
    @php
      $order  = ['c','si','mn','p','s','cr','ni','mo','cu','co','al','v'];
      $labels = ['c'=>'Carbon (C)','si'=>'Silicon (Si)','mn'=>'Manganese (Mn)','p'=>'Phosphorus (P)',
                 's'=>'Sulfur (S)','cr'=>'Chromium (Cr)','ni'=>'Nickel (Ni)','mo'=>'Molybdenum (Mo)',
                 'cu'=>'Copper (Cu)','co'=>'Cobalt (Co)','al'=>'Aluminium (Al)','v'=>'Vanadium (V)'];
    @endphp
    <div class="row g-3">
      @foreach($order as $name)
        <div class="col-6 col-md-3 col-lg-2">
          <label class="form-label">{{ $labels[$name] }}</label>
          <div class="input-group">
            <input type="number" step="0.0001" min="0" max="100" name="{{ $name }}"
                   value="{{ old($name) }}" class="form-control" placeholder="0.0000">
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
            <input type="number" step="0.01" name="ys_mpa" value="{{ old('ys_mpa') }}" class="form-control dec2" placeholder="0.00">
          </div>
          <div class="col-md-4">
            <label class="form-label">UTS (MPa)</label>
            <input type="number" step="0.01" name="uts_mpa" value="{{ old('uts_mpa') }}" class="form-control dec2" placeholder="0.00">
          </div>
          <div class="col-md-4">
            <label class="form-label">Elongation (%)</label>
            <div class="input-group">
              <input type="number" step="0.01" name="elong_pct" value="{{ old('elong_pct') }}" class="form-control dec2" placeholder="0.00">
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
            <input type="number" step="0.01" name="hb" value="{{ old('hb') }}" class="form-control dec2" placeholder="0.00">
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
      <a href="{{ route('samples.index') }}" class="btn btn-outline-secondary">Batal</a>
      <button class="btn btn-primary">Simpan Draft</button>
    </div>
  </form>
@endsection

@push('scripts')
<script>
  // Format 2 desimal saat blur (tanpa mengganggu angka kosong)
  document.querySelectorAll('.dec2').forEach(el => {
    el.addEventListener('blur', () => {
      const v = el.value;
      if (v !== '') {
        const num = Number(v);
        if (!isNaN(num)) el.value = num.toFixed(2);
      }
    });
  });

  // Bootstrap validation
  (function () {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault(); event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  })();
</script>
@endpush
