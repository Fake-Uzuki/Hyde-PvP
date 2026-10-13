<?php

namespace Tests\Feature;

use App\Models\PlayerVerificationCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_generate_in_game_verification_code(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/profile/verify/generate');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'code',
            'command',
            'expires_in_seconds',
        ]);

        $this->assertDatabaseHas('player_verification_codes', [
            'user_id' => $user->id,
            'is_used' => false,
        ]);
    }

    public function test_minecraft_server_can_verify_player_with_valid_key_and_code(): void
    {
        $user = User::factory()->create([
            'minecraft_gamertag' => null,
            'is_verified' => false,
        ]);

        $codeRecord = PlayerVerificationCode::generateForUser($user, 15);

        // Simulate Bedrock server sending HTTP POST to /api/server/verify-player
        $response = $this->postJson('/api/server/verify-player', [
            'code' => $codeRecord->code,
            'gamertag' => 'UzukiBedrockGod',
            'xuid' => '2535418291029384',
        ], [
            'X-Server-Key' => env('HYDE_SERVER_KEY', 'hyde-pvp-secret-key-19015'),
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'gamertag' => 'UzukiBedrockGod',
        ]);

        $user->refresh();
        $this->assertTrue($user->is_verified);
        $this->assertEquals('UzukiBedrockGod', $user->minecraft_gamertag);
        $this->assertEquals('2535418291029384', $user->xuid);
        $this->assertNotNull($user->verified_at);

        $codeRecord->refresh();
        $this->assertTrue($codeRecord->is_used);
    }

    public function test_minecraft_server_rejects_unauthorized_key(): void
    {
        $response = $this->postJson('/api/server/verify-player', [
            'code' => '123456',
            'gamertag' => 'HackerSteve',
        ], [
            'X-Server-Key' => 'wrong-secret-key',
        ]);

        $response->assertStatus(401);
    }

    public function test_minecraft_server_rejects_invalid_or_expired_code(): void
    {
        $response = $this->postJson('/api/server/verify-player', [
            'code' => '999999',
            'gamertag' => 'NormalPlayer',
        ], [
            'X-Server-Key' => env('HYDE_SERVER_KEY', 'hyde-pvp-secret-key-19015'),
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_user_can_check_status_and_unlink(): void
    {
        $user = User::factory()->create([
            'minecraft_gamertag' => 'VerifiedSteve',
            'is_verified' => true,
            'xuid' => '12345',
        ]);

        // Status check
        $statusResponse = $this->actingAs($user)->getJson('/profile/verify/status');
        $statusResponse->assertStatus(200);
        $statusResponse->assertJson([
            'is_verified' => true,
            'gamertag' => 'VerifiedSteve',
        ]);

        // Unlink
        $unlinkResponse = $this->actingAs($user)->post('/profile/verify/unlink');
        $unlinkResponse->assertRedirect();

        $user->refresh();
        $this->assertFalse($user->is_verified);
        $this->assertNull($user->xuid);
    }
}
