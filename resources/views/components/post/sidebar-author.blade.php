@props(['user'])

<div class="sticky top-24 space-y-12">
    
    {{-- Author Info --}}
    <div>
        <h3 class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted border-b border-rule pb-2 mb-6 flex items-center gap-2">
            <span class="w-1.5 h-1.5 bg-ink rounded-full block"></span>
            About the Scholar
        </h3>
        
        <div class="flex items-center gap-4 mb-5">
            <x-user-avatar :src="$user->profile_picture_url" size="lg" class="border border-rule grayscale opacity-90 hover:grayscale-0 transition-all" />
            <div class="min-w-0">
                <a href="{{ route('profile.show', $user->username) }}" class="font-heading font-bold text-lg text-ink hover:text-accent transition-colors truncate block">
                    {{ $user->display_name }}
                </a>
                <p class="font-mono text-[10px] text-muted truncate mt-1">{{ '@'.$user->username }}</p>
            </div>
        </div>

        @if($user->bio)
            <p class="font-serif text-[15px] text-muted line-clamp-4 mb-6 leading-relaxed italic border-l border-rule pl-3">
                "{{ $user->bio }}"
            </p>
        @endif
        
        <div class="flex flex-col gap-3">
            @if(auth()->check() && auth()->id() !== $user->id)
                <x-follow-button :user="$user" />
            @endif
        </div>
    </div>

    {{-- Rules --}}
    <div>
        <h3 class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted border-b border-rule pb-2 mb-4">
            Discourse Rules
        </h3>
        <ul class="font-serif text-sm text-ink space-y-3 list-none p-0">
            <li class="flex items-start gap-2"><span class="text-muted font-mono text-[10px] mt-1">01.</span> Be respectful to peers.</li>
            <li class="flex items-start gap-2"><span class="text-muted font-mono text-[10px] mt-1">02.</span> No spam or self-promotion.</li>
            <li class="flex items-start gap-2"><span class="text-muted font-mono text-[10px] mt-1">03.</span> Cite your sources clearly.</li>
        </ul>
    </div>
</div>