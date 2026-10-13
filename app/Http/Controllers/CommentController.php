<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\NewsPost;
use App\Models\SiteSetting;
use App\Services\SpamProtectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    /**
     * Store a newly created comment under a news post.
     */
    public function store(Request $request, NewsPost $post, SpamProtectionService $spamService): RedirectResponse
    {
        $user = $request->user();

        // 1. Honeypot check
        if ($spamService->isHoneypotTriggered($request)) {
            throw ValidationException::withMessages([
                'content' => 'Automated form submission detected.',
            ]);
        }

        // 2. IP-based rate limit (max 10 comments per 10 minutes per IP)
        $ipKey = 'comment-ip:' . $request->ip();
        if (RateLimiter::tooManyAttempts($ipKey, 10)) {
            $seconds = RateLimiter::availableIn($ipKey);
            throw ValidationException::withMessages([
                'content' => "Too many attempts from your network. Please wait {$seconds} seconds.",
            ]);
        }

        // 3. Authenticated user cooldown check (60 seconds)
        $cooldown = (int) SiteSetting::get('comment_cooldown_seconds', 60);
        $remaining = $spamService->getCooldownRemaining($user->id, $cooldown);
        if ($remaining > 0) {
            throw ValidationException::withMessages([
                'content' => "Please wait {$remaining} seconds before submitting another comment.",
            ]);
        }

        // 4. Basic content validation
        $request->validate([
            'content' => ['required', 'string'],
        ], [
            'content.required' => 'Your comment cannot be blank.',
        ]);

        $content = trim($request->input('content'));

        // 5. Word count limit check (500 words)
        $maxWords = (int) SiteSetting::get('comment_word_limit', 500);
        $wordCount = $spamService->countWords($content);
        if ($wordCount > $maxWords) {
            throw ValidationException::withMessages([
                'content' => "Your comment contains {$wordCount} words, which exceeds the maximum limit of {$maxWords} words.",
            ]);
        }

        // 6. Duplicate spam check
        if ($spamService->isDuplicate($user->id, $content)) {
            throw ValidationException::withMessages([
                'content' => 'Duplicate comment detected. You have already submitted this comment recently.',
            ]);
        }

        // 7. Excessive links check
        if ($spamService->hasExcessiveLinks($content, 2)) {
            throw ValidationException::withMessages([
                'content' => 'To protect the community from spam, comments cannot contain more than 2 links.',
            ]);
        }

        // 8. Repeated characters / spam pattern check
        if ($spamService->hasRepeatedPatterns($content)) {
            throw ValidationException::withMessages([
                'content' => 'Comment was flagged for excessive repetitive characters.',
            ]);
        }

        // Create comment
        Comment::create([
            'news_post_id' => $post->id,
            'user_id' => $user->id,
            'content' => $content,
            'is_hidden' => false,
            'ip_address' => $request->ip(),
        ]);

        RateLimiter::hit($ipKey, 600);

        return back()->with('status', 'Your comment has been published.');
    }

    /**
     * Toggle visibility (hide/unhide) of a comment (Moderators and Admins).
     */
    public function toggleVisibility(Comment $comment): RedirectResponse
    {
        $comment->is_hidden = ! $comment->is_hidden;
        $comment->save();

        $action = $comment->is_hidden ? 'hidden' : 'restored';
        return back()->with('status', "Comment has been {$action}.");
    }

    /**
     * Delete a comment (Moderators and Admins).
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();
        return back()->with('status', 'Comment has been permanently deleted.');
    }
}
