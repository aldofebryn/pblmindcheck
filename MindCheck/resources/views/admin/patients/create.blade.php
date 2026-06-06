@extends('layouts.admin')
@section('title', 'Tambah Pasien Baru')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.patients.index') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-500 hover:text-slate-900 hover:border-slate-300 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Generate ID Pasien</h2>
        <p class="text-slate-500 mt-1">Buat Akses Pasien</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-2xl">
    <form action="{{ route('admin.patients.store') }}" method="POST" class="p-6 sm:p-8">
        @csrf

        <div class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="username" class="block text-sm font-bold text-slate-700 mb-2">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}"
                           oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none" 
                           placeholder="">
                    @error('username') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none" 
                           placeholder="Minimal 6 karakter">
                    @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="umur" class="block text-sm font-bold text-slate-700 mb-2">Umur</label>
                    <input type="number" name="umur" id="umur" value="{{ old('umur') }}" min="1"
                           onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none" 
                           placeholder="">
                    @error('umur') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status_pekerjaan" class="block text-sm font-bold text-slate-700 mb-2">Status Pekerjaan</label>
                    <select name="status_pekerjaan" id="status_pekerjaan"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
                        <option value="">Pilih Status</option>
                        <option value="Pelajar/Mahasiswa" {{ old('status_pekerjaan') == 'Pelajar/Mahasiswa' ? 'selected' : '' }}>Pelajar/Mahasiswa</option>
                        <option value="Bekerja" {{ old('status_pekerjaan') == 'Bekerja' ? 'selected' : '' }}>Bekerja</option>
                        <option value="Tidak Bekerja" {{ old('status_pekerjaan') == 'Tidak Bekerja' ? 'selected' : '' }}>Tidak Bekerja</option>
                        <option value="Lainnya" {{ old('status_pekerjaan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('status_pekerjaan') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="alias" class="block text-sm font-bold text-slate-700 mb-2">Alias / Nama Panggilan (Opsional)</label>
                <input type="text" name="alias" id="alias" value="{{ old('alias') }}"
                       onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32"
                       oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none" 
                       placeholder="">
                @error('alias') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="admin_notes" class="block text-sm font-bold text-slate-700 mb-2">Catatan Internal Admin (Opsional)</label>
                <textarea name="admin_notes" id="admin_notes" rows="4"
                          class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none resize-none" 
                          placeholder="Catatan mengenai pasien (hanya bisa dilihat oleh admin)"></textarea>
                @error('admin_notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3 text-blue-800 text-sm">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p>Generate <strong>ID akses</strong> untuk pasien ini.</p>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.patients.index') }}" class="px-6 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-50 border border-slate-200 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-colors flex items-center gap-2">
                Generate ID
            </button>
        </div>
    </form>
</div>

@endsection
