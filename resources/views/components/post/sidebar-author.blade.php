@props(['user'])

<div class="sticky top-24 space-y-8">
    {{-- Scholar Card --}}
    <div class="bg-paper border border-rule p-6 shadow-sm overflow-hidden">
        <h3 class="font-mono text-[10px] uppercase tracking-widest text-muted border-b border-rule pb-2 mb-6 font-bold flex items-center gap-2">
            <span class="w-1.5 h-1.5 bg-ink rounded-full block"></span>
            About the Scholar
        </h3>

        <div class="flex flex-col items-center text-center w-full">
            
            {{-- Unified Avatar Component --}}
            <x-user-avatar :user="$user" size="xl" class="mb-4 shadow-sm border border-rule grayscale hover:grayscale-0 transition-all flex-shrink-0" />

            {{-- Name Handling --}}
            <div class="w-full px-2 mb-1">
                <a href="{{ route('profile.show', $user->username) }}" 
                   class="font-heading text-lg md:text-xl font-bold text-ink hover:text-accent transition-colors block leading-tight break-words"
                   title="{{ $user->display_name }}">
                    {{ $user->display_name }}
                </a>
            </div>
            
            <p class="font-mono text-[10px] text-muted uppercase tracking-widest mb-4 truncate w-full px-4">
                {{ '@' . $user->username }}
            </p>

            @if($user->academic_title)
                <span class="inline-block px-2 py-0.5 bg-aged border border-rule text-ink font-mono text-[9px] uppercase tracking-widest mb-4 max-w-full truncate">
                    {{ $user->academic_title }}
                </span>
            @endif

            {{-- Stats Grid --}}
            <div class="w-full grid grid-cols-2 gap-0 border-y border-rule py-3 mb-4 font-mono text-xs">
                <div class="text-center border-r border-rule px-1">
                    <span class="block font-bold text-ink">{{ number_format($user->reputation_points ?? 0) }}</span>
                    <span class="text-[9px] text-muted uppercase tracking-widest">Reputation</span>
                </div>
                <div class="text-center px-1">
                    <span class="block font-bold text-ink">{{ $user->posts_count ?? ($user->posts ? $user->posts()->count() : 0) }}</span>
                    <span class="text-[9px] text-muted uppercase tracking-widest">Discussions</span>
                </div>
            </div>

            {{-- Interactive Follow Button --}}
            <div class="w-full mt-2">
                @auth
                    @if(auth()->id() !== $user->id)
                        <x-follow-button :user="$user" />
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block w-full text-center py-2 bg-ink text-paper font-mono text-[10px] uppercase tracking-widest hover:bg-opacity-90 transition-all shadow-sm border border-ink">
                        Follow Scholar
                    </a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Discourse Rules --}}
    <div class="px-2">
        <h3 class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted border-b border-rule pb-2 mb-4 font-bold">
            Discourse Rules
        </h3>
        <ul class="font-serif text-[13px] text-ink space-y-3 list-none p-0">
            <li class="flex items-start gap-3">
                <span class="text-muted font-mono text-[10px] mt-0.5">01.</span> 
                <span class="leading-snug text-muted">Be respectful to peers and scholarly debate.</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-muted font-mono text-[10px] mt-0.5">02.</span> 
                <span class="leading-snug text-muted">No spam or self-promotion.</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-muted font-mono text-[10px] mt-0.5">03.</span> 
                <span class="leading-snug text-muted">Cite your sources clearly.</span>
            </li>
        </ul>
    </div>
</div>