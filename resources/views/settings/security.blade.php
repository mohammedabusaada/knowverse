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

    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
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

        <div class="pt-6 border-t border-gray-100 dark:border-gray-800 flex justify-end">
            <x-button type="submit" primary>
                Update Password
            </x-button>
        </div>
    </form>
@endsection