<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function vote(Request $request)
{
    $request->validate([
        'type'  => ['required', 'string'],   // post or comment
        'id'    => ['required', 'integer'],
        'value' => ['required', 'in:1,-1,0'], // allow unvote
    ]);

    $model = $request->type === 'post'
        ? \App\Models\Post::class
        : \App\Models\Comment::class;

    $target = $model::findOrFail($request->id);

    // Prevent voting on own content
    if ($target->user_id === Auth::id()) {
        return response()->json([
            'success' => false,
            'error' => "You cannot vote on your own content."
        ], 403);
    }

    // --------------------------
    // REMOVE VOTE if value = 0
    // --------------------------
    if ($request->value === 0) {

        $existing = Vote::where([
            'user_id' => Auth::id(),
            'target_id' => $target->id,
            'target_type' => $model,
        ])->first();

        if ($existing) {
            $existing->delete();  // triggers observer + updates score
        }
    }

    // --------------------------
    // CREATE or UPDATE vote
    // --------------------------
    else {
        Vote::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'target_id' => $target->id,
                'target_type' => $model,
            ],
            ['value' => $request->value]
        );
    }

    // Refresh updated upvote_count / downvote_count from DB
    $target->refresh();

    return response()->json([
        'success' => true,
        'upvotes' => $target->upvote_count,
        'downvotes' => $target->downvote_count,
        'score' => $target->upvote_count - $target->downvote_count,
        'user_vote' => Vote::where([
            'user_id' => Auth::id(),
            'target_id' => $target->id,
            'target_type' => $model
        ])->value('value')  // can be 1, -1, or null
    ]);
}


}
