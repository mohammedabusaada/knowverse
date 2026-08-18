@props(['user'])

<div class="sticky top-24 space-y-8">
    {{-- Scholar Card (Cleaner Design) --}}
    <div class="bg-paper p-2">
        <h3 class="font-mono text-[10px] uppercase tracking-widest text-muted border-b border-rule pb-2 mb-6 font-bold flex items-center gap-2">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
            About the Scholar
        </h3>

        <div class="flex flex-col items-center text-center w-full">
            
            {{-- Unified Avatar Component --}}
            <x-user-avatar :user="$user" size="xl" class="mb-4 shadow-sm border border-rule transition-transform hover:scale-105" />

            {{-- Name Handling (Fixed word wrap) --}}
            <div class="w-full px-1 mb-1">
                <a href="{{ route('profile.show', $user->username) }}" 
                   class="font-heading text-lg md:text-xl font-bold text-ink hover:text-accent transition-colors block leading-snug break-words"
                   title="{{ $user->display_name }}">
                    {{ $user->display_name }}
                </a>
            </div>
            
            <p class="font-mono text-[10px] text-muted uppercase tracking-widest mb-3 truncate w-full px-2 opacity-80">
                {{ '@' . $user->username }}
            </p>

            @if($user->academic_title)
                <span class="inline-block px-3 py-1 bg-aged/50 text-ink font-mono text-[9px] uppercase tracking-widest mb-5 rounded-sm border border-rule/50">
                    {{ $user->academic_title }}
                </span>
            @endif

            {{-- Stats Grid (More breathable) --}}
            <div class="w-full flex justify-center gap-6 border-y border-rule/50 py-4 mb-5 font-mono text-xs">
                <div class="text-center">
                    <span class="block font-bold text-ink text-base">{{ number_format($user->reputation_points ?? 0) }}</span>
                    <span class="text-[8px] text-muted uppercase tracking-widest">Reputation</span>
                </div>
                <div class="w-px bg-rule/50"></div>
                <div class="text-center">
                    <span class="block font-bold text-ink text-base">{{ $user->posts_count ?? ($user->posts ? $user->posts()->count() : 0) }}</span>
                    <span class="text-[8px] text-muted uppercase tracking-widest">Discussions</span>
                </div>
            </div>

            {{-- Interactive Follow Button --}}
            <div class="w-full">
                @auth
                    @if(auth()->id() !== $user->id)
                        <x-follow-button :user="$user" />
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block w-full text-center py-2.5 bg-ink text-paper font-mono text-[10px] uppercase tracking-widest hover:bg-opacity-90 transition-all shadow-sm rounded-sm">
                        Follow Scholar
                    </a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Discourse Rules --}}
    <div class="px-2 pt-4">
        <h3 class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted border-b border-rule pb-2 mb-4 font-bold flex items-center gap-2">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            Discourse Rules
        </h3>
        <ul class="font-serif text-xs text-ink space-y-4 list-none p-0 opacity-90">
            <li class="flex items-start gap-2">
                <span class="text-accent font-mono text-[9px] mt-0.5">01.</span> 
                <span class="leading-relaxed text-muted">Be respectful to peers and uphold scholarly debate.</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="text-accent font-mono text-[9px] mt-0.5">02.</span> 
                <span class="leading-relaxed text-muted">No spam, advertising, or unwarranted self-promotion.</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="text-accent font-mono text-[9px] mt-0.5">03.</span> 
                <span class="leading-relaxed text-muted">Cite your sources clearly to maintain academic integrity.</span>
            </li>
        </ul>
    </div>
</div>