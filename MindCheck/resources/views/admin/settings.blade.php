@extends('layouts.admin')
@section('title','Pengaturan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- ── Informasi Sistem ─────────────────────────────────────── --}}
    <div class="bg-white/70 backdrop-blur-md border border-white/60 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 text-base">Informasi Sistem</h3>
        </div>
        <div class="divide-y divide-slate-50">
            @foreach([
                ['Nama Aplikasi',      'MindCheck — Sistem Skrining Kesehatan Mental'],
                ['Instrumen',          'DASS-21 (Lovibond & Lovibond, 1995)'],
                ['Metode Klasifikasi', 'Decision Tree (pohon keputusan biner)'],
                ['Versi',              '1.0.0'],
                ['Admin Aktif',        $adminName],
            ] as [$k, $v])
            <div class="flex items-center gap-4 px-6 py-3.5">
                <span class="text-slate-400 text-sm w-44 shrink-0">{{ $k }}</span>
                <span class="font-semibold text-slate-700 text-sm">{{ $v }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Struktur Decision Tree ───────────────────────────────── --}}
    <div class="bg-white/70 backdrop-blur-md border border-white/60 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-800 text-base">Struktur Decision Tree</h3>
                <p class="text-slate-400 text-xs mt-0.5">Threshold per subskala (skor final = skor mentah × 2)</p>
            </div>
        </div>
        <div class="divide-y divide-slate-50">
            @foreach([
                ['Depresi',   [['Normal','0–9'],['Ringan','10–13'],['Sedang','14–20'],['Berat','21–27'],['Sangat Berat','≥28']], 'blue'],
                ['Kecemasan', [['Normal','0–7'],['Ringan','8–9'],['Sedang','10–14'],['Berat','15–19'],['Sangat Berat','≥20']], 'violet'],
                ['Stres',     [['Normal','0–14'],['Ringan','15–18'],['Sedang','19–25'],['Berat','26–33'],['Sangat Berat','≥34']], 'orange'],
            ] as [$sub, $ranges, $c])
            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-3">
                <span class="text-sm font-bold text-{{ $c }}-700 w-24 shrink-0">{{ $sub }}</span>
                <div class="flex flex-wrap gap-2">
                    @foreach($ranges as [$kat, $range])
                    <span class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-{{ $c }}-50 text-{{ $c }}-700 border border-{{ $c }}-200 whitespace-nowrap">
                        {{ $kat }}<span class="ml-1 opacity-60">{{ $range }}</span>
                    </span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Pohon Rekomendasi ────────────────────────────────────── --}}
    <div class="bg-white/70 backdrop-blur-md border border-white/60 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 text-base">Pohon Rekomendasi</h3>
        </div>
        <div class="divide-y divide-slate-50">
            @foreach([
                ['R16', 'bg-red-50 text-red-700 border-red-200',        'Ada ≥ 1 subskala Berat atau Sangat Berat', 'Segera konsultasi profesional + SEJIWA 119 ext 8'],
                ['R17', 'bg-amber-50 text-amber-700 border-amber-200',  'Ada ≥ 1 subskala Sedang (tidak ada Berat)', 'Disarankan konsultasi psikolog / konselor'],
                ['R18', 'bg-emerald-50 text-emerald-700 border-emerald-200', 'Semua subskala Normal atau Ringan',    'Pantau mandiri, skrining ulang 2–4 minggu'],
            ] as [$kode, $badge, $cond, $action])
            <div class="flex items-start gap-4 px-6 py-4">
                <span class="text-xs font-bold px-2.5 py-1 rounded-lg border {{ $badge }} shrink-0 mt-0.5">{{ $kode }}</span>
                <div>
                    <p class="text-sm font-semibold text-slate-700">{{ $cond }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">→ {{ $action }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Disclaimer ───────────────────────────────────────────── --}}
    <div class="bg-amber-50/80 border border-amber-200/70 rounded-2xl px-6 py-4 flex gap-3 items-start">
        <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p class="text-amber-800 text-sm leading-relaxed">
            MindCheck adalah sistem skrining awal berbasis instrumen tervalidasi. Tidak menggantikan diagnosis klinis oleh tenaga kesehatan berlisensi.
            Pertanyaan DASS-21 bersifat <em>fixed</em> sesuai standar Lovibond &amp; Lovibond (1995).
        </p>
    </div>

</div>
@endsection