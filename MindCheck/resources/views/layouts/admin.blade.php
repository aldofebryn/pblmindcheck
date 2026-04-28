<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title','Dashboard') | MindCheck</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @stack('head')
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="hidden md:flex flex-col bg-white border-r border-slate-100 shrink-0 fixed top-0 left-0 h-full z-30" style="width:220px">
        <div class="px-4 sm:px-6 flex items-center gap-3 border-b border-slate-100" style="height:60px">
            <span class="w-8 h-8 sm:w-9 sm:h-9 bg-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </span>
            <div>
                <span class="font-bold text-slate-900 text-sm sm:text-base">MindCheck</span>
                <span class="block text-xs sm:text-sm text-slate-400">Admin Panel</span>
            </div>
        </div>

        <nav class="flex-1 p-2 sm:p-4 space-y-1">
            @php
            $nav = [
                ['admin.dashboard',  'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'Dashboard'],
                ['admin.questions.index',  'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'Pertanyaan DASS-21'],
                ['admin.admins.index', 'M5.121 17.804A4 4 0 0112 15a4 4 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'Daftar Admin'],
                ['admin.settings',   'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'Pengaturan'],
            ];
            @endphp
            @foreach($nav as [$route, $icon, $label])
            <a href="{{ route($route) }}"
               class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-xl font-medium text-sm sm:text-base transition-colors
                      {{ request()->routeIs($route) ? 'bg-blue-50 text-blue-700' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                <span>{{ $label }}</span>
            </a>
            @endforeach
        </nav>

        <div class="p-3 sm:p-4 border-t border-slate-100">
            <p class="px-3 py-1 text-xs sm:text-sm text-slate-400 truncate mb-1">{{ session('admin_name','Administrator') }}</p>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="w-full flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-xl font-medium text-sm sm:text-base text-slate-500 hover:text-red-600 hover:bg-red-50 transition-colors">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0" style="margin-left:220px">
        <header class="bg-white border-b border-slate-100 flex items-center justify-between px-4 sm:px-8 sticky top-0 z-20" style="height:60px">
            <h1 class="font-semibold text-slate-900 text-base sm:text-lg">@yield('title','Dashboard')</h1>
            @if(($alertCount ?? 0) > 0)
            <span class="inline-flex items-center gap-1 sm:gap-2 bg-red-50 text-red-700 border border-red-200 font-semibold px-3 sm:px-4 py-1 sm:py-2 rounded-full text-xs sm:text-sm">
                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                {{ $alertCount }} kasus perlu perhatian (48 jam)
            </span>
            @endif
        </header>
        <main class="flex-1 p-4 sm:p-8 overflow-auto">@yield('content')</main>
    </div>
</div>
@stack('scripts')
</body>
</html>