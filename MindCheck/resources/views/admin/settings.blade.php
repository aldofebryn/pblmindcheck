@extends('layouts.admin')
@section('title','Pengaturan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 mb-8">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-900">Pengaturan Jeda Screening</h2>
            <p class="text-slate-500 mt-2">
                Atur berapa lama user bisa melanjutkan screening yang belum selesai.
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-2xl bg-green-50 border border-green-200 text-green-700 px-5 py-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-2xl bg-red-50 border border-red-200 text-red-700 px-5 py-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <label for="screening_resume_minutes" class="block font-semibold text-slate-800 mb-2">
                Durasi Jeda Screening
            </label>

            <div class="flex items-center gap-3">
                <input type="number"
                       name="screening_resume_minutes"
                       id="screening_resume_minutes"
                       min="1"
                       max="1440"
                       value="{{ old('screening_resume_minutes', $screeningResumeMinutes) }}"
                       class="w-40 rounded-2xl border border-slate-200 px-4 py-3 focus:border-blue-500 focus:ring-blue-500">

                <span class="text-slate-500 font-medium">menit</span>

                <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition">
                    Simpan
                </button>
            </div>

            <p class="text-sm text-slate-400 mt-3">
                Contoh: isi 30 berarti user bisa melanjutkan screening maksimal 30 menit setelah keluar.
            </p>
        </form>
    </div></div>
@endsection