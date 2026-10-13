<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClanRanking;
use App\Models\Comment;
use App\Models\NewsPost;
use App\Models\PlayerRanking;
use App\Models\Rule;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin overview dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_posts' => NewsPost::count(),
            'total_rules' => Rule::count(),
            'total_rankings' => PlayerRanking::count() + ClanRanking::count(),
            'total_comments' => Comment::count(),
            'hidden_comments' => Comment::where('is_hidden', true)->count(),
        ];

        $recentComments = Comment::with(['user', 'post'])
            ->latest()
            ->take(6)
            ->get();

        $recentPosts = NewsPost::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentComments', 'recentPosts'));
    }
}
