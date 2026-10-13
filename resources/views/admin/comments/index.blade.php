@extends('admin.layout', ['title' => 'Comment Moderation'])

@section('admin_content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white font-gaming">Comment Moderation</h2>
            <p class="text-xs text-slate-400">Review community comments, hide inappropriate content, and delete spam.</p>
        </div>

        <!-- Filter -->
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.comments.index') }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ !request('filter') ? 'bg-purple-600 text-white' : 'bg-slate-900 text-slate-400 hover:text-white' }}">
                All Comments
            </a>
            <a href="{{ route('admin.comments.index', ['filter' => 'hidden']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('filter') === 'hidden' ? 'bg-purple-600 text-white' : 'bg-slate-900 text-slate-400 hover:text-white' }}">
                Hidden Only
            </a>
        </div>
    </div>

    @if($comments->isEmpty())
        <div class="gaming-card p-12 text-center text-slate-500">
            No comments found matching your criteria.
        </div>
    @else
        <div class="gaming-card overflow-hidden">
            <div class="divide-y divide-slate-800/80">
                @foreach($comments as $comment)
                    <div class="p-5 flex flex-col md:flex-row md:items-start justify-between gap-4 hover:bg-slate-900/30 transition">
                        <div class="space-y-2 flex-grow">
                            <div class="flex flex-wrap items-center gap-3 text-xs">
                                <span class="font-bold text-white">{{ $comment->user->name ?? 'Deleted User' }}</span>
                                <span class="text-slate-500">•</span>
                                <span class="text-slate-400">{{ $comment->created_at->format('M d, Y \a\t g:i A') }}</span>
                                <span class="text-slate-500">•</span>
                                <span class="text-slate-400">
                                    Post: <a href="{{ route('news.show', $comment->post->slug ?? '#') }}" target="_blank" class="text-purple-400 hover:underline font-semibold">{{ $comment->post->title ?? 'Deleted Post' }}</a>
                                </span>
                                @if($comment->ip_address)
                                    <span class="text-slate-500 font-mono text-[11px]">(IP: {{ $comment->ip_address }})</span>
                                @endif
                                <span class="text-[11px] px-2 py-0.5 rounded font-mono {{ $comment->is_hidden ? 'bg-amber-950/60 text-amber-400 border border-amber-800/40' : 'bg-emerald-950/60 text-emerald-400 border border-emerald-800/40' }}">
                                    {{ $comment->is_hidden ? 'Hidden' : 'Visible' }}
                                </span>
                            </div>

                            <p class="text-sm text-slate-200 leading-relaxed whitespace-pre-line bg-[#070A12] p-3 rounded-xl border border-slate-800/60">
                                {{ $comment->content }}
                            </p>
                        </div>

                        <!-- Moderation Actions -->
                        <div class="flex items-center gap-2 shrink-0 self-end md:self-center">
                            <form method="POST" action="{{ route('admin.comments.toggle', $comment->id) }}">
                                @csrf
                                <button type="submit"
                                        class="text-xs px-3 py-1.5 rounded font-semibold {{ $comment->is_hidden ? 'bg-emerald-950/50 text-emerald-400 border border-emerald-800/50 hover:bg-emerald-900/50' : 'bg-amber-950/50 text-amber-400 border border-amber-800/50 hover:bg-amber-900/50' }}">
                                    {{ $comment->is_hidden ? 'Restore' : 'Hide' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.comments.destroy', $comment->id) }}" onsubmit="return confirm('Permanently delete this comment?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs px-3 py-1.5 rounded font-semibold bg-rose-950/50 text-rose-400 border border-rose-800/50 hover:bg-rose-900/50">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="pt-4">
            {{ $comments->links() }}
        </div>
    @endif

</div>
@endsection
