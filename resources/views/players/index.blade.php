@extends('layouts.main')

@section('title', 'Player Directory & Stats Lookup | HYDE PVP')
@section('meta_description', 'Search and lookup Minecraft: Bedrock Edition player statistics, ELO combat rankings, win rates, and duel profiles on HYDE PVP.')

@section('content')
<div class="space-y-12">

    <!-- Search Hero -->
    <div class="gaming-card relative overflow-hidden p-8 sm:p-12 text-center max-w-3xl mx-auto space-y-6">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide uppercase bg-purple-950/60 text-purple-300 border border-purple-800/40">
            <span>🔍</span> Combatant Directory
        </div>

        <h1 class="text-3xl sm:text-5xl font-extrabold text-white font-gaming tracking-wide">
            PLAYER <span class="gaming-gradient-text">PROFILES</span>
        </h1>

        <p class="text-slate-400 text-sm max-w-xl mx-auto leading-relaxed">
            Look up any Bedrock fighter to analyze their duel win rate, ELO tier status, K/D ratio, and competitive ladder standing.
        </p>

        <!-- Search Input -->
        <form action="{{ route('players.index') }}" method="GET" class="max-w-lg mx-auto pt-2">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Enter Bedrock Gamertag..."
                           autofocus
                           required
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl pl-11 pr-4 py-3.5 text-sm text-white placeholder-slate-500 font-mono focus:outline-none focus:border-purple-500 transition" />
                    <svg class="w-5 h-5 text-slate-500 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button type="submit" class="btn-primary text-xs py-3.5 px-8 font-gaming tracking-wider uppercase">
                    Lookup
                </button>
            </div>
        </form>

        <div class="flex items-center justify-center gap-4 text-xs text-slate-500 pt-2">
            <span>{{ number_format($totalRanked) }} Ranked Contenders</span>
            <span>•</span>
            <a href="{{ route('rankings') }}" class="text-purple-400 hover:text-purple-300 font-semibold">
                Browse Full Leaderboards →
            </a>
        </div>
    </div>

    <!-- Top Contenders Spotlight Grid -->
    @if($topPlayers->count() > 0)
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <h2 class="text-xl font-extrabold text-white font-gaming flex items-center gap-2">
                    <span>👑</span> Top Ranked Fighters
                </h2>
                <a href="{{ route('rankings') }}" class="text-xs font-bold text-purple-400 hover:text-purple-300">
                    View All Rankings →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($topPlayers as $player)
                    <a href="{{ route('players.show', $player->gamertag) }}"
                       class="gaming-card p-5 space-y-4 hover:border-purple-500/50 transition-all duration-200 group hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold font-gaming {{ $player->rank === 1 ? 'text-amber-400' : ($player->rank === 2 ? 'text-slate-300' : ($player->rank === 3 ? 'text-amber-600' : 'text-slate-500')) }}">
                                #{{ $player->rank }}
                            </span>
                            @if($player->clan)
                                <span class="text-[10px] font-bold text-purple-400 bg-purple-950/40 px-2 py-0.5 rounded border border-purple-800/30">
                                    [{{ $player->clan }}]
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-slate-900 border border-slate-700/60 p-0.5 overflow-hidden flex-shrink-0">
                                <img src="{{ \App\Services\PlayerAvatarService::getAvatarUrl($player->gamertag, 64) }}"
                                     alt="{{ $player->gamertag }}"
                                     onerror="this.style.display='none'"
                                     class="w-full h-full object-cover rounded-lg pixelated" />
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold font-mono text-white truncate group-hover:text-purple-300 transition">
                                    {{ $player->gamertag }}
                                </div>
                                <div class="text-xs font-mono font-bold text-cyan-300">
                                    {{ number_format($player->rating) }} ELO
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-800/80 text-center text-xs">
                            <div>
                                <span class="text-[10px] uppercase text-slate-500 block">Record</span>
                                <span class="text-slate-200 font-semibold">{{ $player->wins }}W / {{ $player->losses }}L</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase text-slate-500 block">K/D</span>
                                <span class="text-fuchsia-400 font-semibold">{{ number_format($player->kd_ratio, 2) }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
