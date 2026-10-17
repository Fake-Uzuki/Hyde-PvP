<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerRanking;
use App\Services\PlayerProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ServerRankingsController extends Controller
{
    public function __construct(
        protected PlayerProfileService $profileService
    ) {}

    /**
     * Authenticate requests originating from the Minecraft Bedrock server.
     */
    protected function authenticateServer(Request $request): bool
    {
        $expectedKey = config('services.hyde.server_key', env('HYDE_SERVER_KEY', 'hyde-pvp-secret-key-19015'));
        $providedKey = $request->header('X-Server-Key') ?? $request->input('server_key');

        return $providedKey && hash_equals($expectedKey, (string) $providedKey);
    }

    /**
     * Ingest batch player rankings & scoreboard scores from the Minecraft Bedrock server.
     * Supports both list format [ { gamertag, kills, points, ... } ]
     * and dictionary format { "PlayerName": { kills: 10, points: 50 } }.
     */
    public function sync(Request $request): JsonResponse
    {
        if (! $this->authenticateServer($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid or missing server key.',
            ], 401);
        }

        $rawPlayers = $request->input('players');
        $rawScores = $request->input('scores');

        $normalized = [];

        // 1. Normalize list format
        if (is_array($rawPlayers)) {
            foreach ($rawPlayers as $p) {
                if (!empty($p['gamertag'])) {
                    $normalized[trim($p['gamertag'])] = [
                        'kills' => (int) ($p['kills'] ?? 0),
                        'deaths' => (int) ($p['deaths'] ?? 0),
                        'points' => (int) ($p['points'] ?? $p['rating'] ?? $p['score'] ?? 0),
                        'wins' => (int) ($p['wins'] ?? 0),
                        'losses' => (int) ($p['losses'] ?? 0),
                        'clan' => !empty($p['clan']) ? trim($p['clan']) : null,
                    ];
                }
            }
        }

        // 2. Normalize dictionary format { "Gamertag": { kills, points } }
        if (is_array($rawScores)) {
            foreach ($rawScores as $gamertag => $stats) {
                if (is_string($gamertag) && !empty(trim($gamertag)) && is_array($stats)) {
                    $gt = trim($gamertag);
                    $normalized[$gt] = [
                        'kills' => (int) ($stats['kills'] ?? $normalized[$gt]['kills'] ?? 0),
                        'deaths' => (int) ($stats['deaths'] ?? $normalized[$gt]['deaths'] ?? 0),
                        'points' => (int) ($stats['points'] ?? $stats['rating'] ?? $stats['score'] ?? $normalized[$gt]['points'] ?? 0),
                        'wins' => (int) ($stats['wins'] ?? $normalized[$gt]['wins'] ?? 0),
                        'losses' => (int) ($stats['losses'] ?? $normalized[$gt]['losses'] ?? 0),
                        'clan' => !empty($stats['clan']) ? trim($stats['clan']) : ($normalized[$gt]['clan'] ?? null),
                    ];
                }
            }
        }

        if (empty($normalized)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid player stats provided in request body.',
            ], 422);
        }

        DB::transaction(function () use ($normalized) {
            foreach ($normalized as $gamertag => $stats) {
                $player = PlayerRanking::where('gamertag', $gamertag)->first();

                if ($player) {
                    $player->update([
                        'rating' => isset($stats['points']) ? (int) $stats['points'] : $player->rating,
                        'kills' => isset($stats['kills']) ? (int) $stats['kills'] : $player->kills,
                        'deaths' => isset($stats['deaths']) ? (int) $stats['deaths'] : $player->deaths,
                        'wins' => isset($stats['wins']) ? (int) $stats['wins'] : $player->wins,
                        'losses' => isset($stats['losses']) ? (int) $stats['losses'] : $player->losses,
                        'clan' => $stats['clan'] ?? $player->clan,
                    ]);
                } else {
                    PlayerRanking::create([
                        'gamertag' => $gamertag,
                        'rank' => 9999, // Will be recalculated below
                        'rating' => $stats['points'],
                        'kills' => $stats['kills'],
                        'deaths' => $stats['deaths'],
                        'wins' => $stats['wins'],
                        'losses' => $stats['losses'],
                        'clan' => $stats['clan'],
                    ]);
                }

                $this->profileService->forget($gamertag);
            }

            // Recalculate official ranks based on Rating / Points descending, then Kills descending
            $allPlayers = PlayerRanking::orderByDesc('rating')
                ->orderByDesc('kills')
                ->orderBy('deaths')
                ->get();

            $currentRank = 1;
            foreach ($allPlayers as $p) {
                if ($p->rank !== $currentRank) {
                    $p->update(['rank' => $currentRank]);
                }
                $currentRank++;
            }
        });

        Cache::forget('hyde_rankings_top5');

        return response()->json([
            'success' => true,
            'message' => 'Successfully synchronized ' . count($normalized) . ' player ranking(s) from in-game scoreboard.',
            'players_synced' => count($normalized),
        ]);
    }

    /**
     * Ingest live individual score update when a player gets a kill/point in-game.
     */
    public function updatePlayer(Request $request): JsonResponse
    {
        if (! $this->authenticateServer($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid or missing server key.',
            ], 401);
        }

        $validated = $request->validate([
            'gamertag' => ['required', 'string', 'max:50'],
            'kills' => ['nullable', 'integer', 'min:0'],
            'deaths' => ['nullable', 'integer', 'min:0'],
            'points' => ['nullable', 'integer', 'min:0'],
            'wins' => ['nullable', 'integer', 'min:0'],
            'losses' => ['nullable', 'integer', 'min:0'],
            'clan' => ['nullable', 'string', 'max:50'],
        ]);

        $gamertag = trim($validated['gamertag']);

        DB::transaction(function () use ($gamertag, $validated) {
            $player = PlayerRanking::firstOrNew(['gamertag' => $gamertag]);

            if (isset($validated['kills'])) {
                $player->kills = $validated['kills'];
            }
            if (isset($validated['deaths'])) {
                $player->deaths = $validated['deaths'];
            }
            if (isset($validated['points'])) {
                $player->rating = $validated['points'];
            }
            if (isset($validated['wins'])) {
                $player->wins = $validated['wins'];
            }
            if (isset($validated['losses'])) {
                $player->losses = $validated['losses'];
            }
            if (!empty($validated['clan'])) {
                $player->clan = trim($validated['clan']);
            }

            if (! $player->exists) {
                $player->rank = 9999;
            }

            $player->save();
            $this->profileService->forget($gamertag);

            // Recalculate ranks
            $allPlayers = PlayerRanking::orderByDesc('rating')
                ->orderByDesc('kills')
                ->orderBy('deaths')
                ->get();

            $currentRank = 1;
            foreach ($allPlayers as $p) {
                if ($p->rank !== $currentRank) {
                    $p->update(['rank' => $currentRank]);
                }
                $currentRank++;
            }
        });

        Cache::forget('hyde_rankings_top5');

        return response()->json([
            'success' => true,
            'message' => "Stats updated and leaderboards recalculated for {$gamertag}!",
        ]);
    }
}
