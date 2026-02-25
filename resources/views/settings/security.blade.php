@extends('settings._layout')

@section('settings-content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Security Settings</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Update your password to keep your account secure.
        </p>
    </div>

    @if (session('status') === 'password-updated')
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-xl border border-green-100 dark:border-green-800 text-sm font-medium">
            Your password has been successfully updated.
        </div>
    @endif

    {{-- Update Password Form --}}
    <form method="POST" action="{{ route('password.update') }}" class="space-y-6 mb-12">
        @csrf
        @method('put')

        <div>
            <x-input 
                label="Current Password" 
                type="password" 
                name="current_password" 
                autocomplete="current-password" 
                required 
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input 
                label="New Password" 
                type="password" 
                name="password" 
                autocomplete="new-password" 
                required 
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input 
                label="Confirm New Password" 
                type="password" 
                name="password_confirmation" 
                autocomplete="new-password" 
                required 
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-6 flex justify-end border-t border-gray-100 dark:border-gray-800">
            <x-button type="submit" primary>
                Update Password
            </x-button>
        </div>
    </form>

    {{-- Danger Zone: Delete Account --}}
    <div class="pt-8 border-t-2 border-red-100 dark:border-red-900/30">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-red-600 dark:text-red-500">Danger Zone</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Once your account is deleted, all of its resources and data will be permanently deleted.
            </p>
        </div>

        <div x-data="{ confirmingUserDeletion: false }">
            <x-button type="button" danger @click="confirmingUserDeletion = true">
                Delete Account
            </x-button>

            {{-- Delete Account Modal --}}
            <x-modal name="confirm-user-deletion" x-show="confirmingUserDeletion" @close-modal.window="confirmingUserDeletion = false" maxWidth="md">
                <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                    @csrf
                    @method('delete')

                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3">
                        Are you sure you want to delete your account?
                    </h2>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
                    </p>

                    <div>
                        <x-input 
                            id="password" 
                            name="password" 
                            type="password" 
                            class="w-full" 
                            placeholder="Password" 
                            required 
                            autofocus 
                        />
                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-button type="button" secondary @click="confirmingUserDeletion = false">
                            Cancel
                        </x-button>

                        <x-button type="submit" danger>
                            Delete Account
                        </x-button>
                    </div>
                </form>
            </x-modal>
        </div>
    </div>
@endsection