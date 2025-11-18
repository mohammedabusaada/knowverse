@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
@endpush

@section('content')

<div class="max-w-3xl mx-auto px-4 py-10">

    <h1 class="text-3xl font-bold mb-6 dark:text-white">
        Edit Profile
    </h1>

    <form action="{{ route('profile.update') }}"
        method="POST"
        enctype="multipart/form-data"
        class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border border-gray-200 dark:border-gray-700"
        x-data="{
              avatarPreview: '{{ $user->profile_picture_url }}',
              newAvatar: null
          }">

        @csrf
        @method('PUT')

        <!-- Profile Picture -->
        <div class="flex items-center gap-6 mb-6">

            <img :src="newAvatar ? URL.createObjectURL(newAvatar) : avatarPreview"
                class="w-24 h-24 rounded-full object-cover shadow border dark:border-gray-600">

            <div class="flex-1">

                <x-input-file
                    label="Change Profile Picture"
                    name="profile_picture"
                    accept="image/*"
                    @change="newAvatar = $event.target.files[0]" />

                @error('profile_picture')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror

            </div>

        </div>

        <!-- Full Name -->
        <x-input
            label="Full Name"
            name="full_name"
            value="{{ old('full_name', $user->full_name) }}"
            required />

        <!-- Academic Title -->
        <x-input
            label="Academic Title (optional)"
            name="academic_title"
            value="{{ old('academic_title', $user->academic_title) }}" />

        <!-- Bio (Markdown) -->
        <x-textarea
            label="Bio (Markdown supported)"
            name="bio"
            id="bio-editor"
            rows="6">{{ old('bio', $user->bio) }}</x-textarea>

        <!-- Save Button -->
        <div class="flex justify-end mt-6">
            <x-button primary>
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