@extends('layouts.patient')
@section('title','Dashboard Pasien')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="w-full max-w-6xl mx-auto py-2 space-y-6">

    {{-- Flash: Welcome baru daftar --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="font-medium text-emerald-800 text-sm">{{ session('success') }} Mulai skrining pertama Anda sekarang.</p>
    </div>
    @endif

    {{-- ── Greeting + Ringkasan Kondisi Terakhir ─────────────────── --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6">

            {{-- Kiri: Sapaan --}}
            <div>
                <p class="text-sm font-semibold text-blue-600 uppercase tracking-widest mb-1">Selamat Datang</p>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-1">
                    Halo, {{ $patient->alias ?? $patient->username }}!
                </h1>
                <p class="text-slate-400 text-sm">
                    ID Pasien: <span class="font-mono bg-slate-100 px-2 py-0.5 rounded-lg text-slate-600">{{ $id_pasien }}</span>
                </p>
            </div>

            {{-- Kanan: Tombol CTA / Status Cooldown --}}
            @if(! $canScreenNow)
            <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-5 py-3 shrink-0">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <div>
                    <p class="text-sm font-bold text-amber-800">Skrining terkunci</p>
                    <p class="text-xs text-amber-600">Tersedia: {{ $nextScreenDate }}</p>
                </div>
            </div>
            @else
            <a href="{{ route('screening') }}"
               class="inline-flex items-center gap-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-2xl text-sm sm:text-base transition-all shadow-md hover:shadow-blue-200 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Mulai Skrining Baru
            </a>
            @endif
        </div>

        {{-- Ringkasan kondisi terakhir --}}
        @if($lastResult)
        <div class="border-t border-slate-100 px-6 sm:px-8 py-4 bg-slate-50/50">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Kondisi Skrining Terakhir</p>
            <div class="flex flex-wrap gap-3">
                <span class="inline-flex items-center gap-1.5 text-sm font-semibold px-3 py-1.5 rounded-full border {{ $lastResult->badgeD() }}">
                    Depresi: {{ $lastResult->skor_depresi }} — {{ $lastResult->kat_depresi }}
                </span>
                <span class="inline-flex items-center gap-1.5 text-sm font-semibold px-3 py-1.5 rounded-full border {{ $lastResult->badgeA() }}">
                    Kecemasan: {{ $lastResult->skor_kecemasan }} — {{ $lastResult->kat_kecemasan }}
                </span>
                <span class="inline-flex items-center gap-1.5 text-sm font-semibold px-3 py-1.5 rounded-full border {{ $lastResult->badgeS() }}">
                    Stres: {{ $lastResult->skor_stres }} — {{ $lastResult->kat_stres }}
                </span>
            </div>
        </div>
        @endif
    </div>

    {{-- ── Empty state ─────────────────────────────────────────────── --}}
    @if($screenings->isEmpty())
    <div class="bg-white border border-slate-100 rounded-3xl p-16 sm:p-20 text-center shadow-sm">
        <div class="w-16 h-16 bg-blue-50 rounded-3xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="font-bold text-slate-700 text-lg sm:text-xl mb-2">Belum ada riwayat skrining</p>
        <p class="text-slate-400 mb-6 text-sm sm:text-base">Mulai skrining pertama Anda untuk melihat hasil kondisi kesehatan mental Anda.</p>
        <a href="{{ route('screening') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-xl text-sm transition-colors shadow-sm">
            Mulai Skrining Pertama
        </a>
    </div>

    @else

    {{-- ── Chart ──────────────────────────────────────────────────── --}}
    <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm">
        @if($screenings->count() === 1)
            {{-- 1 sesi: tampilkan bar chart skor sesi ini --}}
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-bold text-slate-800 text-lg">Hasil Skrining Pertama Anda</h3>
            </div>
            <p class="text-sm text-slate-400 mb-5">Lakukan skrining berikutnya untuk melihat tren perkembangan kondisi Anda dari waktu ke waktu.</p>
            <div class="relative w-full h-48 sm:h-56">
                <canvas id="singleBarChart" class="w-full h-full"></canvas>
            </div>
        @else
            {{-- ≥ 2 sesi: tampilkan line chart tren --}}
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-slate-800 text-lg sm:text-xl">Tren Skor dari Waktu ke Waktu</h3>
                <span class="text-xs text-slate-400 font-medium bg-slate-100 px-3 py-1 rounded-full">
                    {{ $screenings->count() > 10 ? '10 sesi terakhir' : $screenings->count() . ' sesi' }}
                </span>
            </div>
            <div class="relative w-full h-56 sm:h-72">
                <canvas id="trendChart" class="w-full h-full"></canvas>
            </div>
        @endif
    </div>

    {{-- ── Tabel Riwayat ───────────────────────────────────────────── --}}
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <div class="px-6 sm:px-8 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-lg sm:text-xl">Riwayat Skrining</h3>
            <span class="text-sm font-semibold text-slate-400 bg-slate-100 px-3 py-1 rounded-full">
                {{ $screenings->count() }} Sesi
            </span>
        </div>

        <div class="divide-y divide-slate-50">
            @foreach($screenings as $idx => $s)
            @php $r = $s->result; @endphp
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-6 sm:px-8 py-4 hover:bg-slate-50/60 transition-colors">

                {{-- Tanggal + badge terbaru --}}
                <div class="flex items-center gap-3 shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex flex-col items-center justify-center shrink-0 text-slate-600">
                        <span class="text-xs font-bold leading-none">{{ $s->selesai_at?->format('d') }}</span>
                        <span class="text-[10px] font-semibold text-slate-400">{{ $s->selesai_at?->format('M') }}</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-800 text-sm">{{ $s->selesai_at?->format('d F Y') }}</span>
                            @if($idx === 0)
                            <span class="text-[10px] font-bold bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">Terbaru</span>
                            @endif
                        </div>
                        <span class="text-xs text-slate-400">{{ $s->selesai_at?->format('H:i') }} WIB</span>
                    </div>
                </div>

                {{-- 3 skor + rekomendasi --}}
                @if($r)
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ $r->badgeD() }}">D: {{ $r->skor_depresi }}</span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ $r->badgeA() }}">A: {{ $r->skor_kecemasan }}</span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ $r->badgeS() }}">S: {{ $r->skor_stres }}</span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ $r->rekBadge() }}">{{ $r->rekLabel() }}</span>
                </div>
                @else
                <span class="text-slate-300 text-sm">—</span>
                @endif

                {{-- Aksi --}}
                <a href="{{ route('hasil', $s->id) }}"
                   class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-700 font-bold text-sm shrink-0 transition-colors">
                    Lihat Detail
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            @endforeach
        </div>

        {{-- Bottom CTA --}}
        <div class="px-6 sm:px-8 py-5 border-t border-slate-100 bg-slate-50/40 flex justify-center">
            @if(! $canScreenNow)
            <div class="flex items-center gap-2 text-amber-600 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Skrining berikutnya tersedia: {{ $nextScreenDate }}
            </div>
            @else
            <a href="{{ route('screening') }}"
               class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-semibold text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Mulai Skrining Baru
            </a>
            @endif
        </div>
    </div>

    @endif
</div>
@endsection

@push('scripts')
@if($screenings->count() === 1 && $lastResult)
<script>
new Chart(document.getElementById('singleBarChart'), {
    type: 'bar',
    data: {
        labels: ['Depresi', 'Kecemasan', 'Stres'],
        datasets: [{
            label: 'Skor',
            data: [{{ $lastResult->skor_depresi }}, {{ $lastResult->skor_kecemasan }}, {{ $lastResult->skor_stres }}],
            backgroundColor: ['rgba(59,130,246,0.15)', 'rgba(139,92,246,0.15)', 'rgba(249,115,22,0.15)'],
            borderColor: ['#3b82f6', '#8b5cf6', '#f97316'],
            borderWidth: 2,
            borderRadius: 8,
            barPercentage: 0.55,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true, max: 42, ticks: { stepSize: 14, font: { size: 12 } }, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false }, ticks: { font: { size: 13, weight: 'bold' } } }
        },
        plugins: { legend: { display: false } }
    }
});
</script>
@elseif($screenings->count() > 1)
<script>
const cd = @json($chartData);
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: cd.map(d => d.tanggal),
        datasets: [
            { label: 'Depresi',   data: cd.map(d => d.depresi),   borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.07)',  tension: 0.4, borderWidth: 2.5, pointRadius: 4, fill: true },
            { label: 'Kecemasan', data: cd.map(d => d.kecemasan), borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,0.06)',  tension: 0.4, borderWidth: 2.5, pointRadius: 4, fill: true },
            { label: 'Stres',     data: cd.map(d => d.stres),     borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,0.06)',  tension: 0.4, borderWidth: 2.5, pointRadius: 4, fill: true },
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        scales: {
            y: { min: 0, max: 42, grid: { color: '#f1f5f9' }, ticks: { stepSize: 14, font: { size: 12 } } },
            x: { grid: { display: false }, ticks: { font: { size: 12 } } }
        },
        plugins: { legend: { position: 'top', labels: { usePointStyle: true, font: { size: 13 }, padding: 18 } } }
    }
});
</script>
@endif
@endpush