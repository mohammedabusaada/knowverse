<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request)
    {
        // Allow 'tags' to be a single string (from a link) or an array (from checkboxes)
        $selectedTags = $request->get('tags', []);

        if (is_string($selectedTags)) {
            $selectedTags = [$selectedTags];
        }

        $posts = Post::with(['user' => function ($q) {
            // Optimization for the User Hover Card stats
            $q->withCount(['posts', 'followers']);
        }, 'tags'])
            ->published()
            ->when($selectedTags, function ($query) use ($selectedTags) {
                $query->whereHas('tags', function ($q) use ($selectedTags) {
                    $q->whereIn('name', $selectedTags);
                });
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        $tags = Tag::all();

        return view('posts.index', compact('posts', 'tags', 'selectedTags'));
    }

    public function create()
    {
        $tags = Tag::all();
        return view('posts.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body'  => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'tag_ids'   => ['array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status']  = Post::STATUS_PUBLISHED;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('post_images', 'public');
        }

        $post = Post::create($validated);
        $post->tags()->sync($request->tag_ids ?? []);

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Post created successfully.');
    }

    public function show(Post $post)
    {
        $post->incrementViewCount();

        $post->load([
            'user' => function ($q) {
                $q->withCount(['posts', 'followers']);
            },
            'tags',
            'comments.user',
            'comments.replies.user',
        ]);

        $sortedComments = $post->comments->sortByDesc(
            fn($comment) => $comment->id === $post->best_comment_id
        );

        return view('posts.show', [
            'post' => $post,
            'comments' => $sortedComments,
        ]);
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        $tags = Tag::all();

        return view('posts.edit', compact('post', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body'  => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'tag_ids'   => ['array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('post_images', 'public');
        }

        $post->update($validated);
        $post->tags()->sync($request->tag_ids ?? []);

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('status', 'Post deleted.');
    }
}
