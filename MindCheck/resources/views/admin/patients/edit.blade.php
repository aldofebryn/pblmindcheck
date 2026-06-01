@extends('layouts.admin')
@section('title', 'Edit Data Pasien')

@section('content')

{{-- Back button --}}
<div class="mb-8 flex items-center gap-4">
    <a href="{{ route('admin.patients.index') }}"
       class="w-10 h-10 bg-white/70 border border-white/50 rounded-xl flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-white transition-all shadow-sm">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Edit Data Pasien</h2>
        <p class="text-slate-500 font-mono text-sm mt-0.5">ID: {{ $patient->id }}</p>
    </div>
</div>

{{-- Centered card --}}
<div class="max-w-2xl mx-auto">

    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-medium">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Identity card --}}
    <div class="bg-white/70 backdrop-blur-md rounded-2xl shadow-sm border border-white/60 overflow-hidden mb-5">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg shrink-0">
                {{ strtoupper(substr($patient->alias ?? $patient->username ?? 'P', 0, 1)) }}
            </div>
            <div>
                <p class="font-bold text-slate-800">{{ $patient->alias ?? $patient->username ?? 'Pasien Anonim' }}</p>
                <p class="text-slate-400 text-xs">Terdaftar: {{ $patient->created_at->format('d F Y, H:i') }} WIB</p>
            </div>
        </div>

        <form action="{{ route('admin.patients.update', $patient->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            {{-- Row 1: Username + Password --}}
            <div class="grid sm:grid-cols-2 gap-5 mb-5">
                <div>
                    <label for="username" class="block text-sm font-bold text-slate-700 mb-1.5">Username</label>
                    <input type="text" name="username" id="username"
                           value="{{ old('username', $patient->username) }}"
                           onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122)"
                           oninput="this.value = this.value.replace(/[^a-zA-Z]/g, '')"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm"
                           placeholder="Misal: pasien01">
                    @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Password Baru
                        <span class="text-slate-400 font-normal">(kosongkan jika tidak diubah)</span>
                    </label>
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm"
                           placeholder="Min. 6 karakter">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Row 2: Umur + Status --}}
            <div class="grid sm:grid-cols-2 gap-5 mb-5">
                <div>
                    <label for="umur" class="block text-sm font-bold text-slate-700 mb-1.5">Umur</label>
                    <input type="number" name="umur" id="umur"
                           value="{{ old('umur', $patient->umur) }}" min="1"
                           onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm"
                           placeholder="Misal: 25">
                    @error('umur') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status_pekerjaan" class="block text-sm font-bold text-slate-700 mb-1.5">Status Pekerjaan</label>
                    <div class="relative">
                        <select name="status_pekerjaan" id="status_pekerjaan"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none appearance-none text-sm cursor-pointer">
                            <option value="">Pilih Status</option>
                            <option value="Pelajar/Mahasiswa" {{ old('status_pekerjaan', $patient->status_pekerjaan) == 'Pelajar/Mahasiswa' ? 'selected' : '' }}>Pelajar/Mahasiswa</option>
                            <option value="Bekerja" {{ old('status_pekerjaan', $patient->status_pekerjaan) == 'Bekerja' ? 'selected' : '' }}>Bekerja</option>
                            <option value="Tidak Bekerja" {{ old('status_pekerjaan', $patient->status_pekerjaan) == 'Tidak Bekerja' ? 'selected' : '' }}>Tidak Bekerja</option>
                            <option value="Lainnya" {{ old('status_pekerjaan', $patient->status_pekerjaan) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    @error('status_pekerjaan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Alias --}}
            <div class="mb-5">
                <label for="alias" class="block text-sm font-bold text-slate-700 mb-1.5">
                    Alias / Nama Panggilan
                    <span class="text-slate-400 font-normal">(opsional)</span>
                </label>
                <input type="text" name="alias" id="alias"
                       value="{{ old('alias', $patient->alias) }}"
                       onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32"
                       oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm"
                       placeholder="Misal: Pasien A, atau nama inisial">
                @error('alias') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Admin notes --}}
            <div class="mb-6">
                <label for="admin_notes" class="block text-sm font-bold text-slate-700 mb-1.5">
                    Catatan Internal Admin
                </label>
                <textarea name="admin_notes" id="admin_notes" rows="4"
                          class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none resize-none text-sm"
                          placeholder="Catatan khusus mengenai pasien ini (hanya bisa dilihat oleh admin)">{{ old('admin_notes', $patient->admin_notes) }}</textarea>
                @error('admin_notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('admin.patients.index') }}"
                   class="px-5 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-50 border border-slate-200 transition-colors text-sm">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>

@endsection
