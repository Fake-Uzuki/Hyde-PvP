<?php

namespace Tests\Feature;

use App\Models\PlayerRanking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_player_directory(): void
    {
        PlayerRanking::create([
            'gamertag' => 'VortexKing',
            'rank' => 1,
            'rating' => 2100,
            'wins' => 150,
            'losses' => 20,
            'kills' => 520,
            'deaths' => 110,
            'clan' => 'HYDE',
        ]);

        $response = $this->get('/players');

        $response->assertStatus(200);
        $response->assertSee('PLAYER');
        $response->assertSee('PROFILES');
        $response->assertSee('VortexKing');
        $response->assertSee('2,100 ELO');
    }

    public function test_search_redirects_to_player_profile(): void
    {
        $response = $this->get('/players?search=VortexKing');

        $response->assertRedirect('/players/VortexKing');
    }

    public function test_player_profile_displays_combat_analytics_and_tier(): void
    {
        $ranking = PlayerRanking::create([
            'gamertag' => 'BedrockNinja',
            'rank' => 2,
            'rating' => 1850,
            'wins' => 120,
            'losses' => 30,
            'kills' => 450,
            'deaths' => 100,
            'clan' => 'VOID',
        ]);

        $response = $this->get('/players/BedrockNinja');

        $response->assertStatus(200);
        $response->assertSee('BedrockNinja');
        $response->assertSee('Master'); // 1850 is Master tier
        $response->assertSee('1,850 ELO');
        $response->assertSee('80%'); // Win rate 120/(120+30)
        $response->assertSee('4.50'); // K/D 450/100
        $response->assertSee('[VOID]');
        $response->assertSee('Nodebuff (PotPvP)');
    }

    public function test_verified_badge_appears_when_account_linked(): void
    {
        $user = User::factory()->create([
            'name' => 'ShadowGamer',
            'minecraft_gamertag' => 'ShadowGamer',
            'role' => 'moderator',
        ]);

        PlayerRanking::create([
            'gamertag' => 'ShadowGamer',
            'rank' => 3,
            'rating' => 1650,
            'wins' => 90,
            'losses' => 25,
            'kills' => 310,
            'deaths' => 80,
            'clan' => 'HYDE',
        ]);

        $response = $this->get('/players/ShadowGamer');

        $response->assertStatus(200);
        $response->assertSee('Verified Player');
        $response->assertSee('moderator');
    }

    public function test_unranked_player_shows_friendly_not_found_page(): void
    {
        $response = $this->get('/players/GhostPlayer999');

        $response->assertStatus(200);
        $response->assertSee('GhostPlayer999');
        $response->assertSee('Not Found');
    }

    public function test_security_headers_are_present_on_web_responses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
