<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SpamProtectionService
{
    /**
     * Check if a honeypot field was filled.
     */
    public function isHoneypotTriggered(Request $request, string $fieldName = 'website_hp'): bool
    {
        $honeypot = $request->input($fieldName);
        return ! empty($honeypot);
    }

    /**
     * Count words in a string.
     */
    public function countWords(string $text): int
    {
        // Strip markdown / tags, trim
        $clean = trim(preg_replace('/\s+/', ' ', $text));
        if ($clean === '') {
            return 0;
        }

        return str_word_count($clean);
    }

    /**
     * Verify if word count exceeds limit.
     */
    public function exceedsWordLimit(string $text, ?int $limit = null): bool
    {
        $limit = $limit ?? (int) SiteSetting::get('comment_word_limit', 500);
        return $this->countWords($text) > $limit;
    }

    /**
     * Check remaining cooldown seconds for user.
     */
    public function getCooldownRemaining(int $userId, ?int $cooldown = null): int
    {
        $cooldown = $cooldown ?? (int) SiteSetting::get('comment_cooldown_seconds', 60);

        $lastComment = Comment::where('user_id', $userId)
            ->latest('created_at')
            ->first();

        if (! $lastComment) {
            return 0;
        }

        $elapsed = now()->diffInSeconds($lastComment->created_at);
        if ($elapsed < $cooldown) {
            return $cooldown - $elapsed;
        }

        return 0;
    }

    /**
     * Check if content is a duplicate of user's recent comment.
     */
    public function isDuplicate(int $userId, string $content): bool
    {
        $normalized = trim(strtolower($content));

        $recentComments = Comment::where('user_id', $userId)
            ->latest('created_at')
            ->take(5)
            ->get();

        foreach ($recentComments as $comment) {
            if (trim(strtolower($comment->content)) === $normalized) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect excessive links (e.g. > 2 URLs).
     */
    public function hasExcessiveLinks(string $content, int $maxLinks = 2): bool
    {
        $pattern = '/\b(?:https?|ftp):\/\/[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/))/i';
        $matches = [];
        preg_match_all($pattern, $content, $matches);

        return count($matches[0]) > $maxLinks;
    }

    /**
     * Detect repeated characters spam (e.g. "aaaaaaa", "!!!!!!!").
     */
    public function hasRepeatedPatterns(string $content): bool
    {
        // 8 or more identical characters in a row
        if (preg_match('/(.)\1{7,}/', $content)) {
            return true;
        }

        return false;
    }
}
