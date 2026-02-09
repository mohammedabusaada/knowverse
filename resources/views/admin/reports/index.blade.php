@extends('layouts.app')

@section('content')
{{-- Success Toast --}}
@if(session('success'))
<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     class="fixed bottom-5 right-5 z-50">
    <div class="bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
</div>
@endif

{{-- Pass data into the function to keep the script block clean --}}
<div x-data="reportsModeration({{ \Illuminate\Support\Js::from($reports->mapWithKeys(fn($r) => [$r->id => $r->status])) }})" 
     class="w-full px-4 py-6">
    
    <div class="mb-8 text-center sm:text-left">
        <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Reports Moderation</h1>
        <p class="mt-1 text-gray-500">Protect the community by taking action on flagged content.</p>
    </div>

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <table class="min-w-full">
            <thead class="bg-gray-50/80 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Reporter</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Target Content</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach ($reports as $report)
                    <tr class="hover:bg-blue-50/40 transition-colors cursor-pointer group" 
                        @click="open({{ $report->id }}, statuses[{{ $report->id }}])">
                        
                        <td class="px-6 py-5 text-sm font-semibold text-gray-700">
                            {{ $report->reporter->username ?? 'Unknown' }}
                        </td>

                        <td class="px-6 py-5 text-sm">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-blue-500 uppercase tracking-tighter mb-0.5">
                                    {{ class_basename($report->target_type) }}
                                </span>
                                <span class="text-gray-600 font-medium line-clamp-1">
                                    @if($report->target)
                                        {{ $report->target->title ?? $report->target->body ?? $report->target->username }}
                                    @else
                                        <span class="text-red-400 italic">Content Deleted</span>
                                    @endif
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-5">
                            <span class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest border"
                                  :class="badgeClass({{ $report->id }})"
                                  x-text="statuses[{{ $report->id }}]">
                            </span>
                        </td>

                        <td class="px-6 py-5 text-center">
                            <div class="inline-flex items-center gap-1 text-sm font-bold transition group-hover:translate-x-1"
                                 :class="statuses[{{ $report->id }}] === 'pending' ? 'text-blue-600' : 'text-gray-400'">
                                <span x-text="statuses[{{ $report->id }}] === 'pending' ? 'Review' : 'View'"></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </td>
                    </tr>

                    {{-- Modal Template (Efficient rendering inside the loop) --}}
                    <template x-if="openReportId === {{ $report->id }}">
                        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-hidden" x-cloak>
                            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="close()"></div>
                            
                            <div class="relative bg-white rounded-3xl w-full max-w-lg p-8 shadow-2xl overflow-hidden"
                                 x-transition:enter="ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100">
                                
                                <div class="mb-6">
                                    <h2 class="text-2xl font-black text-gray-900 leading-tight">Review Report</h2>
                                    <p class="text-gray-500 text-sm font-medium">Flagged for: <span class="text-red-500 capitalize">{{ $report->reason_type->value ?? 'General' }}</span></p>
                                </div>

                                <div class="bg-gray-50 rounded-2xl p-5 mb-6 border border-gray-100 italic text-gray-600 text-sm leading-relaxed">
                                    "{{ $report->reason }}"
                                </div>

                                <div class="grid grid-cols-1 gap-3">
                                    <button @click="resolve({{ $report->id }})" 
                                            class="w-full py-4 bg-green-600 text-white rounded-2xl hover:bg-green-700 font-bold shadow-lg shadow-green-100 transition-all">
                                        Resolve & Hide Content
                                    </button>
                                    <button @click="dismiss({{ $report->id }})" 
                                            class="w-full py-4 bg-white border-2 border-gray-100 text-gray-500 rounded-2xl hover:border-red-100 hover:text-red-500 font-bold transition-all">
                                        Dismiss Report
                                    </button>
                                </div>

                                <button @click="close()" class="mt-6 w-full text-center text-xs font-bold text-gray-300 hover:text-gray-400 uppercase tracking-widest transition">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </template>
                @endforeach
            </tbody>
        </table>
        
        <div class="p-6 bg-gray-50/50 border-t border-gray-100">
            {{ $reports->links() }}
        </div>
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
            } else {
                window.location.href = `/admin/reports/${id}`;
            }
        },

        close() { this.openReportId = null; },

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
                alert('Connection error. Please check your internet.');
            }
        },

        resolve(id) { this.performAction(id, 'resolve'); },
        dismiss(id) { this.performAction(id, 'dismiss'); },

        badgeClass(id) {
            const s = String(this.statuses[id]).toLowerCase();
            if (s === 'pending') return 'bg-amber-50 text-amber-600 border-amber-100';
            if (s === 'resolved') return 'bg-green-50 text-green-600 border-green-100';
            return 'bg-gray-50 text-gray-500 border-gray-100';
        }
    }
}
</script>
@endsection