<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    /**
     * Handles asynchronous image uploads originating from the Markdown editor.
     * Generates cryptographically secure filenames to prevent directory traversal and file collisions.
     */
    public function store(Request $request)
    {
        try {
            // 1. Enforce strict MIME type and size constraints
            $request->validate([
                'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            ]);

            $file = $request->file('image');
            
            // 2. Construct a deterministic yet randomized filename (e.g., post_1_abc123.png)
            $filename = 'post_' . Auth::id() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // 3. Persist the asset to the public storage disk
            $path = $file->storeAs('markdown_images', $filename, 'public');

            // 4. Return the asset's absolute URL for frontend rendering
            return response()->json([
                'url' => asset('storage/' . $path)
            ]);

        } catch (\Exception $e) {
            // Provide actionable error feedback for the frontend XHR handler
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}