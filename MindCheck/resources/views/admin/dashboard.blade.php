@extends('layouts.admin')
@section('title','Dashboard')

@section('content')

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
@php
$cards = [
    ['Total Sesi',       $totalSesi,  'bg-blue-50 text-blue-600',
     'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
    ['Token Unik',       $totalToken, 'bg-violet-50 text-violet-600',
     'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
    ['Perlu Perhatian',  $r16,        'bg-red-50 text-red-600',
     'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
    ['Pantau Mandiri',   $r18,        'bg-emerald-50 text-emerald-600',
     'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
];
@endphp
@foreach($cards as [$label,$val,$ic,$path])
<div class="bg-white border border-slate-100 rounded-2xl p-7 shadow-sm">
    <span class="w-12 h-12 {{ $ic }} rounded-2xl flex items-center justify-center mb-5">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/></svg>
    </span>
    <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">{{ $label }}</p>
    <p class="font-bold text-slate-900" style="font-size:2rem">{{ number_format($val) }}</p>
</div>
@endforeach
</div>

{{-- Charts row --}}
<div class="grid lg:grid-cols-3 gap-5 mb-8">
    <div class="lg:col-span-2 bg-white border border-slate-100 rounded-2xl p-7 shadow-sm">
        <h3 class="font-bold text-slate-800 text-lg mb-5">Tren skrining 7 hari terakhir</h3>
        <div class="relative" style="height:240px"><canvas id="trenChart"></canvas></div>
    </div>
    <div class="bg-white border border-slate-100 rounded-2xl p-7 shadow-sm">
        <h3 class="font-bold text-slate-800 text-lg mb-5">Distribusi rekomendasi</h3>
        <div class="relative flex items-center justify-center" style="height:180px"><canvas id="rekChart"></canvas></div>
        <div class="mt-5 space-y-3">
            @foreach([['R16','Perlu Segera',$r16,'#ef4444'],['R17','Disarankan',$r17,'#f59e0b'],['R18','Pantau Mandiri',$r18,'#10b981']] as [$k,$l,$v,$c])
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full shrink-0" style="background:{{ $c }}"></span>
                    <span class="text-slate-500 font-medium">{{ $l }}</span>
                </div>
                <span class="font-bold text-slate-800">{{ number_format($v) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Kategori --}}
<div class="bg-white border border-slate-100 rounded-2xl p-7 shadow-sm mb-8">
    <h3 class="font-bold text-slate-800 text-lg mb-5">Distribusi kategori per subskala</h3>
    <div class="relative" style="height:240px"><canvas id="katChart"></canvas></div>
</div>

{{-- Tabel recent --}}
<div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
    <div class="px-7 py-5 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-slate-800 text-lg">10 Hasil skrining terbaru</h3>
        <span class="text-sm text-slate-400">Token ditampilkan anonim</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-slate-50 text-sm font-bold text-slate-400 uppercase tracking-wider">
                    <th class="px-7 py-4 text-left">Token</th>
                    <th class="px-5 py-4 text-center">D</th>
                    <th class="px-5 py-4 text-center">A</th>
                    <th class="px-5 py-4 text-center">S</th>
                    <th class="px-5 py-4 text-left">Rekomendasi</th>
                    <th class="px-5 py-4 text-left">Waktu</th>
                    <th class="px-5 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($recent as $s)
                @php $r=$s->result; @endphp
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-7 py-4 font-mono text-slate-500">{{ substr($s->patient_token,0,8) }}…</td>
                    @if($r)
                    <td class="px-5 py-4 text-center">
                        <span class="font-bold text-slate-800 text-lg block">{{ $r->skor_depresi }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full border {{ $r->badgeD() }} font-semibold">{{ $r->kat_depresi }}</span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="font-bold text-slate-800 text-lg block">{{ $r->skor_kecemasan }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full border {{ $r->badgeA() }} font-semibold">{{ $r->kat_kecemasan }}</span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="font-bold text-slate-800 text-lg block">{{ $r->skor_stres }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full border {{ $r->badgeS() }} font-semibold">{{ $r->kat_stres }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <span class="font-semibold px-3 py-1.5 rounded-full border {{ $r->rekBadge() }}">{{ $r->rekLabel() }}</span>
                    </td>
                    @else
                    <td colspan="4" class="px-5 py-4 text-slate-400">—</td>
                    @endif
                    <td class="px-5 py-4 text-slate-400 whitespace-nowrap">{{ $s->selesai_at?->diffForHumans() }}</td>
                    <td class="px-5 py-4">
                        <a href="{{ route('admin.token.detail',$s->patient_token) }}" class="text-blue-600 hover:text-blue-700 font-bold">Detail →</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-7 py-12 text-center text-slate-400 text-lg">Belum ada data skrining.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
new Chart(document.getElementById('trenChart'),{
    type:'bar',
    data:{
        labels:@json(collect($tren)->pluck('label')),
        datasets:[{label:'Jumlah Sesi',data:@json(collect($tren)->pluck('count')),
            backgroundColor:'rgba(59,130,246,0.15)',borderColor:'#3b82f6',borderWidth:2,borderRadius:10}]
    },
    options:{responsive:true,maintainAspectRatio:false,
        plugins:{legend:{display:false}},
        scales:{y:{beginAtZero:true,grid:{color:'#f1f5f9'},ticks:{font:{size:13},stepSize:1}},x:{grid:{display:false},ticks:{font:{size:13}}}}}
});

new Chart(document.getElementById('rekChart'),{
    type:'doughnut',
    data:{
        labels:['Perlu Segera','Disarankan','Pantau Mandiri'],
        datasets:[{data:[{{ $r16 }},{{ $r17 }},{{ $r18 }}],
            backgroundColor:['#ef4444','#f59e0b','#10b981'],borderWidth:0,hoverOffset:8}]
    },
    options:{responsive:true,maintainAspectRatio:false,cutout:'70%',plugins:{legend:{display:false}}}
});

new Chart(document.getElementById('katChart'),{
    type:'bar',
    data:{
        labels:@json($katLabels),
        datasets:[
            {label:'Depresi',  data:@json($katDepresi),   backgroundColor:'#3b82f6',borderRadius:8},
            {label:'Kecemasan',data:@json($katKecemasan), backgroundColor:'#8b5cf6',borderRadius:8},
            {label:'Stres',    data:@json($katStres),     backgroundColor:'#f97316',borderRadius:8},
        ]
    },
    options:{responsive:true,maintainAspectRatio:false,
        plugins:{legend:{position:'top',labels:{usePointStyle:true,font:{size:13},padding:20}}},
        scales:{y:{beginAtZero:true,grid:{color:'#f1f5f9'},ticks:{font:{size:13},stepSize:1}},x:{grid:{display:false},ticks:{font:{size:13}}}}}
});
</script>
@endpush