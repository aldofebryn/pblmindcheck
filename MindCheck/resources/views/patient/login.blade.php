@extends('layouts.app')
@section('title','Login / Register Pasien')

@section('content')
<div class="w-full max-w-screen-xl mx-auto px-4 sm:px-8 lg:px-16 py-12 sm:py-20">
<div class="max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="text-center mb-10 sm:mb-12">
        <div class="w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center mx-auto mb-4 sm:mb-6">
            <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-contain" alt="Logo">
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-2 sm:mb-3">Akses Skrining</h1>
        <p class="text-sm sm:text-lg text-slate-500 max-w-md mx-auto leading-relaxed">
            Silakan masuk dengan akun Anda untuk melihat riwayat atau daftar jika baru pertama kali.
        </p>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 mb-8 flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="font-medium text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    @error('login')
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-8 font-medium">
        {{ $message }}
    </div>
    @enderror

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Register --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm">
            <h3 class="font-bold text-slate-900 text-xl mb-2">Belum punya akun?</h3>
            <p class="text-slate-500 mb-6 text-sm">Daftar sekarang untuk melacak kondisi kesehatan mental Anda.</p>
            <form method="POST" action="{{ route('patient.login.process') }}">
                @csrf
                <input type="hidden" name="aksi" value="register">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none" required>
                        @error('username')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Umur</label>
                        <input type="number" name="umur" value="{{ old('umur') }}" min="1" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none" required>
                        @error('umur')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Status Pekerjaan</label>
                        <select name="status_pekerjaan" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none" required>
                            <option value="">Pilih...</option>
                            <option value="Pelajar/Mahasiswa" {{ old('status_pekerjaan') == 'Pelajar/Mahasiswa' ? 'selected' : '' }}>Pelajar/Mahasiswa</option>
                            <option value="Bekerja" {{ old('status_pekerjaan') == 'Bekerja' ? 'selected' : '' }}>Bekerja</option>
                            <option value="Tidak Bekerja" {{ old('status_pekerjaan') == 'Tidak Bekerja' ? 'selected' : '' }}>Tidak Bekerja</option>
                            <option value="Lainnya" {{ old('status_pekerjaan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('status_pekerjaan')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Password</label>
                        <input type="password" name="password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none" required>
                        @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <button type="submit" class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 sm:py-4 rounded-xl transition-colors">
                    Daftar Akun
                </button>
            </form>
        </div>

        {{-- Login --}}
        <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 sm:p-8">
            <h3 class="font-bold text-slate-900 text-xl mb-2">Sudah punya akun</h3>
            <p class="text-slate-500 mb-6 text-sm">Masuk untuk melihat riwayat skrining Anda sebelumnya.</p>
            <form method="POST" action="{{ route('patient.login.process') }}">
                @csrf
                <input type="hidden" name="aksi" value="login">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Username</label>
                        <input type="text" name="username" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-400 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Password</label>
                        <input type="password" name="password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-400 outline-none" required>
                    </div>
                </div>
                <button type="submit" class="w-full mt-6 bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 sm:py-4 rounded-xl transition-colors">
                    Masuk
                </button>
            </form>

            <div class="mt-8 p-4 bg-white border border-slate-200 rounded-xl text-sm text-slate-500">
                <p class="font-bold text-slate-700 mb-1">Punya ID UUID lama?</p>
                <p>Sistem kami kini menggunakan Username & Password demi keamanan. ID lama Anda masih tersimpan di sistem namun tidak lagi bisa digunakan untuk login secara langsung.</p>
            </div>
        </div>
    </div>

</div>
</div>
@endsection