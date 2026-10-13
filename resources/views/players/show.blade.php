@extends('layouts.main')

@section('title', ($profile ? $profile['gamertag'] . ' — Player Profile' : 'Player Not Found') . ' | HYDE PVP')
@section('meta_description', 'View Minecraft Bedrock PvP statistics, ELO rating, duels win rate, and combat tier for ' . ($profile['gamertag'] ?? $gamertag) . ' on HYDE PVP.')

@section('content')
<div class="space-y-10">

    <!-- Top Navigation / Search Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <a href="{{ route('rankings') }}"
           class="inline-flex items-center gap-2 text-xs font-semibold text-purple-400 hover:text-purple-300 transition group">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Leaderboards
        </a>

        <!-- Fast Player Search Bar -->
        <form action="{{ route('players.index') }}" method="GET" class="w-full sm:w-auto">
            <div class="relative">
                <input type="text"
                       name="search"
                       placeholder="Search Bedrock Gamertag..."
                       value="{{ $profile ? '' : $gamertag }}"
                       class="w-full sm:w-72 bg-slate-900/90 border border-slate-800 rounded-xl pl-9 pr-4 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 transition font-mono" />
                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </form>
    </div>

    @if($profile)
        <!-- ========================================== -->
        <!-- FOUND PLAYER PROFILE CARD                  -->
        <!-- ========================================== -->
        <div class="gaming-card relative overflow-hidden p-6 sm:p-10 border border-slate-800 {{ $profile['tier']['glow'] }}">
            <!-- Background Radial Ambient Glow -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-gradient-to-br {{ $profile['tier']['gradient'] }} opacity-15 blur-3xl pointer-events-none rounded-full"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 pb-8 border-b border-slate-800">
                <!-- Avatar & Identity -->
                <div class="flex items-center gap-5">
                    <div class="relative group">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-slate-900 border-2 {{ $profile['tier']['border_color'] }} p-1 shadow-2xl overflow-hidden flex items-center justify-center">
                            <img src="{{ $profile['avatar_url'] }}"
                                 alt="{{ $profile['gamertag'] }}"
                                 onerror="this.onerror=null; this.src='{{ $profile['fallback_avatar'] }}';"
                                 class="w-full h-full object-cover rounded-xl pixelated transform transition-transform group-hover:scale-110" />
                        </div>
                        <div class="absolute -bottom-2 -right-2 text-xl" title="{{ $profile['tier']['name'] }} Tier">
                            {{ $profile['tier']['icon'] }}
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl sm:text-4xl font-extrabold text-white font-gaming tracking-wide">
                                {{ $profile['gamertag'] }}
                            </h1>

                            @if($profile['is_verified'])
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-950/80 text-cyan-300 border border-cyan-500/40" title="Linked to HYDE PVP Community Account">
                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Verified Player
                                </span>
                            @endif

                            @if($profile['user'] && $profile['user']['role'] !== 'member')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-900/60 text-purple-300 border border-purple-500/40">
                                    {{ $profile['user']['role'] }}
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400">
                            @if($profile['clan'])
                                <div class="flex items-center gap-1">
                                    <span class="text-slate-500">Clan:</span>
                                    <span class="font-bold text-purple-300 font-gaming">[{{ $profile['clan'] }}]</span>
                                </div>
                                <span class="text-slate-600">•</span>
                            @endif

                            <div class="flex items-center gap-1">
                                <span class="text-slate-500">Edition:</span>
                                <span class="text-slate-300 font-semibold">Minecraft Bedrock</span>
                            </div>

                            @if($profile['rank'])
                                <span class="text-slate-600">•</span>
                                <div class="flex items-center gap-1">
                                    <span class="text-slate-500">Season Standing:</span>
                                    <span class="font-extrabold text-amber-400 font-gaming">#{{ $profile['rank'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tier Badge & Share Actions -->
                <div class="flex flex-col sm:flex-row md:flex-col items-start sm:items-center md:items-end gap-3 w-full sm:w-auto">
                    <div class="px-4 py-2 rounded-xl {{ $profile['tier']['bg_color'] }} border {{ $profile['tier']['border_color'] }} flex items-center gap-2.5">
                        <span class="text-2xl">{{ $profile['tier']['icon'] }}</span>
                        <div class="text-left md:text-right">
                            <div class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Combat Tier</div>
                            <div class="text-sm font-extrabold font-gaming {{ $profile['tier']['text_color'] }}">
                                {{ $profile['tier']['name'] }}
                            </div>
                        </div>
                    </div>

                    <!-- Copy Profile URL button -->
                    <button type="button"
                            onclick="navigator.clipboard.writeText(window.location.href); this.innerText='URL Copied!'; setTimeout(() => this.innerText='Share Profile', 2000)"
                            class="px-4 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-[11px] font-semibold text-slate-300 hover:text-white transition active:scale-95 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                        Share Profile
                    </button>
                </div>
            </div>

            <!-- ELO Milestone Progress Bar -->
            <div class="pt-6 space-y-2">
                <div class="flex items-center justify-between text-xs font-semibold">
                    <span class="text-slate-400 flex items-center gap-1.5">
                        <span>Current Rating:</span>
                        <span class="text-white font-mono font-bold">{{ number_format($profile['rating']) }} ELO</span>
                    </span>
                    <span class="text-purple-300">
                        {{ $profile['tier']['name'] }} Tier Progress ({{ $profile['tier']['progress'] }}%)
                    </span>
                </div>
                <div class="w-full h-2.5 rounded-full bg-slate-900 border border-slate-800 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r {{ $profile['tier']['gradient'] }} transition-all duration-500"
                         style="width: {{ $profile['tier']['progress'] }}%"></div>
                </div>
                <p class="text-[11px] text-slate-500 italic">
                    {{ $profile['tier']['description'] }}
                </p>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- CORE COMBAT MATRIX (4 CARDS)               -->
        <!-- ========================================== -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Global Rank -->
            <div class="gaming-card p-5 space-y-1 text-center">
                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-500 block">Server Ranking</span>
                <div class="text-2xl sm:text-3xl font-extrabold font-gaming text-amber-400">
                    {{ $profile['rank'] ? '#' . $profile['rank'] : 'Unranked' }}
                </div>
                <span class="text-[11px] text-slate-400">Season 1 Ladder</span>
            </div>

            <!-- ELO Rating -->
            <div class="gaming-card p-5 space-y-1 text-center">
                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-500 block">ELO Rating</span>
                <div class="text-2xl sm:text-3xl font-extrabold font-gaming text-cyan-300">
                    {{ number_format($profile['rating']) }}
                </div>
                <span class="text-[11px] text-purple-400 font-semibold">{{ $profile['tier']['name'] }} Tier</span>
            </div>

            <!-- Win Rate -->
            <div class="gaming-card p-5 space-y-1 text-center">
                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-500 block">Win Rate</span>
                <div class="text-2xl sm:text-3xl font-extrabold font-gaming text-emerald-400">
                    {{ $profile['win_rate'] }}%
                </div>
                <span class="text-[11px] text-slate-400">
                    {{ number_format($profile['wins']) }}W / {{ number_format($profile['losses']) }}L
                </span>
            </div>

            <!-- K/D Ratio -->
            <div class="gaming-card p-5 space-y-1 text-center">
                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-500 block">Kill / Death</span>
                <div class="text-2xl sm:text-3xl font-extrabold font-gaming text-fuchsia-400">
                    {{ number_format($profile['kd_ratio'], 2) }}
                </div>
                <span class="text-[11px] text-slate-400">
                    {{ number_format($profile['kills']) }} Kills • {{ number_format($profile['deaths']) }} Deaths
                </span>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- KIT BREAKDOWN & COMMUNITY ACTIVITY         -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kit PvP Performance Matrix (2 Cols) -->
            <div class="lg:col-span-2 gaming-card p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-white font-gaming flex items-center gap-2">
                            <span>⚔️</span> Duel Mode Masteries
                        </h2>
                        <p class="text-xs text-slate-400">Performance ratings across active Bedrock arena duel ladders.</p>
                    </div>
                    <span class="text-[10px] uppercase font-bold tracking-wider text-purple-400 bg-purple-950/40 px-2.5 py-1 rounded-lg border border-purple-800/40">
                        Competitive Ladders
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach($profile['kit_stats'] as $kitName => $kit)
                        <div class="p-3.5 rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-center justify-between hover:border-purple-500/30 transition">
                            <div class="flex items-center gap-3">
                                <span class="text-xl p-2 rounded-lg bg-slate-800 border border-slate-700/60">{{ $kit['icon'] }}</span>
                                <div>
                                    <div class="text-sm font-bold text-white">{{ $kitName }}</div>
                                    <div class="text-[11px] text-slate-500">Rated Arena Duel</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold font-mono {{ $kit['style'] }}">
                                    {{ number_format($kit['rating']) }} ELO
                                </div>
                                <div class="text-[11px] text-emerald-400 font-semibold">
                                    {{ $kit['win_rate'] }}% WR
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right Column: Verification & Rival Contenders -->
            <div class="space-y-6">
                <!-- Community Card -->
                <div class="gaming-card p-6 space-y-4">
                    <h3 class="text-sm font-bold text-white font-gaming flex items-center gap-2">
                        <span>🛡️</span> Community Status
                    </h3>

                    @if($profile['user'])
                        <div class="p-4 rounded-xl bg-purple-950/20 border border-purple-500/20 space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Portal User:</span>
                                <span class="font-bold text-white">{{ $profile['user']['name'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Site Role:</span>
                                <span class="font-bold text-purple-300 capitalize">{{ $profile['user']['role'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Member Since:</span>
                                <span class="text-slate-200">{{ $profile['user']['joined_at'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Comments:</span>
                                <span class="text-slate-200">{{ $profile['user']['comments_count'] }} submitted</span>
                            </div>
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-400 space-y-2">
                            <p>This gamertag has not been claimed yet on the website.</p>
                            @guest
                                <a href="{{ route('register') }}" class="text-purple-400 hover:text-purple-300 font-bold block">
                                    Is this your gamertag? Register now →
                                </a>
                            @else
                                <a href="{{ route('profile.edit') }}" class="text-purple-400 hover:text-purple-300 font-bold block">
                                    Link your Gamertag in profile settings →
                                </a>
                            @endguest
                        </div>
                    @endif
                </div>

                <!-- Ladder Neighbors / Rivals -->
                @if($nearbyPlayers->count() > 0)
                    <div class="gaming-card p-6 space-y-3">
                        <h3 class="text-sm font-bold text-white font-gaming flex items-center gap-2">
                            <span>⚡</span> Ladder Contenders
                        </h3>
                        <p class="text-[11px] text-slate-500">Players nearby in Season 1 ranking.</p>

                        <div class="space-y-2 pt-1">
                            @foreach($nearbyPlayers as $rival)
                                <a href="{{ route('players.show', $rival->gamertag) }}"
                                   class="p-2.5 rounded-xl bg-slate-900/90 border border-slate-800 hover:border-purple-500/40 flex items-center justify-between transition group">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-xs font-bold font-gaming text-slate-500 group-hover:text-amber-400">#{{ $rival->rank }}</span>
                                        <span class="text-xs font-mono font-bold text-white group-hover:text-purple-300">{{ $rival->gamertag }}</span>
                                    </div>
                                    <span class="text-xs font-mono font-bold text-cyan-300">{{ $rival->rating }} ELO</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

    @else
        <!-- ========================================== -->
        <!-- NOT FOUND STATE                            -->
        <!-- ========================================== -->
        <div class="gaming-card p-12 text-center max-w-2xl mx-auto space-y-6">
            <div class="w-20 h-20 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-4xl mx-auto shadow-inner text-slate-600">
                🔍
            </div>

            <div class="space-y-2">
                <h1 class="text-2xl font-extrabold text-white font-gaming">
                    Gamertag <span class="text-purple-400">"{{ $gamertag }}"</span> Not Found
                </h1>
                <p class="text-slate-400 text-sm leading-relaxed">
                    No active season combat records were found for this Minecraft: Bedrock gamertag yet.
                </p>
            </div>

            <!-- Search again -->
            <form action="{{ route('players.index') }}" method="GET" class="max-w-md mx-auto">
                <div class="flex gap-2">
                    <input type="text"
                           name="search"
                           placeholder="Try another Gamertag..."
                           class="flex-1 bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 transition font-mono" />
                    <button type="submit" class="btn-primary text-xs py-3 px-5">
                        Search
                    </button>
                </div>
            </form>

            <div class="pt-4 border-t border-slate-800">
                <a href="{{ route('rankings') }}" class="btn-secondary text-xs py-2.5 px-6 inline-block">
                    View Top Ranked Players
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
