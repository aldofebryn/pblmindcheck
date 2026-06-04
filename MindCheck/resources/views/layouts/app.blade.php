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

        {{-- Menu kosong, tombol ada di hero --}}
        <div></div>
    </div>
</nav>


<main class="flex-1">
    @yield('content')
</main>

@stack('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</body>
</html>