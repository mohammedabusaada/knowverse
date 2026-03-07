<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Post, User, Tag};

class SearchController extends Controller
{
    /**
     * Handles the primary search interface, distributing queries across 
     * Discussions, Scholars, and Topics (Tags).
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $type = $request->query('type', 'posts');
        
        // Handle array of tags from query string (used for deep filtering)
        $selectedTags = (array) $request->query('tags', []);

        // Fast-path return for completely empty queries
        if ($q === '' && empty($selectedTags)) {
            return view('search.results', [
                'q' => '',
                'type' => $type,
                'selectedTags' => [],
                'posts' => collect(),
                'users' => collect(),
                'tags'  => collect(),
                'counts' => ['posts' => 0, 'users' => 0, 'tags' => 0],
            ]);
        }

        // 1. Construct Base Queries
        $postsBase = Post::published();
        
        if ($q !== '') {
            $postsBase->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('body', 'like', "%{$q}%");
            });
        }

        if (!empty($selectedTags)) {
            $postsBase->whereHas('tags', function ($query) use ($selectedTags) {
                $query->whereIn('name', $selectedTags);
            });
        }

        $usersBase = User::where(function ($query) use ($q) {
            $query->where('username', 'like', "%{$q}%")
                  ->orWhere('full_name', 'like', "%{$q}%");
        });

        $tagsBase = Tag::where('name', 'like', "%{$q}%");

        // 2. Aggregate counts for Tab Badges
        $counts = [
            'posts' => $postsBase->count(),
            'users' => $usersBase->count(),
            'tags'  => $tagsBase->count(),
        ];

        // 3. Resolve specific paginated dataset based on active Tab ($type)
        $posts = $users = $tags = collect();

        if ($type === 'posts') {
            $posts = $postsBase->with('user')->latest()->paginate(10)->withQueryString();
        } elseif ($type === 'users') {
            $users = $usersBase->paginate(15)->withQueryString();
        } elseif ($type === 'tags') {
            $tags = $tagsBase->paginate(20)->withQueryString();
        }

        return view('search.results', compact('q', 'type', 'posts', 'users', 'tags', 'counts', 'selectedTags'));
    }

    /**
     * Provides a fast, lightweight JSON endpoint for Alpine.js Live Search suggestions.
     */
    public function suggestions(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        // Prevent heavy DB queries for single-character searches
        if (strlen($q) < 2) {
            return response()->json(['posts' => [], 'users' => [], 'tags' => []]);
        }

        return response()->json([
            'posts' => Post::published()
                ->where('title', 'like', "%{$q}%")
                ->limit(5)
                ->get(['id', 'title']),

            'users' => User::where('username', 'like', "%{$q}%")
                ->orWhere('full_name', 'like', "%{$q}%")
                ->limit(5)
                ->get(['id', 'username', 'full_name', 'profile_picture']), // Include avatar for UI

            'tags' => Tag::where('name', 'like', "%{$q}%")
                ->limit(5)
                ->get(['id', 'name', 'slug']), // Include slug for routing
        ]);
    }
}