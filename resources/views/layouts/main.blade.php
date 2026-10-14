<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>@hasSection('title')@yield('title')@elseif(isset($title) && !empty($title)){{ $title }} | HYDE PVP@elseHYDE PVP@endif</title>
    <meta name="description" content="{{ $metaDescription ?? 'HYDE PVP is the premier Minecraft Bedrock competitive PvP community. Join custom duel arenas, nodebuff, combo, and compete in our official season rankings.' }}">

    <!-- Favicon using official HYDE PVP logo -->
    <link rel="icon" type="image/png" href="{{ asset('images/hyde-pvp-logo.png') }}">

    <!-- Preconnect Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#070A12] text-slate-100 min-h-screen flex flex-col antialiased selection:bg-purple-600 selection:text-white">

    <!-- Navigation Bar -->
    @include('layouts.navigation')

    @auth
        @if (!auth()->user()->hasVerifiedEmail())
            <div class="bg-gradient-to-r from-purple-950/90 via-amber-950/80 to-purple-950/90 border-b border-amber-500/30 text-amber-200 text-xs py-2 px-4 text-center flex items-center justify-center gap-3 shadow-md">
                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Your email address is unverified. Please check your inbox to confirm your account.</span>
                <form method="POST" action="{{ route('verification.send') }}" class="inline">
                    @csrf
                    <button type="submit" class="underline font-bold text-amber-300 hover:text-white transition">
                        Resend Email
                    </button>
                </form>
            </div>
        @endif
    @endauth

    <!-- Flash Messages & Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if (session('status'))
            <div class="p-4 mb-4 rounded-xl bg-purple-950/40 border border-purple-500/40 text-purple-200 flex items-center justify-between shadow-lg shadow-purple-950/20" role="alert">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">{{ session('status') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-purple-400 hover:text-white ml-4" title="Close">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 mb-4 rounded-xl bg-rose-950/40 border border-rose-500/40 text-rose-200 flex items-center justify-between shadow-lg shadow-rose-950/20" role="alert">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white ml-4" title="Close">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 mb-4 rounded-xl bg-rose-950/40 border border-rose-500/40 text-rose-200 shadow-lg shadow-rose-950/20" role="alert">
                <div class="flex items-center gap-2 font-semibold text-sm mb-2 text-rose-300">
                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Please check the following errors:
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 text-rose-200">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-grow">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Consistent Community Footer -->
    @php
        $footerSettings = \App\Models\SiteSetting::getAll();
    @endphp
    <footer class="bg-[#05080E] border-t border-slate-900/90 text-slate-400 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <!-- Brand Info -->
                <div class="space-y-4 md:col-span-1">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/hyde-pvp-logo.png') }}" alt="HYDE PVP" class="h-10 w-auto object-contain rounded-lg shadow-md" />
                        <span class="font-extrabold text-xl tracking-wider text-white font-gaming">
                            HYDE <span class="gaming-gradient-text">PVP</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Competitive Minecraft Bedrock PvP community server. Duel arenas, optimized knockback, and custom game mechanics.
                    </p>
                    <a href="{{ $footerSettings['discord_invite_url'] ?? 'https://discord.gg/VYp7Khvf' }}"
                       target="_blank" rel="noopener noreferrer"
                       class="btn-discord text-xs py-2 px-3 inline-flex">
                        Join Our Discord
                    </a>
                </div>

                <!-- Navigation Links -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-200 mb-4">Quick Navigation</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-purple-400 transition">Homepage</a></li>
                        <li><a href="{{ route('news.index') }}" class="hover:text-purple-400 transition">News & Announcements</a></li>
                        <li><a href="{{ route('server-info') }}" class="hover:text-purple-400 transition">Server Information</a></li>
                        <li><a href="{{ route('rules') }}" class="hover:text-purple-400 transition">Official Rules</a></li>
                        <li><a href="{{ route('rankings') }}" class="hover:text-purple-400 transition">Player & Clan Rankings</a></li>
                    </ul>
                </div>

                <!-- Server Info Snippet -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-200 mb-4">Bedrock Server</h3>
                    <div class="space-y-2 text-xs">
                        <div class="p-3 rounded-xl bg-slate-900/90 border border-slate-800">
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Address</span>
                            <span class="text-cyan-300 font-mono font-semibold">{{ $footerSettings['server_address'] ?? 'hydepvp.ultramc.co' }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-900/90 border border-slate-800">
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Port (Bedrock)</span>
                            <span class="text-slate-300 font-mono font-semibold">{{ $footerSettings['server_port'] ?? '19015' }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-900/90 border border-slate-800">
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Supported Version</span>
                            <span class="text-slate-300 font-mono font-semibold">{{ $footerSettings['server_version'] ?? 'v1.21.x Bedrock' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Community & Discord Notice -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-200 mb-4">Events & Tournaments</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-3">
                        All clan wars, UHC tournaments, and event coordination take place live on our official Discord server.
                    </p>
                    <div class="p-3 rounded-xl bg-purple-950/20 border border-purple-900/40 text-[11px] text-purple-300">
                        Never miss a tournament signup! Join our Discord community for live role notifications.
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-900/90 mt-12 pt-8 flex flex-col md:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>© {{ date('Y') }} HYDE PVP. All rights reserved.</p>
                <p class="text-[11px] text-center md:text-right">
                    Not an official Minecraft product. Not approved by or associated with Mojang or Microsoft.
                </p>
            </div>
        </div>
    </footer>

    <!-- Copy to Clipboard helper script -->
    <script>
        function copyServerAddress(button, text) {
            navigator.clipboard.writeText(text).then(() => {
                const originalText = button.innerHTML;
                button.innerHTML = '<span>Copied!</span>';
                button.classList.add('bg-emerald-600');
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('bg-emerald-600');
                }, 2000);
            }).catch(err => {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
</body>
</html>
