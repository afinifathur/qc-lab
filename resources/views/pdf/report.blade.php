<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Report of Analysis - QC-{{ $sample->report_no ?? $sample->id }}</title>
<style>
  @page {
    margin: 20px 30px 18px 30px;
  }
  body {
    font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
    font-size: 8.2pt;
    color: #111;
    line-height: 1.2;
  }

  /* Header & Logo */
  .header-container {
    width: 100%;
    margin-bottom: 3px;
  }
  .brand-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 2px;
  }
  .brand-table td {
    border: none;
    padding: 0;
    vertical-align: middle;
  }
  .title-main {
    text-align: center;
    font-size: 16pt;
    font-weight: bold;
    letter-spacing: 0.5px;
    margin: 0 0 2px 0;
    text-transform: uppercase;
  }

  /* Meta Info Table */
  .meta-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 5px;
  }
  .meta-table td {
    border: none;
    padding: 2.2px 2px;
    font-size: 8.2pt;
    vertical-align: top;
  }
  .meta-label {
    width: 22%;
    color: #222;
    font-weight: normal;
  }
  .meta-sep {
    width: 2%;
    text-align: center;
  }
  .meta-val {
    width: 26%;
    font-weight: bold;
    color: #000;
  }

  /* Watermark for draft/preview */
  .watermark {
    position: fixed;
    top: 32%;
    left: 5%;
    width: 90%;
    text-align: center;
    opacity: 0.08;
    transform: rotate(-25deg);
    font-size: 42pt;
    font-weight: bold;
    color: #000;
  }

  /* Table Section Titles */
  .table-section-title {
    font-weight: bold;
    font-size: 9.2pt;
    margin-top: 6px;
    margin-bottom: 3px;
    color: #000;
  }

  /* Data Table */
  .data-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 5px;
  }
  .data-table th, .data-table td {
    border: 0.75px solid #333;
    padding: 3.4px 4px;
    font-size: 8pt;
  }
  .data-table th {
    background-color: #f2f2f2;
    font-weight: bold;
    text-align: center;
    text-transform: uppercase;
    font-size: 7.8pt;
    padding: 3.8px 4px;
  }
  .right { text-align: right; }
  .center { text-align: center; }
  .left { text-align: left; }
  .fail { color: #b00020; font-weight: bold; }

  /* Static Methods & Standards Section */
  .static-section {
    margin-top: 4px;
    margin-bottom: 4px;
  }
  .sec-heading {
    font-weight: bold;
    font-size: 8.2pt;
    margin-top: 3.5px;
    margin-bottom: 1.5px;
    color: #000;
  }
  .sec-body {
    font-size: 7.3pt;
    line-height: 1.25;
    color: #222;
    margin-bottom: 2.5px;
  }
  .ref-list {
    margin: 0 0 2.5px 0;
    padding-left: 16px;
    font-size: 7.3pt;
    line-height: 1.25;
  }

  /* Sign / Approval Section (Border-Free Seamless Layout) */
  .sign-wrapper {
    width: 100%;
    border-collapse: collapse;
    margin-top: 7px;
  }
  .sign-wrapper td {
    width: 50%;
    vertical-align: top;
    border: none;
    padding: 0 8px;
  }
  .sign-area {
    height: 90px;
    position: relative;
    text-align: center;
    padding: 1px 0;
    background-color: transparent;
  }
  .sign-title {
    font-weight: bold;
    font-size: 9.2pt;
    color: #111;
  }
  .qc-passed-badge {
    font-size: 11pt;
    font-weight: bold;
    color: #6b21a8;
    border: 1.5px solid #6b21a8;
    display: inline-block;
    padding: 2.5px 12px;
    margin-top: 13px;
    letter-spacing: 0.5px;
  }
  .qc-verif-text {
    font-size: 7.8pt;
    font-weight: 500;
    color: #111;
    margin-top: 11px;
  }
  .stamp-img {
    position: absolute;
    left: 50%;
    top: 7px;
    margin-left: -46px;
    width: 92px;
    opacity: 0.75;
  }
  .sig-img {
    position: absolute;
    left: 50%;
    top: 9px;
    margin-left: -52px;
    width: 104px;
    opacity: 0.95;
  }
  .role-text {
    position: absolute;
    bottom: 15px;
    left: 0;
    right: 0;
    font-size: 8.5pt;
    font-weight: bold;
    color: #111;
  }
  .date-text {
    position: absolute;
    bottom: 0px;
    left: 0;
    right: 0;
    font-size: 7.5pt;
    color: #333;
  }

  /* Footer directly in natural document flow (Tight spacing after approval) */
  .footer-container {
    margin-top: 8px;
    border-top: 0.75px solid #888;
    padding-top: 4px;
    font-size: 6.8pt;
    color: #555;
  }
</style>
</head>
<body>

@php
  use Illuminate\Support\Carbon;

  /** Status approved */
  $approved = strtoupper((string)($sample->status ?? '')) === 'APPROVED';

  /** Config limits & materials */
  $paramsAll    = config('qc.params', []);
  $materialsAll = config('qc.materials', []);

  $cfg  = $paramsAll[$sample->grade ?? ''] ?? ['chem'=>[], 'mech'=>[]];
  $chem = $cfg['chem'] ?? [];
  $mech = $cfg['mech'] ?? [];

  $stdFromCfg = $materialsAll[$sample->grade ?? '']['standard'] ?? 'ASTM A351';

  /** Helpers */
  $fmt = function($v, $dec = 3) {
      return is_null($v) ? '—' : number_format((float)$v, $dec, '.', '');
  };

  $range = function(string $key) use ($chem, $mech) {
      if (array_key_exists($key, $chem)) return $chem[$key];
      if (array_key_exists($key, $mech)) return $mech[$key];
      return [null, null];
  };

  $judge = function($val, $min, $max) {
      if (is_null($val) || (is_null($min) && is_null($max))) return '—';
      if (!is_null($min) && $val < $min) return 'FAIL';
      if (!is_null($max) && $val > $max) return 'FAIL';
      return 'PASS';
  };

  $actual = fn($obj, $field) => $obj ? ($obj->{$field} ?? null) : null;
  $decByUnit = fn($unit) => str_contains($unit, 'wt') ? 4 : 2;

  $s = $sample->spectroResult ?? null;
  $t = $sample->tensileTest ?? null;
  $h = $sample->hardnessTest ?? null;

  $reportNo   = !empty($sample->report_no) ? ('QC-'.$sample->report_no) : '—';
  $reportDate = !empty($sample->test_date)
      ? Carbon::parse($sample->test_date)->timezone('Asia/Jakarta')->format('Y-m-d')
      : now('Asia/Jakarta')->format('Y-m-d');
  $approvedDate = !empty($sample->approved_at)
      ? Carbon::parse($sample->approved_at)->timezone('Asia/Jakarta')->format('Y-m-d')
      : '—';
@endphp

@if(!$approved)
  <div class="watermark">PREVIEW COPY</div>
@endif

<!-- Header Section with Logo & Title -->
<div class="header-container">
  <table class="brand-table">
    <tr>
      <td style="width: 20%;">
        @if(!empty($logoData))
          <img src="{{ $logoData }}" alt="Logo" style="height: 38px;">
        @endif
      </td>
      <td style="width: 60%; text-align: center;">
        <div class="title-main">REPORT OF ANALYSIS</div>
      </td>
      <td style="width: 20%; text-align: right;"></td>
    </tr>
  </table>
</div>

<!-- Header Meta Information (PO/Customer Removed) -->
<table class="meta-table">
  <tr>
    <td class="meta-label">Report No.</td>
    <td class="meta-sep">:</td>
    <td class="meta-val">{{ $reportNo }}</td>
    
    <td class="meta-label">Heat Number</td>
    <td class="meta-sep">:</td>
    <td class="meta-val">{{ trim(($sample->heat_no ?? '') . ($sample->batch_no ? ' / ' . $sample->batch_no : '')) ?: '—' }}</td>
  </tr>
  <tr>
    <td class="meta-label">Date</td>
    <td class="meta-sep">:</td>
    <td class="meta-val">{{ $reportDate }}</td>
    
    <td class="meta-label">Standard</td>
    <td class="meta-sep">:</td>
    <td class="meta-val">{{ $sample->standard ?? ($stdFromCfg ?? '—') }}</td>
  </tr>
  <tr>
    <td class="meta-label">Sample Identification</td>
    <td class="meta-sep">:</td>
    <td class="meta-val">{{ $sample->product_type ?? '—' }}</td>
    
    <td class="meta-label">Grade</td>
    <td class="meta-sep">:</td>
    <td class="meta-val">{{ $sample->grade ?? '—' }}</td>
  </tr>
</table>

<!-- TABLE 1: Chemical Composition (Parameters 1 - 12) -->
<div class="table-section-title">Chemical Composition</div>
<table class="data-table">
  <thead>
    <tr>
      <th style="width: 5%;">No</th>
      <th style="width: 35%;">Parameter</th>
      <th style="width: 10%;">Unit</th>
      <th style="width: 14%;">Test Result</th>
      <th style="width: 13%;">Min</th>
      <th style="width: 13%;">Max</th>
      <th style="width: 10%;">Status</th>
    </tr>
  </thead>
  <tbody>
    @php $no = 1; @endphp

    {{-- Chemical 1 - 12 --}}
    @php [$min,$max] = $range('c'); $val = $actual($s,'c'); $unit = '% wt'; @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Carbon (C)</td><td class="center">{{ $unit }}</td>
      <td class="right">{{ $fmt($val, 4) }}</td><td class="right">{{ $fmt($min, 4) }}</td><td class="right">{{ $fmt($max, 4) }}</td>
      <td class="center {{ $judge($val,$min,$max)==='FAIL'?'fail':'' }}">{{ $judge($val,$min,$max) }}</td>
    </tr>

    @php [$min,$max] = $range('si'); $val = $actual($s,'si'); $unit = '% wt'; @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Silicon (Si)</td><td class="center">{{ $unit }}</td>
      <td class="right">{{ $fmt($val, 4) }}</td><td class="right">{{ $fmt($min, 4) }}</td><td class="right">{{ $fmt($max, 4) }}</td>
      <td class="center {{ $judge($val,$min,$max)==='FAIL'?'fail':'' }}">{{ $judge($val,$min,$max) }}</td>
    </tr>

    @php [$min,$max] = $range('mn'); $val = $actual($s,'mn'); $unit = '% wt'; @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Manganese (Mn)</td><td class="center">{{ $unit }}</td>
      <td class="right">{{ $fmt($val, 4) }}</td><td class="right">{{ $fmt($min, 4) }}</td><td class="right">{{ $fmt($max, 4) }}</td>
      <td class="center {{ $judge($val,$min,$max)==='FAIL'?'fail':'' }}">{{ $judge($val,$min,$max) }}</td>
    </tr>

    @php [$min,$max] = $range('p'); $val = $actual($s,'p'); $unit = '% wt'; @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Phosphorus (P)</td><td class="center">{{ $unit }}</td>
      <td class="right">{{ $fmt($val, 4) }}</td><td class="right">{{ $fmt($min, 4) }}</td><td class="right">{{ $fmt($max, 4) }}</td>
      <td class="center {{ $judge($val,$min,$max)==='FAIL'?'fail':'' }}">{{ $judge($val,$min,$max) }}</td>
    </tr>

    @php [$min,$max] = $range('s'); $val = $actual($s,'s'); $unit = '% wt'; @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Sulphur (S)</td><td class="center">{{ $unit }}</td>
      <td class="right">{{ $fmt($val, 4) }}</td><td class="right">{{ $fmt($min, 4) }}</td><td class="right">{{ $fmt($max, 4) }}</td>
      <td class="center {{ $judge($val,$min,$max)==='FAIL'?'fail':'' }}">{{ $judge($val,$min,$max) }}</td>
    </tr>

    @php [$min,$max] = $range('cr'); $val = $actual($s,'cr'); $unit = '% wt'; @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Chromium (Cr)</td><td class="center">{{ $unit }}</td>
      <td class="right">{{ $fmt($val, 2) }}</td><td class="right">{{ $fmt($min, 2) }}</td><td class="right">{{ $fmt($max, 2) }}</td>
      <td class="center {{ $judge($val,$min,$max)==='FAIL'?'fail':'' }}">{{ $judge($val,$min,$max) }}</td>
    </tr>

    @php [$min,$max] = $range('ni'); $val = $actual($s,'ni'); $unit = '% wt'; @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Nickel (Ni)</td><td class="center">{{ $unit }}</td>
      <td class="right">{{ $fmt($val, 2) }}</td><td class="right">{{ $fmt($min, 2) }}</td><td class="right">{{ $fmt($max, 2) }}</td>
      <td class="center {{ $judge($val,$min,$max)==='FAIL'?'fail':'' }}">{{ $judge($val,$min,$max) }}</td>
    </tr>

    @php [$min,$max] = $range('mo'); $val = $actual($s,'mo'); $unit = '% wt'; @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Molybdenum (Mo)</td><td class="center">{{ $unit }}</td>
      <td class="right">{{ $fmt($val, 3) }}</td><td class="right">{{ $fmt($min, 3) }}</td><td class="right">{{ $fmt($max, 3) }}</td>
      <td class="center {{ $judge($val,$min,$max)==='FAIL'?'fail':'' }}">{{ $judge($val,$min,$max) }}</td>
    </tr>

    @php [$min,$max] = $range('cu'); $val = $actual($s,'cu'); $unit = '% wt'; @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Copper (Cu)</td><td class="center">{{ $unit }}</td>
      <td class="right">{{ $fmt($val, 3) }}</td><td class="right">{{ $fmt($min, 3) }}</td><td class="right">{{ $fmt($max, 3) }}</td>
      <td class="center {{ $judge($val,$min,$max)==='FAIL'?'fail':'' }}">{{ $judge($val,$min,$max) }}</td>
    </tr>

    @php [$min,$max] = $range('co'); $val = $actual($s,'co'); $unit = '% wt'; @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Cobalt (Co)</td><td class="center">{{ $unit }}</td>
      <td class="right">{{ $fmt($val, 3) }}</td><td class="right">{{ $fmt($min, 3) }}</td><td class="right">{{ $fmt($max, 3) }}</td>
      <td class="center {{ $judge($val,$min,$max)==='FAIL'?'fail':'' }}">{{ $judge($val,$min,$max) }}</td>
    </tr>

    @php [$min,$max] = $range('al'); $val = $actual($s,'al'); $unit = '% wt'; @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Aluminium (Al)</td><td class="center">{{ $unit }}</td>
      <td class="right">{{ $fmt($val, 3) }}</td><td class="right">{{ $fmt($min, 3) }}</td><td class="right">{{ $fmt($max, 3) }}</td>
      <td class="center {{ $judge($val,$min,$max)==='FAIL'?'fail':'' }}">{{ $judge($val,$min,$max) }}</td>
    </tr>

    @php [$min,$max] = $range('v'); $val = $actual($s,'v'); $unit = '% wt'; @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Vanadium (V)</td><td class="center">{{ $unit }}</td>
      <td class="right">{{ $fmt($val, 3) }}</td><td class="right">{{ $fmt($min, 3) }}</td><td class="right">{{ $fmt($max, 3) }}</td>
      <td class="center {{ $judge($val,$min,$max)==='FAIL'?'fail':'' }}">{{ $judge($val,$min,$max) }}</td>
    </tr>
  </tbody>
</table>

<!-- TABLE 2: Mechanical Properties (Parameters 13 - 16) -->
<div class="table-section-title">Mechanical Properties</div>
<table class="data-table">
  <thead>
    <tr>
      <th style="width: 5%;">No</th>
      <th style="width: 35%;">Parameter</th>
      <th style="width: 10%;">Unit</th>
      <th style="width: 14%;">Test Result</th>
      <th style="width: 13%;">Min</th>
      <th style="width: 13%;">Max</th>
      <th style="width: 10%;">Status</th>
    </tr>
  </thead>
  <tbody>
    {{-- Mechanical 13 - 16 --}}
    @php [$min,$max] = $range('ys_mpa'); $val = $actual($t,'ys_mpa'); $unit = 'MPa'; @endphp
    <tr>
      <td class="center">13</td><td>Yield Strength</td><td class="center">{{ $unit }}</td>
      <td class="right">{{ $fmt($val, 2) }}</td><td class="right">{{ $fmt($min, 2) }}</td><td class="right">{{ $fmt($max, 2) }}</td>
      <td class="center {{ $judge($val,$min,$max)==='FAIL'?'fail':'' }}">{{ $judge($val,$min,$max) }}</td>
    </tr>

    @php [$min,$max] = $range('uts_mpa'); $val = $actual($t,'uts_mpa'); $unit = 'MPa'; @endphp
    <tr>
      <td class="center">14</td><td>Ultimate Tensile Strength</td><td class="center">{{ $unit }}</td>
      <td class="right">{{ $fmt($val, 2) }}</td><td class="right">{{ $fmt($min, 2) }}</td><td class="right">{{ $fmt($max, 2) }}</td>
      <td class="center {{ $judge($val,$min,$max)==='FAIL'?'fail':'' }}">{{ $judge($val,$min,$max) }}</td>
    </tr>

    @php [$min,$max] = $range('elong_pct'); $val = $actual($t,'elong_pct'); $unit = '%'; @endphp
    <tr>
      <td class="center">15</td><td>Elongation</td><td class="center">{{ $unit }}</td>
      <td class="right">{{ $fmt($val, 2) }}</td><td class="right">{{ $fmt($min, 2) }}</td><td class="right">{{ $fmt($max, 2) }}</td>
      <td class="center {{ $judge($val,$min,$max)==='FAIL'?'fail':'' }}">{{ $judge($val,$min,$max) }}</td>
    </tr>

    @php [$min,$max] = $range('hb'); $val = $actual($h,'avg_value'); $unit = 'HB'; @endphp
    <tr>
      <td class="center">16</td><td>Brinell Hardness</td><td class="center">{{ $unit }}</td>
      <td class="right">{{ $fmt($val, 2) }}</td><td class="right">{{ $fmt($min, 2) }}</td><td class="right">{{ $fmt($max, 2) }}</td>
      <td class="center {{ $judge($val,$min,$max)==='FAIL'?'fail':'' }}">{{ $judge($val,$min,$max) }}</td>
    </tr>
  </tbody>
</table>

<!-- Static Reference Standards & Testing Methods -->
<div class="static-section">
  <div class="sec-heading">Reference Standard :</div>
  <ul class="ref-list">
    <li>ASTM A351 (CF8, CF8M)</li>
    <li>JIS G 5121 (SCS 13A, SCS 14A)</li>
    <li>BS EN ISO 10213 (1.4308, 1.4408)</li>
  </ul>

  <div class="sec-heading">Composition testing methods</div>
  <div class="sec-body">
    Chemical composition is verified by Optical Emission Spectrometry (OES) in accordance with the applicable material standards: ASTM A351/A351M, JIS G5121, and EN 10283.
  </div>

  <div class="sec-heading">Tensile Testing Method</div>
  <div class="sec-body">
    Tensile properties are determined in accordance with the applicable test methods: ASTM E8/E8M, JIS Z 2241, and EN ISO 6892-1.
  </div>

  <div class="sec-heading">Brinell Hardness (HB) Test Method</div>
  <div class="sec-body">
    Brinell hardness is determined in accordance with the applicable test methods: ASTM E10, JIS Z 2243, and EN ISO 6506-1.
  </div>

  <div class="sec-heading">Tensile Test Specimen</div>
  <div class="sec-body">
    Tensile test specimens are prepared in accordance with the applicable requirements of ASTM A703/A703M, JIS Z 2241, and BS EN ISO 6892-1
  </div>
</div>

<!-- Signatures: Checked by (Left - Purple) & Approved By (Right - Existing Stamp/Date) Without Box Borders -->
<table class="sign-wrapper">
  <tr>
    <!-- Left: Checked by (Static, Purple) -->
    <td>
      <div class="sign-area">
        <div class="sign-title">Checked by</div>
        <div class="qc-passed-badge">QC PASSED</div>
        <div class="qc-verif-text">QC Verification</div>
      </div>
    </td>

    <!-- Right: Approved By (Dynamic with existing stamp/signature) -->
    <td>
      <div class="sign-area">
        <div class="sign-title">Approved By</div>
        @if($approved)
          @if(!empty($signatureData))
            <img class="sig-img" src="{{ $signatureData }}" alt="Signature">
          @endif
          @if(!empty($stampImgData))
            <img class="stamp-img" src="{{ $stampImgData }}" alt="Stamp">
          @endif
        @endif
        <div class="role-text">Kabag QC</div>
        <div class="date-text">Approved on: <strong>{{ $approvedDate }}</strong></div>
      </div>
    </td>
  </tr>
</table>

<!-- Footer directly below approval section with complete metadata -->
<div class="footer-container">
  <table style="width: 100%; border-collapse: collapse;">
    <tr>
      <td style="width: 58%; border: none; padding: 0; font-size: 6.2pt; color: #555; vertical-align: top; line-height: 1.25;">
        Printed by: <strong>{{ $stampUser ?? '-' }}</strong><br>
        Access time (WIB): <strong>{{ $stampTime ?? '-' }}</strong><br>
        Stamp ID: <strong>{{ $stampId ?? '-' }}</strong>
      </td>
      <td style="width: 42%; border: none; padding: 0; font-size: 6.2pt; color: #555; text-align: right; vertical-align: top; line-height: 1.25;">
        Document: QC-Report #{{ $sample->id ?? '-' }}<br>
        Page 1 / 1
      </td>
    </tr>
  </table>
</div>

</body>
</html>
