@extends('layouts.admin')
@section('title','Tambah Admin')

@section('content')

<h2 class="text-xl font-bold mb-4">Tambah Admin</h2>

<div class="bg-white p-6 rounded-xl shadow w-full max-w-lg">

<form method="POST" action="{{ route('admin.admins.store') }}">
    @csrf

    <div class="mb-3">
        <label>Nama</label>
        <input name="name" class="w-full border p-2 rounded" required>
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input name="email" class="w-full border p-2 rounded" required>
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="w-full border p-2 rounded" required>
    </div>

    <div class="mb-3">
        <label>Status</label>
        <select name="status" class="w-full border p-2 rounded">
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
        </select>
    </div>

    <div class="flex gap-3">
        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Simpan
        </button>

        <a href="{{ route('admin.admins.index') }}" 
           class="bg-gray-200 px-4 py-2 rounded">
           Kembali
        </a>
    </div>

</form>

</div>

@endsection