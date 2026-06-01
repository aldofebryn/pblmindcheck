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
            Tong Sampah
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

                            <span class="font-semibold text-slate-800">
                                {{ $admin->name }}
                            </span>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-slate-500">
                        {{ $admin->email }}
                    </td>

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

                                <svg class="w-4 h-4"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor"
                                     stroke-width="2">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            {{-- FORM DELETE --}}
                            <form id="del-admin-{{ $admin->id }}"
                                  action="{{ route('admin.admins.delete', $admin->id) }}"
                                  method="POST"
                                  class="hidden">

                                @csrf
                                @method('DELETE')
                            </form>

                            {{-- DELETE --}}
                            @if($admin->id == session('admin_id'))
                                <button type="button"
                                        onclick="openSelfDeleteWarningModal()"
                                        class="p-2 text-red-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Akun sedang aktif digunakan, tidak dapat dihapus">
                                    <svg class="w-4 h-4 opacity-75"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor"
                                         stroke-width="2">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            @else
                                <button type="button"
                                        onclick="window.openModal_deleteAdmin('del-admin-{{ $admin->id }}')"
                                        class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Hapus">
                                    <svg class="w-4 h-4"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor"
                                         stroke-width="2">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
    $modalBody         = 'Admin ini akan dipindahkan ke <strong>tong sampah</strong> dan dapat dipulihkan kapan saja.';
    $modalWarning      = 'Admin tidak dapat login selama berada di tong sampah.';
    $modalConfirmLabel = 'Ya, Hapus';
    $modalConfirmColor = 'red';
@endphp

@include('admin.partials.confirm-modal')

{{-- ── Warning Modal: Self Deletion ── --}}
<div id="selfDeleteWarningModal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     aria-modal="true" role="dialog">

    {{-- Backdrop --}}
    <div id="selfDeleteWarning_backdrop"
         class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity duration-200 opacity-0"
         onclick="closeSelfDeleteWarningModal()"></div>

    {{-- Panel --}}
    <div id="selfDeleteWarning_panel"
         class="relative bg-white/90 backdrop-blur-xl border border-white/60 rounded-2xl shadow-2xl w-full max-w-md p-7 transition-all duration-200 scale-95 opacity-0">

        {{-- Ikon Peringatan (Amber) --}}
        <div class="flex items-center justify-center w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl mx-auto mb-5">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        {{-- Judul & Isi --}}
        <h3 class="text-xl font-bold text-slate-800 text-center mb-2">Tindakan Ditolak</h3>
        <div class="text-slate-500 text-sm text-center mb-6 leading-relaxed">
            Anda tidak dapat menghapus akun admin yang <strong>sedang aktif digunakan</strong> untuk login saat ini. Silahkan nonaktif kan akun ini terlebih dahulu, lalu coba hapus kembali.
        </div>

        {{-- Tombol Tutup --}}
        <div class="flex">
            <button type="button" onclick="closeSelfDeleteWarningModal()"
                    class="w-full px-5 py-3 rounded-xl font-bold text-white bg-slate-800 hover:bg-slate-900 transition-colors text-sm shadow-md">
                Mengerti
            </button>
        </div>
    </div>
</div>

<script>
    function openSelfDeleteWarningModal() {
        const modal = document.getElementById('selfDeleteWarningModal');
        const backdrop = document.getElementById('selfDeleteWarning_backdrop');
        const panel = document.getElementById('selfDeleteWarning_panel');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(() => {
            backdrop.classList.replace('opacity-0', 'opacity-100');
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        });
    }

    function closeSelfDeleteWarningModal() {
        const modal = document.getElementById('selfDeleteWarningModal');
        const backdrop = document.getElementById('selfDeleteWarning_backdrop');
        const panel = document.getElementById('selfDeleteWarning_panel');

        backdrop.classList.replace('opacity-100', 'opacity-0');
        panel.classList.remove('scale-100', 'opacity-100');
        panel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 180);
    }
</script>

</div>
@endsection