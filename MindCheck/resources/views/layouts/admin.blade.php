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
<body class="bg-gradient-to-br from-blue-100/60 via-slate-50 to-slate-100 min-h-screen text-slate-800 antialiased font-sans bg-fixed">
<div class="flex min-h-screen">

    {{-- Glassmorphic Harmonious Sidebar --}}
    <aside class="hidden md:flex flex-col bg-white/60 backdrop-blur-2xl border-r border-white/50 shrink-0 fixed top-0 left-0 h-full z-30 transition-all duration-300 shadow-[4px_0_24px_rgba(0,0,0,0.02)]" style="width:240px">
        
        {{-- Logo Area (Aligned with Header) --}}
        <div class="px-6 flex items-center gap-3 border-b border-white/50" style="height:64px">
            <div class="flex items-center justify-center w-10 h-10">
                <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-contain" alt="Logo">
            </div>
            <div class="flex flex-col justify-center">
                <span class="font-bold text-slate-800 text-base tracking-tight leading-tight">MindCheck</span>
                <span class="text-[10px] text-blue-600 font-semibold tracking-widest uppercase">Admin Panel</span>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
            @php
            $nav = [
                ['admin.dashboard',  'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'Dashboard'],
                ['admin.patients.index',  'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'Daftar Pasien'],
                ['admin.questions.index',  'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'Pertanyaan DASS-21'],
                ['admin.admins.index', 'M5.121 17.804A4 4 0 0112 15a4 4 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'Daftar Admin'],
                ['admin.logs', 'M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 10c-2.485 0-4.5-2.015-4.5-4.5S9.515 9 12 9s4.5 2.015 4.5 4.5S14.485 18 12 18zm0-12a8.5 8.5 0 100 17 8.5 8.5 0 000-17z', 'Log Riwayat'],
                ['admin.settings',   'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'Pengaturan'],
            ];
            @endphp
            
            <div class="text-[11px] font-bold text-slate-500/70 uppercase tracking-widest mb-3 px-4">Menu Utama</div>

            @foreach($nav as [$route, $icon, $label])
            <a href="{{ route($route) }}"
               class="group flex items-center gap-3.5 px-4 py-3 rounded-xl font-medium text-sm transition-all duration-300
                      {{ request()->routeIs($route) ? 'text-blue-700 bg-white/80 shadow-sm ring-1 ring-blue-100/50' : 'text-slate-500 hover:text-slate-900 hover:bg-white/40' }}">
                
                <svg class="w-5 h-5 shrink-0 transition-colors duration-300 {{ request()->routeIs($route) ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs($route) ? '2.5' : '2' }}"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                <span>{{ $label }}</span>
            </a>
            @endforeach
        </nav>

        {{-- Profile Area --}}
        <div class="p-4 border-t border-white/50 bg-white/30 backdrop-blur-sm">
            <div class="flex items-center gap-3 px-4 py-3 mb-2 rounded-xl bg-white/70 backdrop-blur-md border border-white shadow-sm">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-slate-700 to-slate-800 flex items-center justify-center text-white font-bold shrink-0">
                    {{ strtoupper(substr(session('admin_name','A'), 0, 1)) }}
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="text-sm font-bold text-slate-700 truncate">{{ session('admin_name','Administrator') }}</p>
                    <p class="text-xs text-slate-500 truncate">Admin MindCheck</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-500 hover:text-red-600 hover:bg-white/50 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col min-w-0 bg-transparent" style="margin-left:240px">
        {{-- Glassmorphic Harmonious Header --}}
        <header class="bg-white/60 backdrop-blur-2xl border-b border-white/50 flex items-center justify-between px-6 sticky top-0 z-20 shadow-[0_4px_24px_rgba(0,0,0,0.01)] transition-all duration-300" style="height:64px">
            <div class="flex items-center gap-4">
                <h1 class="font-bold text-slate-800 text-base tracking-tight">@yield('title','Dashboard')</h1>
            </div>
            
            <div class="flex items-center gap-5">
                @if(($alertCount ?? 0) > 0)
                <div class="inline-flex items-center gap-2 bg-red-50/80 backdrop-blur-sm text-red-600 border border-red-100 font-medium px-4 py-2 rounded-xl text-sm transition-colors cursor-pointer hover:bg-red-100" title="Ada kasus darurat!">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                    </span>
                    {{ $alertCount }} Perhatian Medis
                </div>
                @endif
                
                <div class="w-px h-8 bg-slate-300/50 hidden sm:block"></div>
                
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">{{ now()->translatedFormat('l, d F Y') }}</span>
                    <span class="text-xs font-bold text-slate-700">MindCheck Admin</span>
                </div>
            </div>
        </header>
        
        <main class="flex-1 p-6 overflow-auto relative">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>
</div>
<style>
    /* Clean Custom Scrollbar for Sidebar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(203, 213, 225, 0.4);
        border-radius: 4px;
    }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.6);
    }
</style>
@stack('scripts')
</body>
</html>