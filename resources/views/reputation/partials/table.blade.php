<div class="border border-rule rounded-sm bg-paper shadow-sm">
    <div class="divide-y divide-rule">
        @forelse ($history as $entry)
            <x-reputation-item 
                :type="$entry->action" 
                :points="$entry->delta" 
                :date="$entry->created_at->diffForHumans()"
                :source="$entry->source"
                :sourceType="$entry->source_type"
            />
        @empty
            <div class="p-16 text-center bg-aged/10">
                <span class="block text-2xl mb-2 opacity-50">✦</span>
                <p class="font-serif text-muted italic text-[15px]">No standing history recorded yet.</p>
            </div>
        @endforelse
    </div>

    @if($history->hasPages())
        <div class="p-5 border-t border-rule bg-aged/30">
            {{ $history->links() }}
        </div>
    @endif
</div>