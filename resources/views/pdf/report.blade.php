@php
  use App\Support\QCHelpers as H;
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>QC Report</title>
<style>
  @page { margin: 100px 40px 100px 40px; }
  body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; }

  header {
    position: fixed; top: -70px; left: 0; right: 0; height: 50px;
    border-bottom: 1px solid #ccc;
  }
  footer {
    position: fixed; bottom: -60px; left: 0; right: 0; height: 50px;
    font-size: 10px; color: #555; border-top: 1px solid #ccc; padding-top: 6px;
  }
  .watermark {
    position: fixed; top: 35%; left: 10%; width: 80%;
    text-align: center; opacity: 0.12; transform: rotate(-25deg); font-size: 48px;
  }
  table { width: 100%; border-collapse: collapse; }
  th, td { border: 1px solid #ccc; padding: 6px; }
  th { background: #f2f2f2; }
  .right { text-align: right; }
  .center { text-align: center; }
</style>
</head>
<body>

@php
  // Ambil batas dari config berdasarkan grade (material)
  $cfg  = config('qc.params.' . $sample->grade) ?? ['chem'=>[], 'mech'=>[]];
  $chem = $cfg['chem'] ?? [];
  $mech = $cfg['mech'] ?? [];

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

  $s = $sample->spectroResult;
  $t = $sample->tensileTest;
  $h = $sample->hardnessTest;

  // helper ambil actual
  $actual = fn($obj, $field) => $obj ? ($obj->{$field} ?? null) : null;

@endphp

<header>
  <div style="padding-top:6px; font-size:14px;">
    <strong>REPORT OF ANALYSIS</strong>
  </div>
</header>

@if(!$isPreview)
  <div class="watermark">OFFICIAL COPY</div>
@else
  <div class="watermark">PREVIEW COPY</div>
@endif

{{-- Identitas singkat --}}
<table style="margin-top:10px;">
  <tr>
    <th style="width:25%;">Sample Identification</th>
    <td style="width:25%;">{{ $sample->product_type ?? '—' }}</td>
    <th style="width:25%;">Standard</th>
    <td style="width:25%;">{{ $sample->standard ?? '—' }}</td>
  </tr>
  <tr>
    <th>Grade</th>
    <td>{{ $sample->grade ?? '—' }}</td>
    <th>PO/Customer</th>
    <td>{{ $sample->po_customer ?? '—' }}</td>
  </tr>
  <tr>
    <th>Heat/Batch</th>
    <td colspan="3">{{ trim(($sample->heat_no ?? '').' / '.($sample->batch_no ?? '')) ?: '—' }}</td>
  </tr>
</table>

{{-- Tabel hasil --}}
<table style="margin-top:12px;">
  <thead>
    <tr>
      <th style="width:6%;">No</th>
      <th style="width:34%;">Parameter</th>
      <th style="width:10%;">Unit</th>
      <th style="width:15%;">Test Result</th>
      <th style="width:15%;">Min</th>
      <th style="width:15%;">Max</th>
      <th style="width:5%;">Status</th>
    </tr>
  </thead>
  <tbody>
    @php $no=1; @endphp

    {{-- 1 C --}}
    @php [$min,$max] = $range('c'); $val = $actual($s,'c'); @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Carbon (C)</td><td class="center">% wt</td>
      <td class="right">{{ $fmt($val,4) }}</td>
      <td class="right">{{ $fmt($min) }}</td>
      <td class="right">{{ $fmt($max) }}</td>
      <td class="center">{{ $judge($val,$min,$max) }}</td>
    </tr>

    {{-- 2 Si --}}
    @php [$min,$max] = $range('si'); $val = $actual($s,'si'); @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Silicon (Si)</td><td class="center">% wt</td>
      <td class="right">{{ $fmt($val,4) }}</td><td class="right">{{ $fmt($min) }}</td>
      <td class="right">{{ $fmt($max) }}</td><td class="center">{{ $judge($val,$min,$max) }}</td>
    </tr>

    {{-- 3 Mn --}}
    @php [$min,$max] = $range('mn'); $val = $actual($s,'mn'); @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Manganese (Mn)</td><td class="center">% wt</td>
      <td class="right">{{ $fmt($val,4) }}</td><td class="right">{{ $fmt($min) }}</td>
      <td class="right">{{ $fmt($max) }}</td><td class="center">{{ $judge($val,$min,$max) }}</td>
    </tr>

    {{-- 4 P --}}
    @php [$min,$max] = $range('p'); $val = $actual($s,'p'); @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Phosphorus (P)</td><td class="center">% wt</td>
      <td class="right">{{ $fmt($val,4) }}</td><td class="right">{{ $fmt($min) }}</td>
      <td class="right">{{ $fmt($max) }}</td><td class="center">{{ $judge($val,$min,$max) }}</td>
    </tr>

    {{-- 5 S --}}
    @php [$min,$max] = $range('s'); $val = $actual($s,'s'); @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Sulphur (S)</td><td class="center">% wt</td>
      <td class="right">{{ $fmt($val,4) }}</td><td class="right">{{ $fmt($min) }}</td>
      <td class="right">{{ $fmt($max) }}</td><td class="center">{{ $judge($val,$min,$max) }}</td>
    </tr>

    {{-- 6 Cr --}}
    @php [$min,$max] = $range('cr'); $val = $actual($s,'cr'); @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Chromium (Cr)</td><td class="center">% wt</td>
      <td class="right">{{ $fmt($val,2) }}</td><td class="right">{{ $fmt($min,2) }}</td>
      <td class="right">{{ $fmt($max,2) }}</td><td class="center">{{ $judge($val,$min,$max) }}</td>
    </tr>

    {{-- 7 Ni --}}
    @php [$min,$max] = $range('ni'); $val = $actual($s,'ni'); @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Nickel (Ni)</td><td class="center">% wt</td>
      <td class="right">{{ $fmt($val,2) }}</td><td class="right">{{ $fmt($min,2) }}</td>
      <td class="right">{{ $fmt($max,2) }}</td><td class="center">{{ $judge($val,$min,$max) }}</td>
    </tr>

    {{-- 8 Mo --}}
    @php [$min,$max] = $range('mo'); $val = $actual($s,'mo'); @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Molybdenum (Mo)</td><td class="center">% wt</td>
      <td class="right">{{ $fmt($val,3) }}</td><td class="right">{{ $fmt($min,3) }}</td>
      <td class="right">{{ $fmt($max,3) }}</td><td class="center">{{ $judge($val,$min,$max) }}</td>
    </tr>

    {{-- 9 Cu --}}
    @php [$min,$max] = $range('cu'); $val = $actual($s,'cu'); @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Copper (Cu)</td><td class="center">% wt</td>
      <td class="right">{{ $fmt($val,3) }}</td><td class="right">{{ $fmt($min,3) }}</td>
      <td class="right">{{ $fmt($max,3) }}</td><td class="center">{{ $judge($val,$min,$max) }}</td>
    </tr>

    {{-- 10 Co --}}
    @php [$min,$max] = $range('co'); $val = $actual($s,'co'); @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Cobalt (Co)</td><td class="center">% wt</td>
      <td class="right">{{ $fmt($val,3) }}</td><td class="right">{{ $fmt($min,3) }}</td>
      <td class="right">{{ $fmt($max,3) }}</td><td class="center">{{ $judge($val,$min,$max) }}</td>
    </tr>

    {{-- 11 Al --}}
    @php [$min,$max] = $range('al'); $val = $actual($s,'al'); @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Aluminium (Al)</td><td class="center">% wt</td>
      <td class="right">{{ $fmt($val,3) }}</td><td class="right">{{ $fmt($min,3) }}</td>
      <td class="right">{{ $fmt($max,3) }}</td><td class="center">{{ $judge($val,$min,$max) }}</td>
    </tr>

    {{-- 12 V --}}
    @php [$min,$max] = $range('v'); $val = $actual($s,'v'); @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Vanadium (V)</td><td class="center">% wt</td>
      <td class="right">{{ $fmt($val,3) }}</td><td class="right">{{ $fmt($min,3) }}</td>
      <td class="right">{{ $fmt($max,3) }}</td><td class="center">{{ $judge($val,$min,$max) }}</td>
    </tr>

    {{-- 13 YS --}}
    @php [$min,$max] = $range('ys_mpa'); $val = $actual($t,'ys_mpa'); @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Yield Strength</td><td class="center">MPa</td>
      <td class="right">{{ $fmt($val,2) }}</td><td class="right">{{ $fmt($min,2) }}</td>
      <td class="right">{{ $fmt($max,2) }}</td><td class="center">{{ $judge($val,$min,$max) }}</td>
    </tr>

    {{-- 14 UTS --}}
    @php [$min,$max] = $range('uts_mpa'); $val = $actual($t,'uts_mpa'); @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Ultimate Tensile Strength</td><td class="center">MPa</td>
      <td class="right">{{ $fmt($val,2) }}</td><td class="right">{{ $fmt($min,2) }}</td>
      <td class="right">{{ $fmt($max,2) }}</td><td class="center">{{ $judge($val,$min,$max) }}</td>
    </tr>

    {{-- 15 Elongation --}}
    @php [$min,$max] = $range('elong_pct'); $val = $actual($t,'elong_pct'); @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Elongation</td><td class="center">%</td>
      <td class="right">{{ $fmt($val,2) }}</td><td class="right">{{ $fmt($min,2) }}</td>
      <td class="right">{{ $fmt($max,2) }}</td><td class="center">{{ $judge($val,$min,$max) }}</td>
    </tr>

    {{-- 16 HB --}}
    @php [$min,$max] = $range('hb'); $val = $actual($h,'avg_value'); @endphp
    <tr>
      <td class="center">{{ $no++ }}</td><td>Brinell Hardness</td><td class="center">HB</td>
      <td class="right">{{ $fmt($val,2) }}</td><td class="right">{{ $fmt($min,2) }}</td>
      <td class="right">{{ $fmt($max,2) }}</td><td class="center">{{ $judge($val,$min,$max) }}</td>
    </tr>

  </tbody>
</table>

<footer>
  <div style="display:flex; justify-content:space-between;">
    <div>
      Printed by: <strong>{{ $stampUser }}</strong><br>
      Access time: <strong>{{ $stampTime }}</strong><br>
      Stamp ID: <strong>{{ $stampId }}</strong>
    </div>
    <div style="text-align:right;">
      Document: QC-Report #{{ $sample->id }}<br>
      Page <span class="pageNumber"></span> / <span class="totalPages"></span>
    </div>
  </div>
</footer>

{{-- Page numbers for DomPDF --}}
<script type="text/php">
if (isset($pdf)) {
    $pdf->page_text(520, 810, "Page {PAGE_NUM} / {PAGE_COUNT}", "DejaVu Sans", 8, array(0,0,0));
}
</script>

</body>
</html>
