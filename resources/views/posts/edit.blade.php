@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
@endpush

@section('content')

<div class="max-w-3xl mx-auto">

    <h1 class="text-3xl font-bold mb-6 dark:text-white">Edit Post</h1>

    <form action="{{ route('posts.update', $post) }}"
        method="POST"
        enctype="multipart/form-data"
        class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border border-gray-200 dark:border-gray-700"
        x-data="{
              imagePreview: null,
              currentImage: '{{ $post->image ? asset("storage/" . $post->image) : "" }}'
          }">

        @csrf
        @method('PUT')

        <!-- Title -->
        <x-input
            label="Title"
            name="title"
            value="{{ old('title', $post->title) }}"
            required />

        <!-- Body (Markdown enabled) -->
        <x-textarea
            label="Body (Markdown supported)"
            name="body"
            id="markdown-editor"
            rows="12"
            required>{{ old('body', $post->body) }}</x-textarea>

        <!-- Current Image Preview -->
        @if ($post->image)
        <div class="mt-6">
            <p class="text-gray-700 dark:text-gray-300 font-semibold mb-2">
                Current Image
            </p>
            <img :src="currentImage"
                class="w-48 rounded-lg shadow border border-gray-300 dark:border-gray-700">
        </div>
        @endif

        <!-- Replace Image -->
        <div class="mt-6">
            <x-input-file
                label="Replace Image (optional)"
                name="image"
                accept="image/*"
                @change="imagePreview = URL.createObjectURL($event.target.files[0])" />

            <template x-if="imagePreview">
                <img :src="imagePreview" class="mt-4 w-48 rounded-lg shadow">
            </template>

            @error('image')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex justify-between items-center mt-8">

            <!-- Update Button -->
            <x-button primary type="submit">Update Post</x-button>

        </div>

    </form> <!-- CLOSE UPDATE FORM HERE -->

    @can('delete', $post)
    <!-- Delete Form OUTSIDE the update form -->
    <form action="{{ route('posts.destroy', $post) }}"
        method="POST"
        class="mt-4"
        onsubmit="return confirm('Are you sure you want to delete this post?');">
        @csrf
        @method('DELETE')

        <x-button danger>Delete</x-button>
    </form>
    @endcan

    </form>

</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new EasyMDE({
            element: document.getElementById("markdown-editor"),
            spellChecker: false,
            autofocus: true,
            autosave: {
                enabled: true,
                uniqueId: "post_edit_{{ $post->id }}",
                delay: 800,
            },
        });
    });
</script>
@endpush