<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAndAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_newly_registered_user_is_strictly_a_member_never_admin(): void
    {
        $response = $this->post('/register', [
            'name' => 'Steve Gamer',
            'email' => 'steve@example.com',
            'minecraft_gamertag' => 'SteveBedrock99',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'role' => 'admin', // Malicious attempt to inject admin role
        ]);

        $this->assertAuthenticated();

        $user = User::where('email', 'steve@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('member', $user->role);
        $this->assertEquals('SteveBedrock99', $user->minecraft_gamertag);
        $this->assertFalse($user->isAdmin());
    }

    public function test_guest_is_redirected_when_accessing_admin(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_regular_member_receives_403_forbidden_on_admin_endpoints(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($member)->get('/admin');
        $response->assertStatus(403);

        $newsAdminResponse = $this->actingAs($member)->get('/admin/news');
        $newsAdminResponse->assertStatus(403);

        $settingsResponse = $this->actingAs($member)->get('/admin/settings');
        $settingsResponse->assertStatus(403);

        $usersResponse = $this->actingAs($member)->get('/admin/users');
        $usersResponse->assertStatus(403);
    }

    public function test_moderator_can_access_dashboard_and_comments_but_not_settings_or_users(): void
    {
        $mod = User::factory()->create(['role' => 'moderator']);

        // Allowed
        $dashboardResponse = $this->actingAs($mod)->get('/admin');
        $dashboardResponse->assertStatus(200);

        $commentsResponse = $this->actingAs($mod)->get('/admin/comments');
        $commentsResponse->assertStatus(200);

        // Forbidden
        $settingsResponse = $this->actingAs($mod)->get('/admin/settings');
        $settingsResponse->assertStatus(403);

        $usersResponse = $this->actingAs($mod)->get('/admin/users');
        $usersResponse->assertStatus(403);
    }

    public function test_admin_can_access_all_management_endpoints(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/news')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/rules')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/rankings')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/comments')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/settings')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/users')->assertStatus(200);
    }

    public function test_artisan_user_promote_command_promotes_user_safely(): void
    {
        $user = User::factory()->create(['email' => 'novice@example.com', 'role' => 'member']);

        $this->artisan('user:promote novice@example.com admin')
            ->assertSuccessful();

        $user->refresh();
        $this->assertEquals('admin', $user->role);
        $this->assertTrue($user->isAdmin());

        // Test invalid role rejection
        $this->artisan('user:promote novice@example.com supergod')
            ->assertFailed();
    }

    public function test_sole_admin_cannot_demote_themselves(): void
    {
        $soleAdmin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($soleAdmin)->patch("/admin/users/{$soleAdmin->id}/role", [
            'role' => 'member',
        ]);

        $soleAdmin->refresh();
        $this->assertEquals('admin', $soleAdmin->role);
    }
}
