@extends('layouts.admin')
@section('title','Detail Token')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="mb-7 flex items-center gap-4">
    <a href="{{ url()->previous() }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-500 hover:text-slate-900 hover:border-slate-300 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <p class="font-mono text-slate-400">{{ $token }}</p>
        <p class="font-bold text-slate-800 text-lg">{{ $screenings->count() }} sesi skrining</p>
    </div>
</div>

@if($screenings->count() > 1)
<div class="bg-white border border-slate-100 rounded-2xl p-7 shadow-sm mb-7">
    <h3 class="font-bold text-slate-800 text-lg mb-5">Tren skor token ini</h3>
    <div class="relative" style="height:240px"><canvas id="tokenChart"></canvas></div>
</div>
@endif

<div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
    <div class="px-7 py-5 border-b border-slate-100">
        <h3 class="font-bold text-slate-800 text-lg">Semua sesi</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-slate-50 text-sm font-bold text-slate-400 uppercase tracking-wider">
                    <th class="px-7 py-4 text-left">Tanggal</th>
                    <th class="px-5 py-4 text-center">Depresi</th>
                    <th class="px-5 py-4 text-center">Kecemasan</th>
                    <th class="px-5 py-4 text-center">Stres</th>
                    <th class="px-5 py-4 text-left">Rekomendasi</th>
                    <th class="px-5 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($screenings as $s)
                @php $r=$s->result; @endphp
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-7 py-5">
                        <span class="font-bold text-slate-800 text-lg block">{{ $s->selesai_at?->format('d M Y') }}</span>
                        <span class="text-slate-400">{{ $s->selesai_at?->format('H:i') }} WIB</span>
                    </td>
                    @if($r)
                    <td class="px-5 py-5 text-center">
                        <span class="font-bold text-slate-900 text-xl block">{{ $r->skor_depresi }}</span>
                        <span class="text-sm px-2.5 py-1 rounded-full border {{ $r->badgeD() }} font-semibold mt-1 inline-block">{{ $r->kat_depresi }}</span>
                    </td>
                    <td class="px-5 py-5 text-center">
                        <span class="font-bold text-slate-900 text-xl block">{{ $r->skor_kecemasan }}</span>
                        <span class="text-sm px-2.5 py-1 rounded-full border {{ $r->badgeA() }} font-semibold mt-1 inline-block">{{ $r->kat_kecemasan }}</span>
                    </td>
                    <td class="px-5 py-5 text-center">
                        <span class="font-bold text-slate-900 text-xl block">{{ $r->skor_stres }}</span>
                        <span class="text-sm px-2.5 py-1 rounded-full border {{ $r->badgeS() }} font-semibold mt-1 inline-block">{{ $r->kat_stres }}</span>
                    </td>
                    <td class="px-5 py-5">
                        <span class="font-semibold px-3 py-1.5 rounded-full border {{ $r->rekBadge() }}">{{ $r->rekLabel() }}</span>
                    </td>
                    @else
                    <td colspan="4" class="px-5 py-5 text-slate-400">—</td>
                    @endif
                    <td class="px-5 py-5">
                        <a href="{{ route('hasil',$s->id) }}" target="_blank" class="text-blue-600 hover:text-blue-700 font-bold">Detail →</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
@if($screenings->count() > 1)
<script>
const cd=@json($chartData);
new Chart(document.getElementById('tokenChart'),{
    type:'line',
    data:{labels:cd.map(d=>d.tanggal),
        datasets:[
            {label:'Depresi',  data:cd.map(d=>d.depresi),  borderColor:'#3b82f6',backgroundColor:'rgba(59,130,246,0.08)', tension:0.4,borderWidth:3,pointRadius:5,fill:true},
            {label:'Kecemasan',data:cd.map(d=>d.kecemasan),borderColor:'#8b5cf6',backgroundColor:'rgba(139,92,246,0.06)',tension:0.4,borderWidth:3,pointRadius:5,fill:true},
            {label:'Stres',    data:cd.map(d=>d.stres),    borderColor:'#f97316',backgroundColor:'rgba(249,115,22,0.06)', tension:0.4,borderWidth:3,pointRadius:5,fill:true},
        ]
    },
    options:{responsive:true,maintainAspectRatio:false,
        scales:{y:{min:0,max:42,grid:{color:'#f1f5f9'},ticks:{font:{size:13}}},x:{grid:{display:false},ticks:{font:{size:13}}}},
        plugins:{legend:{position:'top',labels:{usePointStyle:true,font:{size:13},padding:20}}}}
});
</script>
@endif
@endpush