@extends('admin.layout', ['title' => 'Dashboard Overview'])

@section('admin_content')
<div class="space-y-8">

    <!-- Overview Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Users -->
        <div class="gaming-card p-5 space-y-2">
            <span class="text-xs uppercase tracking-wider text-slate-400 font-bold">Total Members</span>
            <div class="text-3xl font-black text-white font-gaming">{{ $stats['total_users'] }}</div>
            <span class="text-[11px] text-purple-400 font-medium">Registered accounts</span>
        </div>

        <!-- News Posts -->
        <div class="gaming-card p-5 space-y-2">
            <span class="text-xs uppercase tracking-wider text-slate-400 font-bold">News & Updates</span>
            <div class="text-3xl font-black text-cyan-300 font-gaming">{{ $stats['total_posts'] }}</div>
            <span class="text-[11px] text-cyan-400 font-medium">Published announcements</span>
        </div>

        <!-- Rules -->
        <div class="gaming-card p-5 space-y-2">
            <span class="text-xs uppercase tracking-wider text-slate-400 font-bold">Active Rules</span>
            <div class="text-3xl font-black text-amber-300 font-gaming">{{ $stats['total_rules'] }}</div>
            <span class="text-[11px] text-amber-400 font-medium">Server guidelines</span>
        </div>

        <!-- Comments -->
        <div class="gaming-card p-5 space-y-2">
            <span class="text-xs uppercase tracking-wider text-slate-400 font-bold">Total Comments</span>
            <div class="text-3xl font-black text-fuchsia-300 font-gaming">{{ $stats['total_comments'] }}</div>
            <span class="text-[11px] text-fuchsia-400 font-medium">{{ $stats['hidden_comments'] }} hidden/flagged</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Announcements -->
        <div class="gaming-card p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h2 class="text-base font-bold text-white font-gaming">Recent Announcements</h2>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.news.create') }}" class="text-xs text-purple-400 hover:text-purple-300 font-semibold">
                        + New Post
                    </a>
                @endif
            </div>

            @if($recentPosts->isEmpty())
                <p class="text-sm text-slate-500 py-4 text-center">No news posts created yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($recentPosts as $post)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900/60 border border-slate-800 text-sm">
                            <div>
                                <a href="{{ route('news.show', $post->slug) }}" target="_blank" class="font-bold text-white hover:text-purple-300 transition line-clamp-1">
                                    {{ $post->title }}
                                </a>
                                <span class="text-xs text-slate-500">{{ $post->created_at->format('M d, Y') }} • By {{ $post->user->name ?? 'Staff' }}</span>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded font-mono {{ $post->is_published ? 'bg-emerald-950/60 text-emerald-400' : 'bg-amber-950/60 text-amber-400' }}">
                                {{ $post->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Recent Comments Moderation -->
        <div class="gaming-card p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h2 class="text-base font-bold text-white font-gaming">Latest Comments</h2>
                <a href="{{ route('admin.comments.index') }}" class="text-xs text-fuchsia-400 hover:text-fuchsia-300 font-semibold">
                    Moderate All →
                </a>
            </div>

            @if($recentComments->isEmpty())
                <p class="text-sm text-slate-500 py-4 text-center">No comments submitted yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($recentComments as $comment)
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800 text-xs space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white">{{ $comment->user->name ?? 'User' }}</span>
                                <span class="text-slate-500">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-300 line-clamp-2">{{ $comment->content }}</p>
                            <div class="pt-1 flex items-center justify-between text-[11px]">
                                <span class="{{ $comment->is_hidden ? 'text-amber-400' : 'text-emerald-400' }}">
                                    {{ $comment->is_hidden ? '● Hidden' : '● Visible' }}
                                </span>
                                <a href="{{ route('news.show', $comment->post->slug ?? '#') }}#comments" target="_blank" class="text-purple-400 hover:underline">
                                    View on Post
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
