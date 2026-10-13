<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'HYDE PVP') }} — Authentication</title>

        <link rel="icon" type="image/png" href="{{ asset('images/hyde-pvp-logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-100 bg-[#070A12] antialiased selection:bg-purple-600 selection:text-white">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-8 sm:pt-0 px-4">
            <div class="mb-6 flex flex-col items-center">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 group">
                    <img src="{{ asset('images/hyde-pvp-logo.png') }}" alt="HYDE PVP" class="h-16 w-auto object-contain drop-shadow-[0_4px_16px_rgba(168,85,247,0.4)] transition-transform group-hover:scale-105" />
                    <span class="text-2xl font-black tracking-wider text-white font-gaming">
                        HYDE <span class="gaming-gradient-text">PVP</span>
                    </span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-8 gaming-card shadow-2xl border-purple-500/30">
                {{ $slot }}
            </div>

            <div class="mt-8 text-center text-xs text-slate-500">
                <a href="{{ route('home') }}" class="text-purple-400 hover:text-purple-300">← Return to Homepage</a>
            </div>
        </div>
    </body>
</html>
