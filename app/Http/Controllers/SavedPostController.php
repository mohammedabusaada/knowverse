<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedPostController extends Controller
{
    /**
     * Toggle the saved status of a post.
     */
    public function toggle(Post $post)
    {
        $user = Auth::user();

        // Toggle the attachment
        $attaching = !$post->isSavedBy($user);

        if ($attaching) {
            $user->savedPosts()->attach($post->id);
            $message = 'Post saved to your collection.';
        } else {
            $user->savedPosts()->detach($post->id);
            $message = 'Post removed from your collection.';
        }

        // Return JSON for the AJAX call
        return response()->json([
            'saved' => $attaching,
            'message' => $message,
        ]);
    }
    public function index(){
    $user = Auth::user();

    $savedPosts = $user->savedPosts()
        ->with(['user', 'tags', 'bestComment'])
        ->latest()
        ->paginate(10);

    return view('saved-posts.index', compact('savedPosts'));
}
}
