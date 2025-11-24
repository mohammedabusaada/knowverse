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
        return Tag::orderBy('name')->get();
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

    // SEARCH TAGS FOR AUTO COMPLETE
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
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:tags,id'
        ]);

        $post->tags()->sync($request->tag_ids);

        return $post->load('tags');
    }

    // FOLLOW TAG
    public function follow(Tag $tag)
    {
        $tag->followers()->syncWithoutDetaching([Auth::id()]);
        return response()->json(['message' => 'Tag followed']);
    }

    // UNFOLLOW TAG
    public function unfollow(Tag $tag)
    {
        $tag->followers()->detach([Auth::id()]);
        return response()->json(['message' => 'Tag unfollowed']);
}
}
