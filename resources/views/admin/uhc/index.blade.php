@extends('admin.layout')

@section('title', 'UHC Tournament Winners Management')
@section('header', 'UHC Tournament Champions')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white font-gaming flex items-center gap-2">
                <span>🏆</span> Tournament Hall of Fame
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Post and manage official HYDE Bedrock UHC champions and tournament records.</p>
        </div>

        <a href="{{ route('admin.uhc.create') }}" class="btn-primary text-xs py-2.5 px-4 inline-flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Post New UHC Winner
        </a>
    </div>

    @if($tournaments->isEmpty())
        <div class="gaming-card p-12 text-center text-slate-400 space-y-3">
            <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-3xl mx-auto text-amber-500">
                🏆
            </div>
            <h3 class="text-lg font-bold text-white font-gaming">No UHC Tournament Winners Posted Yet</h3>
            <p class="text-xs text-slate-400 max-w-md mx-auto">
                Post your first competitive UHC tournament champion. Once published, it will be visible on the public Leaderboards and featured on the homepage.
            </p>
            <div class="pt-2">
                <a href="{{ route('admin.uhc.create') }}" class="btn-primary text-xs py-2.5 px-5 inline-block">
                    Post Tournament Winner
                </a>
            </div>
        </div>
    @else
        <div class="gaming-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-4 px-6 font-bold">Tournament</th>
                            <th class="py-4 px-6 font-bold">Date & Mode</th>
                            <th class="py-4 px-6 font-bold">🥇 Champion</th>
                            <th class="py-4 px-6 font-bold">🥈 Runner-Up</th>
                            <th class="py-4 px-6 font-bold">Prize</th>
                            <th class="py-4 px-6 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @foreach($tournaments as $t)
                            <tr class="hover:bg-purple-950/20 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-white text-sm">{{ $t->title }}</div>
                                    @if($t->recap_url)
                                        <a href="{{ $t->recap_url }}" target="_blank" rel="noopener noreferrer" class="text-[11px] text-purple-400 hover:underline inline-flex items-center gap-1 mt-0.5">
                                            <span>Highlight VOD</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-300">
                                    <div class="font-mono text-slate-200">{{ $t->event_date->format('M d, Y') }}</div>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-purple-300 border border-slate-700/60">
                                        {{ $t->game_mode }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-slate-800 border border-amber-500/40 overflow-hidden flex-shrink-0">
                                            <img src="{{ $t->winner_avatar_url }}" alt="{{ $t->winner_gamertag }}" class="w-full h-full object-cover pixelated" />
                                        </div>
                                        <div>
                                            <a href="{{ route('players.show', $t->winner_gamertag) }}" class="font-mono font-bold text-amber-300 hover:underline">
                                                {{ $t->winner_gamertag }}
                                            </a>
                                            <div class="text-[11px] text-emerald-400 font-semibold">{{ $t->winner_kills }} Kills</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-xs">
                                    @if($t->runner_up_gamertag)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded bg-slate-800 border border-slate-700 overflow-hidden flex-shrink-0">
                                                <img src="{{ $t->runner_up_avatar_url }}" alt="{{ $t->runner_up_gamertag }}" class="w-full h-full object-cover pixelated" />
                                            </div>
                                            <a href="{{ route('players.show', $t->runner_up_gamertag) }}" class="font-mono text-slate-300 hover:text-white hover:underline">
                                                {{ $t->runner_up_gamertag }}
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-slate-500">—</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-300">
                                    {{ $t->prize ?: '—' }}
                                </td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <a href="{{ route('admin.uhc.edit', $t) }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.uhc.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Remove this UHC tournament record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-950/40 hover:bg-rose-900/60 border border-rose-800/40 text-xs font-semibold text-rose-300 transition">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
