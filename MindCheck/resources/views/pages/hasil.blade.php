@extends('layouts.app')
@section('title','Hasil Skrining')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="w-full max-w-screen-2xl mx-auto px-8 lg:px-16 py-12">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4 mb-10">
        <div>
            <p class="text-sm lg:text-base font-bold text-slate-400 uppercase tracking-widest mb-2">Hasil Skrining DASS-21</p>
            <h1 class="text-3xl lg:text-4xl font-bold text-slate-900">Laporan Kesehatan Mental Anda</h1>
        </div>
        <span class="text-slate-400 mt-2 text-base lg:text-lg">{{ $screening->selesai_at?->format('d M Y, H:i') }} WIB</span>
    </div>

    {{-- 3 kartu skor --}}
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

    {{-- Radar + Rekomendasi --}}
    @php
    $bgClass = match($result->rekomendasi) {
        'R16' => 'bg-red-50 border-red-200',
        'R17' => 'bg-amber-50 border-amber-200',
        default => 'bg-emerald-50 border-emerald-200',
    };
    $textClass = match($result->rekomendasi) {
        'R16' => 'text-red-800',
        'R17' => 'text-amber-800',
        default => 'text-emerald-800',
    };
    @endphp
    <div class="grid lg:grid-cols-5 gap-6 mb-10">
        <div class="lg:col-span-2 bg-white border border-slate-100 rounded-3xl p-8 shadow-sm flex flex-col">
            <h3 class="font-bold text-slate-800 text-xl lg:text-2xl mb-6">Profil kondisi</h3>
            <div class="flex-1 flex items-center justify-center">
                <div class="w-full max-w-md lg:max-w-lg mx-auto">
                    <canvas id="radarChart"></canvas>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3 {{ $bgClass }} border rounded-3xl p-8 shadow-sm flex flex-col">
            <div class="flex items-center gap-4 mb-5">
                @if($result->rekomendasi==='R16')
                    <div class="w-14 h-14 lg:w-16 lg:h-16 bg-red-100 rounded-2xl flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7 lg:w-8 lg:h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="font-bold text-red-800 text-2xl lg:text-3xl">Perlu Konsultasi Segera</h3>
                @elseif($result->rekomendasi==='R17')
                    <div class="w-14 h-14 lg:w-16 lg:h-16 bg-amber-100 rounded-2xl flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7 lg:w-8 lg:h-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-amber-800 text-2xl lg:text-3xl">Disarankan Konsultasi</h3>
                @else
                    <div class="w-14 h-14 lg:w-16 lg:h-16 bg-emerald-100 rounded-2xl flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7 lg:w-8 lg:h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-emerald-800 text-2xl lg:text-3xl">Pantau Secara Mandiri</h3>
                @endif
            </div>

            <p class="leading-relaxed text-lg lg:text-xl mb-6 {{ $textClass }}">
                {{ $teks }}
            </p>

            @if($result->rekomendasi==='R16')
            <div class="bg-white/60 border border-red-200 rounded-2xl px-6 py-5 flex items-center gap-4 mb-6">
                <div class="w-14 h-14 lg:w-16 lg:h-16 bg-red-100 rounded-2xl flex items-center justify-center shrink-0">
                    <svg class="w-7 h-7 lg:w-8 lg:h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <div>
                    <p class="text-sm lg:text-base font-bold text-red-700">Hotline Darurat Mental</p>
                    <p class="font-bold text-red-900 text-lg lg:text-xl">SEJIWA 119 ext 8 &nbsp;·&nbsp; 24 jam</p>
                </div>
            </div>
            @endif

            <div class="flex flex-wrap gap-4 mt-auto pt-2">
                <a href="{{ route('screening') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3.5 lg:px-8 lg:py-4 rounded-2xl transition-colors text-lg lg:text-xl">
                    Skrining Lagi
                </a>
                <a href="{{ route('history') }}" class="inline-flex items-center gap-2 bg-white border-2 border-slate-200 hover:border-slate-300 text-slate-700 font-bold px-6 py-3.5 lg:px-8 lg:py-4 rounded-2xl transition-colors text-lg lg:text-xl">
                    Lihat Riwayat
                </a>
            </div>
        </div>
    </div>

    {{-- Perbandingan sesi sebelumnya --}}
    @if($prev && $prev->result)
    <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm mb-6">
        <h3 class="font-bold text-slate-800 text-xl lg:text-2xl mb-6">Perbandingan dengan sesi sebelumnya
            <span class="font-normal text-slate-400 ml-2 text-base lg:text-lg">({{ $prev->selesai_at?->format('d M Y') }})</span>
        </h3>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['Depresi',   $result->skor_depresi,   $prev->result->skor_depresi],
                ['Kecemasan', $result->skor_kecemasan, $prev->result->skor_kecemasan],
                ['Stres',     $result->skor_stres,     $prev->result->skor_stres],
            ] as [$nm,$now,$before])
            @php $diff = $now - $before; @endphp
            <div class="flex items-center justify-between bg-slate-50 rounded-2xl px-6 py-6">
                <span class="font-semibold text-slate-600 text-lg lg:text-xl">{{ $nm }}</span>
                <div class="flex items-center gap-3">
                    <span class="font-bold text-slate-900 text-xl lg:text-2xl">{{ $now }}</span>
                    @if($diff < 0)
                        <span class="font-bold text-emerald-600 flex items-center gap-1 text-base lg:text-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                            {{ abs($diff) }}
                        </span>
                    @elseif($diff > 0)
                        <span class="font-bold text-red-500 flex items-center gap-1 text-base lg:text-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            +{{ $diff }}
                        </span>
                    @else
                        <span class="text-slate-300 text-lg lg:text-xl">—</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Trace DT --}}
    <details class="bg-slate-800 text-slate-200 rounded-2xl overflow-hidden">
        <summary class="px-6 py-4 text-sm lg:text-base font-semibold cursor-pointer select-none text-slate-400 hover:text-slate-200 transition-colors">
            Lihat jejak Decision Tree
        </summary>
        <div class="px-6 pb-5 font-mono text-sm lg:text-base space-y-1.5 text-slate-300">
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
new Chart(document.getElementById('radarChart'),{
    type:'radar',
    data:{
        labels:['Depresi','Kecemasan','Stres'],
        datasets:[{
            label:'Skor Anda',
            data:[{{ $result->skor_depresi }},{{ $result->skor_kecemasan }},{{ $result->skor_stres }}],
            backgroundColor:'rgba(59,130,246,0.15)',
            borderColor:'#3b82f6', borderWidth:2.5,
            pointBackgroundColor:'#3b82f6', pointRadius:6,
        }]
    },
    options:{
        responsive:true,
        scales:{
            r:{
                min:0,
                max:42,
                ticks:{
                    stepSize:14,
                    font:{size:16} // diperbesar
                },
                pointLabels:{
                    font:{size:18,weight:'700'}
                }
            }
        },
        plugins:{legend:{display:false}}
    }
});
</script>
@endpush