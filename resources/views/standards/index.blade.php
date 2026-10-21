@extends('layouts.modern', ['title' => 'Standard Reference'])

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20">
                <span class="material-symbols-outlined text-[14px]">lock</span>
                Read-Only Catalog
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                Single Source of Truth: <code>config/qc.php</code>
            </span>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Material Standards & Specifications</h1>
        <p class="text-slate-500 mt-1">Katalog acuan standard internasional dan batasan parameter kimia & mekanik material QC Lab.</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="rounded-lg bg-white dark:bg-slate-800 px-4 py-2 border border-slate-200 dark:border-slate-700 text-right shadow-sm">
            <div class="text-xs text-slate-400 font-medium uppercase tracking-wider">Total Standards / Grades</div>
            <div class="text-lg font-bold text-slate-900 dark:text-white">{{ count($groupedStandards) }} Standards / {{ $totalGrades }} Grades</div>
        </div>
    </div>
</div>

<div class="space-y-8">
    @foreach($groupedStandards as $standardName => $grades)
        <div class="rounded-xl bg-white dark:bg-slate-900 shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            {{-- Standard Header Banner --}}
            <div class="bg-slate-50 dark:bg-slate-800/80 px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined text-[20px]">verified</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">{{ $standardName }}</h2>
                        <p class="text-xs text-slate-500">Mencakup {{ count($grades) }} material grade spesifik</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @foreach($grades as $gName => $meta)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 shadow-2xs">
                            {{ $gName }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Grades in this Standard --}}
            <div class="p-6 divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($grades as $gradeName => $gradeInfo)
                    <div class="{{ !$loop->first ? 'pt-8' : '' }} {{ !$loop->last ? 'pb-8' : '' }}">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $gradeName }}</h3>
                                    @if(!empty($gradeInfo['label']) && $gradeInfo['label'] !== $gradeName)
                                        <span class="text-xs text-slate-500 font-medium">({{ $gradeInfo['label'] }})</span>
                                    @endif
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-primary/10 text-primary">
                                        Alias: {{ $gradeInfo['alias'] ?? '—' }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-xs text-slate-400">
                                Standard: <strong class="text-slate-700 dark:text-slate-300">{{ $gradeInfo['standard'] }}</strong>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            {{-- Chemical Composition Table (7 cols) --}}
                            <div class="lg:col-span-7">
                                <div class="rounded-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
                                    <div class="bg-slate-50 dark:bg-slate-800/50 px-3 py-2 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Komposisi Kimia (Chemical Limits)</span>
                                        <span class="text-[11px] text-slate-500 font-medium">Satuan: % wt</span>
                                    </div>
                                    <table class="w-full text-left text-xs border-collapse">
                                        <thead>
                                            <tr class="border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-400 uppercase text-[10px]">
                                                <th class="px-3 py-1.5 font-semibold">Parameter</th>
                                                <th class="px-3 py-1.5 font-semibold text-center">Simbol</th>
                                                <th class="px-3 py-1.5 font-semibold text-right">Min (% wt)</th>
                                                <th class="px-3 py-1.5 font-semibold text-right">Max (% wt)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300">
                                            @php
                                                $chemLimits = $gradeInfo['limits']['chem'] ?? [];
                                            @endphp
                                            @foreach($chemOrder as $el)
                                                @php
                                                    $lim = $chemLimits[$el] ?? [null, null];
                                                    $min = $lim[0] ?? null;
                                                    $max = $lim[1] ?? null;
                                                    $lbl = \App\Services\QCSpecificationService::getChemLabel($el);
                                                    $dec = in_array($el, ['cr', 'ni']) ? 2 : (in_array($el, ['c', 'si', 'mn', 'p', 's']) ? (is_float($min) || is_float($max) ? 3 : 2) : 2);
                                                    if ($el === 'p' || $el === 's') $dec = 3;
                                                @endphp
                                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                                    <td class="px-3 py-1 font-medium">{{ $lbl }}</td>
                                                    <td class="px-3 py-1 text-center font-mono text-[11px] text-slate-500 uppercase">{{ $el }}</td>
                                                    <td class="px-3 py-1 text-right font-mono {{ $min !== null ? 'font-bold text-slate-900 dark:text-white' : 'text-slate-400' }}">
                                                        {{ $min !== null ? number_format((float)$min, 2, '.', '') : '—' }}
                                                    </td>
                                                    <td class="px-3 py-1 text-right font-mono {{ $max !== null ? 'font-bold text-slate-900 dark:text-white' : 'text-slate-400' }}">
                                                        {{ $max !== null ? (in_array($el, ['c', 'si', 'mn', 'p', 's']) && (float)$max < 0.1 ? number_format((float)$max, 3, '.', '') : number_format((float)$max, 2, '.', '')) : '—' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Mechanical Properties Table (5 cols) --}}
                            <div class="lg:col-span-5">
                                <div class="rounded-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
                                    <div class="bg-slate-50 dark:bg-slate-800/50 px-3 py-2 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Sifat Mekanik (Mechanical Limits)</span>
                                        <span class="text-[11px] text-slate-500 font-medium">Baku Mutu Pengujian</span>
                                    </div>
                                    <table class="w-full text-left text-xs border-collapse">
                                        <thead>
                                            <tr class="border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-400 uppercase text-[10px]">
                                                <th class="px-3 py-1.5 font-semibold">Parameter</th>
                                                <th class="px-3 py-1.5 font-semibold text-center">Unit</th>
                                                <th class="px-3 py-1.5 font-semibold text-right">Min</th>
                                                <th class="px-3 py-1.5 font-semibold text-right">Max</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300">
                                            @php
                                                $mechLimits = $gradeInfo['limits']['mech'] ?? [];
                                                $mechKeys = ['ys_mpa', 'uts_mpa', 'elong_pct', 'hb'];
                                            @endphp
                                            @foreach($mechKeys as $mKey)
                                                @php
                                                    $mLim = $mechLimits[$mKey] ?? [null, null];
                                                    $mMin = $mLim[0] ?? null;
                                                    $mMax = $mLim[1] ?? null;
                                                    $mLbl = \App\Services\QCSpecificationService::getMechLabel($mKey);
                                                    $mUnit = \App\Services\QCSpecificationService::getMechUnit($mKey);
                                                @endphp
                                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                                    <td class="px-3 py-2 font-medium">{{ $mLbl }}</td>
                                                    <td class="px-3 py-2 text-center text-[11px] text-slate-500 font-mono">{{ $mUnit }}</td>
                                                    <td class="px-3 py-2 text-right font-mono {{ $mMin !== null ? 'font-bold text-slate-900 dark:text-white' : 'text-slate-400' }}">
                                                        {{ $mMin !== null ? (float)$mMin : '—' }}
                                                    </td>
                                                    <td class="px-3 py-2 text-right font-mono {{ $mMax !== null ? 'font-bold text-slate-900 dark:text-white' : 'text-slate-400' }}">
                                                        {{ $mMax !== null ? (float)$mMax : '—' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 text-[11px] text-slate-500">
                                    <div class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300 font-semibold mb-1">
                                        <span class="material-symbols-outlined text-[15px]">info</span>
                                        Catatan Evaluasi
                                    </div>
                                    Tanda hubung (<strong>—</strong>) menandakan nilai tidak dibatasi atau tidak disyaratkan dalam standard acuan. Sistem evaluator otomatis memberikan status <strong>—</strong> (tidak fail dan tidak 0) untuk parameter tersebut.
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endsection
