<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a new comment or reply.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id'   => ['required', 'exists:posts,id'],
            'parent_id' => ['nullable', 'exists:comments,id'],
            'body'      => ['required', 'string', 'max:3000'],
        ]);

        $validated['user_id'] = Auth::id();

        Comment::create($validated);

        return back()->with('status', 'Comment added.');
    }

    /**
     * Update an existing comment.
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $comment->update($validated);

        return back()->with('status', 'Comment updated successfully.');
    }

    /**
     * Delete a comment.
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('status', 'Comment deleted.');
    }
}
