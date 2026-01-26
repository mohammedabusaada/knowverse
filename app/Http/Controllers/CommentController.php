<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use App\Services\ActivityService;
use App\Enums\NotificationType;

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
    /**
     * Mark comment as best.
     */
    public function markAsBest(Comment $comment)
    {
        $this->authorize('markBest', $comment);

        $post = $comment->post;

        // Prevent re-selecting
        if ($post->best_comment_id === $comment->id) {
            return back();
        }

        // Set best comment
        $post->update([
            'best_comment_id' => $comment->id,
        ]);

        // --------------------------------------------------
        // Reputation
        // --------------------------------------------------

        // Comment owner (receiver)
        $comment->user->addReputation(
            'best_answer_received',
            null,
            $comment
        );

        // Post owner (actor)
        $post->user->addReputation(
            'best_answer_awarded',
            null,
            $comment
        );

        // --------------------------------------------------
        // Activity (public)
        // --------------------------------------------------
        ActivityService::bestAnswerSelected(
            $post->user,
            $comment
        );

        // --------------------------------------------------
        // Notification (ONLY receiver)
        // --------------------------------------------------
        app(NotificationService::class)->notify(
            recipient: $comment->user,
            type: NotificationType::BEST_ANSWER_RECEIVED,
            actor: $post->user,
            target: $comment
        );

        // Check if the current logged-in user ID matches the post owner ID to show the reputation toast
        if ($post->user_id === Auth::id()) {
            return back()->with([
                'status' => 'Best comment selected.',
                'reputation_delta' => config('reputation.points.best_answer_awarded', 2),
            ]);
        }

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

            // Compare IDs to ensure only the actor sees the reputation deduction toast
            if ($post->user_id === Auth::id()) {
                return back()->with([
                    'status' => 'Best comment removed.',
                    'reputation_delta' => -abs(config('reputation.points.best_answer_awarded', 2)),
                ]);
            }
        }

        return back()->with('status', 'Best comment removed.');
    }
}
