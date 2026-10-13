@extends('layouts.main')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-800 pb-6">
        <div>
            <div class="text-xs uppercase tracking-widest text-purple-400 font-bold mb-1">HYDE PVP Updates</div>
            <h1 class="text-3xl sm:text-4xl font-black text-white font-gaming">
                NEWS & <span class="gaming-gradient-text">ANNOUNCEMENTS</span>
            </h1>
        </div>
        <p class="text-slate-400 text-sm max-w-md">
            Stay updated with patch notes, tournament recaps, server maintenance notices, and community highlights.
        </p>
    </div>

    @if($posts->isEmpty())
        <div class="gaming-card p-16 text-center text-slate-400">
            <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <h2 class="text-xl font-bold text-slate-200">No News Published Yet</h2>
            <p class="text-sm text-slate-400 mt-2">New announcements will be posted here as updates roll out.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $post)
                <article class="gaming-card p-6 flex flex-col justify-between overflow-hidden">
                    <div class="space-y-4">
                        @if($post->featured_image_url)
                            <a href="{{ route('news.show', $post->slug) }}" class="block -mx-6 -mt-6 mb-4 overflow-hidden aspect-video bg-slate-950 group/img">
                                <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover/img:scale-105 transition duration-300" />
                            </a>
                        @endif

                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span class="text-purple-300 font-medium">
                                {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                            </span>
                            <div class="flex items-center gap-3">
                                @if($post->video_url)
                                    <span class="text-[11px] text-cyan-400 font-semibold" title="Includes video">🎥 Video</span>
                                @endif
                                @if(!empty($post->images) && count($post->images) > 1)
                                    <span class="text-[11px] text-purple-400 font-semibold" title="Multiple photos">📸 {{ count($post->images) }}</span>
                                @endif
                                <span class="flex items-center gap-1 text-slate-400">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    {{ $post->visible_comments_count }}
                                </span>
                            </div>
                        </div>

                        <h2 class="text-xl font-bold text-white hover:text-purple-300 transition line-clamp-2">
                            <a href="{{ route('news.show', $post->slug) }}">{{ $post->title }}</a>
                        </h2>

                        <p class="text-slate-400 text-sm leading-relaxed line-clamp-3">
                            {{ $post->excerpt ?: Str::limit(strip_tags($post->body), 150) }}
                        </p>
                    </div>

                    <div class="pt-6 border-t border-slate-800/80 mt-6 flex items-center justify-between">
                        <span class="text-xs text-slate-500">By {{ $post->user->name ?? 'Staff' }}</span>
                        <a href="{{ route('news.show', $post->slug) }}"
                           class="inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider text-purple-400 hover:text-purple-300 group">
                            Read More
                            <span class="transition-transform group-hover:translate-x-1">→</span>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pt-6">
            {{ $posts->links() }}
        </div>
    @endif

</div>
@endsection
