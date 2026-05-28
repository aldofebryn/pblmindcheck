@extends('layouts.app')

@section('title', 'Beranda')

@push('head')
<style>

/* =========================
   BREATHING ORB
========================= */

.breathing-wrapper{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:32px;
}

.orb-container{
    position:relative;
    width:260px;
    height:260px;
    display:flex;
    align-items:center;
    justify-content:center;
}

.ring{
    position:absolute;
    border-radius:9999px;
    transition:all 4s cubic-bezier(.4,0,.2,1);
}

.ring-outer{
    width:260px;
    height:260px;
    border:1.5px solid rgba(59,130,246,.2);
    background:radial-gradient(circle,rgba(59,130,246,.08),transparent);
}

.ring-middle{
    width:200px;
    height:200px;
    border:1.5px solid rgba(59,130,246,.3);
    background:radial-gradient(circle,rgba(59,130,246,.15),transparent);
}

.orb-core{
    position:relative;
    z-index:2;
    width:145px;
    height:145px;
    border-radius:9999px;
    background:radial-gradient(circle at 35% 35%,#60a5fa,#3b82f6);
    box-shadow:0 0 40px rgba(59,130,246,.35);
    display:flex;
    align-items:center;
    justify-content:center;
    transition:all 4s cubic-bezier(.4,0,.2,1);
}

.breath-label{
    position:absolute;
    bottom:-38px;
    font-size:15px;
    font-weight:600;
    color:#3b82f6;
    transition:all .4s ease;
}

/* =========================
   CARDS
========================= */

.mental-cards{
    display:flex;
    gap:12px;
    margin-top:12px;
}

.mental-card{
    background:white;
    border-radius:14px;
    padding:12px 16px;
    border:1px solid #e2e8f0;
    box-shadow:0 4px 12px rgba(0,0,0,.06);
    min-width:110px;
}

.card-title{
    font-size:10px;
    color:#94a3b8;
    font-weight:700;
    margin-bottom:6px;
    text-transform:uppercase;
}

.progress-bg{
    height:5px;
    background:#f1f5f9;
    border-radius:999px;
    overflow:hidden;
    margin-bottom:5px;
}

.progress-fill{
    height:100%;
    border-radius:999px;
}

.blue{
    background:#3b82f6;
}

.indigo{
    background:#6366f1;
}

.amber{
    background:#f59e0b;
}

.card-status{
    font-size:11px;
    font-weight:700;
}

.success{
    color:#16a34a;
}

.warning{
    color:#d97706;
}

/* =========================
   FLOATING
========================= */

.float-1{
    animation:float1 3.5s ease-in-out infinite;
}

.float-2{
    animation:float2 4s ease-in-out infinite;
}

.float-3{
    animation:float3 4.5s ease-in-out infinite;
}

@keyframes float1{
    0%,100%{transform:translateY(0)}
    50%{transform:translateY(-8px)}
}

@keyframes float2{
    0%,100%{transform:translateY(0)}
    50%{transform:translateY(-12px)}
}

@keyframes float3{
    0%,100%{transform:translateY(0)}
    50%{transform:translateY(-6px)}
}

/* =========================
   HERO TEXT ANIMATION
========================= */

.anim-1,
.anim-2,
.anim-3,
.anim-4{
    opacity:0;
    transform:translateY(18px);
    animation:fadeUp .8s ease forwards;
}

.anim-2{
    animation-delay:.2s;
}

.anim-3{
    animation-delay:.4s;
}

.anim-4{
    animation-delay:.6s;
}

@keyframes fadeUp{
    to{
        opacity:1;
        transform:translateY(0);
    }
}

</style>
@endpush

@section('content')

{{-- HERO --}}
<section class="w-full px-6 lg:px-14 xl:px-24 py-14 lg:py-20 min-h-screen flex items-center">

    <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center w-full">

        {{-- LEFT --}}
        <div class="max-w-3xl">

            <h1 class="anim-1 font-bold text-slate-900 leading-tight mb-6
                       text-3xl sm:text-4xl lg:text-5xl xl:text-6xl">

                Kenali kondisi <br>

                <span class="text-blue-600">
                    kesehatan mental
                </span>

                <br>

                Anda hari ini.
            </h1>

            <p class="anim-2 text-slate-500 leading-relaxed mb-8 text-base sm:text-lg max-w-xl">

                Skrining DASS-21 mengukur tingkat depresi,
                kecemasan, dan stres dalam ±5 menit.
                Hasilnya ilmiah, anonim, dan dapat dipantau
                dari waktu ke waktu.

            </p>

            <div class="anim-3 flex flex-wrap gap-4 mb-10">

                <a href="{{ route('patient.login') }}"
                   class="inline-flex items-center gap-2.5
                          bg-blue-600 hover:bg-blue-700
                          text-white font-semibold
                          px-6 py-3 rounded-xl
                          text-base transition shadow-md">

                    Mulai Skrining

                    <svg class="w-5 h-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2.5"
                              d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>

                </a>

                <a href="{{ route('patient.register') }}"
                   class="inline-flex items-center gap-2
                          border border-slate-200
                          hover:border-blue-300
                          hover:bg-blue-50
                          text-slate-600
                          hover:text-blue-600
                          font-semibold
                          px-6 py-3 rounded-xl
                          text-base transition">

                    Daftar Gratis

                </a>

            </div>

            @if($totalSesi > 0)

                <p class="anim-4 text-slate-400 text-base">

                    <span class="font-bold text-slate-700 text-xl">
                        {{ number_format($totalSesi) }}
                    </span>

                    skrining telah diselesaikan

                </p>

            @endif

        </div>

        {{-- RIGHT --}}
        <div class="hidden lg:flex justify-center items-center">

            <div class="breathing-wrapper">

                <div class="orb-container">

                    <div class="ring ring-outer"></div>

                    <div class="ring ring-middle"></div>

                    <div class="orb-core">

                        <svg width="56"
                             height="28"
                             viewBox="0 0 60 30"
                             fill="none">

                            <path id="breath-wave"
                                  d="M0 15 Q7.5 5 15 15 Q22.5 25 30 15 Q37.5 5 45 15 Q52.5 25 60 15"
                                  stroke="rgba(255,255,255,.85)"
                                  stroke-width="2.5"
                                  stroke-linecap="round"
                                  fill="none"/>
                        </svg>

                    </div>

                    <div class="breath-label" id="breath-label">
                        Tarik napas...
                    </div>

                </div>

                {{-- CARDS --}}
                <div class="mental-cards">

                    <div class="mental-card float-1">

                        <p class="card-title">
                            Depresi
                        </p>

                        <div class="progress-bg">
                            <div class="progress-fill blue" style="width:30%"></div>
                        </div>

                        <p class="card-status success">
                            Normal
                        </p>

                    </div>

                    <div class="mental-card float-2">

                        <p class="card-title">
                            Kecemasan
                        </p>

                        <div class="progress-bg">
                            <div class="progress-fill indigo" style="width:22%"></div>
                        </div>

                        <p class="card-status success">
                            Normal
                        </p>

                    </div>

                    <div class="mental-card float-3">

                        <p class="card-title">
                            Stres
                        </p>

                        <div class="progress-bg">
                            <div class="progress-fill amber" style="width:45%"></div>
                        </div>

                        <p class="card-status warning">
                            Mild
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

{{-- CARA KERJA --}}
<section class="w-full bg-white border-y border-slate-100 py-20">

    <div class="px-6 lg:px-14 xl:px-24">

        <h2 class="font-bold text-slate-900 text-center mb-14
                   text-3xl sm:text-4xl lg:text-5xl">

            Bagaimana cara kerjanya?

        </h2>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">

            @foreach([
                ['1','Buat Akun','Daftar atau masuk dengan username dan password Anda.','blue'],
                ['2','Isi DASS-21','Jawab 21 pertanyaan kondisi Anda.','indigo'],
                ['3','Lihat Hasil','Dapatkan hasil & rekomendasi.','violet'],
            ] as [$no,$judul,$desc,$c])

                <div class="bg-slate-50 rounded-2xl p-8">

                    <span class="w-14 h-14
                                 bg-{{ $c }}-100
                                 text-{{ $c }}-600
                                 rounded-xl
                                 flex items-center justify-center
                                 font-bold text-xl mb-5">

                        {{ $no }}

                    </span>

                    <h3 class="font-bold text-slate-900 text-xl mb-2">
                        {{ $judul }}
                    </h3>

                    <p class="text-slate-500 text-base leading-relaxed">
                        {{ $desc }}
                    </p>

                </div>

            @endforeach

        </div>

    </div>

</section>

{{-- DISCLAIMER --}}
<section class="w-full px-6 lg:px-14 xl:px-24 py-12">

    <div class="bg-amber-50 border border-amber-200
                rounded-2xl px-8 py-6 flex gap-4">

        <svg class="w-6 h-6 text-amber-500 mt-1 shrink-0"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">

            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 9v2m0 4h.01
                     m-6.938 4h13.856
                     c1.54 0 2.502-1.667 1.732-3
                     L13.732 4c-.77-1.333-2.694-1.333-3.464 0
                     L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>

        <p class="text-amber-800 text-base leading-relaxed">

            <strong>Disclaimer:</strong>

            Ini hanya alat skrining, bukan diagnosis medis.
            Hubungi <strong>SEJIWA 119 (ext 8)</strong>
            jika darurat.

        </p>

    </div>

</section>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const phases = [
        {
            color: "#3b82f6",
            label: "Tarik napas...",
            wave: "M0 15 Q7.5 5 15 15 Q22.5 25 30 15 Q37.5 5 45 15 Q52.5 25 60 15",
            outer: 260,
            middle: 200,
            core: 145
        },
        {
            color: "#6366f1",
            label: "Tahan...",
            wave: "M0 15 Q7.5 5 15 15 Q22.5 25 30 15 Q37.5 5 45 15 Q52.5 25 60 15",
            outer: 240,
            middle: 185,
            core: 135
        },
        {
            color: "#14b8a6",
            label: "Hembuskan...",
            wave: "M0 15 Q30 15 60 15",
            outer: 200,
            middle: 155,
            core: 110
        }
    ];

    let index = 0;

    const outer = document.querySelector(".ring-outer");
    const middle = document.querySelector(".ring-middle");
    const core = document.querySelector(".orb-core");
    const label = document.getElementById("breath-label");
    const wave = document.getElementById("breath-wave");

    function animateOrb() {

        const phase = phases[index];

        outer.style.width = phase.outer + "px";
        outer.style.height = phase.outer + "px";
        outer.style.borderColor = phase.color + "25";

        middle.style.width = phase.middle + "px";
        middle.style.height = phase.middle + "px";
        middle.style.borderColor = phase.color + "40";

        core.style.width = phase.core + "px";
        core.style.height = phase.core + "px";

        core.style.background =
            `radial-gradient(circle at 35% 35%, ${phase.color}cc, ${phase.color})`;

        core.style.boxShadow =
            `0 0 40px ${phase.color}50`;

        label.innerText = phase.label;
        label.style.color = phase.color;

        wave.setAttribute("d", phase.wave);

        index = (index + 1) % phases.length;
    }

    animateOrb();

    setInterval(animateOrb, 4000);

});
</script>

@endsection

