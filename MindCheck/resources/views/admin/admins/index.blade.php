@extends('layouts.admin')
@section('title','Daftar Admin')

@section('content')
<div class="w-full">

@if(session('success'))
<div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    <p class="font-medium">{{ session('success') }}</p>
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    <p class="font-medium">{{ session('error') }}</p>
</div>
@endif

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Daftar Admin</h1>
        <p class="text-slate-500 text-sm mt-1">Kelola akun administrator sistem MindCheck.</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.admins.trash') }}"
           class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700 border border-slate-200 bg-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Tempat Sampah
        </a>
        <a href="{{ route('admin.admins.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Admin
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Admin</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($admins as $admin)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($admin->name, 0, 2)) }}
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-slate-800">{{ $admin->name }}</span>
                                @if($admin->id == session('admin_id'))
                                    <span class="text-xs text-slate-400 font-medium">(Anda)</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-500">{{ $admin->email }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($admin->status)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end items-center gap-2">

                            {{-- EDIT --}}
                            <a href="{{ route('admin.admins.edit', $admin->id) }}"
                               class="p-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors"
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            {{-- DELETE — hanya tampil kalau bukan akun yang sedang login --}}
                            @if($admin->id != session('admin_id'))
                                <form id="del-admin-{{ $admin->id }}"
                                      action="{{ route('admin.admins.delete', $admin->id) }}"
                                      method="POST"
                                      class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                <button type="button"
                                        onclick="window.openModal_deleteAdmin('del-admin-{{ $admin->id }}')"
                                        class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Hapus">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                        <p class="font-medium">Belum ada data admin</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@php
    $modalId           = 'deleteAdmin';
    $modalTitle        = 'Hapus Admin?';
    $modalBody         = 'Admin ini akan dipindahkan ke <strong>tempat sampah</strong> dan dapat dipulihkan kapan saja.';
    $modalWarning      = 'Admin tidak dapat login selama berada di tempat sampah.';
    $modalConfirmLabel = 'Ya, Hapus';
    $modalConfirmColor = 'red';
@endphp

@include('admin.partials.confirm-modal')

</div>
@endsection