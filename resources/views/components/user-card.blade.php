@props(['user'])

@php
    $badge = null;
    if (auth()->check()) {
        if (auth()->id() === $user->id) {
            $badge = 'You';
        } elseif ($user->isFollowedBy(auth()->user())) {
            $badge = 'Follows you';
        }
    }
@endphp

<div class="flex items-center justify-between py-4 border-b border-rule hover:bg-aged/20 transition-colors px-3 group">
    <div class="flex items-center gap-4">
        {{-- Avatar --}}
        <x-user-avatar :user="$user" size="md" class="border border-rule grayscale opacity-90 group-hover:grayscale-0 transition-all" />
        
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                {{-- User Name with correct Hover Color --}}
                <a href="{{ route('profile.show', $user->username) }}" class="font-heading font-bold text-ink hover:text-accent transition-colors text-lg truncate">
    {{ $user->display_name }}
</a>
                
                {{-- Contextual Badge --}}
                @if($badge)
                    <span class="font-mono text-[9px] tracking-[0.15em] uppercase text-muted bg-paper px-2 py-0.5 rounded-sm border border-rule shadow-sm">
                        {{ $badge }}
                    </span>
                @endif
            </div>
            {{-- Username --}}
            <p class="font-mono text-[10px] uppercase tracking-widest text-muted truncate mt-0.5">
                {{ '@' . $user->username }}
            </p>
        </div>
    </div>
    
    {{-- Follow Action --}}
    <div class="shrink-0 pl-4">
        @if(auth()->check() && auth()->id() !== $user->id)
            <div class="scale-90 origin-right">
                <x-follow-button :user="$user" />
            </div>
        @endif
    </div>
</div>