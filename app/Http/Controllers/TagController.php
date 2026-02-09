<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Post;
use Illuminate\Support\Str;

class TagController extends Controller
{
    // GET ALL TAGS
    public function index()
    {
        $tags = Tag::orderBy('name')->withCount('posts')->paginate(24);
        
        // Fetch recommended tags for the sidebar
        $recommendedTags = $this->getRecommendedTags();

        return view('tags.index', compact('tags', 'recommendedTags'));
    }

    /**
     * Display the specified tag's feed.
     */
    public function show(Tag $tag)
    {
        // Fetch posts associated with this tag, paginated for performance
        $posts = $tag->posts()
            ->with(['user', 'tags']) // Eager load for better performance
            ->latest()
            ->paginate(15);

        // If user is logged in, we check if they follow this tag
        $isFollowing = Auth::check() 
        ? Auth::user()->followedTags()->where('tag_id', $tag->id)->exists() 
        : false;

        // Fetch recommended tags for the sidebar
        $recommendedTags = $this->getRecommendedTags();

        return view('tags.show', compact('tag', 'posts', 'isFollowing', 'recommendedTags'));
    }

    // CREATE TAG (ADMIN)
    public function store(Request $request)
    {
        $this->authorize('admin-only');

        $request->validate([
            'name' => 'required|unique:tags,name'
        ]);

        return Tag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);
    }

    // UPDATE TAG (ADMIN)
    public function update(Request $request, Tag $tag)
    {
        $this->authorize('admin-only');

        $request->validate([
            'name' => "required|unique:tags,name,{$tag->id}"
        ]);

        $tag->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return $tag;
    }

    // DELETE TAG (ADMIN)
    public function destroy(Tag $tag)
    {
        $this->authorize('admin-only');

        $tag->delete();
        return response()->json(['message' => 'Tag deleted']);
    }

    // AJAX/API: For search suggestions
    public function search(Request $request)
    {
        $query = $request->query('q', '');
        return Tag::where('name', 'LIKE', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    // ATTACH TAGS TO POST
    public function attachTags(Request $request, Post $post)
    {
        $this->authorize('update', $post);
        $request->validate([
            'tag_ids'   => ['required', 'array', 'max:5'],
            'tag_ids.*' => 'exists:tags,id'
        ]);

        $post->tags()->sync($request->tag_ids);

        return $post->load('tags');
    }

    // FOLLOWERS LIST
    // ========================
    public function followers(Tag $tag)
    {
        $followers = $tag->followers()->paginate(20);
        return view('tags.followers', compact('tag', 'followers'));
    }

    /**
     * Helper to follow a tag
     */
    public function follow(Tag $tag)
    {
        Auth::user()->followedTags()->syncWithoutDetaching($tag->id);
        return response()->json(['success' => true]);
    }

    /**
     * Helper to unfollow a tag
     */
    public function unfollow(Tag $tag)
    {
        Auth::user()->followedTags()->detach($tag->id);
        return response()->json(['success' => true]);
    }

    /**
     * Private helper to get tags the user doesn't follow yet
     */
    private function getRecommendedTags()
    {
        return Tag::withCount('posts')
            ->when(Auth::check(), function ($query) {
                $query->whereDoesntHave('followers', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            })
            ->orderBy('posts_count', 'desc')
            ->take(5)
            ->get();
    }
}