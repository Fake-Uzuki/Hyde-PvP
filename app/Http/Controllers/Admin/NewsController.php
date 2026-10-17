<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $posts = NewsPost::with('user')
            ->withCount('comments')
            ->latest('created_at')
            ->paginate(10);

        return view('admin.news.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.news.create');
    }

    protected function resolvePost(NewsPost $post): NewsPost
    {
        if (! $post->exists) {
            $id = request()->route('post') ?? request()->route('news');
            if ($id) {
                return NewsPost::findOrFail($id);
            }
        }
        return $post;
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:news_posts,slug'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'is_published' => ['boolean'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'], // 10MB max per image
            'external_image_urls' => ['nullable', 'string'],
            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime,video/ogg', 'max:102400'], // 100MB max video
            'external_video_url' => ['nullable', 'string', 'url', 'max:500'],
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']) . '-' . Str::random(5);

        $imagePaths = [];
        // 1. Uploaded files
        if ($request->hasFile('images')) {
            $files = array_slice($request->file('images'), 0, 5);
            foreach ($files as $file) {
                $imagePaths[] = $file->store('news/images', 'public');
            }
        }
        // 2. External image URLs (optional)
        if ($request->filled('external_image_urls')) {
            $lines = preg_split('/[\r\n,]+/', $request->input('external_image_urls'));
            foreach ($lines as $url) {
                $url = trim($url);
                if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL) && count($imagePaths) < 5) {
                    $imagePaths[] = $url;
                }
            }
        }

        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('news/videos', 'public');
        } elseif ($request->filled('external_video_url')) {
            $videoPath = trim($request->input('external_video_url'));
        }

        NewsPost::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'] ?? null,
            'body' => $validated['body'],
            'images' => !empty($imagePaths) ? $imagePaths : null,
            'video_path' => $videoPath,
            'is_published' => $request->boolean('is_published', true),
            'published_at' => $request->boolean('is_published', true) ? now() : null,
        ]);

        return redirect()->route('admin.news.index')->with('status', 'News post published successfully.');
    }

    public function edit(NewsPost $post): View
    {
        $post = $this->resolvePost($post);
        return view('admin.news.edit', compact('post'));
    }

    public function update(Request $request, NewsPost $post): RedirectResponse
    {
        $post = $this->resolvePost($post);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:news_posts,slug,' . $post->id],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'is_published' => ['boolean'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
            'external_image_urls' => ['nullable', 'string'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['string'],
            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime,video/ogg', 'max:102400'],
            'external_video_url' => ['nullable', 'string', 'url', 'max:500'],
            'remove_video' => ['nullable', 'boolean'],
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : $post->slug;

        $wasPublished = $post->is_published;
        $isPublished = $request->boolean('is_published', true);

        $currentImages = is_array($post->images) ? $post->images : [];

        // Remove marked images
        if ($request->has('remove_images') && is_array($request->remove_images)) {
            foreach ($request->remove_images as $pathToRemove) {
                if (($key = array_search($pathToRemove, $currentImages, true)) !== false) {
                    unset($currentImages[$key]);
                    if (! Str::startsWith($pathToRemove, ['http://', 'https://'])) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($pathToRemove);
                    }
                }
            }
            $currentImages = array_values($currentImages);
        }

        // Add new images (respect max 5)
        if ($request->hasFile('images')) {
            $slots = max(0, 5 - count($currentImages));
            $newFiles = array_slice($request->file('images'), 0, $slots);
            foreach ($newFiles as $file) {
                $currentImages[] = $file->store('news/images', 'public');
            }
        }

        // Add external image URLs if provided
        if ($request->filled('external_image_urls')) {
            $lines = preg_split('/[\r\n,]+/', $request->input('external_image_urls'));
            foreach ($lines as $url) {
                $url = trim($url);
                if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL) && count($currentImages) < 5) {
                    $currentImages[] = $url;
                }
            }
        }

        $videoPath = $post->video_path;
        if ($request->boolean('remove_video')) {
            if ($videoPath && ! Str::startsWith($videoPath, ['http://', 'https://'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($videoPath);
            }
            $videoPath = null;
        }

        if ($request->hasFile('video')) {
            if ($videoPath && ! Str::startsWith($videoPath, ['http://', 'https://'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($videoPath);
            }
            $videoPath = $request->file('video')->store('news/videos', 'public');
        } elseif ($request->filled('external_video_url')) {
            if ($videoPath && ! Str::startsWith($videoPath, ['http://', 'https://'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($videoPath);
            }
            $videoPath = trim($request->input('external_video_url'));
        }

        $post->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'] ?? null,
            'body' => $validated['body'],
            'images' => !empty($currentImages) ? $currentImages : null,
            'video_path' => $videoPath,
            'is_published' => $isPublished,
            'published_at' => (! $wasPublished && $isPublished) ? now() : $post->published_at,
        ]);

        return redirect()->route('admin.news.index')->with('status', 'News post updated successfully.');
    }

    public function destroy(NewsPost $post): RedirectResponse
    {
        $post = $this->resolvePost($post);

        if (!empty($post->images) && is_array($post->images)) {
            foreach ($post->images as $img) {
                if (! Str::startsWith($img, ['http://', 'https://'])) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($img);
                }
            }
        }

        if (!empty($post->video_path) && ! Str::startsWith($post->video_path, ['http://', 'https://'])) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($post->video_path);
        }

        $post->delete();

        return redirect()->route('admin.news.index')->with('status', 'News post deleted successfully.');
    }
}
