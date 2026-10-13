<?php

namespace App\Http\Controllers;

use App\Models\NewsPost;
use App\Models\SiteSetting;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Display a listing of published news and announcements.
     */
    public function index(): View
    {
        $posts = NewsPost::published()
            ->with('user')
            ->withCount('visibleComments')
            ->latest('published_at')
            ->paginate(6);

        return view('news.index', compact('posts'));
    }

    /**
     * Display a single news post with comments.
     */
    public function show(string $slug): View
    {
        $post = NewsPost::published()
            ->where('slug', $slug)
            ->with(['user', 'visibleComments.user'])
            ->firstOrFail();

        $wordLimit = (int) SiteSetting::get('comment_word_limit', 500);
        $cooldownSeconds = (int) SiteSetting::get('comment_cooldown_seconds', 60);

        return view('news.show', compact('post', 'wordLimit', 'cooldownSeconds'));
    }
}
