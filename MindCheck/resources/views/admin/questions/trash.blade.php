@extends('layouts.admin')

@section('title', 'Tong Sampah - Pertanyaan DASS-21')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">🗑️ Tong Sampah</h1>
            <p class="text-slate-500 text-sm">Pertanyaan yang telah dihapus (soft delete)</p>
        </div>
        <a href="{{ route('admin.questions.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
            ← Kembali ke Daftar
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
                                <form action="{{ route('admin.questions.restore', $q->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button class="text-green-600 hover:text-green-800 font-medium transition">Pulihkan</button>
                                </form>
                                <form action="{{ route('admin.questions.force', $q->id) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('⚠️ Hapus permanen? Tindakan ini tidak bisa dibatalkan.');">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 font-medium transition">Hapus Permanen</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400">Tong sampah kosong.</td>
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
@endsection