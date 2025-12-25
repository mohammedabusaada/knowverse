<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Tag;

class SearchController extends Controller
{
    /**
     * Main search results page.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $type = $request->query('type', 'posts');
        // Handle array of tags from query string
        $selectedTags = (array) $request->query('tags', []);

        // Initial state for empty search (unless tags are selected)
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

        // 1. Define Base Queries
        $postsBase = Post::published();
        
        // Filter by Keyword if present
        if ($q !== '') {
            $postsBase->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('body', 'like', "%{$q}%");
            });
        }

        // Filter by Tags if present
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

        // 2. Calculate Counts
        $counts = [
            'posts' => $postsBase->count(),
            'users' => $usersBase->count(),
            'tags'  => $tagsBase->count(),
        ];

        // 3. Load Paginated Data
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
     * Live search suggestions (JSON for Alpine.js/Search Bar).
     */
    public function suggestions(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

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
                ->get(['id', 'username', 'full_name']),

            'tags' => Tag::where('name', 'like', "%{$q}%")
                ->limit(5)
                ->get(['id', 'name']),
        ]);
    }
}