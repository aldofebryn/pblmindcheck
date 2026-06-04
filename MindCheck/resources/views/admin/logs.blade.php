@extends('layouts.admin')

@section('title', 'Log Riwayat Admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Log Riwayat Admin</h1>
                <p class="text-slate-500 mt-1">Riwayat aktivitas yang dilakukan oleh admin.</p>
            </div>

            <form method="GET" action="{{ route('admin.logs') }}" class="flex flex-col sm:flex-row gap-3">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari aktivitas..."
                       class="rounded-xl border-slate-200 px-4 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">

                <select name="module"
                        class="rounded-xl border-slate-200 px-4 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Modul</option>
                    <option value="Login" {{ request('module') == 'Login' ? 'selected' : '' }}>Login</option>
                    <option value="Admin" {{ request('module') == 'Admin' ? 'selected' : '' }}>Admin</option>
                    <option value="Pasien" {{ request('module') == 'Pasien' ? 'selected' : '' }}>Pasien</option>
                    <option value="Pertanyaan" {{ request('module') == 'Pertanyaan' ? 'selected' : '' }}>Pertanyaan</option>
                    <option value="Pengaturan" {{ request('module') == 'Pengaturan' ? 'selected' : '' }}>Pengaturan</option>
                    <option value="Dashboard" {{ request('module') == 'Dashboard' ? 'selected' : '' }}>Dashboard</option>
                </select>

                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl px-5 py-2 text-sm">
                    Filter
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-slate-600">Waktu</th>
                        <th class="text-left px-6 py-4 font-bold text-slate-600">Admin</th>
                        <th class="text-left px-6 py-4 font-bold text-slate-600">Modul</th>
                        <th class="text-left px-6 py-4 font-bold text-slate-600">Aksi</th>
                        <th class="text-left px-6 py-4 font-bold text-slate-600">Detail</th>
                        <th class="text-left px-6 py-4 font-bold text-slate-600">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                            {{ $log->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-800">
                            {{ $log->admin_name ?? 'Admin' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-600 font-semibold text-xs">
                                {{ $log->module ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-800">
                            {{ $log->action }}
                        </td>
                        <td class="px-6 py-4 text-slate-500">
                            {{ $log->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-slate-400">
                            {{ $log->ip_address ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            Belum ada log aktivitas admin.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
            <p class="text-sm text-slate-400">
                Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} log
            </p>

            <div class="flex items-center gap-2">
                @if($logs->onFirstPage())
                    <span class="px-4 py-2 rounded-xl bg-slate-100 text-slate-400 font-semibold text-sm">Previous</span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}"
                       class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700">
                        Previous
                    </a>
                @endif

                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}"
                       class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700">
                        Next
                    </a>
                @else
                    <span class="px-4 py-2 rounded-xl bg-slate-100 text-slate-400 font-semibold text-sm">Next</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
