<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentsController extends Controller
{
    public function index(Request $request): View
    {
        $query = Comment::with(['user', 'post'])->latest();

        if ($request->has('filter') && $request->filter === 'hidden') {
            $query->where('is_hidden', true);
        }

        $comments = $query->paginate(15);

        return view('admin.comments.index', compact('comments'));
    }

    public function toggle(Comment $comment): RedirectResponse
    {
        $comment->is_hidden = ! $comment->is_hidden;
        $comment->save();

        $status = $comment->is_hidden ? 'Comment has been hidden from public view.' : 'Comment has been made visible.';
        return back()->with('status', $status);
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('status', 'Comment deleted permanently.');
    }
}
