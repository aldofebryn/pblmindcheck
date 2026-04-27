@extends('layouts.admin')

@section('title', 'Daftar Pertanyaan DASS-21')

@section('content')
<div class="w-full">
    {{-- Header tanpa emoji --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Daftar Pertanyaan</h1>
            <p class="text-slate-500 text-sm">Instrumen DASS-21 versi Indonesia & Inggris</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.questions.trash') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                Tong Sampah
            </a>
            <a href="{{ route('admin.questions.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
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
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $questions->appends(['subskala' => request('subskala')])->links() }}
        </div>
    </div>
</div>
@endsection