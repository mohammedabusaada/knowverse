@extends('admin.layouts.app')

@section('header', 'Reports Moderation')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800 rounded-xl font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-xl font-medium">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header Intro --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Protect the community by taking action on flagged content and users.
            </p>
        </div>
        <div class="text-sm text-gray-500 font-medium">
            Total: {{ number_format($reports->total()) }} reports
        </div>
    </div>

    {{-- Pass data into the Alpine component --}}
    <div x-data="reportsModeration({{ \Illuminate\Support\Js::from($reports->mapWithKeys(fn($r) => [$r->id => $r->status])) }})" 
         class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-950/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reporter</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Target Content</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse ($reports as $report)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors cursor-pointer group" 
                            @click="open({{ $report->id }}, statuses[{{ $report->id }}])">
                            
                            {{-- Reporter Column --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                {{ $report->reporter->username ?? 'Unknown' }}
                            </td>

                            {{-- Target Content Column --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-1">
                                        {{ class_basename($report->target_type) }}
                                    </span>
                                    <span class="text-gray-600 dark:text-gray-300 text-sm font-medium line-clamp-1">
                                        @if($report->target)
                                            {{ $report->target->title ?? $report->target->body ?? $report->target->username }}
                                        @else
                                            <span class="text-red-500 dark:text-red-400 italic">Content Deleted</span>
                                        @endif
                                    </span>
                                </div>
                            </td>

                            {{-- Status Column --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border"
                                      :class="badgeClass({{ $report->id }})"
                                      x-text="statuses[{{ $report->id }}]">
                                </span>
                            </td>

                            {{-- Action Column --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-1 text-sm font-bold transition group-hover:translate-x-1"
                                     :class="statuses[{{ $report->id }}] === 'pending' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500'">
                                    <span x-text="statuses[{{ $report->id }}] === 'pending' ? 'Review' : 'View'"></span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Template --}}
                        <template x-if="openReportId === {{ $report->id }}">
                            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-hidden" x-cloak>
                                <div class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm" @click.stop="close()"></div>
                                
                                <div class="relative bg-white dark:bg-gray-900 rounded-3xl w-full max-w-lg p-8 shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden"
                                     @click.stop
                                     x-transition:enter="ease-out duration-300"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100">
                                    
                                    <div class="mb-6">
                                        <h2 class="text-2xl font-black text-gray-900 dark:text-white leading-tight">Review Report</h2>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium mt-1">
                                            Flagged for: <span class="text-red-600 dark:text-red-400 capitalize">{{ $report->reason_type->value ?? 'General' }}</span>
                                        </p>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-gray-950 rounded-2xl p-5 mb-6 border border-gray-200 dark:border-gray-800 italic text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                                        "{{ $report->reason }}"
                                    </div>

                                    <div class="grid grid-cols-1 gap-3">
                                        <button @click="resolve({{ $report->id }})" 
                                                class="w-full py-4 bg-red-600 text-white rounded-2xl hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 font-bold shadow-md transition-all focus:outline-none focus:ring-2 focus:ring-red-500/50">
                                            {{ $report->target_type === App\Models\User::class ? 'Resolve & Ban User' : 'Resolve & Hide Content' }}
                                        </button>
                                        <button @click="dismiss({{ $report->id }})" 
                                                class="w-full py-4 bg-white dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-2xl hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-900 dark:hover:text-white font-bold transition-all focus:outline-none">
                                            Dismiss Report
                                        </button>
                                    </div>

                                    <button @click="close()" class="mt-6 w-full text-center text-xs font-bold text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 uppercase tracking-widest transition">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </template>
                    
                    {{-- Empty case --}}
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                No reports found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($reports->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/50">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>

<script>
/**
 * Handles the logic for the reports moderation dashboard.
 * @param {Object} initialStatuses - Injected via Blade
 */
function reportsModeration(initialStatuses) {
    return {
        openReportId: null,
        statuses: initialStatuses,

        open(id, currentStatus) {
            const status = String(currentStatus).toLowerCase();
            if (status === 'pending') {
                this.openReportId = id;
                document.body.classList.add('overflow-hidden'); // Prevent background scroll
            } else {
                window.location.href = `/admin/reports/${id}`;
            }
        },

        close() { 
            this.openReportId = null; 
            document.body.classList.remove('overflow-hidden');
        },

        async performAction(id, action) {
            try {
                const response = await fetch(`/admin/reports/${id}/${action}`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.reload(); 
                } else {
                    const error = await response.json();
                    alert(error.message || 'Moderation action failed.');
                }
            } catch (e) {
                alert('Connection error. Please check your network.');
            }
        },

        resolve(id) { this.performAction(id, 'resolve'); },
        dismiss(id) { this.performAction(id, 'dismiss'); },

        badgeClass(id) {
            const s = String(this.statuses[id]).toLowerCase();
            if (s === 'pending') return 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800';
            if (s === 'resolved') return 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border-green-200 dark:border-green-800';
            return 'bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-gray-700';
        }
    }
}
</script>
@endsection