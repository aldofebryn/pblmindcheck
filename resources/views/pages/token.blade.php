@extends('layouts.app')
@section('title','Token Anonim')

@section('content')
<div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-8 lg:px-16 py-12 sm:py-20">
<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="text-center mb-10 sm:mb-12">
        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-blue-50 rounded-3xl flex items-center justify-center mx-auto mb-4 sm:mb-6">
            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-2 sm:mb-3">Token Anonim</h1>
        <p class="text-sm sm:text-lg text-slate-500 max-w-md mx-auto leading-relaxed">
            Token menghubungkan sesi skrining Anda tanpa menyimpan identitas apapun.
        </p>
    </div>

    {{-- Token baru berhasil dibuat --}}
    @if($tokenBaru)
    <div class="bg-emerald-50 border border-emerald-200 rounded-3xl p-5 sm:p-7 mb-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-emerald-800 text-base sm:text-lg mb-2 sm:mb-3">Token berhasil dibuat! Simpan kode ini.</p>
                <div class="bg-white border border-emerald-200 rounded-2xl px-4 sm:px-5 py-3 sm:py-4 font-mono text-sm sm:text-base text-slate-700 break-all flex items-center justify-between gap-3">
                    <span id="token-text" class="text-sm sm:text-base">{{ $tokenBaru }}</span>
                    <button onclick="copyToken()" class="shrink-0 text-emerald-600 hover:text-emerald-700 transition-colors p-1" title="Salin">
                        <svg id="copy-icon" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                            <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                        </svg>
                    </button>
                </div>
                <p class="text-xs sm:text-sm text-emerald-700 mt-2 sm:mt-3 font-medium">⚠ Jika token hilang, riwayat tidak dapat dipulihkan. Salin dan simpan di tempat aman.</p>
            </div>
        </div>
        <a href="{{ route('screening') }}" class="mt-4 sm:mt-6 w-full flex items-center justify-center gap-2 sm:gap-3 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 sm:py-4 rounded-2xl transition-colors text-base sm:text-lg">
            Lanjut ke Skrining
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
    </div>
    @endif

    {{-- Pilihan --}}
    <div class="grid gap-4 sm:gap-5">

        {{-- Generate baru --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm">
            <h3 class="font-bold text-slate-900 text-lg sm:text-xl mb-1 sm:mb-2">Pengguna baru</h3>
            <p class="text-slate-500 mb-4 sm:mb-6 text-sm sm:text-base leading-relaxed">Buat token UUID baru. Token akan disimpan di sesi browser Anda selama sesi berlangsung.</p>
            <form method="POST" action="{{ route('token.process') }}">
                @csrf
                <input type="hidden" name="aksi" value="baru">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 sm:py-4 rounded-2xl transition-colors text-base sm:text-lg">
                    Buat Token Baru
                </button>
            </form>
        </div>

        <div class="text-center text-slate-400 font-medium text-sm sm:text-base">atau</div>

        {{-- Token lama --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm">
            <h3 class="font-bold text-slate-900 text-lg sm:text-xl mb-1 sm:mb-2">Sudah punya token</h3>
            <p class="text-slate-500 mb-4 sm:mb-6 text-sm sm:text-base leading-relaxed">Masukkan token lama untuk melanjutkan dan melihat riwayat skrining Anda.</p>
            <form method="POST" action="{{ route('token.process') }}">
                @csrf
                <input type="hidden" name="aksi" value="lama">
                <input type="text" name="token" value="{{ old('token') }}"
                       placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                       class="w-full font-mono border border-slate-200 rounded-2xl px-4 sm:px-5 py-3 sm:py-4 text-sm sm:text-base mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-300
                              {{ $errors->has('token') ? 'border-red-300 bg-red-50' : '' }}">
                @error('token')
                    <p class="text-red-600 mb-2 sm:mb-3 font-medium text-sm sm:text-base">{{ $message }}</p>
                @enderror
                <button type="submit" class="w-full mt-2 sm:mt-4 bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 sm:py-4 rounded-2xl transition-colors text-base sm:text-lg">
                    Gunakan Token Ini
                </button>
            </form>
        </div>

    </div>

</div>
</div>
@endsection

@push('scripts')
<script>
function copyToken() {
    const text = document.getElementById('token-text').innerText;
    navigator.clipboard.writeText(text).then(() => {
        const icon = document.getElementById('copy-icon');
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>';
        setTimeout(() => {
            icon.innerHTML = '<rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>';
        }, 2000);
    });
}
</script>
@endpush