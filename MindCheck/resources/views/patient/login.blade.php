@extends('layouts.app')
@section('title','Login / Register Pasien')

@push('head')
<style>
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(28px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-header {
        animation: fadeSlideUp .5s ease both;
        animation-delay: .05s;
    }
    .anim-card-left {
        animation: fadeSlideUp .55s ease both;
        animation-delay: .18s;
    }
    .anim-card-right {
        animation: fadeSlideUp .55s ease both;
        animation-delay: .30s;
    }
</style>
@endpush

@section('content')
<div class="w-full max-w-screen-xl mx-auto px-4 sm:px-8 lg:px-16 py-8 sm:py-12">
<div class="max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="text-center mb-8 anim-header">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-2">Akses Skrining</h1>
        <p class="text-sm sm:text-base text-slate-500 max-w-md mx-auto leading-relaxed">
            Silakan masuk dengan akun Anda untuk melihat riwayat<br>atau daftar jika baru pertama kali.
        </p>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="font-medium text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    @error('login')
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 font-medium">
        {{ $message }}
    </div>
    @enderror

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">

        {{-- ══ REGISTER CARD ══ --}}
        <div class="relative overflow-hidden rounded-3xl shadow-lg border border-indigo-100 anim-card-left">
            {{-- Background gradient --}}
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-indigo-500 to-violet-600 opacity-100"></div>
            {{-- Decorative circles --}}
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-white/10 rounded-full"></div>
            <div class="absolute top-1/2 right-4 w-16 h-16 bg-white/5 rounded-full"></div>

            <div class="relative z-10 p-6 sm:p-8">
                {{-- Badge --}}
                <span class="inline-flex items-center gap-1.5 bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full mb-4 backdrop-blur-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 inline-block"></span>
                    Daftar Gratis
                </span>

                <h3 class="font-bold text-white text-xl mb-1">Belum punya akun?</h3>
                <p class="text-indigo-200 mb-6 text-sm">Daftar sekarang untuk melacak kondisi kesehatan mental Anda.</p>

                <form method="POST" action="{{ route('patient.login.process') }}">
                    @csrf
                    <input type="hidden" name="aksi" value="register">
                    <div class="space-y-4">

                        <div>
                            <label class="block text-xs font-semibold text-indigo-100 mb-1.5 uppercase tracking-wide">Username</label>
                            <input type="text" name="username" value="{{ old('username') }}"
                                   placeholder="Masukkan username"
                                   class="w-full px-4 py-3 rounded-xl bg-white/15 backdrop-blur-sm border border-white/25 text-white placeholder-indigo-300 focus:outline-none focus:border-white focus:bg-white/20 transition-all text-sm"
                                   required>
                            @error('username')<p class="text-red-200 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Umur & Status Pekerjaan — 1 baris --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-indigo-100 mb-1.5 uppercase tracking-wide">Umur</label>
                                <input type="number" name="umur" value="{{ old('umur') }}" min="1"
                                       placeholder="Usia"
                                       class="w-full px-4 py-3 rounded-xl bg-white/15 backdrop-blur-sm border border-white/25 text-white placeholder-indigo-300 focus:outline-none focus:border-white focus:bg-white/20 transition-all text-sm"
                                       required>
                                @error('umur')<p class="text-red-200 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-indigo-100 mb-1.5 uppercase tracking-wide">Status Pekerjaan</label>
                                <select name="status_pekerjaan"
                                        class="w-full px-4 py-3 rounded-xl bg-white/15 backdrop-blur-sm border border-white/25 text-white focus:outline-none focus:border-white focus:bg-white/20 transition-all text-sm appearance-none"
                                        required>
                                    <option value="" class="text-slate-700 bg-white">Pilih...</option>
                                    <option value="Pelajar/Mahasiswa" class="text-slate-700 bg-white" {{ old('status_pekerjaan') == 'Pelajar/Mahasiswa' ? 'selected' : '' }}>Pelajar/Mahasiswa</option>
                                    <option value="Bekerja" class="text-slate-700 bg-white" {{ old('status_pekerjaan') == 'Bekerja' ? 'selected' : '' }}>Bekerja</option>
                                    <option value="Tidak Bekerja" class="text-slate-700 bg-white" {{ old('status_pekerjaan') == 'Tidak Bekerja' ? 'selected' : '' }}>Tidak Bekerja</option>
                                    <option value="Lainnya" class="text-slate-700 bg-white" {{ old('status_pekerjaan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('status_pekerjaan')<p class="text-red-200 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-indigo-100 mb-1.5 uppercase tracking-wide">Password</label>
                            <input type="password" name="password"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-3 rounded-xl bg-white/15 backdrop-blur-sm border border-white/25 text-white placeholder-indigo-300 focus:outline-none focus:border-white focus:bg-white/20 transition-all text-sm"
                                   required>
                            @error('password')<p class="text-red-200 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full mt-6 bg-white hover:bg-indigo-50 text-indigo-700 font-bold py-3.5 rounded-xl transition-all shadow-md hover:shadow-lg active:scale-[.98] text-sm">
                        Daftar Akun
                    </button>
                </form>
            </div>
        </div>

        {{-- ══ LOGIN CARD ══ --}}
        <div class="relative overflow-hidden rounded-3xl shadow-lg border border-blue-100 anim-card-right">
            {{-- Background terang --}}
            <div class="absolute inset-0 bg-gradient-to-br from-sky-400 via-blue-500 to-blue-600"></div>
            {{-- Decorative --}}
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/15 rounded-full"></div>
            <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-white/15 rounded-full"></div>
            <div class="absolute top-1/2 left-4 w-16 h-16 bg-white/10 rounded-full"></div>

            <div class="relative z-10 p-6 sm:p-8">
                {{-- Badge --}}
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
                            <input type="text" name="username"
                                   placeholder="Masukkan username"
                                   class="w-full px-4 py-3 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30 text-white placeholder-blue-200 focus:outline-none focus:border-white focus:bg-white/30 transition-all text-sm"
                                   required>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-blue-100 mb-1.5 uppercase tracking-wide">Password</label>
                            <input type="password" name="password"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-3 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30 text-white placeholder-blue-200 focus:outline-none focus:border-white focus:bg-white/30 transition-all text-sm"
                                   required>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full mt-6 bg-white hover:bg-blue-50 text-blue-600 font-bold py-3.5 rounded-xl transition-all shadow-md hover:shadow-lg active:scale-[.98] text-sm">
                        Masuk
                    </button>
                </form>

                {{-- Divider + info --}}
                <div class="mt-6 pt-6 border-t border-white/20 text-center">
                    <p class="text-blue-100 text-xs">Belum terdaftar? Isi form di sebelah kiri.</p>
                </div>
            </div>
        </div>

    </div>
</div>
</div>
@endsection