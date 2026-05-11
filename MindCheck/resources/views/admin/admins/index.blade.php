@extends('layouts.admin')
@section('title','Manajemen Admin')

@section('content')

@if(session('success'))
<div class="mb-4 bg-green-100 text-green-700 px-4 py-2 rounded">
    {{ session('success') }}
</div>
@endif

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-xl font-bold">Daftar Admin</h2>
        <p class="text-gray-500 text-sm">Kelola administrator sistem</p>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('admin.admins.trash') }}" 
           class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">
           Tong Sampah
        </a>

        <a href="{{ route('admin.admins.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg">
           + Tambah Admin
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">

<table class="w-full text-sm">
    <thead class="text-gray-400">
        <tr>
            <th class="text-left py-2">Nama</th>
            <th class="text-left">Email</th>
            <th class="text-center">Status</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>

    <tbody>
        @forelse($admins as $admin)
        <tr class="border-t">
            <td class="py-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold">
                        {{ strtoupper(substr($admin->name,0,2)) }}
                    </div>
                    {{ $admin->name }}
                </div>
            </td>

            <td>{{ $admin->email }}</td>

            <td class="text-center">
                @if($admin->status)
                    <span class="text-green-600 font-semibold text-base">● Aktif</span>
                @else
                    <span class="text-black font-semibold text-base">● Nonaktif</span>
                @endif
            </td>

            <td class="text-center">
                <div class="flex justify-center items-center gap-5">

                    <a href="{{ route('admin.admins.edit',$admin->id) }}" 
                       class="text-blue-600 font-semibold text-base hover:underline px-2 py-1 rounded hover:bg-blue-50">
                       Edit
                    </a>

                    <form action="{{ route('admin.admins.delete',$admin->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="text-red-500 font-semibold text-base hover:underline px-2 py-1 rounded hover:bg-red-50">
                            Hapus
                        </button>
                    </form>

                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center py-5 text-gray-400">
                Belum ada data admin
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

</div>

@endsection