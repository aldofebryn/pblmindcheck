@extends('layouts.admin')
@section('title','Pengaturan')

@section('content')
<div class="max-w-3xl space-y-7">

    <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm">
        <h3 class="font-bold text-slate-800 text-xl mb-6">Informasi sistem</h3>
        <div class="space-y-4">
            @foreach([
                ['Nama Aplikasi',       'MindCheck — Sistem Skrining Kesehatan Mental'],
                ['Instrumen',           'DASS-21 (Lovibond & Lovibond, 1995)'],
                ['Metode Klasifikasi',  'Decision Tree (pohon keputusan biner)'],
                ['Versi',               '1.0.0'],
                ['Admin aktif',         $adminName],
            ] as [$k,$v])
            <div class="flex gap-6 py-3.5 border-b border-slate-50 last:border-0">
                <span class="text-slate-400 font-medium w-52 shrink-0">{{ $k }}</span>
                <span class="font-semibold text-slate-700">{{ $v }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm">
        <h3 class="font-bold text-slate-800 text-xl mb-2">Struktur decision tree</h3>
        <p class="text-slate-400 mb-7">Threshold klasifikasi per subskala (skor final = skor mentah × 2)</p>
        @foreach([
            ['Depresi',   [['Normal','0–9'],['Ringan','10–13'],['Sedang','14–20'],['Berat','21–27'],['Sangat Berat','≥28']],'blue'],
            ['Kecemasan', [['Normal','0–7'],['Ringan','8–9'],['Sedang','10–14'],['Berat','15–19'],['Sangat Berat','≥20']],'violet'],
            ['Stres',     [['Normal','0–14'],['Ringan','15–18'],['Sedang','19–25'],['Berat','26–33'],['Sangat Berat','≥34']],'orange'],
        ] as [$sub,$ranges,$c])
        <div class="mb-5 last:mb-0">
            <p class="font-bold text-{{ $c }}-700 mb-3">{{ $sub }}</p>
            <div class="flex gap-2 flex-wrap">
                @foreach($ranges as [$kat,$range])
                <span class="font-semibold px-4 py-2 rounded-xl bg-{{ $c }}-50 text-{{ $c }}-700 border border-{{ $c }}-200">
                    {{ $kat }}: {{ $range }}
                </span>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm">
        <h3 class="font-bold text-slate-800 text-xl mb-6">Pohon rekomendasi</h3>
        <div class="space-y-4">
            @foreach([
                ['R16','bg-red-50 text-red-700 border-red-200',   'Ada ≥ 1 subskala Berat atau Sangat Berat',   'Segera konsultasi profesional + SEJIWA 119 ext 8'],
                ['R17','bg-amber-50 text-amber-700 border-amber-200','Ada ≥ 1 subskala Sedang (tidak ada Berat)', 'Disarankan konsultasi psikolog / konselor'],
                ['R18','bg-emerald-50 text-emerald-700 border-emerald-200','Semua subskala Normal atau Ringan',      'Pantau mandiri, skrining ulang 2–4 minggu'],
            ] as [$kode,$badge,$cond,$action])
            <div class="flex gap-5 items-start p-5 rounded-2xl bg-slate-50">
                <span class="font-bold px-3 py-1.5 rounded-xl border {{ $badge }} shrink-0 mt-0.5">{{ $kode }}</span>
                <div>
                    <p class="font-semibold text-slate-700">{{ $cond }}</p>
                    <p class="text-slate-400 mt-1">→ {{ $action }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-7 flex gap-4">
        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-amber-800 leading-relaxed">
            MindCheck adalah sistem skrining awal berbasis instrumen tervalidasi. Tidak menggantikan diagnosis klinis oleh tenaga kesehatan berlisensi.
            Pertanyaan DASS-21 bersifat fixed sesuai standar Lovibond &amp; Lovibond (1995).
        </p>
    </div>
</div>
@endsection