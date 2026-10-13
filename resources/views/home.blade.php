@extends('layouts.main')

@section('content')
<div class="space-y-24 pb-16">

    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-12 pb-20 md:pt-20 md:pb-32">
        <!-- Glowing gradient backdrop -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-gradient-to-tr from-purple-600/20 via-fuchsia-600/15 to-cyan-500/10 blur-[130px] rounded-full pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-center max-w-3xl mx-auto space-y-6">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-purple-950/60 border border-purple-500/30 text-purple-300 text-xs font-semibold tracking-wide uppercase shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Minecraft Bedrock PvP Community
                </div>

                <!-- Logo & Heading -->
                <div class="flex flex-col items-center">
                    <div class="logo-lighting-wrap cursor-pointer mb-5 group">
                        <img src="{{ asset('images/hyde-pvp-logo.png') }}" alt="HYDE PVP" class="logo-lighting-glow h-32 sm:h-44 w-auto object-contain rounded-2xl" />
                    </div>
                    <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-white font-gaming">
                        DOMINATE THE <span class="gaming-gradient-text">ARENA</span>
                    </h1>
                </div>

                <p class="text-slate-300 text-base sm:text-lg leading-relaxed max-w-2xl mx-auto">
                    Welcome to <strong class="text-white">HYDE PVP</strong>, the competitive hub for Minecraft Bedrock combat. Experience custom knockback tuning, duel ladders, clan rivalries, and low-latency matchmaking.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                    <a href="{{ $settings['discord_invite_url'] ?? 'https://discord.gg/VYp7Khvf' }}"
                       target="_blank" rel="noopener noreferrer"
                       class="btn-discord text-base py-3.5 px-8">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.929 1.793 8.18 1.793 12.061 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.894.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.028zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                        </svg>
                        Join Discord
                    </a>

                    <a href="{{ route('server-info') }}" class="btn-secondary text-base py-3.5 px-8">
                        Server Info
                    </a>

                    <a href="{{ route('rules') }}" class="btn-secondary text-base py-3.5 px-6">
                        Server Rules
                    </a>
                </div>

                <!-- Server Quick Connect Card with Live Player Counter -->
                <div class="pt-8 max-w-2xl mx-auto">
                    <div class="gaming-card p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4 border border-purple-500/20 shadow-lg shadow-purple-950/20">
                        <div class="flex items-center gap-3.5 text-left">
                            <div class="w-11 h-11 rounded-xl bg-purple-900/40 border border-purple-500/40 flex items-center justify-center text-purple-400 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">
                                        Minecraft Bedrock
                                    </span>
                                    <span class="text-xs px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-mono">
                                        Port: {{ $settings['server_port'] ?? '19015' }}
                                    </span>

                                    <!-- Live Player Counter Badge -->
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold font-mono {{ ($serverStatus['online'] ?? false) ? 'bg-emerald-950/80 text-emerald-300 border border-emerald-500/40' : 'bg-purple-950/60 text-purple-300 border border-purple-700/40' }}"
                                         title="Live player count on hydepvp.ultramc.co:19015">
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ ($serverStatus['online'] ?? false) ? 'bg-emerald-400 opacity-75' : 'bg-purple-400 opacity-50' }}"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 {{ ($serverStatus['online'] ?? false) ? 'bg-emerald-500' : 'bg-purple-500' }}"></span>
                                        </span>
                                        <span>
                                            @if(($serverStatus['online'] ?? false))
                                                {{ $serverStatus['players_online'] }} / {{ $serverStatus['players_max'] }} Players Online
                                            @else
                                                {{ $serverStatus['players_online'] ?? 0 }} Players Online
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="text-lg font-mono font-bold text-cyan-300 select-all">
                                    {{ $settings['server_address'] ?? 'hydepvp.ultramc.co' }}
                                </div>
                            </div>
                        </div>

                        <button type="button"
                                onclick="copyServerAddress(this, '{{ $settings['server_address'] ?? 'hydepvp.ultramc.co' }}')"
                                class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs tracking-wide uppercase transition active:scale-95 shadow-md shadow-purple-600/30 flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                            </svg>
                            <span>Copy Address</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($reigningUhcWinner)
        <!-- Reigning UHC Champion Showcase -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-950/40 via-purple-950/30 to-slate-900 border border-amber-500/30 p-6 sm:p-8 shadow-xl shadow-amber-950/10">
                <div class="absolute -right-8 -top-8 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                        <div class="relative">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-slate-900 border-2 border-amber-400/80 overflow-hidden shadow-lg shadow-amber-500/20 flex-shrink-0">
                                <img src="{{ $reigningUhcWinner->winner_avatar_url }}"
                                     alt="{{ $reigningUhcWinner->winner_gamertag }}"
                                     onerror="this.style.display='none'"
                                     class="w-full h-full object-cover pixelated" />
                            </div>
                            <span class="absolute -top-2 -right-2 text-xl" title="Reigning Champion">👑</span>
                        </div>

                        <div class="space-y-1">
                            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 text-[11px] font-bold uppercase tracking-wider">
                                <span>🏆</span> Reigning UHC Tournament Champion
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-black text-white font-gaming">
                                <a href="{{ route('players.show', $reigningUhcWinner->winner_gamertag) }}" class="hover:text-amber-300 transition">
                                    {{ $reigningUhcWinner->winner_gamertag }}
                                </a>
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-300">
                                Victor of <strong class="text-white">{{ $reigningUhcWinner->title }}</strong> ({{ $reigningUhcWinner->game_mode }})
                                @if($reigningUhcWinner->winner_kills !== null)
                                    • <span class="text-rose-400 font-mono font-bold">{{ $reigningUhcWinner->winner_kills }} Kills</span>
                                @endif
                                @if($reigningUhcWinner->prize)
                                    • <span class="text-emerald-400 font-medium">Prize: {{ $reigningUhcWinner->prize }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 w-full lg:w-auto">
                        <a href="{{ route('rankings') }}?tab=uhc"
                           class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-400 hover:to-yellow-500 text-slate-950 font-bold text-xs uppercase tracking-wider transition text-center shadow-md shadow-amber-500/20">
                            Hall of Champions →
                        </a>
                        <a href="{{ route('players.show', $reigningUhcWinner->winner_gamertag) }}"
                           class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-900/80 hover:bg-slate-800 text-slate-200 border border-slate-700 font-semibold text-xs transition text-center">
                            Profile
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Latest News Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white font-gaming">
                    COMMUNITY <span class="gaming-gradient-text">NEWS</span>
                </h2>
                <p class="text-slate-400 text-sm mt-1">Updates, patch notes, and announcements.</p>
            </div>
            <a href="{{ route('news.index') }}" class="text-sm font-semibold text-purple-400 hover:text-purple-300 flex items-center gap-1 group">
                View All Posts
                <span class="transition-transform group-hover:translate-x-1">→</span>
            </a>
        </div>

        @if($latestNews->isEmpty())
            <div class="gaming-card p-12 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                <p class="text-base font-semibold text-slate-300">No announcements published yet.</p>
                <p class="text-xs text-slate-500 mt-1">Check back soon for patch notes and server updates!</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($latestNews as $post)
                    <article class="gaming-card p-6 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
                                <span class="px-2 py-0.5 rounded bg-purple-900/30 text-purple-300 border border-purple-800/40 font-medium text-[11px]">
                                    By {{ $post->user->name ?? 'Staff' }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-white hover:text-purple-300 transition line-clamp-2">
                                <a href="{{ route('news.show', $post->slug) }}">{{ $post->title }}</a>
                            </h3>
                            <p class="text-slate-400 text-sm line-clamp-3 leading-relaxed">
                                {{ $post->excerpt ?: Str::limit(strip_tags($post->body), 120) }}
                            </p>
                        </div>
                        <div class="pt-6">
                            <a href="{{ route('news.show', $post->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-purple-400 hover:text-purple-300 group">
                                Read Full Post
                                <span class="transition-transform group-hover:translate-x-1">→</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Rankings Preview (Real records only) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white font-gaming">
                    TOP <span class="gaming-gradient-text">RANKINGS</span>
                </h2>
                <p class="text-slate-400 text-sm mt-1">Official verified season leaders.</p>
            </div>
            <a href="{{ route('rankings') }}" class="text-sm font-semibold text-purple-400 hover:text-purple-300 flex items-center gap-1 group">
                Full Leaderboards
                <span class="transition-transform group-hover:translate-x-1">→</span>
            </a>
        </div>

        @if($topPlayers->isEmpty() && $topClans->isEmpty())
            <div class="gaming-card p-10 text-center">
                <div class="w-12 h-12 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center mx-auto text-slate-500 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-200">Current Season Leaderboards Opening Soon</h3>
                <p class="text-xs text-slate-400 max-w-md mx-auto mt-1 leading-relaxed">
                    Official rankings are recorded by authorized administrators after verified tournament and ranked match seasons. Join our Discord for tournament registrations.
                </p>
                <div class="mt-4">
                    <a href="{{ $settings['discord_invite_url'] ?? 'https://discord.gg/hydepvp' }}" target="_blank" rel="noopener noreferrer" class="btn-discord text-xs py-2 px-4 inline-flex">
                        Compete on Discord
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Players Preview -->
                <div class="gaming-card p-6">
                    <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                        Top Players
                    </h3>
                    <div class="space-y-2">
                        @foreach($topPlayers as $player)
                            <a href="{{ route('players.show', $player->gamertag) }}"
                               class="flex items-center justify-between p-3 rounded-xl bg-slate-900/60 hover:bg-purple-950/30 border border-slate-800 hover:border-purple-500/40 text-sm transition group">
                                <div class="flex items-center gap-3">
                                    <span class="font-extrabold text-xs text-purple-400 w-6 group-hover:text-amber-400">#{{ $player->rank }}</span>
                                    <div class="w-6 h-6 rounded-md bg-slate-800 overflow-hidden flex-shrink-0">
                                        <img src="https://mc-heads.net/avatar/{{ rawurlencode($player->gamertag) }}/24"
                                             alt="{{ $player->gamertag }}"
                                             onerror="this.style.display='none'"
                                             class="w-full h-full object-cover pixelated" />
                                    </div>
                                    <span class="font-semibold text-white font-mono group-hover:text-purple-300 transition">{{ $player->gamertag }}</span>
                                </div>
                                <span class="text-cyan-400 font-mono text-xs font-bold">{{ $player->rating }} ELO</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Clans Preview -->
                <div class="gaming-card p-6">
                    <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-fuchsia-500"></span>
                        Top Clans
                    </h3>
                    <div class="space-y-2">
                        @foreach($topClans as $clan)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900/60 border border-slate-800 text-sm">
                                <div class="flex items-center gap-3">
                                    <span class="font-extrabold text-xs text-fuchsia-400 w-6">#{{ $clan->rank }}</span>
                                    <span class="font-semibold text-white">[{{ $clan->tag }}] {{ $clan->name }}</span>
                                </div>
                                <span class="text-fuchsia-300 font-mono text-xs font-bold">{{ $clan->score }} Pts</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </section>

    <!-- Discord Community CTA Banner -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl p-8 sm:p-12 bg-gradient-to-r from-purple-950/60 via-[#10192D] to-indigo-950/60 border border-purple-500/30 shadow-2xl">
            <div class="relative z-10 max-w-2xl space-y-4">
                <span class="text-xs uppercase tracking-widest text-purple-400 font-bold">Never Miss a Battle</span>
                <h2 class="text-3xl sm:text-4xl font-black text-white font-gaming">
                    JOIN THE OFFICIAL <span class="gaming-gradient-text">DISCORD</span>
                </h2>
                <p class="text-slate-300 text-sm leading-relaxed">
                    All clan wars, UHC tournaments, bracket announcements, and player matchmaking take place on Discord. Get roles, organize scrims, and interact with the staff team.
                </p>
                <div class="pt-2">
                    <a href="{{ $settings['discord_invite_url'] ?? 'https://discord.gg/VYp7Khvf' }}"
                       target="_blank" rel="noopener noreferrer"
                       class="btn-discord text-base py-3 px-8">
                        Join Discord Server
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
