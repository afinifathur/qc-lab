<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge"> {{-- cegah Compatibility View di IE --}}
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login database QC</title>

  @php
    // Deteksi IE/Win7 (user agent berisi Trident/MSIE)
    $isIE = isset($_SERVER['HTTP_USER_AGENT']) &&
            (strpos($_SERVER['HTTP_USER_AGENT'],'Trident') !== false ||
             strpos($_SERVER['HTTP_USER_AGENT'],'MSIE') !== false);
  @endphp

  {{-- CSS lokal (modern vs fallback) --}}
  @if(!$isIE)
    <link href="{{ asset('vendor/bootstrap5/bootstrap.min.css') }}" rel="stylesheet">
  @else
    <link href="{{ asset('vendor/bootstrap4/bootstrap.min.css') }}" rel="stylesheet">
  @endif

  <style>
    body { background:#f8f9fa; }
    .login-card { max-width:380px; width:100%; }
    .logo { height:56px; }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">

  <div class="card shadow-sm login-card">
    <div class="card-body p-4">
      <div class="text-center mb-3">
        <img src="{{ asset('storage/assets/logo.png') }}" class="logo" alt="Logo">
      </div>
      <h5 class="text-center mb-3">Login QC</h5>

      @if ($errors->any())
        <div class="alert alert-danger py-2">
          <small>{{ $errors->first() }}</small>
        </div>
      @endif

      <form method="post" action="{{ url('/login') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" class="form-control" name="password" required>
        </div>
        <button class="btn btn-primary w-100">Masuk</button>
      </form>
    </div>
  </div>

  {{-- JS lokal (modern vs fallback) --}}
  @if(!$isIE)
    <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap5/bootstrap.bundle.min.js') }}"></script>
  @else
    <script src="{{ asset('vendor/jquery/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap4/bootstrap.bundle.min.js') }}"></script>
  @endif
</body>
</html>
