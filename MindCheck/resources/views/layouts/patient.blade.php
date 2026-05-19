<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Dashboard Pasien') | MindCheck</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased font-sans">
<div class="flex min-h-screen">

    {{-- Patient Sidebar --}}
    <aside class="hidden md:flex flex-col bg-white border-r border-slate-200/60 shrink-0 fixed top-0 left-0 h-full z-30 transition-all duration-300 shadow-[4px_0_24px_rgba(0,0,0,0.02)]" style="width:240px">
        
        {{-- Logo Area --}}
        <div class="px-6 flex items-center gap-3 border-b border-slate-200/60 bg-white" style="height:64px">
            <div class="flex items-center justify-center w-10 h-10">
                <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-contain" alt="Logo">
            </div>
            <div class="flex flex-col justify-center">
                <span class="font-bold text-slate-800 text-base tracking-tight leading-tight">MindCheck</span>
                <span class="text-[10px] text-blue-600 font-semibold tracking-widest uppercase">Portal Pasien</span>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
            @php
            $nav = [
                ['patient.dashboard',  'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'Dashboard'],
                ['patient.settings',  'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'Pengaturan'],
            ];
            @endphp
            
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3 px-4">Menu Utama</div>

            @foreach($nav as [$route, $icon, $label])
            <a href="{{ route($route) }}"
               class="group flex items-center gap-3.5 px-4 py-3 rounded-xl font-medium text-sm transition-all duration-200
                      {{ request()->routeIs($route) ? 'text-blue-700 bg-blue-50/80 shadow-sm ring-1 ring-blue-100/50' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                
                <svg class="w-5 h-5 shrink-0 transition-colors duration-200 {{ request()->routeIs($route) ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs($route) ? '2.5' : '2' }}"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                <span>{{ $label }}</span>
            </a>
            @endforeach
        </nav>

        {{-- Profile Area (Logout) --}}
        <div class="p-4 border-t border-slate-200/60 bg-slate-50/50">
            <form method="POST" action="{{ route('patient.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-medium text-sm text-red-600 bg-red-50 hover:bg-red-100 transition-colors shadow-sm border border-red-100">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col min-w-0 bg-[#f8fafc] md:ml-[240px]">
        {{-- Header --}}
        <header class="bg-white/90 backdrop-blur-xl border-b border-slate-200/60 flex items-center justify-between px-6 sticky top-0 z-20 shadow-[0_4px_24px_rgba(0,0,0,0.01)] transition-all duration-300" style="height:64px">
            <div class="flex items-center gap-4">
                <h1 class="font-bold text-slate-800 text-base tracking-tight">@yield('title')</h1>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">{{ now()->translatedFormat('l, d F Y') }}</span>
                    <span class="text-xs font-bold text-slate-700">MindCheck</span>
                </div>
            </div>
        </header>
        
        <main class="flex-1 p-4 sm:p-6 overflow-auto">
            @yield('content')
        </main>
    </div>
</div>
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(203, 213, 225, 0.4); border-radius: 4px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.6); }
</style>
@stack('scripts')
</body>
</html>
