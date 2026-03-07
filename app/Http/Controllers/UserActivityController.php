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

        $viewer = Auth::user();
        $type = $request->query('type', 'all');

        $query = UserActivity::forUser($user)
            ->withTargets()
            ->feed();

        match ($type) {
            'posts'    => $query->where('action', 'post_created'),
            'comments' => $query->where('action', 'comment_created'),
            default    => null,
        };

        // Secure Item-Level Filtering
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