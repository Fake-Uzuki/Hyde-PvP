@extends('admin.layout', ['title' => 'Manage Rankings'])

@section('admin_content')
<div class="space-y-8" x-data="{ tab: 'players', addPlayer: false, addClan: false }">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white font-gaming">Season Rankings Management</h2>
            <p class="text-xs text-slate-400">Manage verified player and clan leaderboard statistics.</p>
        </div>

        <!-- Tab Switcher -->
        <div class="inline-flex p-1 rounded-xl bg-slate-900 border border-slate-800 self-start">
            <button type="button" @click="tab = 'players'"
                    :class="tab === 'players' ? 'bg-purple-600 text-white' : 'text-slate-400 hover:text-white'"
                    class="px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-wider transition">
                Players ({{ $players->count() }})
            </button>
            <button type="button" @click="tab = 'clans'"
                    :class="tab === 'clans' ? 'bg-purple-600 text-white' : 'text-slate-400 hover:text-white'"
                    class="px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-wider transition">
                Clans ({{ $clans->count() }})
            </button>
        </div>
    </div>

    <!-- Players Tab -->
    <div x-show="tab === 'players'" class="space-y-6">
        <div class="flex justify-end">
            <button type="button" @click="addPlayer = !addPlayer" class="btn-primary text-xs py-2 px-4">
                <span x-text="addPlayer ? 'Close Form' : '+ Add Player Record'">+ Add Player Record</span>
            </button>
        </div>

        <!-- Add Player Form -->
        <div x-show="addPlayer" x-transition class="gaming-card p-6 border-purple-500/40">
            <h3 class="text-base font-bold text-white mb-4">Add Player Ranking</h3>
            <form method="POST" action="{{ route('admin.rankings.players.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Gamertag</label>
                        <input type="text" name="gamertag" required placeholder="Xbox Gamertag"
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white" />
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Rank (#)</label>
                        <input type="number" name="rank" required min="1" value="1"
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white" />
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Rating (ELO)</label>
                        <input type="number" name="rating" required value="1000"
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white" />
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Clan (Optional)</label>
                        <input type="text" name="clan" placeholder="e.g. HYDE"
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white" />
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Wins</label>
                        <input type="number" name="wins" required value="0" min="0"
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white" />
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Losses</label>
                        <input type="number" name="losses" required value="0" min="0"
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white" />
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Kills</label>
                        <input type="number" name="kills" required value="0" min="0"
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white" />
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Deaths</label>
                        <input type="number" name="deaths" required value="0" min="0"
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white" />
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="addPlayer = false" class="btn-secondary text-xs py-2 px-4">Cancel</button>
                    <button type="submit" class="btn-primary text-xs py-2 px-5">Save Player</button>
                </div>
            </form>
        </div>

        <!-- Players List -->
        @if($players->isEmpty())
            <div class="gaming-card p-12 text-center text-slate-500">
                No player records found. Add verified season records using the button above.
            </div>
        @else
            <div class="gaming-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="py-3 px-5">Rank</th>
                                <th class="py-3 px-5">Gamertag</th>
                                <th class="py-3 px-5">Clan</th>
                                <th class="py-3 px-5 text-right">Rating</th>
                                <th class="py-3 px-5 text-right">W / L</th>
                                <th class="py-3 px-5 text-right">K / D</th>
                                <th class="py-3 px-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            @foreach($players as $player)
                                <tr class="hover:bg-slate-900/40">
                                    <td class="py-3 px-5 font-mono font-bold text-purple-400">#{{ $player->rank }}</td>
                                    <td class="py-3 px-5 font-bold text-white font-mono">{{ $player->gamertag }}</td>
                                    <td class="py-3 px-5 text-xs text-slate-400">{{ $player->clan ?: '—' }}</td>
                                    <td class="py-3 px-5 text-right font-mono font-bold text-cyan-300">{{ $player->rating }}</td>
                                    <td class="py-3 px-5 text-right font-mono text-xs">{{ $player->wins }}W / {{ $player->losses }}L</td>
                                    <td class="py-3 px-5 text-right font-mono text-xs">{{ $player->kd_ratio }}</td>
                                    <td class="py-3 px-5 text-right">
                                        <form method="POST" action="{{ route('admin.rankings.players.destroy', $player->id) }}" onsubmit="return confirm('Delete this player record?')" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 px-2 py-1 rounded bg-rose-950/40 border border-rose-800/40">
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

    <!-- Clans Tab -->
    <div x-show="tab === 'clans'" class="space-y-6" style="display: none;">
        <div class="flex justify-end">
            <button type="button" @click="addClan = !addClan" class="btn-primary text-xs py-2 px-4">
                <span x-text="addClan ? 'Close Form' : '+ Add Clan Record'">+ Add Clan Record</span>
            </button>
        </div>

        <!-- Add Clan Form -->
        <div x-show="addClan" x-transition class="gaming-card p-6 border-fuchsia-500/40">
            <h3 class="text-base font-bold text-white mb-4">Add Clan Ranking</h3>
            <form method="POST" action="{{ route('admin.rankings.clans.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Clan Name</label>
                        <input type="text" name="name" required placeholder="e.g. Hyder Squad"
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white" />
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Tag (Max 10)</label>
                        <input type="text" name="tag" required placeholder="HYDE" maxlength="10"
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white font-mono" />
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Rank (#)</label>
                        <input type="number" name="rank" required min="1" value="1"
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white" />
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Clan Leader</label>
                        <input type="text" name="leader" required placeholder="Leader Gamertag"
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white" />
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Score / Points</label>
                        <input type="number" name="score" required value="0" min="0"
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white" />
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-300 mb-1">War Wins</label>
                        <input type="number" name="wins" required value="0" min="0"
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white" />
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-300 mb-1">Members Count</label>
                        <input type="number" name="members_count" required value="1" min="1"
                               class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-3 py-2 text-sm text-white" />
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="addClan = false" class="btn-secondary text-xs py-2 px-4">Cancel</button>
                    <button type="submit" class="btn-primary text-xs py-2 px-5">Save Clan</button>
                </div>
            </form>
        </div>

        <!-- Clans List -->
        @if($clans->isEmpty())
            <div class="gaming-card p-12 text-center text-slate-500">
                No clan records found. Add verified season records using the button above.
            </div>
        @else
            <div class="gaming-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="py-3 px-5">Rank</th>
                                <th class="py-3 px-5">Clan</th>
                                <th class="py-3 px-5">Leader</th>
                                <th class="py-3 px-5 text-right">Score</th>
                                <th class="py-3 px-5 text-right">Wins</th>
                                <th class="py-3 px-5 text-right">Roster</th>
                                <th class="py-3 px-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            @foreach($clans as $clan)
                                <tr class="hover:bg-slate-900/40">
                                    <td class="py-3 px-5 font-mono font-bold text-fuchsia-400">#{{ $clan->rank }}</td>
                                    <td class="py-3 px-5 font-bold text-white">[{{ $clan->tag }}] {{ $clan->name }}</td>
                                    <td class="py-3 px-5 text-xs text-slate-300 font-mono">{{ $clan->leader }}</td>
                                    <td class="py-3 px-5 text-right font-mono font-bold text-fuchsia-300">{{ $clan->score }}</td>
                                    <td class="py-3 px-5 text-right font-mono text-xs text-emerald-400">{{ $clan->wins }}</td>
                                    <td class="py-3 px-5 text-right font-mono text-xs">{{ $clan->members_count }}</td>
                                    <td class="py-3 px-5 text-right">
                                        <form method="POST" action="{{ route('admin.rankings.clans.destroy', $clan->id) }}" onsubmit="return confirm('Delete this clan record?')" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 px-2 py-1 rounded bg-rose-950/40 border border-rose-800/40">
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

</div>
@endsection
