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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:news_posts,slug'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'is_published' => ['boolean'],
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']) . '-' . Str::random(5);

        NewsPost::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'] ?? null,
            'body' => $validated['body'],
            'is_published' => $request->boolean('is_published', true),
            'published_at' => $request->boolean('is_published', true) ? now() : null,
        ]);

        return redirect()->route('admin.news.index')->with('status', 'News post published successfully.');
    }

    public function edit(NewsPost $post): View
    {
        return view('admin.news.edit', compact('post'));
    }

    public function update(Request $request, NewsPost $post): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:news_posts,slug,' . $post->id],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'is_published' => ['boolean'],
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : $post->slug;

        $wasPublished = $post->is_published;
        $isPublished = $request->boolean('is_published', true);

        $post->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'] ?? null,
            'body' => $validated['body'],
            'is_published' => $isPublished,
            'published_at' => (! $wasPublished && $isPublished) ? now() : $post->published_at,
        ]);

        return redirect()->route('admin.news.index')->with('status', 'News post updated successfully.');
    }

    public function destroy(NewsPost $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.news.index')->with('status', 'News post deleted successfully.');
    }
}
