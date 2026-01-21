@extends('layouts.app', ['title' => 'Daftar Sample'])

@section('content')
  @if(session('ok'))  <div class="alert alert-success">{{ session('ok') }}</div> @endif
  @if(session('err')) <div class="alert alert-danger">{{ session('err') }}</div> @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">Daftar Sample</h4>
    <div class="d-flex gap-2">
      @role('Approver')
        <a href="{{ route('approvals.index') }}" class="btn btn-outline-success">
          <i class="bi bi-check2-square me-1"></i> ANTRIAN PERSETUJUAN
        </a>
      @endrole
      @role('Operator|Approver')
        <a href="{{ route('samples.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-lg me-1"></i> Input Sample Baru
        </a>
      @endrole
    </div>
  </div>

  {{-- FILTER BAR --}}
  <div class="row g-2 mb-3">
    <div class="col-auto">
      <label class="form-label mb-1">Tanggal dari</label>
      <input type="date" id="fltFrom" class="form-control form-control-sm">
    </div>
    <div class="col-auto">
      <label class="form-label mb-1">sampai</label>
      <input type="date" id="fltTo" class="form-control form-control-sm">
    </div>
    <div class="col-auto">
      <label class="form-label mb-1">Grade</label>
      <select id="fltGrade" class="form-select form-select-sm">
        <option value="">(semua)</option>
        <option>304</option>
        <option>316</option>
      </select>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table id="samplesTable" class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:70px">ID</th>
              <th>Report No</th>
              <th>Grade</th>
              <th>Heat</th>
              <th>Test Date</th>
              <th>Status</th>
              <th style="width:220px">Aksi</th>
            </tr>
          </thead>
         <tbody>
  @foreach($samples as $s)
    <tr>
      <td>{{ $s->id }}</td>
      <td>{{ $s->report_no ?? '—' }}</td>
      <td>{{ $s->grade }}</td>
      <td>{{ $s->heat_no ?? '—' }}</td>
      <td>{{ optional($s->test_date)->format('Y-m-d') }}</td>
      <td>
        @php
  $status = strtoupper($s->status ?? '');

  // Warna badge Bootstrap
  $badgeClass = [
    'DRAFT'     => 'secondary',
    'SUBMITTED' => 'info',
    'REJECTED'  => 'warning',
    'APPROVED'  => 'success',
][$status] ?? 'secondary';

  // Teks yang ditampilkan
  $label = [
    'DRAFT'     => 'DRAFT',
    'SUBMITTED' => 'MENUNGGU',
    'REJECTED'  => 'REVISI',
    'APPROVED'  => 'DISETUJUI',
][$status] ?? $status;
@endphp

<span class="badge bg-{{ $badgeClass }}">{{ $label }}</span>


      {{-- ====== A K S I (kolom terakhir) ====== --}}
      <td>
        @role('Operator|Approver')
          @if(in_array($s->status,['DRAFT','REJECTED']))
            {{-- Preview sebelum submit (tab baru, tanpa download) --}}
            <a class="btn btn-sm btn-outline-secondary"
               href="{{ route('reports.pdf',$s) }}?inline=1"
               target="_blank" rel="noopener">
              <i class="bi bi-eye"></i> Preview
            </a>

            {{-- Edit/Revisi data --}}
            <a class="btn btn-sm btn-outline-warning"
               href="{{ route('samples.edit',$s) }}">
              <i class="bi bi-pencil-square"></i> Edit
            </a>

            {{-- Submit untuk minta persetujuan --}}
            <form method="post" action="{{ route('samples.submit',$s) }}" class="d-inline">
              @csrf
              <button class="btn btn-sm btn-outline-primary">
                <i class="bi bi-send"></i> Submit
              </button>
            </form>
			
			<form method="post" action="{{ route('samples.destroy',$s) }}"
      class="d-inline"
      onsubmit="return confirm('Pindahkan ke Recycle Bin? (bisa dipulihkan)');">
  @csrf
  @method('DELETE')
  <button class="btn btn-sm btn-danger">
    <i class="bi bi-trash"></i> Hapus
  </button>
</form>

          @endif
        @endrole

        {{-- Setelah disetujui, semua role boleh preview/download --}}
        @if($s->status==='APPROVED')
          <a class="btn btn-sm btn-outline-secondary"
             href="{{ route('reports.pdf',$s) }}?inline=1"
             target="_blank" rel="noopener">
            <i class="bi bi-eye"></i> Preview
          </a>
          <a class="btn btn-sm btn-outline-secondary"
             href="{{ route('reports.pdf',$s) }}">
            <i class="bi bi-filetype-pdf"></i> Download
          </a>
        @endif
      </td>
      {{-- ====== /AKSI ====== --}}
    </tr>
  @endforeach
</tbody>
        </table>
      </div>
    </div>
  </div>
@endsection



@push('scripts')
<script>
$(function() {
  const dt = $('#samplesTable').DataTable({
    pageLength: 25,
    order: [[0,'desc']],
    language: {
      url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/id.json'
    }
  });

  // Filter Grade
  $('#fltGrade').on('change', function(){
    dt.column(2).search(this.value).draw();
  });

  // Filter tanggal (kolom 4, format 'YYYY-MM-DD')
  function dateInRange(iso, from, to){
    if (!iso) return true;
    const d = dayjs(iso);
    if (from && d.isBefore(dayjs(from), 'day')) return false;
    if (to && d.isAfter(dayjs(to), 'day')) return false;
    return true;
  }
  $.fn.dataTable.ext.search.push(function(settings, data){
    const from = $('#fltFrom').val();
    const to   = $('#fltTo').val();
    const iso  = data[4]; // kolom Test Date
    return dateInRange(iso, from, to);
  });
  $('#fltFrom,#fltTo').on('change', () => dt.draw());
});
</script>
@endpush
