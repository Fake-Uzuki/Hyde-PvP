@extends('admin.layout', ['title' => 'News & Announcements'])

@section('admin_content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white font-gaming">Manage Announcements</h2>
            <p class="text-xs text-slate-400">Create, edit, and publish patch notes and server updates.</p>
        </div>
        <a href="{{ route('admin.news.create') }}" class="btn-primary text-xs py-2.5 px-5">
            + Create New Post
        </a>
    </div>

    @if($posts->isEmpty())
        <div class="gaming-card p-12 text-center text-slate-500">
            <p class="text-base font-semibold text-slate-300">No news posts found.</p>
            <p class="text-xs mt-1">Click the button above to publish your first announcement.</p>
        </div>
    @else
        <div class="gaming-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-5 font-bold">Title</th>
                            <th class="py-3 px-5 font-bold">Author</th>
                            <th class="py-3 px-5 font-bold">Status</th>
                            <th class="py-3 px-5 font-bold">Comments</th>
                            <th class="py-3 px-5 font-bold">Date</th>
                            <th class="py-3 px-5 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @foreach($posts as $post)
                            <tr class="hover:bg-slate-900/40 transition">
                                <td class="py-3 px-5 font-bold text-white max-w-xs truncate">
                                    <a href="{{ route('news.show', $post->slug) }}" target="_blank" class="hover:text-purple-300">
                                        {{ $post->title }}
                                    </a>
                                </td>
                                <td class="py-3 px-5 text-xs text-slate-300">
                                    {{ $post->user->name ?? 'Staff' }}
                                </td>
                                <td class="py-3 px-5">
                                    <span class="text-xs px-2.5 py-0.5 rounded font-mono {{ $post->is_published ? 'bg-emerald-950/60 text-emerald-400 border border-emerald-800/40' : 'bg-amber-950/60 text-amber-400 border border-amber-800/40' }}">
                                        {{ $post->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="py-3 px-5 text-xs text-slate-300 font-mono">
                                    {{ $post->comments_count }}
                                </td>
                                <td class="py-3 px-5 text-xs text-slate-400">
                                    {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                                </td>
                                <td class="py-3 px-5 text-right space-x-2">
                                    <a href="{{ route('admin.news.edit', $post->id) }}" class="text-xs text-purple-400 hover:text-purple-300 px-2.5 py-1 rounded bg-purple-950/40 border border-purple-800/40">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.news.destroy', $post->id) }}" class="inline-block" onsubmit="return confirm('Permanently delete this news post?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 px-2.5 py-1 rounded bg-rose-950/40 border border-rose-800/40">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pt-4">
            {{ $posts->links() }}
        </div>
    @endif

</div>
@endsection
