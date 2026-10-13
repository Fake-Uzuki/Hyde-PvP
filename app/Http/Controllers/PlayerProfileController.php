<?php

namespace App\Http\Controllers;

use App\Models\PlayerRanking;
use App\Services\PlayerProfileService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlayerProfileController extends Controller
{
    public function __construct(
        protected PlayerProfileService $profileService
    ) {}

    /**
     * Display the Player Directory and Search portal.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        if ($search !== '') {
            // Clean up search query for gamertag route
            $cleanTag = preg_replace('/[^a-zA-Z0-9_ ]/', '', $search);
            if ($cleanTag !== '') {
                return redirect()->route('players.show', ['gamertag' => $cleanTag]);
            }
        }

        $topPlayers = PlayerRanking::orderBy('rank', 'asc')
            ->take(12)
            ->get();

        $totalRanked = PlayerRanking::count();

        return view('players.index', [
            'topPlayers' => $topPlayers,
            'totalRanked' => $totalRanked,
            'search' => $search,
        ]);
    }

    /**
     * Display an individual Public Player Profile.
     */
    public function show(string $gamertag): View
    {
        $decodedTag = urldecode($gamertag);
        $profile = $this->profileService->getProfileData($decodedTag);

        // Fetch top rivals around the same ranking for comparison
        $nearbyPlayers = collect();
        if ($profile && $profile['rank']) {
            $nearbyPlayers = PlayerRanking::whereBetween('rank', [
                max(1, $profile['rank'] - 2),
                $profile['rank'] + 2,
            ])
            ->where('gamertag', '!=', $profile['gamertag'])
            ->orderBy('rank', 'asc')
            ->get();
        }

        return view('players.show', [
            'gamertag' => $decodedTag,
            'profile' => $profile,
            'nearbyPlayers' => $nearbyPlayers,
        ]);
    }
}
