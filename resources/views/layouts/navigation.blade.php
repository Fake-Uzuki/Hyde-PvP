@php
    $discordUrl = \App\Models\SiteSetting::get('discord_invite_url', 'https://discord.gg/hydepvp');
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-50 bg-[#070A12]/95 backdrop-blur-md border-b border-slate-800/80 shadow-lg shadow-black/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand & Desktop Navigation -->
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group focus:outline-none">
                    <img src="{{ asset('images/hyde-pvp-logo.png') }}" alt="HYDE PVP" class="h-11 w-auto object-contain rounded-xl shadow-md transition-transform duration-200 group-hover:scale-105" />
                    <div class="flex flex-col">
                        <span class="font-extrabold text-xl tracking-wider text-white font-gaming flex items-center gap-1.5">
                            HYDE <span class="gaming-gradient-text">PVP</span>
                        </span>
                        <span class="text-[10px] uppercase tracking-widest text-purple-400 font-semibold -mt-1">
                            Bedrock Community
                        </span>
                    </div>
                </a>

                <!-- Desktop Links -->
                <div class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a href="{{ route('home') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-colors duration-150 {{ request()->routeIs('home') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Home
                    </a>
                    <a href="{{ route('news.index') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-colors duration-150 {{ request()->routeIs('news.*') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        News
                    </a>
                    <a href="{{ route('server-info') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-colors duration-150 {{ request()->routeIs('server-info') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Server Info
                    </a>
                    <a href="{{ route('rules') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-colors duration-150 {{ request()->routeIs('rules') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Rules
                    </a>
                    <a href="{{ route('rankings') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-colors duration-150 {{ request()->routeIs('rankings') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Rankings
                    </a>
                </div>
            </div>

            <!-- Right Actions & User Menu -->
            <div class="hidden md:flex items-center gap-3">
                <a href="{{ $discordUrl }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-[#5865F2] hover:bg-[#4752C4] shadow-md shadow-indigo-600/20 transition-all duration-200 active:scale-95">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.929 1.793 8.18 1.793 12.061 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.894.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.028zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                    </svg>
                    Discord
                </a>

                @auth
                    <div class="relative" x-data="{ userMenu: false }">
                        <button @click="userMenu = !userMenu"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-purple-500/50 text-slate-200 text-sm font-medium transition focus:outline-none">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-purple-600 to-fuchsia-600 flex items-center justify-center font-bold text-white text-xs shadow-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="text-left hidden lg:block">
                                <div class="text-xs font-bold leading-tight">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] text-purple-400 capitalize">{{ Auth::user()->role }}</div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="userMenu" @click.away="userMenu = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 rounded-2xl bg-[#0F172A] border border-slate-800 shadow-2xl py-2 z-50 text-sm">
                            <div class="px-4 py-2 border-b border-slate-800/80">
                                <p class="text-xs text-slate-400">Signed in as</p>
                                <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->email }}</p>
                                @if(Auth::user()->minecraft_gamertag)
                                    <p class="text-[11px] text-cyan-400 mt-0.5 truncate flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 inline-block"></span>
                                        Xbox: {{ Auth::user()->minecraft_gamertag }}
                                    </p>
                                @endif
                            </div>

                            @if(Auth::user()->isStaff())
                                <a href="{{ route('admin.dashboard') }}"
                                   class="flex items-center gap-2 px-4 py-2 text-purple-400 hover:text-purple-300 hover:bg-purple-900/20 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Admin Dashboard
                                </a>
                            @endif

                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2 px-4 py-2 text-slate-300 hover:text-white hover:bg-slate-800/50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z" />
                                </svg>
                                Account Settings
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left flex items-center gap-2 px-4 py-2 text-rose-400 hover:text-rose-300 hover:bg-rose-950/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-slate-300 hover:text-white transition">
                        Log In
                    </a>
                    <a href="{{ route('register') }}" class="btn-primary text-sm py-2 px-4">
                        Register
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="flex items-center gap-2 md:hidden">
                <a href="{{ $discordUrl }}" target="_blank" rel="noopener noreferrer" class="p-2 rounded-lg bg-[#5865F2] text-white">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.929 1.793 8.18 1.793 12.061 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.894.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.028zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                    </svg>
                </a>
                <button @click="open = !open"
                        class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div x-show="open" x-transition class="md:hidden border-t border-slate-800 bg-[#070A12]/98 px-4 pt-3 pb-6 space-y-2">
        <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('home') ? 'bg-purple-600/20 text-purple-300' : 'text-slate-300 hover:bg-slate-800' }}">
            Home
        </a>
        <a href="{{ route('news.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('news.*') ? 'bg-purple-600/20 text-purple-300' : 'text-slate-300 hover:bg-slate-800' }}">
            News & Updates
        </a>
        <a href="{{ route('server-info') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('server-info') ? 'bg-purple-600/20 text-purple-300' : 'text-slate-300 hover:bg-slate-800' }}">
            Server Info
        </a>
        <a href="{{ route('rules') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('rules') ? 'bg-purple-600/20 text-purple-300' : 'text-slate-300 hover:bg-slate-800' }}">
            Server Rules
        </a>
        <a href="{{ route('rankings') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('rankings') ? 'bg-purple-600/20 text-purple-300' : 'text-slate-300 hover:bg-slate-800' }}">
            Rankings
        </a>

        <div class="pt-4 border-t border-slate-800">
            @auth
                <div class="px-3 py-2 mb-2 bg-slate-900 rounded-xl">
                    <p class="font-bold text-white text-sm">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-purple-400 capitalize">{{ Auth::user()->role }}</p>
                </div>

                @if(Auth::user()->isStaff())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm text-purple-400 hover:bg-purple-950/30 font-semibold">
                        Admin Dashboard
                    </a>
                @endif
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">
                    Account Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm text-rose-400 hover:bg-rose-950/20">
                        Log Out
                    </button>
                </form>
            @else
                <div class="grid grid-cols-2 gap-2 pt-2">
                    <a href="{{ route('login') }}" class="btn-secondary text-center text-sm py-2">Log In</a>
                    <a href="{{ route('register') }}" class="btn-primary text-center text-sm py-2">Register</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
