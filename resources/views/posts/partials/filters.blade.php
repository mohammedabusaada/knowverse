<div class="bg-paper border border-rule p-5 shadow-sm" x-data="{ searchQuery: '' }">
    {{-- Header --}}
    <div class="border-b border-rule pb-3 mb-4 flex items-center justify-between">
        <h2 class="font-mono text-[10px] uppercase tracking-[0.2em] text-ink font-bold">
            Refine Discussions
        </h2>
        @if(count($selectedTags))
            <span class="font-mono text-[9px] px-1.5 py-0.5 bg-ink text-paper rounded-sm">
                {{ count($selectedTags) }} active
            </span>
        @endif
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('posts.index') }}" class="space-y-6">
        
        {{-- Instant Client-Side Search for Tags --}}
        <div>
            <label class="sr-only">Filter tags</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input
                    type="text"
                    x-model="searchQuery"
                    placeholder="Find a topic..."
                    class="w-full pl-8 pr-3 py-2 bg-aged/30 border border-rule rounded-sm text-ink font-serif text-sm placeholder:text-muted placeholder:italic focus:outline-none focus:ring-0 focus:border-ink focus:bg-paper transition-colors"
                />
            </div>
        </div>

        {{-- Scrollable Tags List --}}
        <div>
            <div class="max-h-64 overflow-y-auto pr-2 space-y-1 scrollbar-hide">
                @foreach ($tags as $tag)
                    @php
                        $checked = in_array($tag->name, $selectedTags);
                    @endphp
                    {{-- Alpine.js x-show toggles visibility instantly based on search --}}
                    <label 
                        class="flex items-center gap-3 p-1.5 cursor-pointer hover:bg-aged/50 transition-colors group"
                        x-show="searchQuery === '' || '{{ strtolower($tag->name) }}'.includes(searchQuery.toLowerCase())"
                    >
                        <input
                            type="checkbox"
                            name="tags[]"
                            value="{{ $tag->name }}"
                            class="rounded-sm border-rule text-ink focus:ring-ink bg-transparent w-3.5 h-3.5 cursor-pointer"
                            {{ $checked ? 'checked' : '' }}
                        />
                        <span class="font-serif text-sm transition-colors {{ $checked ? 'text-ink font-bold' : 'text-muted group-hover:text-ink' }}">
                            {{ strtolower($tag->name) }}
                        </span>
                    </label>
                @endforeach
            </div>
            
            {{-- Empty State for Tag Search --}}
            <div x-show="searchQuery !== '' && !document.querySelector('.max-h-64 label[style*=\'display: flex\'], .max-h-64 label:not([style*=\'display: none\'])')" class="text-center py-4 text-muted italic font-serif text-sm" style="display: none;">
                No topics match your search.
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-3 pt-4 border-t border-rule">
            <button type="submit" class="flex-1 bg-ink text-paper border border-ink hover:bg-transparent hover:text-ink font-mono text-[10px] uppercase tracking-widest py-2 transition-colors shadow-sm focus:outline-none">
                Apply
            </button>
            
            <a href="{{ route('posts.index') }}" class="flex-1 text-center bg-transparent text-ink border border-rule hover:border-ink font-mono text-[10px] uppercase tracking-widest py-2 transition-colors focus:outline-none">
                Clear
            </a>
        </div>
    </form>
</div>