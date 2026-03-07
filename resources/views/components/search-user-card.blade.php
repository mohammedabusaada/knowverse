@props(['user'])

<a href="{{ route('profile.show', $user->username) }}"
   class="group flex items-center gap-4 p-4 bg-paper border border-rule rounded-sm hover:border-ink hover:shadow-md transition-all">

    {{-- Scholar Portrait --}}
    <x-user-avatar :user="$user" size="md" class="grayscale opacity-90 group-hover:grayscale-0 transition-all border border-rule" />

    {{-- Scholar Metadata --}}
    <div class="flex-1 min-w-0">
        <p class="font-heading font-bold text-ink truncate text-base group-hover:text-accent transition-colors">
    {{ $user->display_name }}
</p>

        <p class="font-mono text-[10px] uppercase tracking-widest text-muted truncate mt-1">
            {{ '@' . $user->username }}
        </p>
    </div>
    
    <div class="text-muted group-hover:text-ink transition-colors pr-1">
        &rarr;
    </div>
</a>