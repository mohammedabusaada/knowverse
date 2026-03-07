@if($users->isEmpty())
    <div class="py-20 text-center border border-dashed border-rule bg-aged/10">
        <div class="flex justify-center mb-4">
            <svg class="w-8 h-8 text-muted opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <h3 class="font-serif text-lg text-ink font-bold mb-1">No Scholars Found</h3>
        <p class="font-serif text-sm text-muted italic">We couldn't locate any records matching that name.</p>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($users as $user)
            <a href="{{ route('profile.show', $user->username) }}"
               class="group flex items-center gap-4 p-4 bg-paper border border-rule hover:border-ink transition-colors">
                
                <x-user-avatar :user="$user" size="md" class="grayscale opacity-90 group-hover:grayscale-0 transition-all" />
                
                <div class="min-w-0 flex-1">
                    <p class="font-heading font-bold text-ink truncate text-base group-hover:text-accent transition-colors">
                        {{ $user->display_name }}
                    </p>
                    <p class="font-mono text-[10px] uppercase tracking-widest text-muted truncate mt-0.5">
                        {{ '@' . $user->username }}
                    </p>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8 pt-4 border-t border-rule">
        {{ $users->links() }}
    </div>
@endif