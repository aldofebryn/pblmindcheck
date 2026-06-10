@extends('layouts.app')
@section('title','Daftar — MindCheck')

@push('head')
<style>
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(28px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-header { animation: fadeSlideUp .5s ease both; animation-delay: .05s; }
    .anim-card   { animation: fadeSlideUp .55s ease both; animation-delay: .18s; }
    .pw-match-ok   { color: #6ee7b7; font-size: .75rem; margin-top: 4px; display:none; }
    .pw-match-fail { color: #fca5a5; font-size: .75rem; margin-top: 4px; display:none; }
</style>
@endpush

@section('content')
<div class="w-full max-w-screen-xl mx-auto px-4 sm:px-8 lg:px-16 py-4 sm:py-6">
<div class="max-w-md mx-auto">

    {{-- Header --}}
    <div class="text-center mb-4 anim-header">
        <h1 class="text-2xl font-bold text-slate-900 mb-1">Buat Akun Baru</h1>
        <p class="text-sm text-slate-500">Daftar gratis dan mulai skrining kesehatan mental Anda.</p>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="font-medium text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    {{-- ══ REGISTER CARD ══ --}}
    <div class="relative overflow-hidden rounded-3xl shadow-lg border border-indigo-100 anim-card">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-indigo-500 to-violet-600"></div>
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-white/10 rounded-full"></div>
        <div class="absolute top-1/2 right-4 w-16 h-16 bg-white/5 rounded-full"></div>

        <div class="relative z-10 p-6 sm:p-8">
            <span class="inline-flex items-center gap-1.5 bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full mb-4 backdrop-blur-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 inline-block"></span>
                Daftar Gratis
            </span>

            <h3 class="font-bold text-white text-xl mb-1">Belum punya akun?</h3>
            <p class="text-indigo-200 mb-5 text-sm">Isi data berikut untuk membuat akun baru.</p>

            <form method="POST" action="{{ route('patient.login.process') }}">
                @csrf
                <input type="hidden" name="aksi" value="register">
                <div class="space-y-4">

                    <div>
                        <label class="block text-xs font-semibold text-indigo-100 mb-1.5 uppercase tracking-wide">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}"
                               placeholder="Huruf dan spasi saja"
                               pattern="[a-zA-Z ]+"
                               title="Username hanya boleh berisi huruf dan spasi"
                               oninput="this.value=this.value.replace(/[^a-zA-Z ]/g,'')"
                               class="w-full px-4 py-3 rounded-xl bg-white/15 backdrop-blur-sm border border-white/25 text-white placeholder-indigo-300 focus:outline-none focus:border-white focus:bg-white/20 transition-all text-sm"
                               required autofocus>
                        @error('username')<p class="text-red-200 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-indigo-100 mb-1.5 uppercase tracking-wide">Umur</label>
                            <input type="number" name="umur" value="{{ old('umur') }}" min="1" max="120"
                                   placeholder="Usia"
                                   onkeydown="return !['e','E','+','-','.'].includes(event.key)"
                                   oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                   class="w-full px-4 py-3 rounded-xl bg-white/15 backdrop-blur-sm border border-white/25 text-white placeholder-indigo-300 focus:outline-none focus:border-white focus:bg-white/20 transition-all text-sm"
                                   required>
                            @error('umur')<p class="text-red-200 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-indigo-100 mb-1.5 uppercase tracking-wide">Status</label>
                            <select name="status_pekerjaan"
                                    class="w-full px-4 py-3 rounded-xl bg-white/15 backdrop-blur-sm border border-white/25 text-white focus:outline-none focus:border-white focus:bg-white/20 transition-all text-sm appearance-none"
                                    required>
                                <option value="" class="text-slate-700 bg-white">Pilih...</option>
                                <option value="Pelajar/Mahasiswa" class="text-slate-700 bg-white" {{ old('status_pekerjaan')=='Pelajar/Mahasiswa'?'selected':'' }}>Pelajar/Mahasiswa</option>
                                <option value="Bekerja" class="text-slate-700 bg-white" {{ old('status_pekerjaan')=='Bekerja'?'selected':'' }}>Bekerja</option>
                                <option value="Tidak Bekerja" class="text-slate-700 bg-white" {{ old('status_pekerjaan')=='Tidak Bekerja'?'selected':'' }}>Tidak Bekerja</option>
                                <option value="Lainnya" class="text-slate-700 bg-white" {{ old('status_pekerjaan')=='Lainnya'?'selected':'' }}>Lainnya</option>
                            </select>
                            @error('status_pekerjaan')<p class="text-red-200 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-indigo-100 mb-1.5 uppercase tracking-wide">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="regPassword"
                                   placeholder="Min. 6 karakter"
                                   minlength="6"
                                   class="w-full px-4 py-3 pr-12 rounded-xl bg-white/15 backdrop-blur-sm border border-white/25 text-white placeholder-indigo-300 focus:outline-none focus:border-white focus:bg-white/20 transition-all text-sm"
                                   required>
                            <button type="button" onclick="togglePassword('regPassword','eyeReg')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-white/60 hover:text-white transition-colors">
                                <svg id="eyeReg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>
                        </div>
                        <p id="passwordLengthError" class="text-red-200 text-xs mt-1 hidden">Password minimal 6 karakter.</p>
                        @error('password')<p class="text-red-200 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-indigo-100 mb-1.5 uppercase tracking-wide">Konfirmasi Password</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="regPasswordConfirm"
                                   placeholder="Ulangi password"
                                   class="w-full px-4 py-3 pr-12 rounded-xl bg-white/15 backdrop-blur-sm border border-white/25 text-white placeholder-indigo-300 focus:outline-none focus:border-white focus:bg-white/20 transition-all text-sm"
                                   required>
                            <button type="button" onclick="togglePassword('regPasswordConfirm','eyeRegConfirm')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-white/60 hover:text-white transition-colors">
                                <svg id="eyeRegConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>
                        </div>
                        <p id="passwordConfirmationWarning" class="text-red-200 text-xs mt-1 hidden">Konfirmasi password belum sama.</p>
                    </div>

                </div>

                <button type="submit" id="submitBtn"
                        class="w-full mt-5 bg-white hover:bg-indigo-50 text-indigo-700 font-bold py-3.5 rounded-xl transition-all shadow-md hover:shadow-lg active:scale-[.98] text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    Daftar Akun
                </button>
            </form>

            {{-- Footer card: kembali + sudah punya akun --}}
            <div class="mt-5 pt-5 border-t border-white/20 flex items-center justify-between gap-3">
                <a href="{{ route('landing') }}"
                   class="inline-flex items-center gap-1.5 bg-white/15 hover:bg-white/25 text-white text-xs font-semibold px-4 py-2.5 rounded-xl border border-white/20 transition-all backdrop-blur-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    Beranda
                </a>
                <p class="text-indigo-200 text-sm">Sudah punya akun?
                    <a href="{{ route('patient.login') }}" class="text-white font-semibold hover:underline">Masuk di sini</a>
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
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const passwordInput = document.getElementById('regPassword');
    const confirmationInput = document.getElementById('regPasswordConfirm');
    const passwordWarning = document.getElementById('passwordLengthError');
    const confirmationWarning = document.getElementById('passwordConfirmationWarning');
    const submitButton = document.getElementById('submitBtn');

    if (!form || !passwordInput || !confirmationInput || !passwordWarning || !confirmationWarning || !submitButton) {
        return;
    }

    function setInvalid(input) {
        input.classList.remove('border-white/25', 'focus:ring-white/70');
        input.classList.add('border-red-300', 'focus:ring-red-200');
    }

    function setValid(input) {
        input.classList.remove('border-red-300', 'focus:ring-red-200');
        input.classList.add('border-white/25', 'focus:ring-white/70');
    }

    function validatePasswordLength() {
        const password = passwordInput.value;
        if (password.length > 0 && password.length < 6) {
            passwordWarning.classList.remove('hidden');
            setInvalid(passwordInput);
            return false;
        }

        passwordWarning.classList.add('hidden');
        setValid(passwordInput);
        return password.length >= 6;
    }

    function validatePasswordConfirmation() {
        const password = passwordInput.value;
        const confirmation = confirmationInput.value;
        if (confirmation.length === 0) {
            confirmationWarning.classList.add('hidden');
            confirmationInput.classList.remove('border-red-300', 'focus:ring-red-200');
            return false;
        }

        if (password !== confirmation) {
            confirmationWarning.classList.remove('hidden');
            setInvalid(confirmationInput);
            return false;
        }

        confirmationWarning.classList.add('hidden');
        setValid(confirmationInput);
        return true;
    }

    function updateSubmitState() {
        const passwordValid = validatePasswordLength();
        const confirmationValid = validatePasswordConfirmation();
        const formValid = passwordValid && confirmationValid;

        submitButton.disabled = !formValid;
        submitButton.classList.toggle('opacity-60', !formValid);
        submitButton.classList.toggle('cursor-not-allowed', !formValid);
        return formValid;
    }

    passwordInput.addEventListener('input', updateSubmitState);
    passwordInput.addEventListener('blur', updateSubmitState);
    confirmationInput.addEventListener('input', updateSubmitState);
    confirmationInput.addEventListener('blur', updateSubmitState);

    form.addEventListener('submit', function (event) {
        if (!updateSubmitState()) {
            event.preventDefault();
            passwordInput.focus();
        }
    });

    updateSubmitState();
});
</script>
@endpush
@endsection