@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
<style>
    /* Clean up the Markdown editor for Settings */
    .editor-toolbar { border: 2px solid #e5e7eb !important; border-bottom: 1px solid #e5e7eb !important; border-radius: 0.5rem 0.5rem 0 0 !important; opacity: 0.8; transition: opacity 0.2s; }
    .dark .editor-toolbar { border-color: #374151 !important; }
    .editor-toolbar:hover { opacity: 1; }
    .CodeMirror { border: 2px solid #e5e7eb !important; border-top: none !important; border-radius: 0 0 0.5rem 0.5rem !important; font-size: 0.875rem; line-height: 1.6; }
    .dark .CodeMirror { border-color: #374151 !important; color: #f3f4f6; background: #000; }
    .dark .editor-toolbar a { color: #9ca3af !important; }
    .dark .editor-toolbar a:hover, .dark .editor-toolbar a.active { color: #fff !important; background: #374151 !important; border-color: #374151 !important; }
</style>
@endpush

@section('content')

<div class="max-w-4xl mx-auto px-4 py-10">

    {{-- Page Header --}}
    <div class="mb-10">
        <h1 class="text-3xl md:text-4xl font-black tracking-tight text-black dark:text-white">
            Edit Profile
        </h1>
        <p class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">
            Update your personal information and privacy preferences.
        </p>
    </div>

    {{-- Form Card --}}
    <form
        action="{{ route('profile.update') }}"
        method="POST"
        enctype="multipart/form-data"
        x-data="{
            avatarPreview: '{{ $user->profile_picture_url }}',
            newAvatar: null
        }"
        class="bg-white dark:bg-black
               border-2 border-black dark:border-white
               rounded-2xl shadow-sm
               p-6 md:p-10 space-y-10"
    >

        @csrf
        @method('PUT')

        {{-- Profile Picture --}}
        <div>
            <h2 class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-6 border-b-2 border-gray-100 dark:border-gray-800 pb-2">
                Profile Picture
            </h2>

            <div class="flex items-center gap-8">
                <div class="relative group shrink-0">
                    <img
                        :src="newAvatar ? URL.createObjectURL(newAvatar) : avatarPreview"
                        class="w-24 h-24 rounded-full object-cover
                               border-4 border-gray-100 dark:border-gray-900
                               group-hover:border-black dark:group-hover:border-white
                               transition-all duration-300"
                    />
                </div>

                <div class="flex-1">
                    <label class="cursor-pointer inline-flex items-center justify-center px-4 py-2 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl hover:border-black dark:hover:border-white transition group mb-1">
                        <span class="text-sm font-bold text-gray-500 group-hover:text-black dark:group-hover:text-white">Choose New Image</span>
                        <input type="file" name="profile_picture" accept="image/*" class="hidden" @change="newAvatar = $event.target.files[0]" />
                    </label>
                    <p class="text-xs text-gray-400 mt-1">JPG, JPEG, PNG, or WEBP (Max: 2MB)</p>

                    @error('profile_picture')
                        <p class="text-red-500 font-bold text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Personal Information --}}
        <div>
            <h2 class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-6 border-b-2 border-gray-100 dark:border-gray-800 pb-2">
                Personal Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input
                    label="Username"
                    name="username"
                    value="{{ old('username', $user->username) }}"
                    required />

                <x-input
                    label="Full name"
                    name="full_name"
                    value="{{ old('full_name', $user->full_name) }}"
                    required />

                <x-input
                    label="Academic title (optional)"
                    name="academic_title"
                    value="{{ old('academic_title', $user->academic_title) }}" />
            </div>
        </div>

        {{-- Bio --}}
        <div>
            <h2 class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-6 border-b-2 border-gray-100 dark:border-gray-800 pb-2">
                Bio
            </h2>

            <textarea
                name="bio"
                id="bio-editor"
                rows="6"
            >{{ old('bio', $user->bio) }}</textarea>
        </div>

        {{-- Privacy Settings --}}
        <div class="bg-gray-50 dark:bg-black border-2 border-gray-200 dark:border-gray-800 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="pr-6">
                    <label for="public_follow_lists" class="text-sm font-bold text-black dark:text-white block mb-1">Public Follow Lists</label>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-relaxed">
                        Allow other users to see who you are following and who follows you. If disabled, these tabs will be hidden from your profile for others.
                    </p>
                </div>
                
                {{-- Toggle Switch (Monochrome) --}}
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="hidden" name="public_follow_lists" value="0">
                    <input type="checkbox" name="public_follow_lists" id="public_follow_lists" value="1" class="sr-only peer" {{ old('public_follow_lists', $user->public_follow_lists) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-800 
                                peer-checked:after:translate-x-full peer-checked:after:border-white 
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] 
                                after:bg-white after:border-gray-300 after:border after:rounded-full 
                                after:h-5 after:w-5 after:transition-all dark:border-gray-600 
                                peer-checked:bg-black dark:peer-checked:bg-white"></div>
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-4 pt-8 border-t-2 border-gray-100 dark:border-gray-800">
            <x-button href="{{ route('profile.show', $user->username) }}" secondary>
                Cancel
            </x-button>
            <x-button type="submit" primary>
                Save Changes
            </x-button>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const easyMDE = new EasyMDE({
            element: document.getElementById("bio-editor"),
            spellChecker: false,
            status: false,
            maxHeight: "250px",
            toolbar: [
                "bold", "italic", "heading", "|",
                "unordered-list", "ordered-list", "|",
                "link", "quote", "|",
                "preview"
            ],
        });
    });
</script>
@endpush