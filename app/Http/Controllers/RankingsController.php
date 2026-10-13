<?php

namespace App\Http\Controllers;

use App\Models\ClanRanking;
use App\Models\PlayerRanking;
use App\Models\SiteSetting;
use App\Models\UhcWinner;
use Illuminate\View\View;

class RankingsController extends Controller
{
    /**
     * Display Player, Clan, and UHC Tournament rankings.
     */
    public function index(): View
    {
        $players = PlayerRanking::orderBy('rank', 'asc')->get();
        $clans = ClanRanking::orderBy('rank', 'asc')->get();
        $uhcWinners = UhcWinner::orderBy('event_date', 'desc')->get();
        $discordUrl = SiteSetting::get('discord_invite_url', 'https://discord.gg/VYp7Khvf');

        return view('rankings', compact('players', 'clans', 'uhcWinners', 'discordUrl'));
    }
}
