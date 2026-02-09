<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
    <div class="divide-y divide-gray-100 dark:divide-gray-700">
        @forelse ($history as $entry)
            <x-reputation-item 
                :type="$entry->action" 
                :points="$entry->delta" 
                :date="$entry->created_at->diffForHumans()"
                :source="$entry->source"
                :sourceType="$entry->source_type"
            />
        @empty
            <div class="p-12 text-center">
                <x-icons.chart class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                <p class="text-gray-500 dark:text-gray-400 font-medium">No reputation history found.</p>
            </div>
        @endforelse
    </div>

    @if($history->hasPages())
        <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
            {{ $history->links() }}
        </div>
    @endif
</div>