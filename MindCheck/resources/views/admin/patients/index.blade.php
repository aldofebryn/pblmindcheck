@extends('layouts.admin')
@section('title', 'Daftar Pasien')

@section('content')

@php
    $sortUrl = fn(string $col) => route('admin.patients.index', [
        'sort'  => $col,
        'order' => ($sort === $col && $order === 'asc') ? 'desc' : 'asc',
    ]);
    $sortIcon = function(string $col) use ($sort, $order): string {
        if ($sort !== $col) return '<svg class="w-3.5 h-3.5 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>';
        return $order === 'asc'
            ? '<svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>'
            : '<svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
    };
@endphp

@if(session('success'))
<div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    <p class="font-medium">{{ session('success') }}</p>
</div>
@endif

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <p class="text-slate-500 mt-1">Daftar ID dan riwayat skrining pasien</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.patients.trash') }}"
           class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700 border border-slate-200 bg-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Tong Sampah
        </a>
        <a href="{{ route('admin.patients.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors flex items-center gap-2 shadow-sm">
           <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
           Tambah Pasien Baru
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50/80 text-slate-500 font-semibold uppercase tracking-wider text-xs">
                <tr>
                    <th class="px-6 py-4 w-20">
                        <a href="{{ $sortUrl('id') }}" class="inline-flex items-center gap-1 hover:text-blue-600 transition-colors">
                            ID {!! $sortIcon('id') !!}
                        </a>
                    </th>
                    <th class="px-6 py-4">
                        <a href="{{ $sortUrl('username') }}" class="inline-flex items-center gap-1 hover:text-blue-600 transition-colors">
                            Pasien {!! $sortIcon('username') !!}
                        </a>
                    </th>
                    <th class="px-6 py-4 text-center">
                        <a href="{{ $sortUrl('umur') }}" class="inline-flex items-center justify-center gap-1 hover:text-blue-600 transition-colors">
                            Umur & Pekerjaan {!! $sortIcon('umur') !!}
                        </a>
                    </th>
                    <th class="px-6 py-4 text-center">
                        <a href="{{ $sortUrl('screenings_count') }}" class="inline-flex items-center justify-center gap-1 hover:text-blue-600 transition-colors">
                            Total Skrining {!! $sortIcon('screenings_count') !!}
                        </a>
                    </th>
                    <th class="px-6 py-4">
                        <a href="{{ $sortUrl('created_at') }}" class="inline-flex items-center gap-1 hover:text-blue-600 transition-colors">
                            Tanggal Dibuat {!! $sortIcon('created_at') !!}
                        </a>
                    </th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($patients as $patient)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-6 py-4 font-mono text-slate-500 font-medium">{{ $patient->id }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center font-bold">
                                {{ strtoupper(substr($patient->alias ?? 'P', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 text-base">{{ $patient->alias ?? $patient->username ?? 'Pasien Anonim' }}</p>
                                <p class="text-slate-500 text-xs mt-0.5">{{ $patient->username ? '@'.$patient->username : 'No Username' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="text-sm">
                            <span class="font-semibold text-slate-700">{{ $patient->umur ? $patient->umur . ' tahun' : '-' }}</span>
                            <span class="text-slate-400 block text-xs mt-0.5">{{ $patient->status_pekerjaan ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-semibold text-sm">
                            {{ $patient->screenings_count }} Sesi
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-500">{{ $patient->created_at->format('d M Y, H:i') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end items-center gap-2">
                            <a href="{{ route('admin.patients.show', $patient->id) }}"
                               class="p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-colors" title="Detail Riwayat">
                               <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('admin.patients.edit', $patient->id) }}"
                               class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Data">
                               <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            {{-- Tombol hapus — trigger modal --}}
                            <button type="button"
                                    onclick="openDeleteModal({{ $patient->id }}, '{{ addslashes($patient->alias ?? $patient->username ?? 'Pasien #'.$patient->id) }}', {{ $patient->screenings_count }})"
                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Pasien">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            {{-- Form hapus (hidden, di-submit via JS) --}}
                            <form id="delete-form-{{ $patient->id }}"
                                  action="{{ route('admin.patients.destroy', $patient->id) }}"
                                  method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <p class="font-medium text-slate-500">Belum ada pasien terdaftar</p>
                        <p class="text-sm mt-1">Pasien yang mendaftar akan muncul di sini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Custom Delete Confirmation Modal ───────────────────────────── --}}
<div id="deleteModal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     aria-modal="true" role="dialog">

    {{-- Backdrop --}}
    <div id="modalBackdrop"
         class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity duration-200 opacity-0"
         onclick="closeDeleteModal()"></div>

    {{-- Panel --}}
    <div id="modalPanel"
         class="relative bg-white/90 backdrop-blur-xl border border-white/60 rounded-2xl shadow-2xl w-full max-w-md p-7 transition-all duration-200 scale-95 opacity-0">

        {{-- Icon --}}
        <div class="flex items-center justify-center w-14 h-14 bg-red-50 rounded-2xl mx-auto mb-5">
            <svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>

        {{-- Title --}}
        <h3 class="text-xl font-bold text-slate-800 text-center mb-1">Hapus Pasien ke Tong Sampah?</h3>
        <p class="text-slate-500 text-sm text-center mb-5">
            Anda akan menghapus pasien <span id="modalPatientName" class="font-semibold text-slate-700"></span>.
        </p>

        {{-- Warning box --}}
        <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-3 mb-6 flex gap-3 items-start">
            <svg class="w-5 h-5 text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="text-sm text-red-700">
                <p class="font-semibold mb-0.5">Pasien akan dipindahkan ke Tong Sampah.</p>
                <p>Pasien dan riwayatnya (<span id="modalSessionCount" class="font-bold"></span> sesi) dapat dipulihkan kembali sewaktu-waktu.</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <button type="button" onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-2.5 rounded-xl font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors text-sm">
                Batal
            </button>
            <button type="button" id="confirmDeleteBtn" onclick="submitDelete()"
                    class="flex-1 px-4 py-2.5 rounded-xl font-semibold text-white bg-red-600 hover:bg-red-700 transition-colors text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let _deleteId = null;

    function openDeleteModal(id, name, sessions) {
        _deleteId = id;
        document.getElementById('modalPatientName').textContent = name;
        document.getElementById('modalSessionCount').textContent = sessions;

        const modal   = document.getElementById('deleteModal');
        const backdrop = document.getElementById('modalBackdrop');
        const panel   = document.getElementById('modalPanel');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        });
    }

    function closeDeleteModal() {
        const modal    = document.getElementById('deleteModal');
        const backdrop = document.getElementById('modalBackdrop');
        const panel    = document.getElementById('modalPanel');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('scale-100', 'opacity-100');
        panel.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            _deleteId = null;
        }, 180);
    }

    function submitDelete() {
        if (_deleteId) {
            document.getElementById('delete-form-' + _deleteId).submit();
        }
    }

    // Tutup dengan Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeDeleteModal();
    });
</script>
@endpush
