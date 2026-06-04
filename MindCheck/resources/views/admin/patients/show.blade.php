@extends('layouts.admin')
@section('title', 'Detail Riwayat Pasien')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="mb-7 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.patients.index') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-500 hover:text-slate-900 hover:border-slate-300 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">{{ $patient->alias ?? $patient->username ?? 'Pasien Anonim' }}</h2>
            <div class="flex items-center gap-3 mt-1.5">
                <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-xs font-semibold">
                    {{ $patient->username ? '@'.$patient->username : 'No Username' }}
                </span>
                <span class="text-slate-500 text-sm">
                    {{ $patient->umur ? $patient->umur . ' tahun' : 'Umur -' }} • {{ $patient->status_pekerjaan ?? 'Pekerjaan -' }}
                </span>
            </div>
            <p class="font-mono text-slate-400 text-xs mt-1.5" title="ID">ID: {{ $patient->id }}</p>
        </div>
    </div>
    
    <div class="bg-indigo-50 border border-indigo-100 text-indigo-700 px-4 py-2 rounded-xl font-bold flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
        Total {{ $screenings->count() }} Sesi
    </div>
</div>

@if($patient->admin_notes)
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 shadow-sm mb-7">
    <h3 class="font-bold text-amber-800 text-sm uppercase tracking-wider mb-2 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
        Catatan Admin
    </h3>
    <p class="text-amber-900 whitespace-pre-wrap leading-relaxed">{{ $patient->admin_notes }}</p>
</div>
@endif

@if($screenings->count() > 1)
<div class="bg-white border border-slate-100 rounded-2xl p-7 shadow-sm mb-7">
    <h3 class="font-bold text-slate-800 text-lg mb-5 flex items-center gap-2">
        <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
        Tren Skor Pasien
    </h3>
    <div class="relative w-full" style="height:280px"><canvas id="patientChart"></canvas></div>
</div>
@endif

<div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
    <div class="px-7 py-5 border-b border-slate-100 bg-slate-50/50">
        <h3 class="font-bold text-slate-800 text-lg">Riwayat Semua Sesi</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-white text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
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
                <tr class="hover:bg-slate-50/80 transition-colors group">
                    <td class="px-7 py-5">
                        <span class="font-bold text-slate-800 text-lg block">{{ $s->selesai_at?->format('d M Y') }}</span>
                        <span class="text-slate-400 text-sm font-medium">{{ $s->selesai_at?->format('H:i') }} WIB</span>
                    </td>
                    @if($r)
                    <td class="px-5 py-5 text-center">
                        <span class="font-bold text-slate-900 text-2xl block">{{ $r->skor_depresi }}</span>
                        <span class="text-xs px-2.5 py-1 rounded-full border {{ $r->badgeD() }} font-bold mt-1.5 inline-block">{{ $r->kat_depresi }}</span>
                    </td>
                    <td class="px-5 py-5 text-center">
                        <span class="font-bold text-slate-900 text-2xl block">{{ $r->skor_kecemasan }}</span>
                        <span class="text-xs px-2.5 py-1 rounded-full border {{ $r->badgeA() }} font-bold mt-1.5 inline-block">{{ $r->kat_kecemasan }}</span>
                    </td>
                    <td class="px-5 py-5 text-center">
                        <span class="font-bold text-slate-900 text-2xl block">{{ $r->skor_stres }}</span>
                        <span class="text-xs px-2.5 py-1 rounded-full border {{ $r->badgeS() }} font-bold mt-1.5 inline-block">{{ $r->kat_stres }}</span>
                    </td>
                    <td class="px-5 py-5">
                        <span class="font-bold text-xs px-3 py-1.5 rounded-full border {{ $r->rekBadge() }} inline-block shadow-sm">{{ $r->rekLabel() }}</span>
                    </td>
                    @else
                    <td colspan="4" class="px-5 py-5 text-slate-400 text-center italic">— Belum Selesai —</td>
                    @endif
                    <td class="px-5 py-5 text-right text-slate-300 text-xs">—</td>
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
new Chart(document.getElementById('patientChart'),{
    type:'line',
    data:{labels:cd.map(d=>d.tanggal),
        datasets:[
            {label:'Depresi',  data:cd.map(d=>d.depresi),  borderColor:'#3b82f6',backgroundColor:'rgba(59,130,246,0.1)', tension:0.4,borderWidth:3,pointRadius:4,pointBackgroundColor:'#3b82f6',fill:true},
            {label:'Kecemasan',data:cd.map(d=>d.kecemasan),borderColor:'#8b5cf6',backgroundColor:'rgba(139,92,246,0.1)',tension:0.4,borderWidth:3,pointRadius:4,pointBackgroundColor:'#8b5cf6',fill:true},
            {label:'Stres',    data:cd.map(d=>d.stres),    borderColor:'#f97316',backgroundColor:'rgba(249,115,22,0.1)', tension:0.4,borderWidth:3,pointRadius:4,pointBackgroundColor:'#f97316',fill:true},
        ]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales:{
            y:{
                min:0,max:42,
                grid:{color:'#f8fafc', drawBorder: false},
                border: {dash: [4, 4]},
                ticks:{font:{size:12, family: "'Inter', sans-serif"}, color: '#94a3b8'}
            },
            x:{
                grid:{display:false},
                ticks:{font:{size:12, family: "'Inter', sans-serif"}, color: '#64748b'}
            }
        },
        plugins:{
            legend:{
                position:'top',
                labels:{usePointStyle:true,boxWidth:8,font:{size:13, family: "'Inter', sans-serif", weight: '600'},padding:20, color: '#334155'}
            },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                titleFont: {family: "'Inter', sans-serif", size: 13},
                bodyFont: {family: "'Inter', sans-serif", size: 13},
                padding: 12,
                cornerRadius: 8,
                usePointStyle: true,
            }
        }
    }
});
</script>
@endif
@endpush
