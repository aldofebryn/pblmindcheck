@extends('layouts.app')
@section('title','Riwayat Skrining')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-8 lg:px-16 py-8 sm:py-12">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-5 mb-10">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-2">Riwayat & Tren</h1>
            <p class="text-sm sm:text-base text-slate-400">
                Token: <span class="font-mono text-xs sm:text-sm bg-slate-100 px-2 sm:px-3 py-1 rounded-lg">{{ substr($token,0,8) }}…</span>
            </p>
        </div>
        <a href="{{ route('screening') }}"
           class="inline-flex items-center gap-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 sm:px-6 py-2.5 sm:py-3 rounded-2xl text-sm sm:text-lg transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Skrining Baru
        </a>
    </div>

    {{-- Jika belum ada riwayat --}}
    @if($screenings->isEmpty())
    <div class="bg-white border border-slate-100 rounded-3xl p-16 sm:p-20 text-center shadow-sm">
        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="font-bold text-slate-700 text-lg sm:text-xl">Belum ada riwayat skrining</p>
        <p class="text-slate-400 mt-2 mb-6 text-sm sm:text-lg">Mulai skrining pertama Anda sekarang.</p>
        <a href="{{ route('screening') }}" class="inline-flex items-center gap-2 text-blue-600 font-bold text-sm sm:text-lg hover:underline">Mulai Skrining →</a>
    </div>
    @else

    {{-- Chart tren --}}
    @if($screenings->count() > 1)
    <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm mb-8">
        <h3 class="font-bold text-slate-800 text-lg sm:text-xl mb-6">Tren skor dari waktu ke waktu</h3>
        <div class="relative w-full h-56 sm:h-72 md:h-80 lg:h-72 xl:h-80">
            <canvas id="trendChart" class="w-full h-full"></canvas>
        </div>
    </div>
    @endif

    {{-- Tabel --}}
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <div class="px-4 sm:px-8 py-4 sm:py-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-lg sm:text-xl">Semua sesi ({{ $screenings->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 text-xs sm:text-sm font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-3 sm:px-8 py-2 sm:py-4 text-left">Tanggal</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-4 text-center">Depresi</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-4 text-center">Kecemasan</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-4 text-center">Stres</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-4 text-left">Rekomendasi</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($screenings as $s)
                    @php $r = $s->result; @endphp
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-3 sm:px-8 py-2 sm:py-5">
                            <span class="font-bold text-slate-800 text-sm sm:text-lg">{{ $s->selesai_at?->format('d M Y') }}</span>
                            <span class="block text-slate-400 text-xs sm:text-sm">{{ $s->selesai_at?->format('H:i') }} WIB</span>
                        </td>
                        @if($r)
                        <td class="px-3 sm:px-6 py-2 sm:py-5 text-center">
                            <span class="font-bold text-slate-900 text-base sm:text-xl block">{{ $r->skor_depresi }}</span>
                            <span class="text-xs sm:text-sm px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full border {{ $r->badgeD() }} mt-1 inline-block font-semibold">{{ $r->kat_depresi }}</span>
                        </td>
                        <td class="px-3 sm:px-6 py-2 sm:py-5 text-center">
                            <span class="font-bold text-slate-900 text-base sm:text-xl block">{{ $r->skor_kecemasan }}</span>
                            <span class="text-xs sm:text-sm px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full border {{ $r->badgeA() }} mt-1 inline-block font-semibold">{{ $r->kat_kecemasan }}</span>
                        </td>
                        <td class="px-3 sm:px-6 py-2 sm:py-5 text-center">
                            <span class="font-bold text-slate-900 text-base sm:text-xl block">{{ $r->skor_stres }}</span>
                            <span class="text-xs sm:text-sm px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full border {{ $r->badgeS() }} mt-1 inline-block font-semibold">{{ $r->kat_stres }}</span>
                        </td>
                        <td class="px-3 sm:px-6 py-2 sm:py-5">
                            <span class="font-semibold text-xs sm:text-sm px-2.5 py-1 rounded-full border {{ $r->rekBadge() }}">{{ $r->rekLabel() }}</span>
                        </td>
                        @else
                        <td colspan="4" class="px-3 sm:px-6 py-2 sm:py-5 text-slate-400 text-sm">—</td>
                        @endif
                        <td class="px-3 sm:px-6 py-2 sm:py-5">
                            <a href="{{ route('hasil', $s->id) }}" class="text-blue-600 hover:text-blue-700 font-bold text-sm sm:text-base">Detail →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
@if($screenings->count() > 1)
<script>
const cd = @json($chartData);
new Chart(document.getElementById('trendChart'),{
    type:'line',
    data:{
        labels:cd.map(d=>d.tanggal),
        datasets:[
            {label:'Depresi',  data:cd.map(d=>d.depresi),   borderColor:'#3b82f6',backgroundColor:'rgba(59,130,246,0.08)',  tension:0.4,borderWidth:3,pointRadius:5,fill:true},
            {label:'Kecemasan',data:cd.map(d=>d.kecemasan), borderColor:'#8b5cf6',backgroundColor:'rgba(139,92,246,0.06)', tension:0.4,borderWidth:3,pointRadius:5,fill:true},
            {label:'Stres',    data:cd.map(d=>d.stres),     borderColor:'#f97316',backgroundColor:'rgba(249,115,22,0.06)', tension:0.4,borderWidth:3,pointRadius:5,fill:true},
        ]
    },
    options:{
        responsive:true,maintainAspectRatio:false,
        scales:{
            y:{min:0,max:42,grid:{color:'#f1f5f9'},ticks:{font:{size:13}}},
            x:{grid:{display:false},ticks:{font:{size:13}}}
        },
        plugins:{legend:{position:'top',labels:{usePointStyle:true,font:{size:14},padding:20}}}
    }
});
</script>
@endif
@endpush