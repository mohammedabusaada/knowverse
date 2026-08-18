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

        // 4b. Privilege Gate: downvoting is a reputation-gated participation privilege.
        //     Low-standing scholars may only upvote. (This is NOT RBAC authority.)
        if ($value === -1 && ! Auth::user()->hasPrivilege('downvote')) {
            return response()->json([
                'success' => false,
                'error'   => 'You need at least ' . config('reputation.privileges.downvote') . ' reputation points to downvote.',
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

        // 6. The Vote observer has already recalculated and persisted the aggregate
        //    counts as a side effect of the create/update/delete above. We only refresh
        //    to pull those committed values into this instance for the JSON response,
        //    avoiding a redundant second recount on the request path.
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