<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Rules\CleanContent;
use Illuminate\Support\Facades\Storage;
use App\Jobs\NotifyFollowersOnNewPost;

class PostController extends Controller
{
    /**
     * Retrieve a filtered and paginated feed of published discussions.
     */
    public function index(Request $request)
    {
        // Normalize tag input to handle both single strings and arrays
        $selectedTags = (array) $request->get('tags', []);

        $posts = Post::with(['user' => function ($q) {
                // Optimization: Eager load counts for Author Hover Card data
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

    /**
     * Show the formal interface to draft a new scholarly post.
     */
    public function create()
    {
        $tags = Tag::all();
        return view('posts.create', compact('tags'));
    }

    /**
     * Validate and persist a new scholarly discussion into the platform.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => ['required', 'string', 'max:255', new CleanContent],
            'body'    => ['required', 'string', new CleanContent],
            'image'   => ['nullable', 'image', 'max:4096'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status']  = Post::STATUS_PUBLISHED;

        // Handle cover image upload if provided during the initial publication
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('post_images', 'public');
        }

        $post = Post::create($validated);
        
        // Sync Many-to-Many tag relationships
        $post->tags()->sync($request->tag_ids ?? []);

        /** * Dispatch real-time notifications to followers via background workers (Queues).
         * This prevents the main HTTP thread from stalling during mass-dispatch.
         */
        NotifyFollowersOnNewPost::dispatch($post, $request->tag_ids ?? []);

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Discussion published successfully.');
    }

    /**
     * Display a comprehensive view of a single discussion thread.
     */
    public function show(Post $post)
    {
        $post->incrementViewCount();

        // Comprehensive eager loading to eliminate N+1 issues in the UI
        $post->load([
            'user' => function ($q) {
                $q->withCount(['posts', 'followers']);
            },
            'tags',
            'comments.user',
            'comments.replies.user',
        ]);

        // Prioritize the 'Best Answer' to be the first in the comment sequence
        $sortedComments = $post->comments->sortByDesc(
            fn($comment) => $comment->id === $post->best_comment_id
        );

        return view('posts.show', [
            'post' => $post,
            'comments' => $sortedComments,
        ]);
    }

    /**
     * Provide the interface for authors to refine their existing discussions.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        $tags = Tag::all();

        return view('posts.edit', compact('post', 'tags'));
    }

    /**
     * Apply verified updates to an existing discussion and manage assets.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title'   => ['required', 'string', 'max:255', new CleanContent],
            'body'    => ['required', 'string', new CleanContent],
            'image'   => ['nullable', 'image', 'max:4096'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ]);

        /**
         * 1. If a new image is uploaded, purge the old asset and store the new one.
         * 2. If the 'remove_image' flag is present and no new file is provided, purge the asset.
         */
        if ($request->hasFile('image')) {
            // Clean up old image asset if it exists to free up space
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }

            $validated['image'] = $request->file('image')->store('post_images', 'public');
        }elseif ($request->boolean('remove_image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
                $validated['image'] = null;
            }
        }

        $post->update($validated);
        $post->tags()->sync($request->tag_ids ?? []);

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Discussion refined successfully.');
    }

    /**
     * Execute a Soft-Delete on a post. 
     * NOTE: We keep the physical image to allow for record restoration (Restore).
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        // We do NOT delete the Storage file here because this is a SoftDelete.
        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('status', 'Discussion moved to trash/archives.');
    }
}