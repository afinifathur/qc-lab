@extends('layouts.app', ['title' => 'Antrian Persetujuan'])

@section('content')
  @if(session('ok'))  <div class="alert alert-success">{{ session('ok') }}</div> @endif
  @if(session('err')) <div class="alert alert-danger">{{ session('err') }}</div> @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">Antrian Persetujuan</h4>
    <a href="{{ route('samples.index') }}" class="btn btn-outline-secondary">
      ← Kembali ke Daftar Sample
    </a>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table id="approvalTable" class="table table-bordered table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:70px">ID</th>
              <th>Report No</th>
              <th>Grade</th>
              <th>Heat</th>
              <th>Status</th>
              <th style="width:360px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($submitted as $s)
              <tr>
                <td>{{ $s->id }}</td>
                <td>{{ $s->report_no }}</td>
                <td>{{ $s->grade }}</td>
                <td>{{ $s->heat_no }}</td>
                <td><span class="badge text-bg-warning">SUBMITTED</span></td>
                <td class="text-nowrap">
                  {{-- PREVIEW (tanpa download). Wajib diklik sebelum Approve diaktifkan --}}
                  <a class="btn btn-sm btn-outline-secondary js-preview"
                     href="{{ route('reports.pdf',$s) }}?inline=1"
                     data-sample-id="{{ $s->id }}"
                     target="_blank" rel="noopener">
                    <i class="bi bi-eye"></i> Preview PDF
                  </a>

                  {{-- SETUJU & ARSIPKAN (aktif setelah Preview dibuka) --}}
                  <form method="post"
                        action="{{ route('approvals.approve',$s) }}"
                        class="d-inline js-approve-form"
                        data-sample-id="{{ $s->id }}">
                    @csrf
                    <button type="submit"
                            class="btn btn-sm btn-success js-approve-btn"
                            data-sample-id="{{ $s->id }}"
                            disabled>
                      <i class="bi bi-check2-circle"></i> Setuju & Arsipkan
                    </button>
                  </form>

                  {{-- REVISI (kembalikan ke operator) --}}
                  <form method="post"
                        action="{{ route('approvals.reject',$s) }}"
                        class="d-inline"
                        onsubmit="return confirm('Kembalikan ke operator untuk revisi?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning text-dark">
                      <i class="bi bi-arrow-counterclockwise"></i> Revisi
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                  Tidak ada yang menunggu persetujuan.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Modal peringatan: wajib preview dulu --}}
  <div class="modal fade" id="reviewRequiredModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Pratinjau diperlukan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          Anda harus <strong>membuka Preview PDF</strong> terlebih dahulu sebelum menyetujui dan mengarsipkan dokumen ini.
          Klik tombol <em>Preview PDF</em> di sebelah tombol persetujuan, lalu kembali ke halaman ini untuk melanjutkan.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
$(function() {
  // DataTables
  $('#approvalTable').DataTable({
    pageLength: 25,
    order: [[0,'desc']],
    language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/id.json' }
  });

  // Helper: enable tombol approve untuk sample tertentu
  function enableApprove(id){
    try { sessionStorage.setItem('reviewed-'+id, '1'); } catch(e) {}
    const btn = document.querySelector('.js-approve-btn[data-sample-id="'+id+'"]');
    if (btn) btn.disabled = false;
  }

  // Pulihkan status tombol (kalau sudah pernah preview di tab ini)
  document.querySelectorAll('.js-approve-btn').forEach(btn => {
    const id = btn.dataset.sampleId;
    try {
      if (sessionStorage.getItem('reviewed-'+id) === '1') btn.disabled = false;
    } catch(e) {}
  });

  // Klik Preview → tandai reviewed + aktifkan tombol Approve
  document.querySelectorAll('.js-preview').forEach(a => {
    a.addEventListener('click', () => {
      const id = a.dataset.sampleId;
      enableApprove(id);
    });
  });

  // Cegah Approve bila belum Preview → tampilkan modal
  document.querySelectorAll('.js-approve-form').forEach(form => {
    form.addEventListener('submit', function(e){
      const id = this.dataset.sampleId;
      try {
        if (sessionStorage.getItem('reviewed-'+id) !== '1') {
          e.preventDefault();
          const modal = new bootstrap.Modal('#reviewRequiredModal');
          modal.show();
        }
      } catch(e) {}
    });
  });
});
</script>
@endpush
