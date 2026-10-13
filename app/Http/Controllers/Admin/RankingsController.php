<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClanRanking;
use App\Models\PlayerRanking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RankingsController extends Controller
{
    public function index(): View
    {
        $players = PlayerRanking::orderBy('rank')->get();
        $clans = ClanRanking::orderBy('rank')->get();

        return view('admin.rankings.index', compact('players', 'clans'));
    }

    public function storePlayer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'gamertag' => ['required', 'string', 'max:50'],
            'rank' => ['required', 'integer', 'min:1'],
            'rating' => ['required', 'integer', 'min:0'],
            'wins' => ['required', 'integer', 'min:0'],
            'losses' => ['required', 'integer', 'min:0'],
            'kills' => ['required', 'integer', 'min:0'],
            'deaths' => ['required', 'integer', 'min:0'],
            'clan' => ['nullable', 'string', 'max:50'],
        ]);

        PlayerRanking::create($validated);

        return redirect()->route('admin.rankings.index')->with('status', 'Player ranking entry created.');
    }

    public function updatePlayer(Request $request, PlayerRanking $player): RedirectResponse
    {
        $validated = $request->validate([
            'gamertag' => ['required', 'string', 'max:50'],
            'rank' => ['required', 'integer', 'min:1'],
            'rating' => ['required', 'integer', 'min:0'],
            'wins' => ['required', 'integer', 'min:0'],
            'losses' => ['required', 'integer', 'min:0'],
            'kills' => ['required', 'integer', 'min:0'],
            'deaths' => ['required', 'integer', 'min:0'],
            'clan' => ['nullable', 'string', 'max:50'],
        ]);

        $player->update($validated);

        return redirect()->route('admin.rankings.index')->with('status', 'Player ranking entry updated.');
    }

    public function destroyPlayer(PlayerRanking $player): RedirectResponse
    {
        $player->delete();

        return redirect()->route('admin.rankings.index')->with('status', 'Player ranking entry removed.');
    }

    public function storeClan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'tag' => ['required', 'string', 'max:10'],
            'rank' => ['required', 'integer', 'min:1'],
            'leader' => ['required', 'string', 'max:50'],
            'score' => ['required', 'integer', 'min:0'],
            'wins' => ['required', 'integer', 'min:0'],
            'members_count' => ['required', 'integer', 'min:1'],
        ]);

        ClanRanking::create($validated);

        return redirect()->route('admin.rankings.index')->with('status', 'Clan ranking entry created.');
    }

    public function updateClan(Request $request, ClanRanking $clan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'tag' => ['required', 'string', 'max:10'],
            'rank' => ['required', 'integer', 'min:1'],
            'leader' => ['required', 'string', 'max:50'],
            'score' => ['required', 'integer', 'min:0'],
            'wins' => ['required', 'integer', 'min:0'],
            'members_count' => ['required', 'integer', 'min:1'],
        ]);

        $clan->update($validated);

        return redirect()->route('admin.rankings.index')->with('status', 'Clan ranking entry updated.');
    }

    public function destroyClan(ClanRanking $clan): RedirectResponse
    {
        $clan->delete();

        return redirect()->route('admin.rankings.index')->with('status', 'Clan ranking entry removed.');
    }
}
