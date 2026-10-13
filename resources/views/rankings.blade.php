@extends('layouts.main')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12" x-data="{ activeTab: new URLSearchParams(window.location.search).get('tab') || 'players' }">

    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto space-y-4">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-fuchsia-950/60 border border-fuchsia-500/30 text-fuchsia-300 text-xs font-semibold tracking-wide uppercase">
            Official Leaderboards
        </div>
        <h1 class="text-4xl sm:text-5xl font-black text-white font-gaming">
            SEASON <span class="gaming-gradient-text">RANKINGS</span>
        </h1>
        <p class="text-slate-300 text-base leading-relaxed">
            Verified competitive statistics for HYDE PVP tournament seasons and rated duel ladders.
        </p>

        <!-- Tab Controls -->
        <div class="inline-flex p-1.5 rounded-2xl bg-slate-900 border border-slate-800 shadow-inner mt-4 flex-wrap justify-center gap-1">
            <button
                type="button"
                @click="activeTab = 'players'"
                :class="activeTab === 'players' ? 'bg-gradient-to-r from-purple-600 to-fuchsia-600 text-white shadow-md' : 'text-slate-400 hover:text-white'"
                class="px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all duration-200">
                Top Players
            </button>
            <button
                type="button"
                @click="activeTab = 'clans'"
                :class="activeTab === 'clans' ? 'bg-gradient-to-r from-purple-600 to-fuchsia-600 text-white shadow-md' : 'text-slate-400 hover:text-white'"
                class="px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all duration-200">
                Top Clans
            </button>
            <button
                type="button"
                @click="activeTab = 'uhc'"
                :class="activeTab === 'uhc' ? 'bg-gradient-to-r from-purple-600 to-fuchsia-600 text-white shadow-md' : 'text-slate-400 hover:text-white'"
                class="px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all duration-200">
                🏆 UHC Champions
            </button>
        </div>
    </div>

    <!-- Notice Box -->
    <div class="max-w-4xl mx-auto p-4 rounded-xl bg-purple-950/20 border border-purple-900/40 text-xs text-purple-300 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2.5">
            <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Rankings are audited and maintained by tournament administrators at the end of each competitive rotation.</span>
        </div>
        <a href="{{ $discordUrl }}" target="_blank" rel="noopener noreferrer" class="underline text-purple-400 hover:text-purple-300 shrink-0 font-medium">
            Join Tournaments
        </a>
    </div>

    <!-- Players Rankings Tab -->
    <div x-show="activeTab === 'players'" x-data="{ playerSearch: '' }" class="space-y-6">
        @if($players->isEmpty())
            <div class="gaming-card p-16 text-center max-w-2xl mx-auto space-y-4">
                <div class="w-16 h-16 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center mx-auto text-slate-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white font-gaming">No Player Ranking Records Yet</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Official rankings will appear once the active competitive season results are registered by tournament staff.
                </p>
                <div class="pt-2">
                    <a href="{{ $discordUrl }}" target="_blank" rel="noopener noreferrer" class="btn-discord text-xs py-2 px-5 inline-flex">
                        Compete in Next Tournament
                    </a>
                </div>
            </div>
        @else
            <!-- Search & Filter Controls -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-400">
                    Showing <span class="text-white font-bold">{{ $players->count() }}</span> ranked contenders • Click any gamertag to view full combat analytics
                </div>
                <div class="relative w-full sm:w-72">
                    <input type="text"
                           x-model="playerSearch"
                           placeholder="Filter gamertag or clan..."
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl pl-9 pr-4 py-2 text-xs text-white placeholder-slate-500 font-mono focus:outline-none focus:border-purple-500 transition" />
                    <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="gaming-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="py-4 px-6 font-bold">Rank</th>
                                <th class="py-4 px-6 font-bold">Gamertag</th>
                                <th class="py-4 px-6 font-bold">Clan</th>
                                <th class="py-4 px-6 font-bold text-right">Rating (ELO)</th>
                                <th class="py-4 px-6 font-bold text-right">W / L</th>
                                <th class="py-4 px-6 font-bold text-right">K / D</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            @foreach($players as $player)
                                <tr x-show="!playerSearch || '{{ strtolower($player->gamertag) }} {{ strtolower($player->clan ?? '') }}'.includes(playerSearch.toLowerCase())"
                                    class="hover:bg-purple-950/20 transition-colors">
                                    <td class="py-4 px-6 font-extrabold text-sm font-gaming">
                                        @if($player->rank === 1)
                                            <span class="inline-flex items-center gap-1 text-amber-400 font-black">
                                                ★ 1
                                            </span>
                                        @elseif($player->rank === 2)
                                            <span class="inline-flex items-center gap-1 text-slate-300 font-bold">
                                                ★ 2
                                            </span>
                                        @elseif($player->rank === 3)
                                            <span class="inline-flex items-center gap-1 text-amber-600 font-bold">
                                                ★ 3
                                            </span>
                                        @else
                                            <span class="text-slate-400">#{{ $player->rank }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 font-mono font-bold text-white">
                                        <a href="{{ route('players.show', $player->gamertag) }}"
                                           class="inline-flex items-center gap-2.5 hover:text-purple-300 transition group"
                                           title="View {{ $player->gamertag }}'s combat profile">
                                            <div class="w-7 h-7 rounded-lg bg-slate-800 border border-slate-700/80 overflow-hidden flex items-center justify-center text-[10px] text-cyan-300 shrink-0">
                                                <img src="https://mc-heads.net/avatar/{{ rawurlencode($player->gamertag) }}/28"
                                                     alt="{{ $player->gamertag }}"
                                                     onerror="this.style.display='none'"
                                                     class="w-full h-full object-cover pixelated" />
                                            </div>
                                            <span class="group-hover:underline">{{ $player->gamertag }}</span>
                                        </a>
                                    </td>
                                    <td class="py-4 px-6 text-xs text-purple-400 font-medium">
                                        {{ $player->clan ?: '—' }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-mono font-bold text-cyan-300">
                                        {{ $player->rating }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-mono text-xs text-slate-300">
                                        <span class="text-emerald-400">{{ $player->wins }}W</span> / <span class="text-rose-400">{{ $player->losses }}L</span>
                                    </td>
                                    <td class="py-4 px-6 text-right font-mono text-xs text-slate-300">
                                        {{ $player->kd_ratio }} ({{ $player->kills }}k / {{ $player->deaths }}d)
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Clan Rankings Tab -->
    <div x-show="activeTab === 'clans'" class="space-y-6" style="display: none;">
        @if($clans->isEmpty())
            <div class="gaming-card p-16 text-center max-w-2xl mx-auto space-y-4">
                <div class="w-16 h-16 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center mx-auto text-slate-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white font-gaming">No Clan Ranking Records Yet</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Official clan leaderboards are compiled from registered clan wars and tournaments on Discord.
                </p>
                <div class="pt-2">
                    <a href="{{ $discordUrl }}" target="_blank" rel="noopener noreferrer" class="btn-discord text-xs py-2 px-5 inline-flex">
                        Register Your Clan on Discord
                    </a>
                </div>
            </div>
        @else
            <div class="gaming-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="py-4 px-6 font-bold">Rank</th>
                                <th class="py-4 px-6 font-bold">Clan Name</th>
                                <th class="py-4 px-6 font-bold">Leader</th>
                                <th class="py-4 px-6 font-bold text-right">Score</th>
                                <th class="py-4 px-6 font-bold text-right">War Wins</th>
                                <th class="py-4 px-6 font-bold text-right">Roster</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            @foreach($clans as $clan)
                                <tr class="hover:bg-fuchsia-950/20 transition-colors">
                                    <td class="py-4 px-6 font-extrabold text-sm font-gaming">
                                        @if($clan->rank === 1)
                                            <span class="text-amber-400 font-black">★ 1</span>
                                        @elseif($clan->rank === 2)
                                            <span class="text-slate-300 font-bold">★ 2</span>
                                        @elseif($clan->rank === 3)
                                            <span class="text-amber-600 font-bold">★ 3</span>
                                        @else
                                            <span class="text-slate-400">#{{ $clan->rank }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 font-bold text-white">
                                        <span class="text-fuchsia-400 font-mono mr-1">[{{ $clan->tag }}]</span>
                                        {{ $clan->name }}
                                    </td>
                                    <td class="py-4 px-6 font-mono text-xs text-slate-300">
                                        {{ $clan->leader }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-mono font-bold text-fuchsia-300">
                                        {{ $clan->score }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-mono text-xs text-emerald-400 font-semibold">
                                        {{ $clan->wins }} Wins
                                    </td>
                                    <td class="py-4 px-6 text-right font-mono text-xs text-slate-400">
                                        {{ $clan->members_count }} Players
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- UHC Tournament Champions Tab -->
    <div x-show="activeTab === 'uhc'" class="space-y-6" style="display: none;">
        @if(auth()->user()?->isAdmin())
            <div class="flex justify-end">
                <a href="{{ route('admin.uhc.create') }}" class="btn-primary text-xs py-2 px-4 inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Post New UHC Winner (Admin)
                </a>
            </div>
        @endif

        @if($uhcWinners->isEmpty())
            <div class="gaming-card p-16 text-center max-w-2xl mx-auto space-y-4">
                <div class="w-16 h-16 rounded-full bg-amber-500/10 border border-amber-500/30 flex items-center justify-center mx-auto text-amber-400">
                    <span class="text-3xl">🏆</span>
                </div>
                <h3 class="text-xl font-bold text-white font-gaming">No UHC Tournament Champions Recorded Yet</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Official tournament champions will be enshrined in this hall of fame following verified HYDE PVP UHC events.
                </p>
                <div class="pt-2">
                    <a href="{{ $discordUrl }}" target="_blank" rel="noopener noreferrer" class="btn-discord text-xs py-2 px-5 inline-flex">
                        Join Discord for Tournament Announcements
                    </a>
                </div>
            </div>
        @else
            <!-- Tournament Winner Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($uhcWinners as $winner)
                    <div class="gaming-card p-6 flex flex-col justify-between relative overflow-hidden group hover:border-amber-500/40 transition duration-300">
                        <!-- Top Accent Glow for Reigning/First Winner -->
                        @if($loop->first)
                            <div class="absolute -top-12 -right-12 w-28 h-28 bg-amber-500/15 rounded-full blur-2xl pointer-events-none"></div>
                        @endif

                        <div class="space-y-4">
                            <!-- Tournament Header & Mode -->
                            <div class="flex items-start justify-between gap-2 border-b border-slate-800/80 pb-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-base">🏆</span>
                                        <span class="text-xs uppercase font-mono tracking-wider font-bold text-amber-400">
                                            {{ $winner->game_mode }}
                                        </span>
                                        @if($loop->first)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 uppercase">
                                                Reigning
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-base font-bold text-white mt-1 group-hover:text-amber-300 transition">
                                        {{ $winner->title }}
                                    </h3>
                                </div>
                                <span class="text-[11px] font-mono text-slate-400 shrink-0">
                                    {{ $winner->event_date->format('M d, Y') }}
                                </span>
                            </div>

                            <!-- Champion Box -->
                            <div class="p-4 rounded-xl bg-gradient-to-r from-amber-950/20 to-purple-950/20 border border-amber-500/30 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-900 border border-amber-500/50 overflow-hidden flex-shrink-0 shadow-md">
                                        <img src="{{ $winner->winner_avatar_url }}"
                                             alt="{{ $winner->winner_gamertag }}"
                                             onerror="this.style.display='none'"
                                             class="w-full h-full object-cover pixelated" />
                                    </div>
                                    <div>
                                        <span class="text-[10px] uppercase font-bold tracking-wider text-amber-400 flex items-center gap-1">
                                            <span>👑</span> 1st Place Champion
                                        </span>
                                        <a href="{{ route('players.show', $winner->winner_gamertag) }}"
                                           class="font-mono font-black text-white hover:text-amber-300 transition text-base block">
                                            {{ $winner->winner_gamertag }}
                                        </a>
                                    </div>
                                </div>

                                @if($winner->winner_kills !== null)
                                    <div class="text-right">
                                        <span class="text-[10px] uppercase text-slate-400 block font-semibold">Kills</span>
                                        <span class="font-mono font-bold text-rose-400 text-sm">
                                            ⚔️ {{ $winner->winner_kills }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Runner-Up (if exists) -->
                            @if($winner->runner_up_gamertag)
                                <div class="p-3 rounded-lg bg-slate-900/60 border border-slate-800 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-slate-800 border border-slate-700 overflow-hidden shrink-0">
                                            <img src="{{ $winner->runner_up_avatar_url }}"
                                                 alt="{{ $winner->runner_up_gamertag }}"
                                                 onerror="this.style.display='none'"
                                                 class="w-full h-full object-cover pixelated" />
                                        </div>
                                        <div>
                                            <span class="text-[10px] uppercase text-slate-400 font-bold block">🥈 2nd Place</span>
                                            <a href="{{ route('players.show', $winner->runner_up_gamertag) }}"
                                               class="font-mono font-bold text-slate-200 hover:text-purple-300 transition">
                                                {{ $winner->runner_up_gamertag }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Prize & Extra details -->
                            @if($winner->prize)
                                <div class="text-xs text-slate-300 flex items-center gap-1.5 font-medium">
                                    <span class="text-emerald-400">💰 Prize:</span>
                                    <span>{{ $winner->prize }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Footer Actions / VOD Link -->
                        <div class="pt-4 mt-4 border-t border-slate-800/80 flex items-center justify-between text-xs">
                            <a href="{{ route('players.show', $winner->winner_gamertag) }}" class="text-purple-400 hover:text-purple-300 font-semibold inline-flex items-center gap-1 group/link">
                                <span>Champion Profile</span>
                                <span class="transition-transform group-hover/link:translate-x-0.5">→</span>
                            </a>

                            @if($winner->recap_url)
                                <a href="{{ $winner->recap_url }}" target="_blank" rel="noopener noreferrer" class="text-cyan-400 hover:text-cyan-300 font-mono inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Recap / VOD</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
