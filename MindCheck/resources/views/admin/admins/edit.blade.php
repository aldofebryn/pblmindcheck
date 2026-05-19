@extends('layouts.admin')
@section('title','Edit Admin')

@section('content')
<div class="max-w-xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.admins.index') }}"
           class="w-9 h-9 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-lg font-bold text-slate-800">Edit Admin</h2>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-7">
        <form method="POST" action="{{ route('admin.admins.update', $admin->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                <input name="name" type="text" value="{{ old('name', $admin->name) }}" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-sm">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                <input name="email" type="email" value="{{ old('email', $admin->email) }}" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-sm">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status</label>
                <select name="status" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-sm">
                    <option value="1" {{ $admin->status ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !$admin->status ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition-colors text-sm">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.admins.index') }}"
                   class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-3 rounded-xl transition-colors text-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection