@extends('layouts.admin')
@section('title','Dashboard')

@section('content')

{{-- Stat cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 mb-6 sm:mb-8">
@php
$cards = [
    [
        'Total Sesi',
        $totalSesi,
        'bg-blue-50 text-blue-600',
        'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'
    ],
    [
        'Total User',
        $totalUser,
        'bg-violet-50 text-violet-600',
        'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
    ],
    [
        'Perlu Perhatian',
        $r16,
        'bg-red-50 text-red-600',
        'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'
    ],
    [
        'Pantau Mandiri',
        $r18,
        'bg-emerald-50 text-emerald-600',
        'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'
    ],
];
@endphp

@foreach($cards as [$label, $val, $ic, $path])
    <div class="bg-white border border-slate-100 rounded-2xl p-5 sm:p-7 shadow-sm">
        <span class="w-11 h-11 sm:w-12 sm:h-12 {{ $ic }} rounded-2xl flex items-center justify-center mb-4 sm:mb-5">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
            </svg>
        </span>

        <p class="text-xs sm:text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">
            {{ $label }}
        </p>

        <p class="font-bold text-slate-900 text-2xl sm:text-[2rem] leading-tight">
            {{ number_format($val) }}
        </p>
    </div>
@endforeach
</div>

{{-- Charts row --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-5 mb-6 sm:mb-8">

    {{-- Visualisasi User --}}
    <div class="xl:col-span-2 bg-white border border-slate-100 rounded-2xl p-5 sm:p-7 shadow-sm">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <h3 class="font-bold text-slate-800 text-base sm:text-lg">
                Visualisasi User
            </h3>

            <select id="filterChart" class="border border-slate-300 rounded-lg px-3 py-2 sm:py-1 text-sm bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                <option value="harian">Harian</option>
                <option value="mingguan">Mingguan</option>
                <option value="bulanan">Bulanan</option>
            </select>
        </div>

        <div class="relative h-[220px] sm:h-[260px]">
            <canvas id="trenChart"></canvas>
        </div>
    </div>

    {{-- Distribusi Rekomendasi --}}
    <div class="bg-white border border-slate-100 rounded-2xl p-5 sm:p-7 shadow-sm">
        <h3 class="font-bold text-slate-800 text-base sm:text-lg mb-5">
            Distribusi rekomendasi
        </h3>

        <div class="relative flex items-center justify-center h-[180px]">
            <canvas id="rekChart"></canvas>
        </div>

        <div class="mt-5 space-y-3">
            @foreach([
                ['R16','Perlu Segera',$r16,'#ef4444'],
                ['R17','Disarankan',$r17,'#f59e0b'],
                ['R18','Pantau Mandiri',$r18,'#10b981']
            ] as [$k, $l, $v, $c])
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-3 h-3 rounded-full shrink-0" style="background:{{ $c }}"></span>
                        <span class="text-slate-500 font-medium text-sm sm:text-base truncate">
                            {{ $l }}
                        </span>
                    </div>

                    <span class="font-bold text-slate-800 shrink-0">
                        {{ number_format($v) }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Kategori --}}
<div class="bg-white border border-slate-100 rounded-2xl p-5 sm:p-7 shadow-sm mb-6 sm:mb-8">
    <h3 class="font-bold text-slate-800 text-base sm:text-lg mb-5">
        Distribusi kategori per subskala
    </h3>

    <div class="relative h-[240px] sm:h-[260px]">
        <canvas id="katChart"></canvas>
    </div>
</div>

{{-- Tabel recent --}}
<div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 sm:px-7 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <h3 class="font-bold text-slate-800 text-base sm:text-lg">
            10 Hasil skrining terbaru
        </h3>

        <span class="text-sm text-slate-400">
            ID ditampilkan anonim
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[780px]">
            <thead>
                <tr class="bg-slate-50 text-xs sm:text-sm font-bold text-slate-400 uppercase tracking-wider">
                    <th class="px-5 sm:px-7 py-4 text-left">ID Pasien</th>
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
                    @php $r = $s->result; @endphp

                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 sm:px-7 py-4 font-mono text-slate-500 whitespace-nowrap">
                            {{ $s->patient_id }}
                        </td>

                        @if($r)
                            <td class="px-5 py-4 text-center">
                                <span class="font-bold text-slate-800 text-lg block">
                                    {{ $r->skor_depresi }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded-full border {{ $r->badgeD() }} font-semibold whitespace-nowrap">
                                    {{ $r->kat_depresi }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <span class="font-bold text-slate-800 text-lg block">
                                    {{ $r->skor_kecemasan }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded-full border {{ $r->badgeA() }} font-semibold whitespace-nowrap">
                                    {{ $r->kat_kecemasan }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <span class="font-bold text-slate-800 text-lg block">
                                    {{ $r->skor_stres }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded-full border {{ $r->badgeS() }} font-semibold whitespace-nowrap">
                                    {{ $r->kat_stres }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <span class="font-semibold px-3 py-1.5 rounded-full border {{ $r->rekBadge() }} whitespace-nowrap">
                                    {{ $r->rekLabel() }}
                                </span>
                            </td>
                        @else
                            <td colspan="4" class="px-5 py-4 text-slate-400">
                                —
                            </td>
                        @endif

                        <td class="px-5 py-4 text-slate-400 whitespace-nowrap">
                            {{ $s->selesai_at?->diffForHumans() }}
                        </td>

                        <td class="px-5 py-4 whitespace-nowrap">
                            <a href="{{ route('admin.patients.show', $s->patient_id) }}" class="text-blue-600 hover:text-blue-700 font-bold">
                                Detail →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-7 py-12 text-center text-slate-400 text-base sm:text-lg">
                            Belum ada data skrining.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const dataHarian = {
        labels: @json($userHarian->pluck('label')),
        data: @json($userHarian->pluck('count'))
    };

    const dataMingguan = {
        labels: @json($userMingguan->pluck('label')),
        data: @json($userMingguan->pluck('count'))
    };

    const dataBulanan = {
        labels: @json($userBulanan->pluck('label')),
        data: @json($userBulanan->pluck('count'))
    };

    let currentData = dataHarian;

    const trenCanvas = document.getElementById('trenChart');
    const rekCanvas = document.getElementById('rekChart');
    const katCanvas = document.getElementById('katChart');
    const filterChart = document.getElementById('filterChart');

    const chart = new Chart(trenCanvas, {
        type: 'line',
        data: {
            labels: currentData.labels,
            datasets: [{
                label: 'Jumlah User',
                data: currentData.data,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 32,
                        font: {
                            size: 12
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        precision: 0,
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        maxRotation: 0,
                        autoSkip: true,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    filterChart.addEventListener('change', function () {
        if (this.value === 'harian') {
            currentData = dataHarian;
        } else if (this.value === 'mingguan') {
            currentData = dataMingguan;
        } else {
            currentData = dataBulanan;
        }

        chart.data.labels = currentData.labels;
        chart.data.datasets[0].data = currentData.data;
        chart.update();
    });

    new Chart(rekCanvas, {
        type: 'doughnut',
        data: {
            labels: ['Perlu Segera', 'Disarankan', 'Pantau Mandiri'],
            datasets: [{
                data: [{{ $r16 }}, {{ $r17 }}, {{ $r18 }}],
                backgroundColor: ['#ef4444', '#f59e0b', '#10b981'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    new Chart(katCanvas, {
        type: 'bar',
        data: {
            labels: @json($katLabels),
            datasets: [
                {
                    label: 'Depresi',
                    data: @json($katDepresi),
                    backgroundColor: '#3b82f6',
                    borderRadius: 8
                },
                {
                    label: 'Kecemasan',
                    data: @json($katKecemasan),
                    backgroundColor: '#8b5cf6',
                    borderRadius: 8
                },
                {
                    label: 'Stres',
                    data: @json($katStres),
                    backgroundColor: '#f97316',
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        font: {
                            size: 12
                        },
                        padding: 14
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        precision: 0,
                        stepSize: 1,
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
</script>
@endpush