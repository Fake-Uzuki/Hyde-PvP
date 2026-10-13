<?php

namespace App\Http\Controllers;

use App\Models\ClanRanking;
use App\Models\PlayerRanking;
use App\Models\SiteSetting;
use Illuminate\View\View;

class RankingsController extends Controller
{
    /**
     * Display Player and Clan rankings.
     */
    public function index(): View
    {
        $players = PlayerRanking::orderBy('rank', 'asc')->get();
        $clans = ClanRanking::orderBy('rank', 'asc')->get();
        $discordUrl = SiteSetting::get('discord_invite_url', 'https://discord.gg/hydepvp');

        return view('rankings', compact('players', 'clans', 'discordUrl'));
    }
}
