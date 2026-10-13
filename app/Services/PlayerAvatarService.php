<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PlayerAvatarService
{
    /**
     * Resolve a player's head avatar URL.
     * If the player has a registered account with an uploaded custom skin,
     * returns their custom head icon; otherwise falls back to mc-heads.net.
     */
    public static function getAvatarUrl(?string $gamertag, int $size = 64): string
    {
        if (empty($gamertag)) {
            return "https://mc-heads.net/avatar/MHF_Steve/{$size}";
        }

        $trimmed = trim($gamertag);
        $cacheKey = 'player_avatar_url_' . strtolower($trimmed) . "_{$size}";

        return Cache::remember($cacheKey, 60, function () use ($trimmed, $size) {
            $user = User::whereRaw('LOWER(minecraft_gamertag) = ?', [strtolower($trimmed)])
                ->whereNotNull('avatar_head_path')
                ->first();

            if ($user && $user->avatar_head_path) {
                return Storage::url($user->avatar_head_path);
            }

            return "https://mc-heads.net/avatar/" . rawurlencode($trimmed) . "/{$size}";
        });
    }

    /**
     * Clear cached avatar URL for a given gamertag.
     */
    public static function clearCache(string $gamertag): void
    {
        Cache::forget('player_avatar_url_' . strtolower(trim($gamertag)) . '_24');
        Cache::forget('player_avatar_url_' . strtolower(trim($gamertag)) . '_28');
        Cache::forget('player_avatar_url_' . strtolower(trim($gamertag)) . '_48');
        Cache::forget('player_avatar_url_' . strtolower(trim($gamertag)) . '_64');
        Cache::forget('player_avatar_url_' . strtolower(trim($gamertag)) . '_96');
        Cache::forget('player_avatar_url_' . strtolower(trim($gamertag)) . '_128');
    }
}
