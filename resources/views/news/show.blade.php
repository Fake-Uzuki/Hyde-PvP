@extends('layouts.main')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">

    <!-- Back to News -->
    <div>
        <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-purple-400 hover:text-purple-300 group">
            <span class="transition-transform group-hover:-translate-x-1">←</span>
            Back to News & Announcements
        </a>
    </div>

    <!-- Article Card -->
    <article class="gaming-card p-6 sm:p-10 space-y-8">
        <header class="space-y-4 border-b border-slate-800 pb-8">
            <div class="flex items-center gap-3 text-xs text-slate-400">
                <span class="px-2.5 py-1 rounded-md bg-purple-950/60 text-purple-300 border border-purple-800/40 font-semibold uppercase tracking-wider text-[11px]">
                    Announcement
                </span>
                <span>•</span>
                <span>{{ $post->published_at ? $post->published_at->format('F d, Y \a\t g:i A') : $post->created_at->format('F d, Y') }}</span>
                <span>•</span>
                <span class="text-slate-300">Author: <strong class="text-white">{{ $post->user->name ?? 'Staff Team' }}</strong></span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white font-gaming leading-tight">
                {{ $post->title }}
            </h1>

            @if($post->excerpt)
                <p class="text-slate-300 text-base sm:text-lg italic leading-relaxed border-l-2 border-purple-500 pl-4 py-1">
                    {{ $post->excerpt }}
                </p>
            @endif
        </header>

        <!-- Post Content (Escaped and formatted) -->
        <div class="prose prose-invert max-w-none text-slate-300 text-base leading-relaxed space-y-4 whitespace-pre-line">
            {{ $post->body }}
        </div>
    </article>

    <!-- Comments Section -->
    <section class="space-y-8" id="comments">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <h2 class="text-2xl font-bold text-white font-gaming flex items-center gap-3">
                Comments
                <span class="text-sm font-normal px-2.5 py-0.5 rounded-full bg-slate-800 text-purple-300 border border-slate-700">
                    {{ $post->visibleComments->count() }}
                </span>
            </h2>
            <span class="text-xs text-slate-500">Live discussion</span>
        </div>

        <!-- Comment Form -->
        @auth
            <div class="gaming-card p-6 space-y-4" x-data="commentForm({{ $wordLimit }}, {{ $cooldownSeconds }})">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs text-slate-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Commenting as <strong class="text-white">{{ Auth::user()->name }}</strong>
                    </div>
                    <div class="text-xs font-mono" :class="wordCount > maxWords ? 'text-rose-400 font-bold' : 'text-slate-400'">
                        <span x-text="wordCount">0</span> / <span x-text="maxWords">{{ $wordLimit }}</span> words
                    </div>
                </div>

                <form method="POST" action="{{ route('comments.store', $post->slug) }}" class="space-y-4" @submit="handleSubmit">
                    @csrf

                    <!-- Honeypot anti-bot field (hidden from real users) -->
                    <div class="hidden" aria-hidden="true">
                        <label for="website_hp">Leave this field blank</label>
                        <input type="text" name="website_hp" id="website_hp" tabindex="-1" autocomplete="off" />
                    </div>

                    <div>
                        <textarea
                            name="content"
                            rows="4"
                            x-model="content"
                            @input="calculateWords"
                            required
                            placeholder="Share your feedback, congratulations, or questions (max {{ $wordLimit }} words)..."
                            class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition resize-y"
                        ></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
                        <p class="text-[11px] text-slate-500">
                            Strict anti-spam policy: 1 comment per 60 seconds. Maximum {{ $wordLimit }} words. Links are restricted.
                        </p>

                        <button
                            type="submit"
                            :disabled="isSubmitting || wordCount === 0 || wordCount > maxWords"
                            class="btn-primary text-xs py-2.5 px-6 self-end sm:self-auto disabled:opacity-50 disabled:cursor-not-allowed">
                            Post Comment
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="gaming-card p-6 text-center space-y-3">
                <p class="text-slate-300 text-sm">You must be logged in to post a comment.</p>
                <div class="flex items-center justify-center gap-3">
                    <a href="{{ route('login') }}" class="btn-secondary text-xs py-2 px-4">Log In</a>
                    <a href="{{ route('register') }}" class="btn-primary text-xs py-2 px-4">Register an Account</a>
                </div>
            </div>
        @endauth

        <!-- Comments List -->
        @if($post->visibleComments->isEmpty())
            <div class="gaming-card p-8 text-center text-slate-500 text-sm">
                No comments yet. Be the first to share your thoughts!
            </div>
        @else
            <div class="space-y-4">
                @foreach($post->visibleComments as $comment)
                    <div class="gaming-card p-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-purple-700 to-fuchsia-600 flex items-center justify-center font-bold text-white text-xs">
                                    {{ strtoupper(substr($comment->user->name ?? 'P', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-white flex items-center gap-1.5">
                                        {{ $comment->user->name ?? 'Deleted User' }}
                                        @if(optional($comment->user)->role === 'admin')
                                            <span class="px-1.5 py-0.2 rounded bg-purple-900/60 text-purple-300 text-[10px] font-semibold border border-purple-700/50">Admin</span>
                                        @elseif(optional($comment->user)->role === 'moderator')
                                            <span class="px-1.5 py-0.2 rounded bg-blue-900/60 text-blue-300 text-[10px] font-semibold border border-blue-700/50">Mod</span>
                                        @endif
                                    </div>
                                    <span class="text-[11px] text-slate-500">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Staff Moderation Actions -->
                            @auth
                                @if(Auth::user()->isStaff())
                                    <div class="flex items-center gap-2">
                                        <!-- Hide/Unhide -->
                                        <form method="POST" action="{{ route('admin.comments.toggle', $comment->id) }}">
                                            @csrf
                                            <button type="submit" class="text-xs text-amber-400 hover:text-amber-300 px-2 py-1 rounded bg-amber-950/30 border border-amber-800/40">
                                                {{ $comment->is_hidden ? 'Restore' : 'Hide' }}
                                            </button>
                                        </form>

                                        <!-- Delete -->
                                        <form method="POST" action="{{ route('admin.comments.destroy', $comment->id) }}" onsubmit="return confirm('Permanently delete this comment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 px-2 py-1 rounded bg-rose-950/30 border border-rose-800/40">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>

                        <!-- Safe escaped comment content -->
                        <div class="text-slate-300 text-sm leading-relaxed whitespace-pre-line pl-10">
                            {{ $comment->content }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </section>

</div>

<!-- Word Counter Client-side script -->
<script>
    function commentForm(maxWords, cooldown) {
        return {
            content: '',
            wordCount: 0,
            maxWords: maxWords,
            cooldown: cooldown,
            isSubmitting: false,
            calculateWords() {
                const trimmed = this.content.trim();
                if (!trimmed) {
                    this.wordCount = 0;
                    return;
                }
                const matches = trimmed.match(/\S+/g);
                this.wordCount = matches ? matches.length : 0;
            },
            handleSubmit(e) {
                this.calculateWords();
                if (this.wordCount === 0) {
                    e.preventDefault();
                    alert('Comment cannot be empty.');
                    return false;
                }
                if (this.wordCount > this.maxWords) {
                    e.preventDefault();
                    alert('Your comment exceeds the ' + this.maxWords + ' word limit (currently ' + this.wordCount + ' words).');
                    return false;
                }
                this.isSubmitting = true;
            }
        };
    }
</script>
@endsection
