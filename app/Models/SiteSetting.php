<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function defaults(): array
    {
        return [
            'server_address' => env('BEDROCK_SERVER_ADDRESS', 'play.hydepvp.net'),
            'server_port' => env('BEDROCK_SERVER_PORT', '19132'),
            'server_version' => env('BEDROCK_SERVER_VERSION', 'v1.21.x (Bedrock)'),
            'discord_invite_url' => env('DISCORD_INVITE_URL', 'https://discord.gg/hydepvp'),
            'discord_server_id' => env('DISCORD_SERVER_ID', ''),
            'comment_word_limit' => '500',
            'comment_cooldown_seconds' => '60',
        ];
    }

    public static function get(string $key, $default = null): ?string
    {
        $all = self::getAll();
        if (array_key_exists($key, $all)) {
            return $all[$key];
        }

        $defaults = self::defaults();
        return $defaults[$key] ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('hyde_site_settings');
    }

    public static function getAll(): array
    {
        return Cache::remember('hyde_site_settings', 3600, function () {
            $settings = self::defaults();
            try {
                $dbSettings = self::pluck('value', 'key')->toArray();
                return array_merge($settings, $dbSettings);
            } catch (\Throwable $e) {
                return $settings;
            }
        });
    }
}
