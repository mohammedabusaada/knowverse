@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
@endpush

@section('content')

<div class="max-w-4xl mx-auto px-4 py-10">

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">
            Edit Profile
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Update your personal information and privacy preferences
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
        class="bg-white dark:bg-gray-900
               border border-gray-200/80 dark:border-gray-700/70
               rounded-2xl
               shadow-lg shadow-gray-200/40 dark:shadow-black/40
               p-6 md:p-8 space-y-8"
    >

        @csrf
        @method('PUT')

        {{-- Profile Picture --}}
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Profile Picture
            </h2>

            <div class="flex items-center gap-6">
                <div class="relative group">
                    <img
                        :src="newAvatar ? URL.createObjectURL(newAvatar) : avatarPreview"
                        class="w-24 h-24 rounded-full object-cover
                               border-4 border-white dark:border-gray-800
                               shadow-md
                               transition-transform duration-300
                               group-hover:scale-105"
                    />
                    <div
                        class="absolute inset-0 rounded-full
                               bg-gradient-to-br from-blue-500/20 to-purple-500/10
                               opacity-0 group-hover:opacity-100
                               transition-opacity duration-300 pointer-events-none">
                    </div>
                </div>

                <div class="flex-1">
                    <x-input-file
                        label="Change profile picture"
                        name="profile_picture"
                        accept="image/*"
                        @change="newAvatar = $event.target.files[0]" />

                    @error('profile_picture')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Personal Information --}}
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-800 pb-2">
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
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                Bio
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Markdown is supported. Tell the community about your expertise.
            </p>

            <x-textarea
                name="bio"
                id="bio-editor"
                rows="6"
            >{{ old('bio', $user->bio) }}</x-textarea>
        </div>

        {{-- Privacy Settings --}}
        <div class="bg-gray-50 dark:bg-gray-800/40 p-6 rounded-xl border border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Privacy & Visibility
            </h2>
            
            <div class="flex items-center justify-between">
                <div class="pr-4">
                    <label for="public_follow_lists" class="font-medium text-gray-900 dark:text-white">Public Follow Lists</label>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Allow other users to see who you are following and who follows you. If disabled, these tabs will be hidden from your profile for others.
                    </p>
                </div>
                
                {{-- Toggle Switch --}}
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="public_follow_lists" value="0">
                    <input type="checkbox" name="public_follow_lists" id="public_follow_lists" value="1" class="sr-only peer" {{ old('public_follow_lists', $user->public_follow_lists) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
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
            maxHeight: "300px",
            toolbar: [
                "bold", "italic", "heading", "|",
                "unordered-list", "ordered-list", "|",
                "link", "quote", "|",
                "preview", "side-by-side", "fullscreen"
            ],
        });
    });
</script>
@endpush