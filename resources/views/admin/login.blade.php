<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — MindCheck</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-linear-to-tr from-blue-50 via-slate-100 to-blue-100 min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-hidden">

    <!-- Background blur circles -->
    <div class="absolute -top-24 -left-24 w-72 h-72 bg-blue-300 rounded-full filter blur-3xl opacity-30 animate-pulse-slow"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-purple-300 rounded-full filter blur-3xl opacity-30 animate-pulse-slow"></div>

    <div class="relative w-full max-w-sm sm:max-w-md">

        <div class="text-center mb-8 sm:mb-10 relative z-10">
            <span class="w-14 h-14 sm:w-16 sm:h-16 bg-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-4 sm:mb-5 shadow-lg">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </span>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">MindCheck Admin</h1>
            <p class="text-slate-400 mt-1 sm:mt-1.5 text-sm sm:text-base">Masuk ke panel administrasi</p>
        </div>

        <div class="bg-white/70 backdrop-blur-lg border border-white/30 rounded-3xl p-6 sm:p-10 shadow-xl relative z-10">
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 sm:px-5 py-3 sm:py-4 mb-5 sm:mb-6 font-medium text-sm sm:text-base">
                {{ $errors->first() }}
            </div>
            @endif
            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4 sm:space-y-5">
                @csrf
                <div>
                    <label class="block text-sm sm:text-base font-bold text-slate-600 mb-1.5 sm:mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="admin@mindcheck.id"
                           class="w-full border border-slate-200 rounded-2xl px-4 sm:px-5 py-3 sm:py-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm sm:text-base
                                  {{ $errors->has('email') ? 'border-red-300 bg-red-50' : '' }}">
                </div>
                <div>
                    <label class="block text-sm sm:text-base font-bold text-slate-600 mb-1.5 sm:mb-2">Kata Sandi</label>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="w-full border border-slate-200 rounded-2xl px-4 sm:px-5 py-3 sm:py-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm sm:text-base">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 sm:py-4 rounded-2xl transition-all text-base sm:text-lg mt-1 sm:mt-2 shadow-lg hover:shadow-xl">
                    Masuk
                </button>
            </form>
        </div>

        <div class="text-center mt-4 sm:mt-6 space-y-1 sm:space-y-2 relative z-10">
            <a href="{{ route('landing') }}" class="block text-slate-400 hover:text-slate-600 transition-colors text-sm sm:text-base">← Kembali ke beranda</a>
            <p class="text-slate-300 text-xs sm:text-sm">Default: admin@mindcheck.id / mindcheck2026</p>
        </div>
    </div>

    <style>
        @keyframes pulse-slow {
            0%, 100% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.1); opacity: 0.5; }
        }
        .animate-pulse-slow {
            animation: pulse-slow 8s ease-in-out infinite;
        }
    </style>
</body>
</html>