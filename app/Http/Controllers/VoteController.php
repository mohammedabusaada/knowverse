<?php

namespace App\Http\Controllers;

use App\Models\{Vote, Post, Comment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    /**
     * Processes an asynchronous peer-review voting request.
     * Utilizes dynamic polymorphic mapping to evaluate both Discussions and Responses identically.
     */
    public function vote(Request $request)
    {
        // 1. Validate payload integrity
        $request->validate([
            'type'  => ['required', 'string', 'in:post,comment'],
            'id'    => ['required', 'integer'],
            'value' => ['required', 'in:1,-1,0'], 
        ]);

        // 2. Resolve the target entity dynamically based on type
        $model = $request->type === 'post' ? Post::class : Comment::class;
        $target = $model::findOrFail($request->id);
        $value = (int) $request->value;

        // 3. Extract the morph alias (e.g., 'post')
        $targetType = $target->getMorphClass();

        // 4. Governance Rule: Scholars cannot evaluate their own contributions
        if ($target->user_id === Auth::id()) {
            return response()->json([
                'success' => false,
                'error'   => "You cannot evaluate your own contributions."
            ], 403);
        }

        // 5. State Management: Remove vote (0) or upsert new value (1 / -1)
        if ($value === 0) {
            $vote = Vote::where([
                'user_id'     => Auth::id(),
                'target_id'   => $target->id,
                'target_type' => $targetType,
            ])->first();

            if ($vote) {
                $vote->delete(); 
            }
        } 
        else {
            Vote::updateOrCreate(
                [
                    'user_id'     => Auth::id(),
                    'target_id'   => $target->id,
                    'target_type' => $targetType,
                ],
                ['value' => $value]
            );
        }

        // 6. Force recalculation of aggregate metrics to avoid cache staleness
        $target->updateVoteCounts();
        $target->refresh();

        // 7. Return synchronous UI state to update Alpine.js bindings
        return response()->json([
            'success'   => true,
            'upvotes'   => $target->upvote_count,
            'downvotes' => $target->downvote_count,
            'score'     => $target->upvote_count - $target->downvote_count,
            'user_vote' => $value
        ]);
    }
}