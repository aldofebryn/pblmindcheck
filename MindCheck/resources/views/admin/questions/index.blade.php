@extends('layouts.admin')

@section('title', 'Daftar Pertanyaan DASS-21')

@section('content')
<div class="w-full">
    {{-- Header tanpa emoji --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Daftar Pertanyaan</h1>
            <p class="text-slate-500 text-sm mt-1">Instrumen DASS-21 versi Indonesia & Inggris</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.questions.trash') }}" 
               class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700 border border-slate-200 bg-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Tempat Sampah
            </a>
            <a href="{{ route('admin.questions.create') }}" 
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Pertanyaan
            </a>
        </div>
    </div>

    {{-- Filter Subskala --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @php $current = request('subskala', 'all'); @endphp
        @foreach(['all' => 'Semua', 'Depression' => 'Depresi', 'Anxiety' => 'Kecemasan', 'Stress' => 'Stres'] as $val => $label)
        <a href="{{ route('admin.questions.index', ['subskala' => $val]) }}" 
           class="px-4 py-2 rounded-full border font-medium text-sm transition-all
                  {{ $current == $val ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-300 hover:bg-blue-50' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Tabel full-width --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-16">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Pertanyaan (ID)</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Pertanyaan (EN)</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-28">Subskala</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @include('admin.questions._table_rows', ['questions' => $questions])
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
            <p class="text-sm text-slate-400">
                Menampilkan {{ $questions->firstItem() ?? 0 }} - {{ $questions->lastItem() ?? 0 }} dari {{ $questions->total() }} pertanyaan
            </p>
            <div class="flex items-center gap-2">
                @if ($questions->onFirstPage())
                    <span class="px-4 py-2 rounded-xl bg-slate-100 text-slate-400 font-semibold text-sm cursor-not-allowed">Previous</span>
                @else
                    <a href="{{ $questions->previousPageUrl() }}&subskala={{ request('subskala') }}"
                       class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 transition">
                        Previous
                    </a>
                @endif

                @if ($questions->hasMorePages())
                    <a href="{{ $questions->nextPageUrl() }}&subskala={{ request('subskala') }}"
                       class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 transition">
                        Next
                    </a>
                @else
                    <span class="px-4 py-2 rounded-xl bg-slate-100 text-slate-400 font-semibold text-sm cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
    </div>
</div>

@php
    $modalId           = 'deleteQuestion';
    $modalTitle        = 'Hapus Pertanyaan?';
    $modalBody         = 'Pertanyaan akan dipindahkan ke <strong>tempat sampah</strong> dan dapat dipulihkan kapan saja.';
    $modalWarning      = 'Pertanyaan tidak akan aktif selama berada di tempat sampah.';
    $modalConfirmLabel = 'Ya, Hapus';
    $modalConfirmColor = 'red';
@endphp
@include('admin.partials.confirm-modal')

@endsection