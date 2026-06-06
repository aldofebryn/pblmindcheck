@extends('layouts.patient')
@section('title','Pengaturan Profil')

@section('content')
<div class="max-w-2xl mx-auto py-2">
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 font-medium flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 font-medium flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 font-medium">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
        <div class="border-b border-slate-100 pb-5 mb-8">
            <h2 class="text-lg font-bold text-slate-800">Informasi Pribadi</h2>
            <p class="text-slate-500 mt-1">Perbarui data diri dan preferensi akun Anda.</p>
        </div>

        <form method="POST" action="{{ route('patient.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="grid sm:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama Panggilan (Alias)</label>
                    <input type="text" name="alias" value="{{ old('alias', $patient->alias) }}" onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none" placeholder="Opsional">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Username</label>
                    <input type="text" name="username" value="{{ old('username', $patient->username) }}" oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Umur</label>
                    <input type="number" name="umur" min="1" value="{{ old('umur', $patient->umur) }}" onkeypress="return event.charCode >= 48 && event.charCode <= 57" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Status Pekerjaan</label>
                    <div class="relative">
                        <select name="status_pekerjaan" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none appearance-none bg-white cursor-pointer text-slate-700 font-medium" required>
                            <option value="Pelajar/Mahasiswa" {{ old('status_pekerjaan', $patient->status_pekerjaan) == 'Pelajar/Mahasiswa' ? 'selected' : '' }}>Pelajar/Mahasiswa</option>
                            <option value="Bekerja" {{ old('status_pekerjaan', $patient->status_pekerjaan) == 'Bekerja' ? 'selected' : '' }}>Bekerja</option>
                            <option value="Tidak Bekerja" {{ old('status_pekerjaan', $patient->status_pekerjaan) == 'Tidak Bekerja' ? 'selected' : '' }}>Tidak Bekerja</option>
                            <option value="Lainnya" {{ old('status_pekerjaan', $patient->status_pekerjaan) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-8 pb-5 mb-8">
                <h2 class="text-base font-bold text-slate-800">Ubah Password</h2>
                <p class="text-slate-500 mt-1 text-sm">Kosongkan jika Anda tidak ingin mengubah password.</p>
            </div>

            <div class="grid sm:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Password Lama</label>
                    <div class="relative">
                        <input type="password" id="old_password" name="old_password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none pr-12">
                        <button type="button" onclick="togglePassword('old_password')" class="absolute inset-y-0 right-0 px-4 flex items-center text-slate-400 hover:text-blue-500 transition-colors focus:outline-none">
                            <svg id="eye-old_password" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Password Baru</label>
                    <div class="relative">
                        <input type="password" id="new_password" name="new_password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none pr-12">
                        <button type="button" onclick="togglePassword('new_password')" class="absolute inset-y-0 right-0 px-4 flex items-center text-slate-400 hover:text-blue-500 transition-colors focus:outline-none">
                            <svg id="eye-new_password" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById('eye-' + inputId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
        }
    }
</script>
@endpush
