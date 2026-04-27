@extends('layouts.app')
@section('title', 'Hasil Screening DASS-21')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-8 lg:px-16 py-10">

    {{-- HEADER --}}
    <div class="border-b border-slate-200 pb-5 mb-10">
        <p class="text-sm font-semibold text-slate-400 uppercase tracking-wide">Hasil Screening DASS-21</p>
        <h1 class="text-3xl lg:text-4xl font-bold text-slate-800 mt-1">Laporan Riwayat Screening</h1>
        <p class="text-slate-500 text-sm mt-2">{{ $screening->selesai_at?->format('d M Y, H:i') }} WIB</p>
    </div>

    {{-- GRAFIK VISUAL (Bar + Line) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-14">
        {{-- Bar Chart --}}
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-slate-700 flex items-center gap-2 border-l-4 border-blue-500 pl-3 mb-5">
                Skor Saat Ini
            </h2>
            <canvas id="barChartHasil" style="height: 260px; width: 100%;"></canvas>
            <div class="flex flex-wrap justify-center gap-3 mt-5 text-sm">
                @foreach($barData['categories'] as $idx => $kat)
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-50 border border-slate-200 text-slate-700">
                    <strong>{{ $barData['labels'][$idx] }}</strong>: {{ $kat }}
                </span>
                @endforeach
            </div>
        </div>

        {{-- Line Chart --}}
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-slate-700 flex items-center gap-2 border-l-4 border-blue-500 pl-3 mb-5">
                Perkembangan Skor dari Waktu ke Waktu
            </h2>
            <canvas id="lineChartRiwayat" style="height: 260px; width: 100%;"></canvas>
            <p class="text-xs text-slate-400 text-center mt-3">*menampilkan maksimal 5 screening terakhir</p>
        </div>
    </div>

    {{-- TIGA KARTU SKOR (model lama yang rapi, tidak mepet) --}}
    @php
    $subskala = [
        ['Depresi',   $result->skor_depresi,   $result->kat_depresi,   $result->pctD(), $result->badgeD(), 'bg-blue-500',
         ['Normal'=>'0–9','Ringan'=>'10–13','Sedang'=>'14–20','Berat'=>'21–27','Sangat Berat'=>'≥28']],
        ['Kecemasan', $result->skor_kecemasan, $result->kat_kecemasan, $result->pctA(), $result->badgeA(), 'bg-violet-500',
         ['Normal'=>'0–7','Ringan'=>'8–9','Sedang'=>'10–14','Berat'=>'15–19','Sangat Berat'=>'≥20']],
        ['Stres',     $result->skor_stres,     $result->kat_stres,     $result->pctS(), $result->badgeS(), 'bg-orange-500',
         ['Normal'=>'0–14','Ringan'=>'15–18','Sedang'=>'19–25','Berat'=>'26–33','Sangat Berat'=>'≥34']],
    ];
    @endphp

    <div class="grid sm:grid-cols-3 gap-6 mb-10">
        @foreach($subskala as [$nama,$skor,$kat,$pct,$badge,$bar,$ranges])
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
            <div class="flex justify-between items-start mb-6">
                <span class="text-base lg:text-lg font-bold text-slate-400 uppercase tracking-wider">{{ $nama }}</span>
                <span class="text-sm lg:text-base font-bold px-3 py-1.5 rounded-full border {{ $badge }}">{{ $kat }}</span>
            </div>
            <div class="mb-5">
                <span class="font-bold text-slate-900" style="font-size:4rem;line-height:1">{{ $skor }}</span>
                <span class="text-slate-400 font-medium text-2xl lg:text-3xl ml-2">/ 42</span>
            </div>
            <div class="h-4 lg:h-5 bg-slate-100 rounded-full overflow-hidden mb-5">
                <div class="h-full {{ $bar }} rounded-full" style="width:{{ $pct }}%"></div>
            </div>
            <div class="space-y-2">
                @foreach($ranges as $k => $v)
                <div class="flex justify-between text-sm lg:text-base {{ $k===$kat ? 'font-bold text-slate-800' : 'text-slate-400' }}">
                    <span>{{ $k }}</span><span class="font-mono">{{ $v }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    {{-- REKOMENDASI + SARAN (formal dengan emoji minimal) --}}
    @php
        $level = match($result->rekomendasi) {
            'R16' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-800', 'title' => 'Rekomendasi Konsultasi Psikolog', 'hotline' => true],
            'R17' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-800', 'title' => 'Disarankan Konsultasi', 'hotline' => false],
            default => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-800', 'title' => 'Pantau Mandiri', 'hotline' => false],
        };
    @endphp

    <div class="{{ $level['bg'] }} border {{ $level['border'] }} rounded-2xl shadow-sm p-8 mb-10">
        <h2 class="text-2xl font-bold {{ $level['text'] }} border-b {{ $level['border'] }} pb-4 mb-5">
            {{ $level['title'] }}
        </h2>
        <p class="leading-relaxed text-slate-700 text-lg mb-6">
            {{ $teks }}
        </p>

        @php
            $tips = [];
            if (in_array($result->kat_depresi, ['Berat','Sangat Berat']))
                $tips[] = 'Atur jadwal tidur teratur (7–8 jam) dan kurangi gadget sebelum tidur.';
            if (in_array($result->kat_kecemasan, ['Berat','Sangat Berat']))
                $tips[] = 'Latih pernapasan dalam (teknik 4-7-8) atau meditasi 5 menit setiap hari.';
            if (in_array($result->kat_stres, ['Berat','Sangat Berat']))
                $tips[] = 'Luangkan waktu berjalan santai 15 menit setiap hari di luar ruangan.';
        @endphp

        @if(count($tips))
        <div class="bg-white/70 rounded-xl p-5 mb-6 border border-slate-100">
            <p class="font-semibold text-slate-700 mb-2 flex items-center gap-2">Saran yang dapat Anda coba:</p>
            <ul class="list-disc list-inside space-y-1 text-slate-600">
                @foreach($tips as $tip)
                    <li>{{ $tip }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($level['hotline'])
        <div class="bg-white border border-red-200 rounded-xl p-5 mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-red-700">📞 Layanan Dukungan Darurat Mental</p>
                    <p class="font-bold text-red-800 text-lg">SEJIWA 119 ext 8 — 24 jam</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Tombol aksi – diperbesar dan jelas --}}
        <div class="flex flex-wrap gap-4 mt-2">
            <a href="{{ route('screening') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-7 py-3 rounded-xl shadow-md transition">
                Skrining Lagi
            </a>
            <a href="{{ route('history') }}" class="inline-flex items-center gap-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold px-7 py-3 rounded-xl shadow-sm transition">
                Lihat Riwayat
            </a>
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-7 py-3 rounded-xl shadow-sm transition">
                Kembali ke Dashboard
            </a>
        </div>

        {{-- Konsultasi screening selanjutnya --}}
        <div class="mt-8 pt-5 border-t border-slate-200 text-center">
            <p class="text-slate-500 text-sm">Butuh panduan lebih lanjut sebelum screening berikutnya?</p>
            <button onclick="alert('Silakan hubungi layanan konsultasi kami melalui WhatsApp 0812-3456-7890 atau email konseling@sehatjiwa.com')" 
                    class="text-blue-600 hover:text-blue-800 font-medium text-sm underline inline-flex items-center gap-1 mt-1">
                📞 Hubungi Layanan Konsultasi
            </button>
        </div>
    </div>

    {{-- Perbandingan sesi sebelumnya --}}
    @if($prev && $prev->result)
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm mb-8">
        <h3 class="font-semibold text-slate-800 text-lg flex items-center gap-2 mb-2">Perbandingan dengan Screening Sebelumnya</h3>
        <p class="text-slate-500 text-sm mb-5">{{ $prev->selesai_at?->format('d M Y') }}</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach([
                ['Depresi', $result->skor_depresi, $prev->result->skor_depresi],
                ['Kecemasan', $result->skor_kecemasan, $prev->result->skor_kecemasan],
                ['Stres', $result->skor_stres, $prev->result->skor_stres],
            ] as [$nm, $now, $before])
            @php $diff = $now - $before; @endphp
            <div class="bg-slate-50 rounded-xl p-4 flex justify-between items-center">
                <span class="font-medium text-slate-600">{{ $nm }}</span>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-800 text-lg">{{ $now }}</span>
                    @if($diff < 0)
                        <span class="text-emerald-600 text-sm font-medium">▼ {{ abs($diff) }}</span>
                    @elseif($diff > 0)
                        <span class="text-red-500 text-sm font-medium">▲ +{{ $diff }}</span>
                    @else
                        <span class="text-slate-300 text-sm">—</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Jejak decision tree --}}
    <details class="bg-slate-50 border border-slate-200 rounded-xl overflow-hidden">
        <summary class="px-6 py-3 text-sm font-medium text-slate-500 cursor-pointer hover:text-slate-700 select-none">
            Lihat detail keputusan klinis
        </summary>
        <div class="px-6 pb-4 font-mono text-sm text-slate-600 space-y-1 border-t border-slate-100 pt-3">
            <div>Depresi   : {{ $result->skor_depresi }} → {{ $result->kat_depresi }}</div>
            <div>Kecemasan : {{ $result->skor_kecemasan }} → {{ $result->kat_kecemasan }}</div>
            <div>Stres     : {{ $result->skor_stres }} → {{ $result->kat_stres }}</div>
            <div class="pt-1 text-slate-400">Rekomendasi: {{ $result->rekomendasi }} → {{ $result->rekLabel() }}</div>
        </div>
    </details>
</div>
@endsection

@push('scripts')
<script>
    // Bar chart
    const barCtx = document.getElementById('barChartHasil').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: @json($barData['labels']),
            datasets: [{
                label: 'Skor',
                data: @json($barData['scores']),
                backgroundColor: ['#3b82f6', '#8b5cf6', '#f97316'],
                borderRadius: 8,
                barPercentage: 0.65,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: { beginAtZero: true, max: 42, title: { display: true, text: 'Skor', font: { size: 12 } }, ticks: { stepSize: 14 } },
                x: { title: { display: true, text: 'Subskala', font: { size: 12 } } }
            },
            plugins: { legend: { display: false } }
        }
    });

    // Line chart
    @if(count($lineData['labels']) > 0)
    const lineCtx = document.getElementById('lineChartRiwayat').getContext('2d');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: @json($lineData['labels']),
            datasets: [
                { label: 'Depresi', data: @json($lineData['depresi']), borderColor: '#3b82f6', tension: 0.2, fill: false, pointRadius: 4, borderWidth: 2 },
                { label: 'Kecemasan', data: @json($lineData['kecemasan']), borderColor: '#8b5cf6', tension: 0.2, fill: false, pointRadius: 4, borderWidth: 2 },
                { label: 'Stres', data: @json($lineData['stres']), borderColor: '#f97316', tension: 0.2, fill: false, pointRadius: 4, borderWidth: 2 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: { beginAtZero: true, max: 42, title: { display: true, text: 'Skor' }, ticks: { stepSize: 14 } }
            }
        }
    });
    @endif
</script>
@endpush