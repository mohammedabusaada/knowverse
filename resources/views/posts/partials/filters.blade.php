<div class="space-y-6">
    {{-- Header --}}
    <div class="border-b border-rule pb-2 flex items-center justify-between">
        <h2 class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted">
            Refine Archive
        </h2>
        @if(count($selectedTags))
            <span class="font-mono text-[9px] px-1.5 py-0.5 bg-ink text-paper rounded-sm">
                {{ count($selectedTags) }} active
            </span>
        @endif
    </div>

    {{-- Body --}}
    <form method="GET" action="{{ route('posts.index') }}" class="space-y-6">
        {{-- Search Input --}}
        <div>
            <input
                type="text"
                name="query" {{-- x-model removed for simplicity or adapt as needed --}}
                placeholder="Search tags..."
                class="w-full px-3 py-2 bg-transparent border border-rule rounded-sm text-ink font-serif text-sm placeholder:text-muted placeholder:italic focus:outline-none focus:ring-0 focus:border-ink transition-colors"
            />
        </div>

        {{-- Tags List --}}
        <div>
            <div class="max-h-64 overflow-y-auto pr-2 space-y-1">
                @foreach ($tags as $tag)
                    @php
                        $checked = in_array($tag->name, $selectedTags);
                    @endphp
                    <label class="flex items-center gap-3 p-1.5 cursor-pointer hover:bg-aged/50 transition-colors group">
                        <input
                            type="checkbox"
                            name="tags[]"
                            value="{{ $tag->name }}"
                            class="rounded-sm border-rule text-ink focus:ring-ink bg-transparent w-3.5 h-3.5"
                            {{ $checked ? 'checked' : '' }}
                        />
                        <span class="font-serif text-sm text-muted group-hover:text-ink transition-colors {{ $checked ? 'text-ink font-bold' : '' }}">
                            {{ strtolower($tag->name) }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 pt-4 border-t border-rule">
            <button type="submit" class="flex-1 bg-ink text-paper border border-ink hover:bg-transparent hover:text-ink font-mono text-[10px] uppercase tracking-widest py-2 transition-colors">
                Apply
            </button>
            <a href="{{ route('posts.index') }}" class="flex-1 text-center bg-transparent text-ink border border-rule hover:border-ink font-mono text-[10px] uppercase tracking-widest py-2 transition-colors">
                Clear
            </a>
        </div>
    </form>
</div>