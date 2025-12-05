<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $tagIds = $request->query('tag_ids');

        $posts = Post::with('user')
            ->published()
            ->filterByTags($tagIds)
            ->latest()
            ->paginate(10);

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
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
            $validated['image'] = $request->file('image')
                ->store('post_images', 'public');
        }

        $post = Post::create($validated);

        // Attach tags
        $post->tags()->sync($request->tag_ids ?? []);

        // ★ Give reputation for writing a post
        $post->user->addReputation('post_created', null, $post);


        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Post created successfully.');
    }

    public function show(Post $post)
    {
        $post->incrementViewCount();

        $post->load([
            'user',
            'tags',
            'comments.user',
            'comments.replies.user',
        ]);

        // ★ Sort comments: best comment first
        $sortedComments = $post->comments->sortByDesc(
            fn ($comment) => $comment->id === $post->best_comment_id
        );

        return view('posts.show', [
            'post'      => $post,
            'comments'  => $sortedComments,
        ]);
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        return view('posts.edit', compact('post'));
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
            $validated['image'] = $request->file('image')
                ->store('post_images', 'public');
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
