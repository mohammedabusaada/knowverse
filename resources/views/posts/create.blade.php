@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
<style>
    /* Clean up the Markdown editor to fit the Monochrome theme */
    .editor-toolbar { border: none !important; border-bottom: 1px solid #e5e7eb !important; opacity: 0.7; transition: opacity 0.2s; }
    .dark .editor-toolbar { border-bottom-color: #374151 !important; }
    .editor-toolbar:hover { opacity: 1; }
    .CodeMirror { border: none !important; padding: 0 !important; font-size: 1.1rem; line-height: 1.7; }
    .CodeMirror-scroll { min-height: 400px; }
    .dark .CodeMirror { color: #f3f4f6; background: transparent; }
    .dark .editor-toolbar a { color: #9ca3af !important; }
    .dark .editor-toolbar a:hover, .dark .editor-toolbar a.active { color: #fff !important; background: #374151 !important; border-color: #374151 !important; }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" x-data="{ imagePreview: null }">
        @csrf

        {{-- Top Actions --}}
        <div class="flex items-center justify-between mb-12">
            <a href="{{ route('posts.index') }}" class="text-sm font-bold text-gray-500 hover:text-black dark:hover:text-white transition">
                &larr; Cancel
            </a>
            <x-button primary type="submit" size="lg" class="px-8 shadow-xl">
                Publish
            </x-button>
        </div>

        {{-- Title Input (Huge and borderless) --}}
        <div class="mb-8">
            <input 
                type="text" 
                name="title" 
                value="{{ old('title') }}" 
                placeholder="Title" 
                required 
                class="w-full bg-transparent border-none text-4xl sm:text-5xl font-black text-black dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:ring-0 p-0"
            />
            @error('title')
                <p class="text-red-500 text-sm mt-2 font-bold">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tags Selection (Simplified) --}}
        <div class="mb-10 border-b border-gray-200 dark:border-gray-800 pb-8">
            <x-tag-multiselect
                :options="$tags"
                :selected="old('tag_ids', [])"
                max="5" 
                placeholder="Add up to 5 tags..."
            />
        </div>

        {{-- Markdown Body --}}
        <div class="mb-10">
            <textarea name="body" id="markdown-editor" required>{{ old('body') }}</textarea>
            @error('body')
                <p class="text-red-500 text-sm mt-2 font-bold">{{ $message }}</p>
            @enderror
        </div>

        {{-- Cover Image --}}
        <div class="mb-12 border-t border-gray-200 dark:border-gray-800 pt-8">
            <label class="block text-sm font-bold text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-widest">
                Cover Image (Optional)
            </label>
            
            <div class="flex items-center gap-6">
                <label class="cursor-pointer inline-flex items-center justify-center px-4 py-2 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl hover:border-black dark:hover:border-white transition group">
                    <span class="text-sm font-bold text-gray-500 group-hover:text-black dark:group-hover:text-white">Choose File</span>
                    <input type="file" name="image" accept="image/*" class="hidden" @change="imagePreview = URL.createObjectURL($event.target.files[0])" />
                </label>

                <template x-if="imagePreview">
                    <img :src="imagePreview" class="h-20 w-auto rounded-lg shadow-sm border border-gray-200 dark:border-gray-800">
                </template>
            </div>
            
            @error('image')
                <p class="text-red-500 text-sm mt-2 font-bold">{{ $message }}</p>
            @enderror
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
            placeholder: "Write your story...",
            status: false,
            autosave: {
                enabled: true,
                uniqueId: "post_create_{{ auth()->id() }}",
                delay: 1000,
            },
        });
    });
</script>
@endpush