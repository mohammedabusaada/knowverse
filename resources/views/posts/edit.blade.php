@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
<style>
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

    <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data" 
          x-data="{ imagePreview: null, currentImage: '{{ $post->image ? asset("storage/" . $post->image) : "" }}' }">
        
        @csrf
        @method('PUT')

        {{-- Top Actions --}}
        <div class="flex items-center justify-between mb-12">
            <a href="{{ route('posts.show', $post) }}" class="text-sm font-bold text-gray-500 hover:text-black dark:hover:text-white transition">
                &larr; Cancel
            </a>
            
            <div class="flex items-center gap-3">
                <x-button primary type="submit" size="md" class="px-6">
                    Save Changes
                </x-button>
            </div>
        </div>

        {{-- Title Input --}}
        <div class="mb-8">
            <input 
                type="text" 
                name="title" 
                value="{{ old('title', $post->title) }}" 
                placeholder="Title" 
                required 
                class="w-full bg-transparent border-none text-4xl sm:text-5xl font-black text-black dark:text-white placeholder-gray-300 dark:placeholder-gray-700 focus:ring-0 p-0"
            />
            @error('title')
                <p class="text-red-500 text-sm mt-2 font-bold">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tags Selection --}}
        <div class="mb-10 border-b border-gray-200 dark:border-gray-800 pb-8">
            <x-tag-multiselect
                :options="$tags"
                :selected="old('tag_ids', $post->tags->pluck('id')->toArray())"
                max="5" 
            />
        </div>

        {{-- Markdown Body --}}
        <div class="mb-10">
            <textarea name="body" id="markdown-editor" required>{{ old('body', $post->body) }}</textarea>
            @error('body')
                <p class="text-red-500 text-sm mt-2 font-bold">{{ $message }}</p>
            @enderror
        </div>

        {{-- Cover Image --}}
        <div class="mb-12 border-t border-gray-200 dark:border-gray-800 pt-8 flex flex-col sm:flex-row items-start sm:items-center gap-8">
            <div>
                <label class="block text-sm font-bold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-widest">
                    Cover Image
                </label>
                <label class="cursor-pointer inline-flex items-center justify-center px-4 py-2 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl hover:border-black dark:hover:border-white transition group">
                    <span class="text-sm font-bold text-gray-500 group-hover:text-black dark:group-hover:text-white">Replace Image</span>
                    <input type="file" name="image" accept="image/*" class="hidden" @change="imagePreview = URL.createObjectURL($event.target.files[0])" />
                </label>
                @error('image')
                    <p class="text-red-500 text-sm mt-2 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-4">
                <template x-if="currentImage && !imagePreview">
                    <img :src="currentImage" class="h-24 w-auto rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 opacity-50">
                </template>
                
                <template x-if="imagePreview">
                    <div class="flex items-center gap-4">
                        <span class="text-gray-400">&rarr;</span>
                        <img :src="imagePreview" class="h-24 w-auto rounded-lg shadow-md border-2 border-black dark:border-white">
                    </div>
                </template>
            </div>
        </div>
    </form>

    {{-- Danger Zone (Delete) --}}
    @can('delete', $post)
        <div class="border-t-2 border-red-100 dark:border-red-900/30 pt-8 mt-12">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-red-600 dark:text-red-500">Danger Zone</h3>
                    <p class="text-sm text-gray-500 mt-1">Permanently delete this post and all its comments.</p>
                </div>
                <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                    @csrf @method('DELETE')
                    <x-button danger type="submit">Delete Post</x-button>
                </form>
            </div>
        </div>
    @endcan

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new EasyMDE({
            element: document.getElementById("markdown-editor"),
            spellChecker: false,
            status: false,
        });
    });
</script>
@endpush