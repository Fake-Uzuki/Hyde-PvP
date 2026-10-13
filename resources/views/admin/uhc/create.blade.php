@extends('admin.layout')

@section('title', 'Post New UHC Tournament Winner')
@section('header', 'Post UHC Champion')

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.uhc.index') }}" class="text-xs text-purple-400 hover:text-purple-300 font-semibold flex items-center gap-1">
            ← Back to UHC Winners
        </a>
    </div>

    <div class="gaming-card p-6 sm:p-8 space-y-6">
        <div>
            <h2 class="text-xl font-bold text-white font-gaming flex items-center gap-2">
                <span>🏆</span> Publish Tournament Champion
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Enter tournament details. The champion's avatar and combat profile will be automatically linked on the leaderboards.
            </p>
        </div>

        <form action="{{ route('admin.uhc.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Tournament Title -->
            <div>
                <label for="title" class="block text-xs uppercase font-bold text-slate-300 mb-1">Tournament Title *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="e.g. HYDE Bedrock UHC Season 1 — Solos"
                       class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-purple-500" />
                @error('title') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Winner Gamertag -->
                <div>
                    <label for="winner_gamertag" class="block text-xs uppercase font-bold text-amber-400 mb-1">🥇 Champion Gamertag *</label>
                    <input type="text" name="winner_gamertag" id="winner_gamertag" value="{{ old('winner_gamertag') }}" required placeholder="e.g. Uzuki"
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-3 text-sm text-amber-300 font-mono focus:outline-none focus:border-amber-500" />
                    @error('winner_gamertag') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Winner Kills -->
                <div>
                    <label for="winner_kills" class="block text-xs uppercase font-bold text-slate-300 mb-1">Champion Total Kills *</label>
                    <input type="number" name="winner_kills" id="winner_kills" value="{{ old('winner_kills', 0) }}" min="0" required
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-3 text-sm text-emerald-400 font-mono focus:outline-none focus:border-purple-500" />
                    @error('winner_kills') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Runner-Up Gamertag -->
                <div>
                    <label for="runner_up_gamertag" class="block text-xs uppercase font-bold text-slate-300 mb-1">🥈 Runner-Up Gamertag (Optional)</label>
                    <input type="text" name="runner_up_gamertag" id="runner_up_gamertag" value="{{ old('runner_up_gamertag') }}" placeholder="e.g. VortexKing"
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 font-mono focus:outline-none focus:border-purple-500" />
                    @error('runner_up_gamertag') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Event Date -->
                <div>
                    <label for="event_date" class="block text-xs uppercase font-bold text-slate-300 mb-1">Tournament Date *</label>
                    <input type="date" name="event_date" id="event_date" value="{{ old('event_date', date('Y-m-d')) }}" required
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-purple-500" />
                    @error('event_date') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Game Mode -->
                <div>
                    <label for="game_mode" class="block text-xs uppercase font-bold text-slate-300 mb-1">Tournament Format / Mode *</label>
                    <select name="game_mode" id="game_mode" class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-purple-500">
                        <option value="Solo UHC" {{ old('game_mode') === 'Solo UHC' ? 'selected' : '' }}>Solo UHC</option>
                        <option value="Duo UHC" {{ old('game_mode') === 'Duo UHC' ? 'selected' : '' }}>Duo UHC</option>
                        <option value="Squad UHC (4v4)" {{ old('game_mode') === 'Squad UHC (4v4)' ? 'selected' : '' }}>Squad UHC (4v4)</option>
                        <option value="NoClean Blitz UHC" {{ old('game_mode') === 'NoClean Blitz UHC' ? 'selected' : '' }}>NoClean Blitz UHC</option>
                        <option value="Custom Rules UHC" {{ old('game_mode') === 'Custom Rules UHC' ? 'selected' : '' }}>Custom Rules UHC</option>
                    </select>
                    @error('game_mode') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Prize / Rewards -->
                <div>
                    <label for="prize" class="block text-xs uppercase font-bold text-slate-300 mb-1">Prize / Reward Awarded</label>
                    <input type="text" name="prize" id="prize" value="{{ old('prize') }}" placeholder="e.g. VIP Rank + Discord Champion Role"
                           class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:border-purple-500" />
                    @error('prize') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Video / Clip Recap URL -->
            <div>
                <label for="recap_url" class="block text-xs uppercase font-bold text-slate-300 mb-1">Highlight / Stream Clip URL (Optional)</label>
                <input type="url" name="recap_url" id="recap_url" value="{{ old('recap_url') }}" placeholder="https://youtube.com/watch?v=... or Twitch VOD"
                       class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:border-purple-500" />
                @error('recap_url') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <a href="{{ route('admin.uhc.index') }}" class="btn-secondary text-xs py-3 px-5">
                    Cancel
                </a>
                <button type="submit" class="btn-primary text-xs py-3 px-6">
                    Publish UHC Winner
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
