@extends('admin.layout', ['title' => 'Edit Announcement'])

@section('admin_content')
<div class="max-w-4xl space-y-6">

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-white font-gaming">Edit Announcement</h2>
        <a href="{{ route('admin.news.index') }}" class="text-xs text-slate-400 hover:text-white">
            ← Back to List
        </a>
    </div>

    <form method="POST" action="{{ route('admin.news.update', $post->id) }}" enctype="multipart/form-data" class="gaming-card p-6 sm:p-8 space-y-6">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div>
            <label for="title" class="block text-xs uppercase font-bold text-slate-300 mb-1">Post Title</label>
            <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" required
                   class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500" />
            @error('title')
                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Slug -->
        <div>
            <label for="slug" class="block text-xs uppercase font-bold text-slate-300 mb-1">Custom URL Slug</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', $post->slug) }}" required
                   class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 font-mono focus:outline-none focus:border-purple-500" />
            @error('slug')
                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Excerpt -->
        <div>
            <label for="excerpt" class="block text-xs uppercase font-bold text-slate-300 mb-1">Short Excerpt / Summary</label>
            <textarea name="excerpt" id="excerpt" rows="2"
                      class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-purple-500">{{ old('excerpt', $post->excerpt) }}</textarea>
            @error('excerpt')
                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Media Management Section -->
        <div class="p-5 rounded-xl bg-slate-900/60 border border-slate-800/80 space-y-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-purple-300">
                Media Attachments (Images & Video)
            </h3>

            <!-- Existing Images Preview & Removal -->
            @if(!empty($post->images) && is_array($post->images))
                <div class="space-y-3">
                    <span class="text-xs font-semibold text-slate-300 block">
                        Current Attached Images ({{ count($post->images) }} / 5)
                    </span>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                        @foreach($post->images as $img)
                            <div class="relative group rounded-xl overflow-hidden border border-slate-700 bg-slate-950 p-1">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($img) }}" alt="News image" class="w-full h-20 object-cover rounded-lg" />
                                <label class="mt-1 flex items-center gap-1.5 text-[11px] text-rose-300 cursor-pointer">
                                    <input type="checkbox" name="remove_images[]" value="{{ $img }}" class="rounded bg-slate-900 border-slate-700 text-rose-500 h-3.5 w-3.5" />
                                    <span>Delete</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Upload More Images -->
            <div class="space-y-2">
                <label for="images" class="flex items-center gap-1.5 text-xs uppercase font-bold text-slate-200">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Add Images (Max 5 total, up to 10MB each)</span>
                </label>
                <input type="file" name="images[]" id="images" multiple accept="image/png,image/jpeg,image/webp,image/gif"
                       class="block w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-600/20 file:text-purple-300 hover:file:bg-purple-600/30 file:cursor-pointer border border-slate-800 rounded-xl p-2 bg-[#070A12]" />
                @error('images')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Existing Video & Replacement -->
            <div class="pt-4 border-t border-slate-800 space-y-3">
                <label for="video" class="flex items-center gap-1.5 text-xs uppercase font-bold text-slate-200">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span>Video Attachment (Max 100MB)</span>
                </label>

                @if($post->video_path)
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                        <video controls class="w-full max-h-48 rounded-lg bg-black">
                            <source src="{{ $post->video_url }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <label class="flex items-center gap-2 text-xs text-rose-300 cursor-pointer">
                            <input type="checkbox" name="remove_video" value="1" class="rounded bg-slate-900 border-slate-700 text-rose-500 h-4 w-4" />
                            <span>Remove current video</span>
                        </label>
                    </div>
                @endif

                <input type="file" name="video" id="video" accept="video/mp4,video/webm,video/quicktime,video/ogg"
                       class="block w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-cyan-600/20 file:text-cyan-300 hover:file:bg-cyan-600/30 file:cursor-pointer border border-slate-800 rounded-xl p-2 bg-[#070A12]" />
                <p class="text-[11px] text-slate-400">
                    Select a new video to replace the existing one, or upload a new one. Max 100MB.
                </p>
                @error('video')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Body -->
        <div>
            <label for="body" class="block text-xs uppercase font-bold text-slate-300 mb-1">Post Content (Body)</label>
            <textarea name="body" id="body" rows="12" required
                      class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-purple-500 font-sans">{{ old('body', $post->body) }}</textarea>
            @error('body')
                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Published Status Checkbox -->
        <div class="flex items-center gap-3 pt-2">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', $post->is_published) ? 'checked' : '' }}
                   class="rounded bg-slate-900 border-slate-800 text-purple-600 focus:ring-purple-500 h-4 w-4">
            <label for="is_published" class="text-sm font-semibold text-slate-200 cursor-pointer">
                Published to community website
            </label>
        </div>

        <!-- Submit Buttons -->
        <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
            <a href="{{ route('news.show', $post->slug) }}" target="_blank" class="text-xs text-purple-400 hover:underline">
                View on website ↗
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.news.index') }}" class="btn-secondary text-xs py-2.5 px-4">Cancel</a>
                <button type="submit" class="btn-primary text-xs py-2.5 px-6">
                    Update Post
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
