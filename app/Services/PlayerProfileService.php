<?php

namespace App\Services;

use App\Models\PlayerRanking;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class PlayerProfileService
{
    /**
     * Tiers configuration based on ELO ratings.
     */
    public const TIERS = [
        'Grandmaster' => [
            'min' => 2000,
            'icon' => '👑',
            'gradient' => 'from-fuchsia-500 via-purple-500 to-cyan-400',
            'text_color' => 'text-fuchsia-300',
            'bg_color' => 'bg-fuchsia-950/40',
            'border_color' => 'border-fuchsia-500/40',
            'glow' => 'shadow-[0_0_25px_rgba(217,70,239,0.35)]',
            'description' => 'The apex of competitive Minecraft Bedrock PvP.',
        ],
        'Master' => [
            'min' => 1800,
            'icon' => '💎',
            'gradient' => 'from-purple-500 to-indigo-500',
            'text_color' => 'text-purple-300',
            'bg_color' => 'bg-purple-950/40',
            'border_color' => 'border-purple-500/40',
            'glow' => 'shadow-[0_0_20px_rgba(168,85,247,0.3)]',
            'description' => 'Elite warrior dominating tournament duels.',
        ],
        'Diamond' => [
            'min' => 1600,
            'icon' => '💠',
            'gradient' => 'from-cyan-400 to-blue-600',
            'text_color' => 'text-cyan-300',
            'bg_color' => 'bg-cyan-950/40',
            'border_color' => 'border-cyan-500/40',
            'glow' => 'shadow-[0_0_20px_rgba(6,182,212,0.3)]',
            'description' => 'Seasoned combat veteran with exceptional mechanics.',
        ],
        'Platinum' => [
            'min' => 1400,
            'icon' => '⚔️',
            'gradient' => 'from-emerald-400 to-teal-600',
            'text_color' => 'text-emerald-300',
            'bg_color' => 'bg-emerald-950/40',
            'border_color' => 'border-emerald-500/40',
            'glow' => 'shadow-[0_0_20px_rgba(16,185,129,0.25)]',
            'description' => 'Skilled combatant with high mechanical precision.',
        ],
        'Gold' => [
            'min' => 1200,
            'icon' => '🏆',
            'gradient' => 'from-amber-400 to-yellow-600',
            'text_color' => 'text-amber-300',
            'bg_color' => 'bg-amber-950/40',
            'border_color' => 'border-amber-500/40',
            'glow' => 'shadow-[0_0_15px_rgba(245,158,11,0.2)]',
            'description' => 'Formidable fighter climbing the ranked ladders.',
        ],
        'Silver' => [
            'min' => 1000,
            'icon' => '🛡️',
            'gradient' => 'from-slate-300 to-slate-500',
            'text_color' => 'text-slate-200',
            'bg_color' => 'bg-slate-800/40',
            'border_color' => 'border-slate-600/40',
            'glow' => 'shadow-none',
            'description' => 'Active combatant proving their battle worth.',
        ],
        'Bronze' => [
            'min' => 0,
            'icon' => '🥉',
            'gradient' => 'from-amber-700 to-orange-900',
            'text_color' => 'text-orange-300',
            'bg_color' => 'bg-orange-950/30',
            'border_color' => 'border-orange-800/40',
            'glow' => 'shadow-none',
            'description' => 'Aspiring warrior beginning their arena journey.',
        ],
    ];

    /**
     * Determine the player's tier metadata.
     */
    public function getTier(int $rating): array
    {
        foreach (self::TIERS as $name => $meta) {
            if ($rating >= $meta['min']) {
                $meta['name'] = $name;
                $meta['progress'] = $this->calculateTierProgress($rating, $name);
                return $meta;
            }
        }

        $fallback = self::TIERS['Bronze'];
        $fallback['name'] = 'Bronze';
        $fallback['progress'] = 0;
        return $fallback;
    }

    /**
     * Calculate progress toward the next tier (0% - 100%).
     */
    protected function calculateTierProgress(int $rating, string $currentTier): int
    {
        $tiersKeys = array_keys(self::TIERS);
        $currentIndex = array_search($currentTier, $tiersKeys, true);

        // Grandmaster is top tier
        if ($currentIndex === 0) {
            return 100;
        }

        $nextTier = self::TIERS[$tiersKeys[$currentIndex - 1]];
        $currentMin = self::TIERS[$currentTier]['min'];
        $nextMin = $nextTier['min'];

        $range = $nextMin - $currentMin;
        if ($range <= 0) {
            return 100;
        }

        $progress = (($rating - $currentMin) / $range) * 100;
        return (int) min(100, max(0, round($progress)));
    }

    /**
     * Get avatar head image URL for the player.
     */
    public function getAvatarUrl(string $gamertag, int $size = 128): string
    {
        return PlayerAvatarService::getAvatarUrl($gamertag, $size);
    }

    /**
     * Generate fallback initials SVG avatar when offline or rendering fails.
     */
    public function getInitialsAvatar(string $gamertag): string
    {
        $char = strtoupper(substr(trim($gamertag), 0, 1) ?: 'H');
        return "data:image/svg+xml;utf8," . rawurlencode(
            "<svg xmlns='http://www.w3.org/2000/svg' width='128' height='128' viewBox='0 0 128 128'>" .
            "<rect width='128' height='128' rx='24' fill='#1E1B4B'/>" .
            "<text x='50%' y='55%' font-family='sans-serif' font-size='56' font-weight='bold' fill='#C084FC' text-anchor='middle' dominant-baseline='middle'>{$char}</text>" .
            "</svg>"
        );
    }

    /**
     * Fetch complete player profile data with caching.
     */
    public function getProfileData(string $gamertag): ?array
    {
        $cacheKey = 'hyde_player_profile_' . strtolower(str_replace(' ', '_', trim($gamertag)));

        return Cache::remember($cacheKey, 300, function () use ($gamertag) {
            // Find player ranking record (case-insensitive)
            $ranking = PlayerRanking::whereRaw('LOWER(gamertag) = ?', [strtolower(trim($gamertag))])->first();

            // Find linked website user account
            $user = User::whereRaw('LOWER(minecraft_gamertag) = ?', [strtolower(trim($gamertag))])
                ->orWhereRaw('LOWER(name) = ?', [strtolower(trim($gamertag))])
                ->first();

            if (! $ranking && ! $user) {
                return null;
            }

            $rating = $ranking ? $ranking->rating : 1000;
            $wins = $ranking ? $ranking->wins : 0;
            $losses = $ranking ? $ranking->losses : 0;
            $totalMatches = $wins + $losses;
            $winRate = $totalMatches > 0 ? round(($wins / $totalMatches) * 100, 1) : 0.0;
            $kills = $ranking ? $ranking->kills : 0;
            $deaths = $ranking ? $ranking->deaths : 0;
            $kd = $ranking ? $ranking->kd_ratio : 0.0;
            $clan = $ranking ? $ranking->clan : null;
            $rank = $ranking ? $ranking->rank : null;

            $tier = $this->getTier($rating);

            // Estimated Kit masteries based on rating variance
            $kitStats = [
                'Nodebuff (PotPvP)' => [
                    'rating' => $rating,
                    'win_rate' => $winRate,
                    'icon' => '🧪',
                    'style' => 'text-purple-400',
                ],
                'Boxing' => [
                    'rating' => max(800, $rating - 25),
                    'win_rate' => max(0, min(100, round($winRate * 0.96, 1))),
                    'icon' => '🥊',
                    'style' => 'text-amber-400',
                ],
                'Sumo' => [
                    'rating' => max(800, $rating + 15),
                    'win_rate' => max(0, min(100, round($winRate * 1.02, 1))),
                    'icon' => '🥋',
                    'style' => 'text-cyan-400',
                ],
                'Gapple' => [
                    'rating' => max(800, $rating - 10),
                    'win_rate' => max(0, min(100, round($winRate * 0.98, 1))),
                    'icon' => '🍏',
                    'style' => 'text-yellow-400',
                ],
                'Combo' => [
                    'rating' => max(800, $rating + 35),
                    'win_rate' => max(0, min(100, round($winRate * 1.04, 1))),
                    'icon' => '⚡',
                    'style' => 'text-fuchsia-400',
                ],
            ];

            return [
                'gamertag' => $ranking ? $ranking->gamertag : ($user->minecraft_gamertag ?: $user->name),
                'avatar_url' => $this->getAvatarUrl($ranking ? $ranking->gamertag : ($user->minecraft_gamertag ?: $user->name)),
                'fallback_avatar' => $this->getInitialsAvatar($ranking ? $ranking->gamertag : ($user->minecraft_gamertag ?: $user->name)),
                'rank' => $rank,
                'rating' => $rating,
                'tier' => $tier,
                'wins' => $wins,
                'losses' => $losses,
                'total_matches' => $totalMatches,
                'win_rate' => $winRate,
                'kills' => $kills,
                'deaths' => $deaths,
                'kd_ratio' => $kd,
                'clan' => $clan,
                'kit_stats' => $kitStats,
                'is_verified' => $user !== null,
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'joined_at' => $user->created_at->format('M Y'),
                    'comments_count' => $user->comments()->count(),
                ] : null,
            ];
        });
    }

    /**
     * Clear cached profile data for a gamertag.
     */
    public function forget(string $gamertag): void
    {
        $cacheKey = 'hyde_player_profile_' . strtolower(str_replace(' ', '_', trim($gamertag)));
        Cache::forget($cacheKey);
    }
}
