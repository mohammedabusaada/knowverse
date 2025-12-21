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

        $comment = Comment::create($validated);

        // ★ Give reputation for writing a comment
        $comment->user->addReputation('comment_created', null, $comment);

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

    /**
     * Mark comment as best.
     */
    public function markAsBest(Comment $comment)
    {
        $this->authorize('markBest', $comment);

        $post = $comment->post;

        // Update post's best comment
        $post->update([
            'best_comment_id' => $comment->id,
        ]);

        // ★ Reputation logic
        // Awarded to comment author (best answer received)
        $comment->user->addReputation('best_answer_received', null, $comment);

        // Awarded to post author (best answer awarded)
        $post->user->addReputation('best_answer_awarded', null, $comment);

        return back()->with('status', 'Best comment selected.');
    }

    public function unmarkBest(Comment $comment)
{
    $this->authorize('unmarkBest', $comment);

    $post = $comment->post;

    if ($post->best_comment_id === $comment->id) {

        // Undo rep for comment author
        $comment->user->removeReputation('best_answer_received', $comment);

        // Undo rep for post author
        $post->user->removeReputation('best_answer_awarded', $comment);

        // Remove best comment
        $post->update(['best_comment_id' => null]);
    }

    return back()->with('status', 'Best comment removed.');
}

}
