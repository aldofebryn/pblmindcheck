@extends('layouts.admin')
@section('title','Tong Sampah Admin')

@section('content')

@if(session('success'))
<div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    <p class="font-medium">{{ session('success') }}</p>
</div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-slate-500 text-sm">Admin yang dihapus sementara — dapat dipulihkan atau dihapus permanen.</p>
    </div>
    <a href="{{ route('admin.admins.index') }}"
       class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700 text-sm font-semibold transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar Admin
    </a>
</div>

<div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50/80 text-slate-500 font-semibold uppercase tracking-wider text-xs">
                <tr>
                    <th class="px-6 py-4 text-left">Admin</th>
                    <th class="px-6 py-4 text-left">Email</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($admins as $admin)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($admin->name, 0, 2)) }}
                            </div>
                            <span class="font-semibold text-slate-600">{{ $admin->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-500">{{ $admin->email }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end items-center gap-2">
                            {{-- Pulihkan --}}
                            <form action="{{ route('admin.admins.restore', $admin->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors">
                                    Pulihkan
                                </button>
                            </form>
                            {{-- Hapus Permanen --}}
                            <button type="button"
                                    onclick="window.openModal_forceDeleteAdmin('force-admin-{{ $admin->id }}')"
                                    class="px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                Hapus Permanen
                            </button>
                            <form id="force-admin-{{ $admin->id }}" action="{{ route('admin.admins.force', $admin->id) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center text-slate-400">
                        <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <p class="font-medium">Tong sampah kosong</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@php
    $modalId           = 'forceDeleteAdmin';
    $modalTitle        = 'Hapus Admin Permanen?';
    $modalBody         = 'Admin ini akan <strong>dihapus permanen</strong> dari sistem.';
    $modalWarning      = 'Tindakan ini tidak dapat dibatalkan. Data tidak bisa dipulihkan.';
    $modalConfirmLabel = 'Ya, Hapus Permanen';
    $modalConfirmColor = 'red';
@endphp
@include('admin.partials.confirm-modal')

@endsection
