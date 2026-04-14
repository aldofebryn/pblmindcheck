@extends('layouts.app')
@section('title','Beranda')

@section('content')

{{-- ── Hero ───────────────────────────────────────────────────── --}}
<section class="w-full px-6 lg:px-14 xl:px-24 py-24 lg:py-28 min-h-screen flex items-center">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center w-full">

        {{-- Kiri: teks --}}
        <div class="max-w-3xl">
            <span class="inline-flex items-center gap-2 text-blue-600 bg-blue-50 border border-blue-100 font-semibold px-5 py-2.5 rounded-full mb-8 text-base">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Anonim · Berbasis Ilmiah · Gratis
            </span>

            <h1 class="font-bold text-slate-900 leading-tight mb-8 
                       text-5xl sm:text-6xl lg:text-7xl xl:text-[5.5rem]">
                Kenali kondisi<br>
                <span class="text-blue-600">kesehatan mental</span><br>
                Anda hari ini.
            </h1>

            <p class="text-slate-500 leading-relaxed mb-10 
                      text-lg sm:text-xl lg:text-2xl max-w-2xl">
                Skrining DASS-21 mengukur tingkat depresi, kecemasan, dan stres dalam ±5 menit.
                Hasilnya ilmiah, anonim, dan dapat dipantau dari waktu ke waktu.
            </p>

            <div class="flex flex-wrap gap-5 mb-10">
                <a href="{{ route('token') }}"
                   class="inline-flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-4 rounded-xl text-lg transition shadow-md">
                    Mulai Skrining
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>

                @if(session('patient_token'))
                <a href="{{ route('history') }}"
                   class="inline-flex items-center bg-white border border-slate-300 hover:border-slate-400 text-slate-700 font-semibold px-8 py-4 rounded-xl text-lg transition">
                    Lihat Riwayat
                </a>
                @endif
            </div>

            @if($totalSesi > 0)
            <p class="text-slate-400 text-base">
                <span class="font-bold text-slate-700 text-xl">{{ number_format($totalSesi) }}</span>
                skrining telah diselesaikan
            </p>
            @endif
        </div>

        {{-- Kanan: kartu --}}
        <div class="hidden lg:flex justify-end">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-2xl p-10 w-full max-w-lg">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <p class="text-sm font-bold text-slate-400 uppercase mb-1">HASIL</p>
                        <p class="font-bold text-slate-900 text-xl">Contoh Laporan</p>
                    </div>
                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold px-4 py-1.5 rounded-full text-sm">
                        Mandiri
                    </span>
                </div>

                @foreach([['Depresi','8','emerald'],['Kecemasan','6','sky'],['Stres','14','amber']] as [$label,$skor,$c])
                <div class="mb-6 last:mb-0">
                    <div class="flex justify-between mb-2 text-base">
                        <span class="font-semibold text-slate-700">{{ $label }}</span>
                        <span class="text-slate-400">{{ $skor }}/42</span>
                    </div>
                    <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-{{ $c }}-400 rounded-full"
                             style="width:{{ round($skor/42*100) }}%"></div>
                    </div>
                    <p class="text-sm text-{{ $c }}-600 font-semibold mt-2">Normal</p>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</section>

{{-- ── Cara kerja --}}
<section class="w-full bg-white border-y border-slate-100 py-20">
    <div class="px-6 lg:px-14 xl:px-24">
        <h2 class="font-bold text-slate-900 text-center mb-14 
                   text-3xl sm:text-4xl lg:text-5xl">
            Bagaimana cara kerjanya?
        </h2>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach([
                ['1','Dapatkan Token','Token anonim dibuat tanpa identitas.','blue'],
                ['2','Isi DASS-21','Jawab 21 pertanyaan kondisi Anda.','indigo'],
                ['3','Lihat Hasil','Dapatkan hasil & rekomendasi.','violet'],
            ] as [$no,$judul,$desc,$c])
            <div class="bg-slate-50 rounded-2xl p-8">
                <span class="w-14 h-14 bg-{{ $c }}-100 text-{{ $c }}-600 rounded-xl flex items-center justify-center font-bold text-xl mb-5">
                    {{ $no }}
                </span>
                <h3 class="font-bold text-slate-900 text-xl mb-2">{{ $judul }}</h3>
                <p class="text-slate-500 text-base leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Disclaimer --}}
<section class="w-full px-6 lg:px-14 xl:px-24 py-12">
    <div class="bg-amber-50 border border-amber-200 rounded-2xl px-8 py-6 flex gap-4">
        <svg class="w-6 h-6 text-amber-500 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p class="text-amber-800 text-base leading-relaxed">
            <strong>Disclaimer:</strong> Ini hanya alat skrining, bukan diagnosis medis.
            Hubungi <strong>SEJIWA 119 (ext 8)</strong> jika darurat.
        </p>
    </div>
</section>

@endsection