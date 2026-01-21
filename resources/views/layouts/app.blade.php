<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  {{-- cegah Compatibility View di IE --}}
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>{{ $title ?? 'QC DB' }}</title>

  @php
      // IE11 / Win7 detection (user agent berisi Trident/MSIE)
      $isIE = isset($_SERVER['HTTP_USER_AGENT']) &&
              (strpos($_SERVER['HTTP_USER_AGENT'],'Trident') !== false ||
               strpos($_SERVER['HTTP_USER_AGENT'],'MSIE') !== false);
  @endphp

  {{-- ========= CSS ========= --}}
  @if(!$isIE)
    {{-- Modern: Bootstrap 5 + DataTables (Bootstrap 5 theme) --}}
    <link href="{{ asset('vendor/bootstrap5/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
  @else
    {{-- Fallback IE/Win7: Bootstrap 4 + DataTables (Bootstrap 4 theme) --}}
    <link href="{{ asset('vendor/bootstrap4/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
  @endif

  <style>
    .brand-logo { height: 34px; }
    .status-badge { font-weight: 600; }
    .dt-input { width: 220px !important; } /* search box DataTables */
    /* Garis tabel & kartu lebih tegas namun tidak berlebihan */
    .table-bordered > :not(caption) > * > * { border-width: 1px; }
    .card { border-width: 1px; }
  </style>

  @stack('head')
</head>
<body class="bg-light">
  {{-- NAVBAR --}}
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('samples.index') }}">
        <img src="{{ asset('storage/assets/logo.png') }}" class="brand-logo" alt="Logo">
        <span class="fw-semibold">QC Database</span>
      </a>

      <div class="d-flex align-items-center gap-3">
        @auth
          <span class="text-secondary small">
            {{ auth()->user()->name }}
            @role('Operator')<span class="badge text-bg-primary ms-1">Operator</span>@endrole
            @role('Approver')<span class="badge text-bg-success ms-1">Approver</span>@endrole
            @role('Auditor') <span class="badge text-bg-secondary ms-1">Auditor</span>@endrole
          </span>
          <form method="post" action="/logout" class="m-0">@csrf
            <button class="btn btn-sm btn-outline-secondary">Logout</button>
          </form>
        @endauth
      </div>
    </div>
  </nav>

  <main class="container py-4">
    @yield('content')
  </main>

  {{-- ========= JS ========= --}}
  @if(!$isIE)
    <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap5/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap5.min.js') }}"></script>
  @else
    <script src="{{ asset('vendor/jquery/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap4/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
  @endif

  <script src="{{ asset('vendor/dayjs/dayjs.min.js') }}"></script>

  @stack('scripts')
</body>
</html>
