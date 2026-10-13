@extends('admin.layout', ['title' => 'Create Announcement'])

@section('admin_content')
<div class="max-w-4xl space-y-6">

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-white font-gaming">New Announcement</h2>
        <a href="{{ route('admin.news.index') }}" class="text-xs text-slate-400 hover:text-white">
            ← Back to List
        </a>
    </div>

    <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data" class="gaming-card p-6 sm:p-8 space-y-6">
        @csrf

        <!-- Title -->
        <div>
            <label for="title" class="block text-xs uppercase font-bold text-slate-300 mb-1">Post Title</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="e.g. Bedrock Season 4 Launch & Combat Balance"
                   class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500" />
            @error('title')
                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Slug (Optional) -->
        <div>
            <label for="slug" class="block text-xs uppercase font-bold text-slate-300 mb-1">Custom URL Slug (Optional)</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug') }}" placeholder="leave blank to auto-generate"
                   class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 font-mono focus:outline-none focus:border-purple-500" />
            <p class="text-[11px] text-slate-500 mt-1">If left empty, a slug will be generated automatically from the title.</p>
            @error('slug')
                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Excerpt -->
        <div>
            <label for="excerpt" class="block text-xs uppercase font-bold text-slate-300 mb-1">Short Excerpt / Summary</label>
            <textarea name="excerpt" id="excerpt" rows="2" placeholder="Brief summary displayed on cards and search results..."
                      class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-purple-500">{{ old('excerpt') }}</textarea>
            @error('excerpt')
                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Media Uploads (Images & Video) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 rounded-xl bg-slate-900/60 border border-slate-800/80">
            <!-- Images Upload (1 - 5 images, max 10MB each) -->
            <div class="space-y-2">
                <label for="images" class="block text-xs uppercase font-bold text-slate-200">
                    📸 Images (1 to 5 images)
                </label>
                <input type="file" name="images[]" id="images" multiple accept="image/png,image/jpeg,image/webp,image/gif"
                       class="block w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-600/20 file:text-purple-300 hover:file:bg-purple-600/30 file:cursor-pointer border border-slate-800 rounded-xl p-2 bg-[#070A12]" />
                <p class="text-[11px] text-slate-400">
                    Max <strong>5 images</strong>, up to <strong>10MB</strong> per image (PNG, JPG, WEBP, GIF).
                </p>
                @error('images')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Video Upload (Max 100MB) -->
            <div class="space-y-2">
                <label for="video" class="block text-xs uppercase font-bold text-slate-200">
                    🎥 Video Highlight (Optional)
                </label>
                <input type="file" name="video" id="video" accept="video/mp4,video/webm,video/quicktime,video/ogg"
                       class="block w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-cyan-600/20 file:text-cyan-300 hover:file:bg-cyan-600/30 file:cursor-pointer border border-slate-800 rounded-xl p-2 bg-[#070A12]" />
                <p class="text-[11px] text-slate-400">
                    Max <strong>1 video</strong>, up to <strong>100MB</strong> (MP4, WebM).
                </p>
                @error('video')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Body -->
        <div>
            <label for="body" class="block text-xs uppercase font-bold text-slate-300 mb-1">Post Content (Body)</label>
            <textarea name="body" id="body" rows="12" required placeholder="Write the full announcement here..."
                      class="w-full bg-[#070A12] border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-purple-500 font-sans">{{ old('body') }}</textarea>
            @error('body')
                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Published Status Checkbox -->
        <div class="flex items-center gap-3 pt-2">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', '1') == '1' ? 'checked' : '' }}
                   class="rounded bg-slate-900 border-slate-800 text-purple-600 focus:ring-purple-500 h-4 w-4">
            <label for="is_published" class="text-sm font-semibold text-slate-200 cursor-pointer">
                Publish immediately to the community website
            </label>
        </div>

        <!-- Submit Buttons -->
        <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
            <a href="{{ route('admin.news.index') }}" class="btn-secondary text-xs py-2.5 px-4">Cancel</a>
            <button type="submit" class="btn-primary text-xs py-2.5 px-6">
                Publish Announcement
            </button>
        </div>
    </form>

</div>
@endsection
