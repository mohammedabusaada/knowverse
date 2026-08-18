<?php

namespace App\Http\Controllers;

use App\Enums\NotificationType;
use App\Models\Comment;
use App\Rules\CleanContent;
use App\Services\ActivityService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Persists a new comment to a discussion.
     * Enforces strict content sanitization via the CleanContent rule to mitigate XSS vulnerabilities.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => ['required', 'exists:posts,id'],
            'parent_id' => ['nullable', 'exists:comments,id'],
            'body' => ['required', 'string', 'max:3000', new CleanContent],
        ]);

        $validated['user_id'] = Auth::id();
        Comment::create($validated);

        if ($request->filled('parent_id')) {
            return back()->with('status', 'Reply posted successfully.');
        }

        return back()->with('status', 'Comment successfully added to the discussion.');
    }

    /**
     * Updates an existing comment while verifying authorization policies.
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:3000', new CleanContent],
        ]);

        $comment->update($validated);

        $message = is_null($comment->parent_id) ? 'Comment refined successfully.' : 'Reply refined successfully.';

        return back()->with('status', $message);
    }

    /**
     * Executes a soft-delete on the comment, preserving the relational integrity of child replies.
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();
        $message = is_null($comment->parent_id) ? 'Comment moved to trash.' : 'Reply moved to trash.';

        return back()->with('status', $message);
    }

    /**
     * Elevates a comment to "Author's Pick".
     * Orchestrates a multi-step transaction: updating post state, awarding reputation points,
     * logging activity, and dispatching real-time notifications.
     */
    public function markAsBest(Comment $comment)
    {
        $this->authorize('markBest', $comment);

        $post = $comment->post;

        // Idempotency check: prevent duplicate awards if already marked as Author's Pick
        if ($post->best_comment_id === $comment->id) {
            return back();
        }

        $post->update(['best_comment_id' => $comment->id]);

        // Reward the ecosystem participants
        $comment->user->addReputation('authors_pick_received', null, $comment);
        $post->user->addReputation('authors_pick_awarded', null, $comment);

        ActivityService::authorsPickSelected($post->user, $comment);

        app(NotificationService::class)->notify(
            recipient: $comment->user,
            type: NotificationType::AUTHORS_PICK_RECEIVED,
            actor: $post->user,
            target: $comment
        );

        return back()->with([
            'status' => 'Comment successfully accepted as the Author\'s Pick.',
            'reputation_delta' => config('reputation.points.authors_pick_awarded', 2),
        ]);
    }

    /**
     * Reverses the "Author's Pick" designation.
     * Executes a reputation rollback to maintain ledger accuracy.
     */
    public function unmarkBest(Comment $comment)
    {
        $this->authorize('unmarkBest', $comment);

        $post = $comment->post;

        if ($post->best_comment_id === $comment->id) {
            $comment->user->removeReputation('authors_pick_received', $comment);
            $post->user->removeReputation('authors_pick_awarded', $comment);
            $post->update(['best_comment_id' => null]);

            return back()->with([
                'status' => 'Author\'s Pick designation retracted.',
                'reputation_delta' => -abs(config('reputation.points.authors_pick_awarded', 2)),
            ]);
        }

        return back();
    }
}
