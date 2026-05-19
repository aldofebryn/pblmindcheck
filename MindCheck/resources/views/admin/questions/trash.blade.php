@extends('layouts.admin')

@section('title', 'Tong Sampah - Pertanyaan DASS-21')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tong Sampah</h1>
            <p class="text-slate-500 text-sm">Pertanyaan yang telah dihapus (soft delete)</p>
        </div>
        <a href="{{ route('admin.questions.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">Pertanyaan (ID)</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">Subskala</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($questions as $q)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-5 font-mono text-sm font-semibold text-slate-700">
                            {{ str_pad($q->nomor, 2, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-5 text-slate-800">{{ $q->teks_id }}</td>
                        <td class="px-6 py-5">
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                {{ $q->subskala }}
                            </span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-3">
                                {{-- Pulihkan --}}
                                <form action="{{ route('admin.questions.restore', $q->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button class="px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors">
                                        Pulihkan
                                    </button>
                                </form>
                                {{-- Hapus Permanen --}}
                                <button type="button"
                                        onclick="window.openModal_forceDeleteQuestion('force-q-{{ $q->id }}')"
                                        class="px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                    Hapus Permanen
                                </button>
                                <form id="force-q-{{ $q->id }}" action="{{ route('admin.questions.force', $q->id) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Tong sampah kosong.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($questions->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $questions->links() }}
        </div>
        @endif
    </div>
</div>

@php
    $modalId           = 'forceDeleteQuestion';
    $modalTitle        = 'Hapus Permanen?';
    $modalBody         = 'Pertanyaan ini akan dihapus dari sistem secara <strong>permanen</strong>.';
    $modalWarning      = 'Tindakan ini tidak dapat dibatalkan. Data tidak bisa dipulihkan.';
    $modalConfirmLabel = 'Ya, Hapus Permanen';
    $modalConfirmColor = 'red';
@endphp
@include('admin.partials.confirm-modal')

@endsection