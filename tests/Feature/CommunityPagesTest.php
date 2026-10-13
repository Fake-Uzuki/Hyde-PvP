<?php

namespace Tests\Feature;

use App\Models\NewsPost;
use App\Models\Rule;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_homepage_with_branding_and_connection_info(): void
    {
        SiteSetting::set('server_address', 'hydepvp.ultramc.co');
        SiteSetting::set('server_port', '19015');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('HYDE');
        $response->assertSee('PVP');
        $response->assertSee('hydepvp.ultramc.co');
        $response->assertSee('19015');
        $response->assertSee('Join Discord');
    }

    public function test_guests_can_view_news_index_and_detail_page(): void
    {
        $author = User::factory()->create(['role' => 'admin']);
        $post = NewsPost::create([
            'user_id' => $author->id,
            'title' => 'Bedrock Season 1 Launch',
            'slug' => 'bedrock-season-1-launch',
            'excerpt' => 'Our competitive season has officially launched.',
            'body' => 'Welcome to the arena. May the best Bedrock fighter win!',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $indexResponse = $this->get('/news');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Bedrock Season 1 Launch');

        $detailResponse = $this->get('/news/bedrock-season-1-launch');
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Bedrock Season 1 Launch');
        $detailResponse->assertSee('May the best Bedrock fighter win!');
    }

    public function test_guests_can_view_server_info_and_rules(): void
    {
        Rule::create([
            'category' => 'Gameplay',
            'title' => 'No Auto-Clickers Allowed',
            'description' => 'Unfair modifications are banned.',
            'order' => 1,
        ]);

        $serverInfoResponse = $this->get('/server-info');
        $serverInfoResponse->assertStatus(200);
        $serverInfoResponse->assertSee('HOW TO');
        $serverInfoResponse->assertSee('CONNECT');

        $rulesResponse = $this->get('/rules');
        $rulesResponse->assertStatus(200);
        $rulesResponse->assertSee('No Auto-Clickers Allowed');
    }

    public function test_rankings_page_shows_empty_state_gracefully(): void
    {
        $response = $this->get('/rankings');

        $response->assertStatus(200);
        $response->assertSee('No Player Ranking Records Yet');
    }
}
