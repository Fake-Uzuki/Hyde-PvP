<?php

namespace App\Http\Controllers;

use App\Models\ClanRanking;
use App\Models\NewsPost;
use App\Models\PlayerRanking;
use App\Models\SiteSetting;
use App\Models\UhcWinner;
use App\Services\BedrockServerStatusService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        protected BedrockServerStatusService $statusService
    ) {}

    /**
     * Display the HYDE PVP homepage.
     */
    public function index(): View
    {
        $settings = SiteSetting::getAll();
        $serverStatus = $this->statusService->getStatus();
        $reigningUhcWinner = UhcWinner::orderBy('event_date', 'desc')->first();

        $latestNews = NewsPost::published()
            ->with('user')
            ->latest('published_at')
            ->take(3)
            ->get();

        $topPlayers = PlayerRanking::orderBy('rank', 'asc')
            ->take(5)
            ->get();

        $topClans = ClanRanking::orderBy('rank', 'asc')
            ->take(5)
            ->get();

        return view('home', compact('settings', 'latestNews', 'topPlayers', 'topClans', 'serverStatus', 'reigningUhcWinner'));
    }
}
