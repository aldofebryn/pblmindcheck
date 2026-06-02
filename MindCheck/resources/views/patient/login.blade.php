@extends('layouts.app')
@section('title','Masuk — MindCheck')

@push('head')
<style>
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(28px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-header { animation: fadeSlideUp .5s ease both; animation-delay: .05s; }
    .anim-card   { animation: fadeSlideUp .55s ease both; animation-delay: .18s; }
</style>
@endpush

@section('content')
<div class="w-full max-w-screen-xl mx-auto px-4 sm:px-8 lg:px-16 py-4 sm:py-6">
<div class="max-w-md mx-auto">

    {{-- Header --}}
    <div class="text-center mb-6 anim-header">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-2">Selamat Datang</h1>
        <p class="text-sm sm:text-base text-slate-500 max-w-md mx-auto leading-relaxed">
            Masuk untuk melanjutkan skrining kesehatan mental Anda.
        </p>
    </div>

    @error('login')
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 font-medium text-sm">
        {{ $message }}
    </div>
    @enderror

    {{-- ══ LOGIN CARD ══ --}}
    <div class="relative overflow-hidden rounded-3xl shadow-lg border border-blue-100 anim-card">
        <div class="absolute inset-0 bg-gradient-to-br from-sky-400 via-blue-500 to-blue-600"></div>
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/15 rounded-full"></div>
        <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-white/15 rounded-full"></div>
        <div class="absolute top-1/2 left-4 w-16 h-16 bg-white/10 rounded-full"></div>

        <div class="relative z-10 p-6 sm:p-8">
            <span class="inline-flex items-center gap-1.5 bg-white/25 text-white text-xs font-semibold px-3 py-1 rounded-full mb-4 backdrop-blur-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 inline-block"></span>
                Masuk Akun
            </span>

            <h3 class="font-bold text-white text-xl mb-1">Sudah punya akun</h3>
            <p class="text-blue-100 mb-6 text-sm">Masuk untuk melihat riwayat skrining Anda sebelumnya.</p>

            <form method="POST" action="{{ route('patient.login.process') }}">
                @csrf
                <input type="hidden" name="aksi" value="login">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-blue-100 mb-1.5 uppercase tracking-wide">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}"
                               placeholder="Masukkan username"
                               class="w-full px-4 py-3 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30 text-white placeholder-blue-200 focus:outline-none focus:border-white focus:bg-white/30 transition-all text-sm"
                               required autofocus>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-blue-100 mb-1.5 uppercase tracking-wide">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="loginPassword"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-3 pr-12 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30 text-white placeholder-blue-200 focus:outline-none focus:border-white focus:bg-white/30 transition-all text-sm"
                                   required>
                            <button type="button" onclick="togglePassword('loginPassword','eyeLogin')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-white/60 hover:text-white transition-colors">
                                <svg id="eyeLogin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="w-full mt-6 bg-white hover:bg-blue-50 text-blue-600 font-bold py-3.5 rounded-xl transition-all shadow-md hover:shadow-lg active:scale-[.98] text-sm">
                    Masuk
                </button>
            </form>

            {{-- Footer card: kembali + daftar --}}
            <div class="mt-6 pt-5 border-t border-white/20 flex items-center justify-between gap-3">
                <a href="{{ route('landing') }}"
                   class="inline-flex items-center gap-1.5 bg-white/15 hover:bg-white/25 text-white text-xs font-semibold px-4 py-2.5 rounded-xl border border-white/20 transition-all backdrop-blur-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    Beranda
                </a>
                <p class="text-blue-100 text-sm">Belum punya akun?
                    <a href="{{ route('patient.register') }}" class="text-white font-semibold hover:underline">Daftar di sini</a>
                </p>
            </div>
        </div>
    </div>

    {{-- Copyright --}}
    <p class="text-center text-slate-400 text-xs mt-5">© {{ date('Y') }} MindCheck · Sistem Skrining Kesehatan Mental</p>

</div>
</div>

@push('scripts')
<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>`;
    } else {
        input.type = 'password';
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>`;
    }
}
</script>
@endpush
@endsection