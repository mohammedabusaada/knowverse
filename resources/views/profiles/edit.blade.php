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
            Update your personal information and profile details
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
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Personal Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                Markdown is supported
            </p>

            <x-textarea
                name="bio"
                id="bio-editor"
                rows="6"
            >{{ old('bio', $user->bio) }}</x-textarea>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
            <x-button primary>
                Save changes
            </x-button>
        </div>

    </form>

</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new EasyMDE({
            element: document.getElementById("bio-editor"),
            spellChecker: false,
            autosave: {
                enabled: true,
                uniqueId: "profile_bio_{{ auth()->id() }}",
                delay: 500,
            },
            toolbar: [
                "bold", "italic", "heading", "|",
                "unordered-list", "ordered-list", "|",
                "link", "preview"
            ],
        });
    });
</script>
@endpush
