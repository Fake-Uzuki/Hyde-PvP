@extends('layouts.main')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12" x-data="{ activeTab: 'players' }">

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
        <div class="inline-flex p-1.5 rounded-2xl bg-slate-900 border border-slate-800 shadow-inner mt-4">
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
    <div x-show="activeTab === 'players'" class="space-y-6">
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
                                <tr class="hover:bg-purple-950/20 transition-colors">
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
                                    <td class="py-4 px-6 font-mono font-bold text-white flex items-center gap-2">
                                        <div class="w-7 h-7 rounded bg-slate-800 border border-slate-700 flex items-center justify-center text-[10px] text-cyan-300">
                                            ⚔
                                        </div>
                                        {{ $player->gamertag }}
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

</div>
@endsection
