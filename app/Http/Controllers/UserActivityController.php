<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class UserActivityController extends Controller
{
    /**
     * Display a user's activity feed.
     */
    public function index(Request $request, User $user)
    {
        $this->authorize('view', $user);

        $viewer = Auth::user();
        $type = $request->query('type', 'all');

        // Start the query using our Model scopes
        $query = UserActivity::forUser($user)
            ->withTargets()
            ->feed();

        // Apply semantic filters
        match ($type) {
            'posts' => $query->whereIn('action', [
                'post_created', 
                'comment_created', 
                'best_answer_selected'
            ]),

            'votes' => $query->whereIn('action', [
                'vote_up', 
                'vote_down', 
                'vote_removed'
            ]),

            'reputation' => $query->where('action', 'reputation_changed'),

            default => null,
        };

        // Fetch and filter by visibility policy
        $activities = $query->get()->filter(function ($activity) use ($viewer) {
            return $viewer 
                ? $viewer->can('view', $activity) 
                : $activity->isPublic();
        });

        // Setup Manual Pagination
        $perPage = 20;
        $page = $request->integer('page', 1);

        $paginated = new LengthAwarePaginator(
            $activities->forPage($page, $perPage),
            $activities->count(),
            $perPage,
            $page,
            [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('activity.index', [
            'user'       => $user,
            'activities' => $paginated,
            'type'       => $type,
        ]);
    }
}