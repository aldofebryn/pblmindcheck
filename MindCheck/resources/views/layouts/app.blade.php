<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','MindCheck') — Skrining Kesehatan Mental</title>
    <link rel="icon" href="{{ asset('logo1.png') }}" type="image/png">
    @vite(['resources/css/app.css','resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

<nav class="bg-white/90 backdrop-blur-md border-b border-slate-100 sticky top-0 z-40">
    <div class="w-full px-6 lg:px-14 xl:px-24"
         style="height:60px;display:flex;align-items:center;justify-content:space-between;gap:16px">

        {{-- Logo --}}
        <a href="{{ route('landing') }}" class="flex items-center gap-3 shrink-0">
            <span class="w-9 h-9 flex items-center justify-center">
                <img src="{{ asset('logo1.png') }}" class="w-full h-full object-contain" alt="Logo">
            </span>
            <span class="font-bold text-slate-900 text-lg">MindCheck</span>
        </a>

        {{-- Menu --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('patient.login') }}"
               class="px-6 py-3 text-slate-700 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition font-semibold text-lg">
                Mulai Skrining
            </a>
        </div>
    </div>
</nav>


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