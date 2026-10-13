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

        <!-- Video Player Attachment (if present) -->
        @if($post->video_url)
            <div class="rounded-2xl overflow-hidden border border-cyan-500/30 bg-black shadow-2xl space-y-2">
                <div class="p-3 bg-slate-900/80 border-b border-slate-800 flex items-center justify-between text-xs text-cyan-300">
                    <span class="font-bold flex items-center gap-1.5">
                        <span>🎥</span> Video Highlight
                    </span>
                    <span class="text-slate-400">Click to play</span>
                </div>
                <video controls class="w-full max-h-[480px] bg-black rounded-b-xl" preload="metadata">
                    <source src="{{ $post->video_url }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        @endif

        <!-- Post Content (Escaped and formatted) -->
        <div class="prose prose-invert max-w-none text-slate-300 text-base leading-relaxed space-y-4 whitespace-pre-line">
            {{ $post->body }}
        </div>

        <!-- Image Gallery Attachment (1 to 5 images) -->
        @if(!empty($post->image_urls) && count($post->image_urls) > 0)
            <div x-data="{ modalOpen: false, modalSrc: '' }" class="space-y-4 pt-6 border-t border-slate-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-purple-300 flex items-center gap-2">
                        <span>📸</span> Photo Gallery ({{ count($post->image_urls) }})
                    </h3>
                    <span class="text-xs text-slate-400">Click any image to view full size</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 {{ count($post->image_urls) >= 3 ? 'lg:grid-cols-3' : '' }} gap-4">
                    @foreach($post->image_urls as $imgUrl)
                        <div @click="modalSrc = '{{ $imgUrl }}'; modalOpen = true"
                             class="group cursor-pointer relative rounded-2xl overflow-hidden border border-slate-800 hover:border-purple-500/60 transition shadow-md bg-slate-950 aspect-video">
                            <img src="{{ $imgUrl }}"
                                 alt="Announcement image"
                                 class="w-full h-full object-cover transition duration-300 group-hover:scale-105"
                                 loading="lazy" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition flex items-end p-3">
                                <span class="text-xs text-white font-semibold flex items-center gap-1">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                    </svg>
                                    View Full Image
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Lightbox Modal -->
                <div x-show="modalOpen"
                     x-cloak
                     @keydown.escape.window="modalOpen = false"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-md"
                     style="display: none;">
                    <div @click.away="modalOpen = false" class="relative max-w-5xl max-h-[90vh] flex flex-col items-center">
                        <button @click="modalOpen = false"
                                class="absolute -top-10 right-0 text-white hover:text-rose-400 text-sm font-bold uppercase tracking-wider flex items-center gap-1 bg-slate-900/80 px-3 py-1 rounded-full border border-slate-700">
                            ✕ Close
                        </button>
                        <img :src="modalSrc" alt="Full Preview" class="max-w-full max-h-[85vh] object-contain rounded-2xl border border-purple-500/40 shadow-2xl" />
                    </div>
                </div>
            </div>
        @endif
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
