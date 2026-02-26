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

<div class="flex items-center justify-between py-4 border-b border-rule hover:bg-aged/30 transition-colors px-3 group">
    <div class="flex items-center gap-4">
        <x-user-avatar :user="$user" size="md" class="border border-rule grayscale opacity-90 group-hover:grayscale-0 transition-all" />
        
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <a href="{{ route('profile.show', $user->username) }}" class="font-heading font-bold text-ink hover:text-accent transition-colors text-lg truncate">
                    {{ $user->display_name }}
                </a>
                
                @if($badge)
                    <span class="font-mono text-[9px] tracking-[0.15em] uppercase text-muted bg-aged px-2 py-0.5 rounded-sm border border-rule">
                        {{ $badge }}
                    </span>
                @endif
            </div>
            <p class="font-mono text-[11px] text-muted truncate mt-0.5">{{ '@' . $user->username }}</p>
        </div>
    </div>
    
    <div class="shrink-0 pl-4">
        @if(auth()->check() && auth()->id() !== $user->id)
            <div class="scale-90 origin-right">
                <x-follow-button :user="$user" />
            </div>
        @endif
    </div>
</div>