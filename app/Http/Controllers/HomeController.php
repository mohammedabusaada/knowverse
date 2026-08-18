<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Models\Comment;

class HomeController extends Controller
{
    public function index()
    {
        return view('home.index', [
            'recentPosts' => Post::with(['user', 'tags'])
                ->withCount('comments')
                ->latest()
                ->take(6)
                ->get(),

            'popularTags' => Tag::withCount('posts')
                ->orderByDesc('posts_count')
                ->take(10)
                ->get(),

            // Right-rail data
            'topScholars' => User::whereNull('banned_at')
                ->orderByDesc('reputation_points')
                ->take(5)
                ->get(),

            'stats' => [
                'discussions' => Post::count(),
                'scholars'    => User::whereNull('banned_at')->count(),
                'comments'    => Comment::count(),
            ],
        ]);
    }
}
