<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;

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
        ]);
    }
}
