@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
@endpush

@section('content')

<div class="max-w-3xl mx-auto">

    <h1 class="text-3xl font-bold mb-6 dark:text-white">Create New Post</h1>

    <form action="{{ route('posts.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border border-gray-200 dark:border-gray-700"
        x-data="{ imagePreview: null }">

        @csrf

        <!-- Title -->
        <x-input
            label="Title"
            name="title"
            value="{{ old('title') }}"
            required />

        <!-- Body (Markdown) -->
        <x-textarea
            label="Body (Markdown supported)"
            name="body"
            id="markdown-editor"
            rows="10"
            required>{{ old('body') }}</x-textarea>

        <!-- Image Upload -->
        <div class="mt-6">
            <x-input-file
                label="Image (optional)"
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

        <!-- Submit -->
        <div class="flex justify-end mt-6">
            <x-button primary>Publish</x-button>
        </div>

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
                uniqueId: "post_create_{{ auth()->id() }}",
                delay: 800,
            },
        });
    });
</script>
@endpush