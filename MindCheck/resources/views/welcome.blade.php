@extends('layouts.app')
@section('title','Beranda')

@push('head')
<style>
/* ── Floating animation untuk ilustrasi ── */
@keyframes floatUp {
    0%,100% { transform: translateY(0px); }
    50%      { transform: translateY(-10px); }
}
@keyframes fadeInUp {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes pulseRing {
    0%   { transform: scale(1);   opacity:.6; }
    100% { transform: scale(1.5); opacity:0; }
}
@keyframes countUp {
    from { opacity:0; transform:translateY(8px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes barGrow {
    from { width: 0%; }
}

.hero-visual { animation: floatUp 4s ease-in-out infinite; }
.anim-1 { animation: fadeInUp .6s ease both; animation-delay:.1s; }
.anim-2 { animation: fadeInUp .6s ease both; animation-delay:.25s; }
.anim-3 { animation: fadeInUp .6s ease both; animation-delay:.4s; }
.anim-4 { animation: fadeInUp .6s ease both; animation-delay:.55s; }

.pulse-ring {
    position:absolute; inset:0; border-radius:50%;
    border: 2px solid #3b82f6;
    animation: pulseRing 2s ease-out infinite;
}
.pulse-ring-2 {
    position:absolute; inset:0; border-radius:50%;
    border: 2px solid #3b82f6;
    animation: pulseRing 2s ease-out infinite;
    animation-delay: .6s;
}

.bar-anim { animation: barGrow 1.2s ease both; animation-delay: .8s; }
</style>
@endpush

@section('content')

{{-- ── Hero ── --}}
<section class="w-full px-6 lg:px-14 xl:px-24 pt-4 pb-8 lg:pt-6 lg:pb-12 min-h-[calc(100vh-60px)] flex items-center">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center w-full">

        {{-- Kiri: teks --}}
        <div class="max-w-3xl">
            <h1 class="anim-1 font-bold text-slate-900 leading-tight mb-6
                       text-3xl sm:text-4xl lg:text-5xl xl:text-6xl">
                Kenali kondisi<br>
                <span class="text-blue-600">kesehatan mental</span><br>
                Anda hari ini.
            </h1>

            <p class="anim-2 text-slate-500 leading-relaxed mb-8 text-base sm:text-lg max-w-xl">
                Skrining DASS-21 mengukur tingkat depresi, kecemasan, dan stres dalam ±5 menit.
                Hasilnya ilmiah, anonim, dan dapat dipantau dari waktu ke waktu.
            </p>

            <div class="anim-3 flex flex-wrap gap-4 mb-10">
                <a href="{{ route('patient.login') }}"
                   class="inline-flex items-center gap-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl text-base transition shadow-md">
                    Mulai Skrining
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                <a href="{{ route('patient.register') }}"
                   class="inline-flex items-center gap-2 border border-slate-200 hover:border-blue-300 hover:bg-blue-50 text-slate-600 hover:text-blue-600 font-semibold px-6 py-3 rounded-xl text-base transition">
                    Daftar Gratis
                </a>
            </div>

            @if($totalSesi > 0)
            <p class="anim-4 text-slate-400 text-base">
                <span class="font-bold text-slate-700 text-xl">{{ number_format($totalSesi) }}</span>
                skrining telah diselesaikan
            </p>
            @endif
        </div>

        {{-- Kanan: ilustrasi animasi --}}
        <div class="hidden lg:flex justify-center items-center">
            <div class="hero-visual relative w-72 h-72 flex items-center justify-center">

                {{-- Lingkaran luar pulse --}}
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="relative w-64 h-64">
                        <div class="pulse-ring"></div>
                        <div class="pulse-ring-2"></div>
                    </div>
                </div>

                {{-- Lingkaran tengah --}}
                <div class="relative z-10 w-52 h-52 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full shadow-2xl flex flex-col items-center justify-center gap-1">
                    <svg class="w-16 h-16 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-white font-bold text-lg">DASS-21</span>
                    <span class="text-blue-200 text-xs">±5 menit</span>
                </div>

                {{-- Badge melayang: Depresi --}}
                <div class="absolute -left-6 top-8 bg-white rounded-2xl shadow-lg px-4 py-3 border border-slate-100 w-40"
                     style="animation: floatUp 3.5s ease-in-out infinite; animation-delay:.3s;">
                    <p class="text-xs text-slate-400 font-semibold mb-1.5">Depresi</p>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-400 rounded-full bar-anim" style="width:30%"></div>
                    </div>
                    <p class="text-xs text-emerald-600 font-bold mt-1">Normal</p>
                </div>

                {{-- Badge melayang: Kecemasan --}}
                <div class="absolute -right-6 top-16 bg-white rounded-2xl shadow-lg px-4 py-3 border border-slate-100 w-40"
                     style="animation: floatUp 4s ease-in-out infinite; animation-delay:.8s;">
                    <p class="text-xs text-slate-400 font-semibold mb-1.5">Kecemasan</p>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-400 rounded-full bar-anim" style="width:22%"></div>
                    </div>
                    <p class="text-xs text-emerald-600 font-bold mt-1">Normal</p>
                </div>

                {{-- Badge melayang: Stres --}}
                <div class="absolute -left-4 bottom-8 bg-white rounded-2xl shadow-lg px-4 py-3 border border-slate-100 w-40"
                     style="animation: floatUp 4.5s ease-in-out infinite; animation-delay:1.2s;">
                    <p class="text-xs text-slate-400 font-semibold mb-1.5">Stres</p>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-400 rounded-full bar-anim" style="width:45%"></div>
                    </div>
                    <p class="text-xs text-amber-600 font-bold mt-1">Mild</p>
                </div>

            </div>
        </div>

    </div>
</section>

{{-- ── Cara kerja ── --}}
<section class="w-full bg-white border-y border-slate-100 py-20">
    <div class="px-6 lg:px-14 xl:px-24">
        <h2 class="font-bold text-slate-900 text-center mb-14 text-3xl sm:text-4xl lg:text-5xl">
            Bagaimana cara kerjanya?
        </h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach([
                ['1','Buat Akun','Daftar atau masuk dengan username dan password Anda.','blue'],
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

{{-- ── Disclaimer ── --}}
<section class="w-full px-6 lg:px-14 xl:px-24 py-12">
    <div class="bg-amber-50 border border-amber-200 rounded-2xl px-8 py-6 flex gap-4">
        <svg class="w-6 h-6 text-amber-500 mt-1 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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