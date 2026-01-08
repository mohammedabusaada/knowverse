<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserActivityController extends Controller
{
    /**
     * Display a user's activity feed.
     */
    public function index(Request $request, User $user)
    {
        // Profile visibility (already correct)
        $this->authorize('view', $user);

        $viewer = Auth::user();
        $type   = $request->query('type', 'all');

        $query = UserActivity::query()
            ->where('user_id', $user->id)
            ->with([
                'target' => function ($morph) {
                    $morph->morphWith([
                        \App\Models\Post::class    => ['user', 'tags'],
                        \App\Models\Comment::class => ['user', 'post'],
                    ]);
                }
            ]);

        // --------------------------------------------------
        // Filters (semantic grouping)
        // --------------------------------------------------
        match ($type) {
            'posts' => $query->whereIn('action', [
                'post_created',
                'comment_created',
                'best_answer_selected',
            ]),

            'votes' => $query->whereIn('action', [
                'vote_up',
                'vote_down',
                'vote_removed',
            ]),

            'reputation' => $query->where('action', 'reputation_changed'),

            default => null,
        };

        // --------------------------------------------------
        // Fetch & apply visibility rules
        // --------------------------------------------------
        $activities = $query
            ->latest('created_at')
            ->get()
            ->filter(fn ($activity) =>
                $viewer
                    ? $viewer->can('view', $activity)
                    : app(\App\Policies\UserActivityPolicy::class)
                        ->view(null, $activity)
            );

        // --------------------------------------------------
        // Manual pagination (after filtering)
        // --------------------------------------------------
        $perPage = 25;
        $page    = request()->integer('page', 1);

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $activities->forPage($page, $perPage),
            $activities->count(),
            $perPage,
            $page,
            [
                'path'  => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('activity.index', [
            'user'       => $user,
            'activities' => $paginated,
            'type'       => $type,
        ]);
    }
}
