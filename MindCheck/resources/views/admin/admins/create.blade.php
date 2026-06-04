@extends('layouts.admin')
@section('title','Tambah Admin')

@section('content')
<div class="max-w-xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.admins.index') }}"
           class="w-9 h-9 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-lg font-bold text-slate-800">Tambah Admin Baru</h2>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-7">
        <form method="POST" action="{{ route('admin.admins.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                <input name="name"
                       type="text"
                       value="{{ old('name') }}"
                       required
                       pattern="[A-Za-zÀ-ÿ\s]+"
                       onkeypress="return /[A-Za-zÀ-ÿ\s]/.test(event.key)"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-sm"
                       placeholder="Contoh: Budi Santoso">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                <input name="email" type="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-sm"
                       placeholder="admin@contoh.com">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                <div class="relative">
                    <input id="password" name="password" type="password" required
                           class="w-full px-4 py-3 pr-12 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-sm"
                           placeholder="Minimal 6 karakter">
                    <button type="button" onclick="togglePassword('password', 'eyePassword')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
                        <svg id="eyePassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                </div>
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status</label>
                <select name="status" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-sm">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition-colors text-sm">
                    Simpan Admin
                </button>
                <a href="{{ route('admin.admins.index') }}"
                   class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-3 rounded-xl transition-colors text-sm">
                    Batal
                </a>
            </div>
        </form>
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