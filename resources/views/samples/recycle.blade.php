@extends('layouts.app', ['title' => 'Recycle Bin'])

@section('content')
  @if(session('ok'))  <div class="alert alert-success">{{ session('ok') }}</div> @endif
  @if(session('err')) <div class="alert alert-danger">{{ session('err') }}</div> @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">Recycle Bin</h4>
    <a href="{{ route('samples.index') }}" class="btn btn-outline-secondary">← Kembali</a>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:70px">ID</th>
              <th>Report No</th>
              <th>Grade</th>
              <th>Heat</th>
              <th>Dihapus</th>
              <th style="width:260px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($trashed as $s)
              <tr>
                <td>{{ $s->id }}</td>
                <td>{{ $s->report_no }}</td>
                <td>{{ $s->grade }}</td>
                <td>{{ $s->heat_no }}</td>
                <td>{{ optional($s->deleted_at)->format('Y-m-d H:i') }}</td>
                <td class="text-nowrap">
                  <form method="post" action="{{ route('samples.restore',$s->id) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-success">
                      <i class="bi bi-arrow-counterclockwise"></i> Restore
                    </button>
                  </form>

                  <form method="post" action="{{ route('samples.force',$s->id) }}" class="d-inline"
                        onsubmit="return confirm('Hapus permanen? PDF arsip juga akan dihapus.');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">
                      <i class="bi bi-x-octagon"></i> Hapus Permanen
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center py-4 text-muted">Kosong.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
