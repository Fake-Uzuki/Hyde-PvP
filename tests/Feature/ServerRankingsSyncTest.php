<?php

namespace Tests\Feature;

use App\Models\PlayerRanking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServerRankingsSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_unauthorized_server_request(): void
    {
        $response = $this->postJson('/api/server/sync-rankings', [
            'players' => [
                ['gamertag' => 'Player1', 'kills' => 10, 'points' => 100],
            ],
        ]);

        $response->assertStatus(401);
        $response->assertJson(['success' => false]);
    }

    public function test_syncs_batch_player_rankings_and_recalculates_ranks(): void
    {
        $serverKey = env('HYDE_SERVER_KEY', 'hyde-pvp-secret-key-19015');

        $response = $this->postJson('/api/server/sync-rankings', [
            'players' => [
                [
                    'gamertag' => 'PlayerOne',
                    'kills' => 50,
                    'deaths' => 10,
                    'points' => 800,
                    'wins' => 15,
                    'losses' => 2,
                    'clan' => 'ALPHA',
                ],
                [
                    'gamertag' => 'PlayerTwo',
                    'kills' => 90,
                    'deaths' => 5,
                    'points' => 1500, // Highest points -> should be rank 1
                    'wins' => 30,
                    'losses' => 1,
                    'clan' => 'HYDE',
                ],
                [
                    'gamertag' => 'PlayerThree',
                    'kills' => 20,
                    'deaths' => 15,
                    'points' => 400, // Lowest points -> should be rank 3
                    'wins' => 5,
                    'losses' => 10,
                ],
            ],
        ], [
            'X-Server-Key' => $serverKey,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'players_synced' => 3,
        ]);

        // Verify PlayerTwo is Rank 1
        $playerTwo = PlayerRanking::where('gamertag', 'PlayerTwo')->first();
        $this->assertNotNull($playerTwo);
        $this->assertEquals(1, $playerTwo->rank);
        $this->assertEquals(1500, $playerTwo->rating);

        // Verify PlayerOne is Rank 2
        $playerOne = PlayerRanking::where('gamertag', 'PlayerOne')->first();
        $this->assertNotNull($playerOne);
        $this->assertEquals(2, $playerOne->rank);

        // Verify PlayerThree is Rank 3
        $playerThree = PlayerRanking::where('gamertag', 'PlayerThree')->first();
        $this->assertNotNull($playerThree);
        $this->assertEquals(3, $playerThree->rank);

        // Verify public rankings page displays the synced players
        $rankingPage = $this->get('/rankings');
        $rankingPage->assertStatus(200);
        $rankingPage->assertSee('PlayerTwo');
        $rankingPage->assertSee('PlayerOne');
    }

    public function test_syncs_scoreboard_dictionary_format(): void
    {
        $serverKey = env('HYDE_SERVER_KEY', 'hyde-pvp-secret-key-19015');

        $response = $this->postJson('/api/server/sync-rankings', [
            'scores' => [
                'Uzuki' => ['kills' => 120, 'points' => 2000],
                'Shadow' => ['kills' => 45, 'points' => 900],
            ],
        ], [
            'X-Server-Key' => $serverKey,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'players_synced' => 2,
        ]);

        $uzuki = PlayerRanking::where('gamertag', 'Uzuki')->first();
        $this->assertNotNull($uzuki);
        $this->assertEquals(1, $uzuki->rank);
        $this->assertEquals(2000, $uzuki->rating);
        $this->assertEquals(120, $uzuki->kills);
    }
}
