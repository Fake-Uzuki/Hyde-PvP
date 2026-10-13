<?php

namespace Tests\Feature;

use App\Models\UhcWinner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UhcWinnersTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_members_can_view_uhc_winners_on_rankings(): void
    {
        $winner = UhcWinner::create([
            'title' => 'Spring Bedrock UHC Cup Season 1',
            'winner_gamertag' => 'UzukiPVP',
            'runner_up_gamertag' => 'BedrockNinja',
            'winner_kills' => 14,
            'event_date' => '2026-04-10',
            'game_mode' => 'Solo UHC',
            'prize' => '$250 + VIP Tier',
            'recap_url' => 'https://youtube.com/watch?v=example',
        ]);

        $response = $this->get('/rankings?tab=uhc');
        $response->assertStatus(200);
        $response->assertSee('Spring Bedrock UHC Cup Season 1');
        $response->assertSee('UzukiPVP');
        $response->assertSee('BedrockNinja');
        $response->assertSee('Solo UHC');

        // Check on home page
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('UzukiPVP');
        $homeResponse->assertSee('Reigning UHC Tournament Champion');
    }

    public function test_non_admins_cannot_access_admin_uhc_routes(): void
    {
        // Guest
        $this->get('/admin/uhc')->assertRedirect('/login');
        $this->post('/admin/uhc', [])->assertRedirect('/login');

        // Member
        $member = User::factory()->create(['role' => 'member']);
        $this->actingAs($member)->get('/admin/uhc')->assertStatus(403);
        $this->actingAs($member)->post('/admin/uhc', [])->assertStatus(403);
    }

    public function test_admin_can_create_update_and_delete_uhc_winner(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Create
        $response = $this->actingAs($admin)->post('/admin/uhc', [
            'title' => 'Fall Duel Invitational 2026',
            'winner_gamertag' => 'UzukiMaster',
            'runner_up_gamertag' => 'RunnerSteve',
            'winner_kills' => 18,
            'event_date' => '2026-10-12',
            'game_mode' => 'Duos UHC',
            'prize' => '$500 Cash',
            'recap_url' => 'https://youtube.com/watch?v=sample',
        ]);

        $response->assertRedirect(route('admin.uhc.index'));
        $this->assertDatabaseHas('uhc_winners', [
            'title' => 'Fall Duel Invitational 2026',
            'winner_gamertag' => 'UzukiMaster',
            'winner_kills' => 18,
        ]);

        $record = UhcWinner::where('title', 'Fall Duel Invitational 2026')->first();

        // 2. Update
        $updateResponse = $this->actingAs($admin)->put("/admin/uhc/{$record->id}", [
            'title' => 'Fall Duel Invitational 2026 (Updated)',
            'winner_gamertag' => 'UzukiMaster',
            'runner_up_gamertag' => 'RunnerSteve',
            'winner_kills' => 20,
            'event_date' => '2026-10-12',
            'game_mode' => 'Duos UHC',
            'prize' => '$600 Cash',
            'recap_url' => 'https://youtube.com/watch?v=sample',
        ]);

        $updateResponse->assertRedirect(route('admin.uhc.index'));
        $this->assertDatabaseHas('uhc_winners', [
            'id' => $record->id,
            'title' => 'Fall Duel Invitational 2026 (Updated)',
            'winner_kills' => 20,
        ]);

        // 3. Delete
        $deleteResponse = $this->actingAs($admin)->delete("/admin/uhc/{$record->id}");
        $deleteResponse->assertRedirect(route('admin.uhc.index'));
        $this->assertDatabaseMissing('uhc_winners', ['id' => $record->id]);
    }
}
