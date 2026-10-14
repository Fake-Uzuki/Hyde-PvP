<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>@hasSection('title')@yield('title') | HYDE PVP@elseif(isset($title) && !empty($title)){{ $title }} | HYDE PVP@elseAdmin | HYDE PVP@endif</title>
    <link rel="icon" type="image/png" href="{{ asset('images/hyde-pvp-logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#070A12] text-slate-100 min-h-screen antialiased flex flex-col md:flex-row">

    <!-- Admin Sidebar -->
    <aside class="w-full md:w-64 bg-[#0B1120] border-r border-slate-800/80 flex flex-col shrink-0">
        <!-- Brand Header -->
        <div class="h-20 flex items-center gap-3 px-6 border-b border-slate-800/80">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('images/hyde-pvp-logo.png') }}" alt="HYDE PVP" class="h-10 w-auto object-contain rounded-lg shadow-sm" />
                <div class="flex flex-col">
                    <span class="font-black text-lg text-white font-gaming">
                        HYDE <span class="gaming-gradient-text">ADMIN</span>
                    </span>
                    <span class="text-[10px] text-purple-400 font-semibold uppercase tracking-wider -mt-1">
                        Control Center
                    </span>
                </div>
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-grow p-4 space-y-1.5 text-sm font-medium">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <!-- Moderator & Admin: Comments -->
            <a href="{{ route('admin.comments.index') }}"
               class="flex items-center justify-between px-4 py-2.5 rounded-xl transition {{ request()->routeIs('admin.comments.*') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    Comments
                </span>
                <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-300">
                    Moderation
                </span>
            </a>

            @if(Auth::user()->isAdmin())
                <div class="pt-4 pb-1 px-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">Content Management</div>

                <a href="{{ route('admin.news.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('admin.news.*') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    News & Updates
                </a>

                <a href="{{ route('admin.rules.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('admin.rules.*') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Server Rules
                </a>

                <a href="{{ route('admin.rankings.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('admin.rankings.*') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Rankings
                </a>

                <a href="{{ route('admin.uhc.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('admin.uhc.*') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                    </svg>
                    UHC Champions
                </a>

                <div class="pt-4 pb-1 px-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">System Settings</div>

                <a href="{{ route('admin.settings.edit') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('admin.settings.*') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Site Settings
                </a>
            @endif

            @if(Auth::user()->isSuperAdmin())
                <div class="pt-4 pb-1 px-4 text-[10px] font-bold uppercase tracking-wider text-purple-400 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Super Admin Control
                </div>

                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('admin.users.*') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    User Accounts & Roles
                </a>

                <a href="{{ route('admin.backups.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('admin.backups.*') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                    Database Backups
                </a>
            @endif
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-800/80 space-y-3">
            <a href="{{ route('home') }}" class="btn-secondary text-xs w-full py-2">
                ← View Public Website
            </a>
            <div class="flex items-center justify-between text-xs text-slate-400 px-1">
                <div>
                    <span class="text-white font-bold block">{{ Auth::user()->name }}</span>
                    <span class="text-[10px] text-purple-400 capitalize">{{ Auth::user()->role }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 font-semibold">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Admin Content Area -->
    <div class="flex-grow flex flex-col min-w-0">
        <!-- Top Banner -->
        <header class="h-20 bg-[#070A12]/80 backdrop-blur-md border-b border-slate-800/80 px-6 sm:px-10 flex items-center justify-between">
            <h1 class="text-xl font-bold text-white font-gaming">
                {{ $title ?? 'Administration' }}
            </h1>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 rounded-full text-xs font-mono font-semibold {{ Auth::user()->isAdmin() ? 'bg-purple-900/40 text-purple-300 border border-purple-800/50' : 'bg-blue-900/40 text-blue-300 border border-blue-800/50' }}">
                    {{ strtoupper(Auth::user()->role) }} ACCESS
                </span>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-grow p-6 sm:p-10 space-y-8 max-w-7xl w-full">
            <!-- Flash Message Alerts -->
            @if (session('status'))
                <div class="p-4 rounded-xl bg-purple-950/40 border border-purple-500/40 text-purple-200 text-sm flex items-center justify-between shadow-lg" role="alert">
                    <span>{{ session('status') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-purple-400 hover:text-white" title="Close">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-950/40 border border-rose-500/40 text-rose-200 text-sm flex items-center justify-between shadow-lg" role="alert">
                    <span>{{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white" title="Close">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-950/40 border border-rose-500/40 text-rose-200 text-sm shadow-lg">
                    <p class="font-bold mb-1">Please correct the following errors:</p>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('admin_content')
        </main>
    </div>

</body>
</html>
