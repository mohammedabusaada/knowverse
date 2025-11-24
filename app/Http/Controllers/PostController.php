<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * List all published posts.
     */
    public function index(Request $request)
    {
        $tagIds = $request->query('tag_ids'); // array of tag IDs
        $posts = Post::with('user')
            ->published()
            ->filterByTags($tagIds)   // ← Required by the sprint
            ->latest()
            ->paginate(10);

        return view('posts.index', compact('posts'));
    }

    /**
     * Show create post page.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store new post.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body'  => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            // Required for tag logic
            'tag_ids'   => ['array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status']  = Post::STATUS_PUBLISHED;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('post_images', 'public');
        }

        $post = Post::create($validated);

         // Attach tags (required by deliverables)
        $post->tags()->sync($request->tag_ids ?? []);

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Post created successfully.');
    }

    /**
     * Show a single post with all comments.
     */
    public function show(Post $post)
    {
        $post->incrementViewCount();

        $post->load([
            'user',
            'comments.user',
            'comments.replies.user',
            'tags'
        ]);

        return view('posts.show', compact('post'));
    }

    /**
     * Show edit page.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update a post.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body'  => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            // Required for tag logic
            'tag_ids'   => ['array'],
            'tag_ids.*' => ['integer', 'exists:tags,id']
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('post_images', 'public');
        }

        $post->update($validated);
        // Sync tags on update (required)
        $post->tags()->sync($request->tag_ids ?? []);

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Post updated successfully.');
    }

    /**
     * Soft delete post.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('status', 'Post deleted.');
    }
}
