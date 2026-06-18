@extends('layouts.app')
@section('title','Beranda')

@push('head')
<style>
@keyframes fadeInUp {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes float0 { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
@keyframes float1 { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
@keyframes float2 { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
@keyframes barGrow { from { width: 0%; } }

.anim-1 { animation: fadeInUp .6s ease both; animation-delay:.1s; }
.anim-2 { animation: fadeInUp .6s ease both; animation-delay:.25s; }
.anim-3 { animation: fadeInUp .6s ease both; animation-delay:.4s; }
.anim-4 { animation: fadeInUp .6s ease both; animation-delay:.55s; }
.bar-anim { animation: barGrow 1.2s ease both; animation-delay: .8s; }

/* Breathing Orb */
.orb-ring {
    position: absolute; border-radius: 50%;
    transition: width 4s cubic-bezier(.4,0,.2,1),
                height 4s cubic-bezier(.4,0,.2,1),
                background 4s ease, border-color 4s ease;
}
.orb-core {
    position: relative; z-index: 2; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    transition: width 4s cubic-bezier(.4,0,.2,1),
                height 4s cubic-bezier(.4,0,.2,1),
                background 4s ease, box-shadow 4s ease;
}
.orb-label {
    position: absolute; bottom: -40px; left: 50%; transform: translateX(-50%);
    font-weight: 600; font-size: 15px; white-space: nowrap;
    transition: color 1s ease;
}
.badge-float-0 { animation: float0 3.5s ease-in-out infinite; }
.badge-float-1 { animation: float1 4s ease-in-out infinite; }
.badge-float-2 { animation: float2 4.5s ease-in-out infinite; }
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
            <div class="anim-4 inline-flex items-center gap-3 bg-white border border-slate-200 rounded-2xl px-5 py-3 shadow-sm">
                <div class="flex -space-x-2">
                    <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs font-bold">A</div>
                    <div class="w-8 h-8 rounded-full bg-indigo-500 border-2 border-white flex items-center justify-center text-white text-xs font-bold">B</div>
                    <div class="w-8 h-8 rounded-full bg-teal-500 border-2 border-white flex items-center justify-center text-white text-xs font-bold">C</div>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-bold text-slate-900 text-base">{{ number_format($totalSesi) }}+</span>
                        <span class="text-slate-500 text-sm">orang sudah mencoba</span>
                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-600 text-xs font-semibold px-2 py-0.5 rounded-full border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                            Gratis
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Kenali kondisimu sekarang, hanya ±5 menit</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Kanan: Breathing Orb --}}
        <div class="hidden lg:flex justify-center items-center">
            <div style="display:flex;flex-direction:column;align-items:center;gap:32px;">

                {{-- Instruksi --}}
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:14px 20px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,.05);max-width:280px;">
                    <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:6px;">
                        <div style="width:7px;height:7px;border-radius:50%;background:#22c55e;box-shadow:0 0 6px #22c55e;"></div>
                        <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#22c55e;">Latihan Napas</span>
                    </div>
                    <p style="font-size:13.5px;color:#475569;line-height:1.6;margin:0;">
                        Ikuti gerakan lingkaran ini —<br>
                        <strong style="color:#3b82f6;">tarik napas</strong> saat membesar,
                        <strong style="color:#14b8a6;">hembuskan</strong> saat mengecil
                    </p>
                </div>

                {{-- Orb --}}
                <div style="position:relative;width:280px;height:280px;display:flex;align-items:center;justify-content:center;">
                    <div id="orbRing3" class="orb-ring" style="width:260px;height:260px;background:rgba(59,130,246,.06);border:1.5px solid rgba(59,130,246,.15);"></div>
                    <div id="orbRing2" class="orb-ring" style="width:200px;height:200px;background:rgba(59,130,246,.10);border:1.5px solid rgba(59,130,246,.22);"></div>
                    <div id="orbCore" class="orb-core" style="width:145px;height:145px;background:radial-gradient(circle at 35% 35%,#60a5facc,#3b82f6);box-shadow:0 0 40px rgba(59,130,246,.5);">
                        <svg width="56" height="28" viewBox="0 0 60 30" fill="none">
                            <path id="wavePath" d="M0 15 Q7.5 5 15 15 Q22.5 25 30 15 Q37.5 5 45 15 Q52.5 25 60 15"
                                  stroke="rgba(255,255,255,.85)" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                        </svg>
                    </div>
                    <div id="orbLabel" style="position:absolute;bottom:-36px;left:50%;transform:translateX(-50%);font-weight:600;font-size:15px;white-space:nowrap;color:#3b82f6;transition:color 1s ease;">Tarik napas...</div>
                </div>

                {{-- 3 Badge --}}
                <div style="display:flex;gap:14px;margin-top:16px;">
                    <div class="badge-float-0" style="background:#fff;border-radius:14px;padding:12px 16px;border:1px solid #e2e8f0;box-shadow:0 4px 12px rgba(0,0,0,.06);min-width:108px;">
                        <p style="font-size:10px;color:#94a3b8;font-weight:600;margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em;">Depresi</p>
                        <div style="height:5px;background:#f1f5f9;border-radius:99px;overflow:hidden;margin-bottom:5px;">
                            <div class="bar-anim" style="height:100%;width:30%;background:#3b82f6;border-radius:99px;"></div>
                        </div>
                        <p style="font-size:11px;color:#16a34a;font-weight:700;">Normal</p>
                    </div>
                    <div class="badge-float-1" style="background:#fff;border-radius:14px;padding:12px 16px;border:1px solid #e2e8f0;box-shadow:0 4px 12px rgba(0,0,0,.06);min-width:108px;">
                        <p style="font-size:10px;color:#94a3b8;font-weight:600;margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em;">Kecemasan</p>
                        <div style="height:5px;background:#f1f5f9;border-radius:99px;overflow:hidden;margin-bottom:5px;">
                            <div class="bar-anim" style="height:100%;width:22%;background:#6366f1;border-radius:99px;animation-delay:1s;"></div>
                        </div>
                        <p style="font-size:11px;color:#16a34a;font-weight:700;">Normal</p>
                    </div>
                    <div class="badge-float-2" style="background:#fff;border-radius:14px;padding:12px 16px;border:1px solid #e2e8f0;box-shadow:0 4px 12px rgba(0,0,0,.06);min-width:108px;">
                        <p style="font-size:10px;color:#94a3b8;font-weight:600;margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em;">Stres</p>
                        <div style="height:5px;background:#f1f5f9;border-radius:99px;overflow:hidden;margin-bottom:5px;">
                            <div class="bar-anim" style="height:100%;width:45%;background:#f59e0b;border-radius:99px;animation-delay:1.2s;"></div>
                        </div>
                        <p style="font-size:11px;color:#d97706;font-weight:700;">Mild</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

{{-- ── Cara kerja ── --}}
<section class="w-full bg-white border-y border-slate-100 py-14">
    <div class="px-6 lg:px-14 xl:px-24">
        <h2 class="font-bold text-slate-900 text-center mb-12 text-2xl sm:text-3xl">
            Bagaimana cara kerjanya?
        </h2>

        <div class="relative max-w-5xl mx-auto">
            {{-- Garis penghubung --}}
            <div class="hidden sm:block absolute top-5 left-[16.5%] right-[16.5%] h-0.5 bg-gradient-to-r from-blue-200 via-indigo-200 to-violet-200"></div>

            <div class="grid sm:grid-cols-3 gap-8 relative">
                @foreach([
                    ['1','Buat Akun','Daftar atau masuk dengan username dan password Anda.','blue','#3b82f6'],
                    ['2','Isi DASS-21','Jawab 21 pertanyaan kondisi Anda.','indigo','#6366f1'],
                    ['3','Lihat Hasil','Dapatkan hasil & rekomendasi.','violet','#7c3aed'],
                ] as [$no,$judul,$desc,$c,$hex])
                <div class="flex flex-col items-center text-center">
                    {{-- Nomor bubble --}}
                    <div class="w-10 h-10 rounded-full bg-{{ $c }}-100 border-2 border-{{ $c }}-300 flex items-center justify-center font-bold text-{{ $c }}-600 text-sm mb-4 relative z-10 bg-white">
                        {{ $no }}
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1.5">{{ $judul }}</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ── Disclaimer ── --}}
<section class="w-full px-6 lg:px-14 xl:px-24 py-6 text-center">
    <p class="text-slate-400 text-xs leading-relaxed">
        ⚠️ Ini hanya alat skrining, bukan diagnosis medis. &nbsp;·&nbsp;
        Hubungi <strong class="text-slate-500">SEJIWA 119 (ext 8)</strong> jika darurat.
    </p>
</section>

@push('scripts')
<script>
(function(){
    const phases = [
        { name:'inhale', dur:4000, label:'Tarik napas...', color:'#3b82f6',
          r3:260, r2:200, core:145, wave:'M0 15 Q7.5 5 15 15 Q22.5 25 30 15 Q37.5 5 45 15 Q52.5 25 60 15' },
        { name:'hold',   dur:2000, label:'Tahan...',       color:'#6366f1',
          r3:240, r2:185, core:135, wave:'M0 15 Q7.5 5 15 15 Q22.5 25 30 15 Q37.5 5 45 15 Q52.5 25 60 15' },
        { name:'exhale', dur:4000, label:'Hembuskan...',   color:'#14b8a6',
          r3:200, r2:155, core:110, wave:'M0 15 Q15 15 30 15 Q45 15 60 15' },
    ];
    let idx = 0;

    function applyPhase(p) {
        const r3   = document.getElementById('orbRing3');
        const r2   = document.getElementById('orbRing2');
        const core = document.getElementById('orbCore');
        const lbl  = document.getElementById('orbLabel');
        const wave = document.getElementById('wavePath');
        if(!r3) return;

        r3.style.width  = p.r3+'px'; r3.style.height = p.r3+'px';
        r3.style.background   = `rgba(${p.color==='#3b82f6'?'59,130,246':p.color==='#6366f1'?'99,102,241':'20,184,166'},.06)`;
        r3.style.borderColor  = `rgba(${p.color==='#3b82f6'?'59,130,246':p.color==='#6366f1'?'99,102,241':'20,184,166'},.18)`;

        r2.style.width  = p.r2+'px'; r2.style.height = p.r2+'px';
        r2.style.background   = `rgba(${p.color==='#3b82f6'?'59,130,246':p.color==='#6366f1'?'99,102,241':'20,184,166'},.12)`;
        r2.style.borderColor  = `rgba(${p.color==='#3b82f6'?'59,130,246':p.color==='#6366f1'?'99,102,241':'20,184,166'},.25)`;

        core.style.width  = p.core+'px'; core.style.height = p.core+'px';
        core.style.background   = `radial-gradient(circle at 35% 35%,${p.color}cc,${p.color})`;
        core.style.boxShadow    = `0 0 40px ${p.color}50`;

        lbl.textContent = p.label;
        lbl.style.color = p.color;
        if(wave) wave.setAttribute('d', p.wave);
    }

    function next() {
        idx = (idx + 1) % phases.length;
        applyPhase(phases[idx]);
    }

    applyPhase(phases[0]);
    setInterval(next, 4000);
})();
</script>
@endpush

@endsection