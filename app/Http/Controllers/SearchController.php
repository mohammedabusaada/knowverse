<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $qTrim = trim($q);

        if ($qTrim === '') {
            // show empty result page or redirect — we'll show empty with message
            $posts = collect();
            $users = collect();
            $tags  = collect();
            return view('search.results', compact('q', 'posts', 'users', 'tags'));
        }

        // posts: title and body (published only)
        $posts = Post::with('user')
            ->where(function ($query) use ($qTrim) {
                $query->where('title', 'like', '%' . $qTrim . '%')
                    ->orWhere('body', 'like', '%' . $qTrim . '%');
            })
            ->published()
            ->latest()
            ->paginate(10)
            ->appends(['q' => $qTrim]);

        // users
        $users = User::where(function ($query) use ($qTrim) {
            $query->where('username', 'like', '%' . $qTrim . '%')
                ->orWhere('full_name', 'like', '%' . $qTrim . '%');
        })
            ->limit(8)
            ->get();

        // tags
        $tags = Tag::where('name', 'like', '%' . $qTrim . '%')
            ->orderBy('name')
            ->limit(20)
            ->get();

        return view('search.results', compact('q', 'posts', 'users', 'tags'));
    }
}
