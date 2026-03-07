@extends('settings._layout')

@section('settings-content')
<div>
    <div class="mb-10 border-b border-rule pb-4">
        <h1 class="font-heading text-3xl md:text-4xl font-bold tracking-tight text-ink">
            Public Identity
        </h1>
        <p class="mt-2 font-serif text-[15px] italic text-muted">
            Manage your scholarly credentials and visual presence within the Community.
        </p>
    </div>

    {{-- Comprehensive Identity Configuration Form --}}
    <form
        action="{{ route('profile.update') }}"
        method="POST"
        enctype="multipart/form-data"
        x-data="{
            avatarPreview: '{{ $user->profile_picture ? asset('storage/'.$user->profile_picture) : '' }}',
            newAvatar: null
        }"
        class="space-y-12"
    >
        @csrf
        @method('PUT')

        {{-- Visual Identity Section --}}
        <div>
            <h2 class="text-[10px] font-mono font-bold uppercase tracking-[0.2em] text-muted mb-6 border-b border-rule pb-2">
                Scholar Portrait
            </h2>

            <div class="flex items-center gap-8">
                <div class="relative group shrink-0">
                    <img
                        x-show="newAvatar || avatarPreview"
                        :src="newAvatar ? URL.createObjectURL(newAvatar) : avatarPreview"
                        class="w-24 h-24 rounded-full object-cover border-2 border-rule group-hover:border-ink transition-all duration-300"
                    />
                    <div x-show="!newAvatar && !avatarPreview" class="w-24 h-24 rounded-full border-2 border-rule bg-aged flex items-center justify-center group-hover:border-ink transition-colors">
                         <span class="font-heading text-3xl text-muted uppercase">{{ mb_substr($user->display_name ?? $user->username, 0, 1) }}</span>
                    </div>
                </div>

                <div class="flex-1">
                    <label class="cursor-pointer inline-flex items-center justify-center px-4 py-2 border border-ink bg-transparent text-ink hover:bg-ink hover:text-paper font-mono text-[10px] uppercase tracking-widest transition-colors mb-2 shadow-sm">
                        <span>Upload New Portrait</span>
                        <input type="file" name="profile_picture" accept="image/*" class="hidden" @change="newAvatar = $event.target.files[0]" />
                    </label>
                    <p class="font-serif text-xs italic text-muted">High-resolution square portraits recommended (Max 2MB).</p>

                    @error('profile_picture')
                        <p class="text-accent-warm font-mono text-[10px] font-bold tracking-widest mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Core Credentials --}}
        <div>
            <h2 class="text-[10px] font-mono font-bold uppercase tracking-[0.2em] text-muted mb-6 border-b border-rule pb-2">
                Scholarly Metadata
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Username Field --}}
                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2 font-bold">Pseudonym / Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                           class="w-full px-0 py-2 border-0 border-b border-rule bg-transparent focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg" />
                    @error('username') <span class="text-xs font-mono text-accent-warm block mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Full Name Field --}}
                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2 font-bold">Formal Name</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required
                           class="w-full px-0 py-2 border-0 border-b border-rule bg-transparent focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg" />
                    @error('full_name') <span class="text-xs font-mono text-accent-warm block mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Locked Email Field --}}
                <div class="opacity-80">
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2 font-bold">Email Identifier (Verified)</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                           class="w-full px-0 py-2 border-0 border-b border-rule bg-aged/20 text-muted font-serif text-lg cursor-not-allowed" 
                           readonly 
                           title="Email cannot be modified to maintain identity persistence." />
                    <p class="mt-2 text-[10px] font-serif italic text-muted leading-relaxed">
                        Registration email is permanent and serves as your primary academic key.
                    </p>
                </div>

                {{-- Optional Credentials --}}
                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2 font-bold">Titles & Designations</label>
                    <input type="text" name="academic_title" value="{{ old('academic_title', $user->academic_title) }}" placeholder="e.g. Researcher, PhD, MSc"
                           class="w-full px-0 py-2 border-0 border-b border-rule bg-transparent focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg placeholder:italic placeholder:opacity-50" />
                    @error('academic_title') <span class="text-xs font-mono text-accent-warm block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Academic Biography --}}
        <div>
            <h2 class="text-[10px] font-mono font-bold uppercase tracking-[0.2em] text-muted mb-6 border-b border-rule pb-2">
                Biography
            </h2>
            <x-markdown-editor 
                name="bio" 
                id="bio-editor"
                :value="old('bio', $user->bio)" 
            />
            @error('bio') <span class="text-xs font-mono text-accent-warm block mt-2">{{ $message }}</span> @enderror
        </div>

        {{-- Footer Actions --}}
        <div class="flex justify-end gap-4 pt-6 border-t border-rule">
            <a href="{{ route('profile.show', $user->username) }}" class="px-6 py-3 text-muted hover:text-ink font-mono text-[10px] uppercase tracking-widest transition-colors">
                Cancel Edit
            </a>
            <button type="submit" class="px-8 py-3 bg-ink text-paper font-mono text-[10px] uppercase tracking-widest hover:opacity-80 transition-opacity shadow-sm">
                Commit Updates
            </button>
        </div>
    </form>
</div>
@endsection