<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','MindCheck') — Skrining Kesehatan Mental</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

<nav class="bg-white/90 backdrop-blur-md border-b border-slate-100 sticky top-0 z-40">
    <div class="w-full px-6 lg:px-14 xl:px-24"
         style="height:72px;display:flex;align-items:center;justify-content:space-between;gap:16px">

        {{-- Logo --}}
        <a href="{{ route('landing') }}" class="flex items-center gap-3 shrink-0">
            <span class="w-11 h-11 bg-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </span>
            <span class="font-bold text-slate-900 text-2xl">MindCheck</span>
        </a>

        {{-- Menu --}}
        <div class="flex items-center gap-3">
    @if(session('patient_token'))
        <a href="{{ route('screening') }}"
           class="px-6 py-3 text-slate-700 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition font-semibold text-lg">
            Skrining
        </a>

        <a href="{{ route('history') }}"
           class="px-6 py-3 text-slate-700 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition font-semibold text-lg">
            Riwayat
        </a>
    @else
        <a href="{{ route('token') }}"
           class="px-6 py-3 text-slate-700 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition font-semibold text-lg">
            Mulai
        </a>
    @endif

    {{-- Divider --}}
    <div class="w-px h-7 bg-slate-300 mx-2"></div>

    <a href="{{ route('admin.login') }}"
       class="px-5 py-3 text-slate-500 hover:text-slate-700 text-base font-medium rounded-xl hover:bg-slate-100 transition">
        Admin
    </a>
</div>
</nav>

@if ($errors->any())
<div class="w-full px-6 lg:px-14 xl:px-24 pt-5">
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-4 font-medium">
        {{ $errors->first() }}
    </div>
</div>
@endif

@if(session('success'))
<div class="w-full px-6 lg:px-14 xl:px-24 pt-5">
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-5 py-4 font-medium">
        {{ session('success') }}
    </div>
</div>
@endif

<main class="flex-1">
    @yield('content')
</main>

<footer class="border-t border-slate-100 py-10 mt-16">
    <div class="w-full px-6 lg:px-14 xl:px-24 text-center text-slate-400 space-y-1.5">
        <p>
            MindCheck menggunakan instrumen 
            <strong class="text-slate-500">DASS-21</strong> 
            (Lovibond &amp; Lovibond, 1995) dengan metode 
            <strong class="text-slate-500">Decision Tree</strong>.
        </p>
        <p class="text-sm">
            Bukan pengganti diagnosis profesional · Darurat:
            <strong class="text-slate-600">SEJIWA 119 ext 8</strong> (24 jam)
        </p>
    </div>
</footer>

@stack('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</body>
</html>