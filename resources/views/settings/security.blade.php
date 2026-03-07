@extends('settings._layout')

{{-- 
    Identity & Account Security Configuration
    -----------------------------------------
    This view manages sensitive user lifecycle events, password update, 
    and social privacy parameters using Alpine.js for reactive UI components.
--}}

@section('settings-content')
    <div class="mb-8 border-b border-rule pb-4">
        <h1 class="font-heading text-3xl font-bold text-ink">Settings & Privacy</h1>
        <p class="font-serif text-[15px] text-muted italic mt-2">
            Manage your password, privacy preferences, and account status.
        </p>
    </div>

    {{-- 
        Self-Dismissing Toast Notification
        ---------------------------------
        Utilizes Alpine.js to automatically clear status messages after a defined interval (4s).
    --}}
    @if (session('status'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 4000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             class="fixed bottom-5 right-5 z-[100]">
            <x-toast>{{ session('status') }}</x-toast>
        </div>
    @endif

    {{-- 
        1. Privacy Control Section
        --------------------------
        Synchronizes social visibility settings with the profile backend.
    --}}
    <div class="mb-12">
        <h2 class="text-[10px] font-mono font-bold uppercase tracking-[0.2em] text-muted mb-6 border-b border-rule pb-2">
            Privacy
        </h2>
        
        <form method="POST" action="{{ route('profile.update') }}" class="bg-aged/20 border border-rule p-6 rounded-sm">
            @csrf
            @method('PUT')
            
            {{-- Maintaining state for required identity fields during partial updates --}}
            <input type="hidden" name="username" value="{{ auth()->user()->username }}">
            <input type="hidden" name="full_name" value="{{ auth()->user()->full_name }}">

            <div class="flex items-center justify-between">
                <div class="pr-6">
                    <label for="public_follow_lists" class="text-base font-serif font-bold text-ink block mb-1">
                        Public Follow Lists
                    </label>
                    <p class="text-sm font-serif italic text-muted leading-relaxed">
                        Allow others to view your following and followers network.
                    </p>
                </div>
                
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="hidden" name="public_follow_lists" value="0">
                    <input type="checkbox" name="public_follow_lists" id="public_follow_lists" value="1" 
                           class="sr-only peer" {{ auth()->user()->public_follow_lists ? 'checked' : '' }} 
                           onchange="this.form.submit()">
                    <div class="w-11 h-6 bg-rule rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-paper after:border after:border-rule after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-ink"></div>
                </label>
            </div>
        </form>
    </div>

    {{-- 
        2. Credential Management Section
        -------------------------------
        Facilitates secure password updates with standard verification protocols.
    --}}
    <div class="mb-16">
        <h2 class="text-[10px] font-mono font-bold uppercase tracking-[0.2em] text-muted mb-6 border-b border-rule pb-2">
            Change Password
        </h2>
        <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            @method('put')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2 md:col-span-1">
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2 font-bold">Current Password</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-3 border border-rule bg-paper focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg rounded-sm" />
                    @error('current_password', 'updatePassword') <span class="text-xs font-mono text-accent-warm block mt-2">{{ $message }}</span> @enderror
                </div>

                <div class="hidden md:block"></div>

                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2 font-bold">New Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-rule bg-paper focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg rounded-sm" />
                    @error('password', 'updatePassword') <span class="text-xs font-mono text-accent-warm block mt-2">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2 font-bold">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 border border-rule bg-paper focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg rounded-sm" />
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="px-8 py-3 bg-ink text-paper font-mono text-[10px] uppercase tracking-widest hover:opacity-80 shadow-sm transition-opacity">
                    Update Password
                </button>
            </div>
        </form>
    </div>

    {{-- 
        3. Account Lifecycle Section
        ----------------------------
        Handles destructive and reversible account status transitions.
    --}}
    <div class="pt-8 border-t border-rule mt-12">
        <h2 class="text-[10px] font-mono font-bold uppercase tracking-[0.2em] text-muted mb-6 border-b border-rule pb-2">
            Account Status
        </h2>

        {{-- Account Deactivation (Soft Delete / Reversible) --}}
        <div class="mb-10 bg-aged/30 border border-rule p-6 rounded-sm">
            <h3 class="text-lg font-heading font-bold text-ink mb-1">Deactivate Account</h3>
            <p class="text-[14px] font-serif italic text-muted mb-6 leading-relaxed">
                Temporarily hide your profile and contributions. You can reactivate at any time by logging back in.
            </p>

            <div x-data="{ open: {{ $errors->userDeactivation->any() ? 'true' : 'false' }} }">
                <button type="button" @click="open = true" 
                        class="px-6 py-2.5 border border-ink text-ink font-mono text-[10px] uppercase tracking-widest hover:bg-ink hover:text-paper transition-all focus:outline-none">
                    Deactivate Account
                </button>

                {{-- Persistent Modal Overlay --}}
                <div x-show="open" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center bg-ink/70 backdrop-blur-sm px-4">
                    <div @click.away="open = false" class="bg-paper border border-rule shadow-2xl p-8 max-w-md w-full rounded-sm">
                        <form method="post" action="{{ route('profile.deactivate') }}">
                            @csrf @method('patch')
                            <h2 class="text-2xl font-heading font-bold text-ink mb-3">Confirm Deactivation</h2>
                            <p class="text-sm font-serif text-muted mb-6 italic">Please provide your password to confirm account deactivation.</p>
                            <div class="mb-6">
                                <label class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-1 font-bold">Password</label>
                                <input type="password" name="password" required class="w-full px-4 py-3 border border-rule bg-paper text-ink font-serif text-lg rounded-sm" />
                                @error('password', 'userDeactivation') <span class="text-xs font-mono text-accent-warm block mt-2">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex justify-end gap-6 items-center">
                                <button type="button" @click="open = false" class="font-mono text-[10px] uppercase tracking-widest text-muted hover:text-ink">Cancel</button>
                                <button type="submit" class="px-6 py-3 bg-ink text-paper font-mono text-[10px] uppercase tracking-widest hover:opacity-90">Deactivate</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Account Deletion (Hard Delete / Irreversible) --}}
        <div class="bg-accent-warm/5 border border-accent-warm/20 p-6 rounded-sm">
            <h3 class="text-lg font-heading font-bold text-accent-warm mb-1">Delete Account</h3>
            <p class="text-[14px] font-serif italic text-muted mb-6 leading-relaxed">
                Permanently purge your scholarly record and data. This action is irreversible.
            </p>

            <div x-data="{ open: {{ $errors->userDeletion->any() ? 'true' : 'false' }}, confirmText: '' }">
                <button type="button" @click="open = true" 
                        class="px-6 py-2.5 border border-accent-warm text-accent-warm font-mono text-[10px] uppercase tracking-widest hover:bg-accent-warm hover:text-paper transition-all focus:outline-none">
                    Delete Account
                </button>

                <div x-show="open" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center bg-ink/70 backdrop-blur-sm px-4">
                    <div @click.away="open = false; confirmText = ''" class="bg-paper border border-rule shadow-2xl p-8 max-w-md w-full rounded-sm">
                        <form method="post" action="{{ route('profile.destroy') }}">
                            @csrf @method('delete')
                            <h2 class="text-2xl font-heading font-bold text-accent-warm mb-3">Delete Forever?</h2>
                            <p class="text-sm font-serif text-muted mb-6 leading-relaxed">
                                Once deleted, your data cannot be recovered. Type <strong class="text-ink">DELETE</strong> to authorize this action.
                            </p>
                            
                            <div class="mb-4">
                                <label class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-1 font-bold">Password</label>
                                <input type="password" name="password" required class="w-full px-4 py-3 border border-rule bg-paper text-ink font-serif text-lg rounded-sm" />
                                @error('password', 'userDeletion') <span class="text-xs font-mono text-accent-warm block mt-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-6">
                                <label class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-1 font-bold">Verification Word</label>
                                <input type="text" x-model="confirmText" placeholder="Type DELETE" required class="w-full px-4 py-3 border border-rule bg-paper text-accent-warm font-mono text-lg rounded-sm" />
                            </div>

                            <div class="flex justify-end gap-6 items-center">
                                <button type="button" @click="open = false; confirmText = ''" class="font-mono text-[10px] uppercase tracking-widest text-muted hover:text-ink">Cancel</button>
                                <button type="submit" 
                                        :disabled="confirmText !== 'DELETE'" 
                                        :class="confirmText !== 'DELETE' ? 'opacity-30 cursor-not-allowed' : 'hover:opacity-80'" 
                                        class="px-6 py-3 bg-accent-warm text-paper font-mono text-[10px] uppercase tracking-widest transition-all">
                                    Final Delete
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection