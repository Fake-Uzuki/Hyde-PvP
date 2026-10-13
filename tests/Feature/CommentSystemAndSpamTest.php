<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\NewsPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentSystemAndSpamTest extends TestCase
{
    use RefreshDatabase;

    private User $author;
    private NewsPost $post;
    private User $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->author = User::factory()->create(['role' => 'admin']);
        $this->post = NewsPost::create([
            'user_id' => $this->author->id,
            'title' => 'Patch 1.5 Released',
            'slug' => 'patch-1-5-released',
            'excerpt' => 'Combat hitboxes and sprint-reset changes.',
            'body' => 'Details about patch 1.5.',
            'is_published' => true,
            'published_at' => now(),
        ]);
        $this->member = User::factory()->create(['role' => 'member']);
    }

    public function test_guest_cannot_submit_comment(): void
    {
        $response = $this->post("/news/{$this->post->slug}/comments", [
            'content' => 'Great update!',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_authenticated_member_can_submit_valid_comment(): void
    {
        $response = $this->actingAs($this->member)->post("/news/{$this->post->slug}/comments", [
            'content' => 'Love the new knockback tuning. Keep it up!',
            'website_hp' => '', // honeypot left blank
        ]);

        $response->assertSessionHas('status', 'Your comment has been published.');
        $this->assertDatabaseHas('comments', [
            'news_post_id' => $this->post->id,
            'user_id' => $this->member->id,
            'content' => 'Love the new knockback tuning. Keep it up!',
            'is_hidden' => false,
        ]);
    }

    public function test_comment_exceeding_500_words_is_rejected_by_backend(): void
    {
        // Generate a 505-word string
        $longContent = implode(' ', array_fill(0, 505, 'pvp'));

        $response = $this->actingAs($this->member)->post("/news/{$this->post->slug}/comments", [
            'content' => $longContent,
        ]);

        $response->assertSessionHasErrors(['content']);
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_second_comment_within_60_seconds_is_rejected_due_to_cooldown(): void
    {
        // First comment succeeds
        $this->actingAs($this->member)->post("/news/{$this->post->slug}/comments", [
            'content' => 'First feedback message.',
        ])->assertSessionHas('status');

        $this->assertDatabaseCount('comments', 1);

        // Immediate second comment must fail cooldown
        $response = $this->actingAs($this->member)->post("/news/{$this->post->slug}/comments", [
            'content' => 'Second feedback message very quickly.',
        ]);

        $response->assertSessionHasErrors(['content']);
        $this->assertDatabaseCount('comments', 1);
    }

    public function test_duplicate_comment_is_rejected(): void
    {
        // First comment
        Comment::create([
            'news_post_id' => $this->post->id,
            'user_id' => $this->member->id,
            'content' => 'Same exact comment content',
            'created_at' => now()->subMinutes(5), // bypass cooldown
        ]);

        $response = $this->actingAs($this->member)->post("/news/{$this->post->slug}/comments", [
            'content' => 'Same exact comment content',
        ]);

        $response->assertSessionHasErrors(['content']);
        $this->assertDatabaseCount('comments', 1);
    }

    public function test_honeypot_submission_is_rejected(): void
    {
        $response = $this->actingAs($this->member)->post("/news/{$this->post->slug}/comments", [
            'content' => 'Normal looking comment from a spam bot',
            'website_hp' => 'http://spambot-payload.ru',
        ]);

        $response->assertSessionHasErrors(['content']);
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_excessive_links_in_comment_are_rejected(): void
    {
        $content = 'Check out https://link1.com and https://link2.com and also https://link3.com';

        $response = $this->actingAs($this->member)->post("/news/{$this->post->slug}/comments", [
            'content' => $content,
        ]);

        $response->assertSessionHasErrors(['content']);
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comment_content_is_escaped_in_blade_view(): void
    {
        Comment::create([
            'news_post_id' => $this->post->id,
            'user_id' => $this->member->id,
            'content' => '<script>alert("XSS")</script><b>Dangerous</b>',
            'is_hidden' => false,
        ]);

        $response = $this->get("/news/{$this->post->slug}");

        $response->assertStatus(200);
        $response->assertDontSee('<script>alert("XSS")</script>', false);
        $response->assertSee('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', false);
    }

    public function test_moderator_can_toggle_hidden_and_delete_comment(): void
    {
        $mod = User::factory()->create(['role' => 'moderator']);
        $comment = Comment::create([
            'news_post_id' => $this->post->id,
            'user_id' => $this->member->id,
            'content' => 'Inappropriate content to moderate',
            'is_hidden' => false,
        ]);

        // Toggle visibility
        $toggleResponse = $this->actingAs($mod)->post("/admin/comments/{$comment->id}/toggle");
        $toggleResponse->assertSessionHas('status');

        $comment->refresh();
        $this->assertTrue($comment->is_hidden);

        // Delete comment
        $deleteResponse = $this->actingAs($mod)->delete("/admin/comments/{$comment->id}");
        $deleteResponse->assertSessionHas('status');

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
