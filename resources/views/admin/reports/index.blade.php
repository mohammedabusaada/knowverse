@extends('admin.layouts.app')

@section('header', 'Reports Moderation')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 animate-[fadeUp_0.8s_ease_both]">

    {{-- Alert Messages --}}
    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif
    @if(session('error'))
        <x-alert type="error">{{ session('error') }}</x-alert>
    @endif

    {{-- Header Intro --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <p class="font-serif text-[15px] text-muted italic">
                Review flagged content to maintain a safe and respectful community.
            </p>
        </div>
        <div class="font-mono text-[10px] uppercase tracking-widest text-ink bg-aged/50 px-3 py-1 border border-rule">
            Total: {{ number_format($reports->total()) }} reports
        </div>
    </div>

    {{-- Data Table with inline Alpine.js --}}
    <div x-data="{
            openReportId: null,
            statuses: {{ \Illuminate\Support\Js::from($reports->mapWithKeys(fn($r) => [$r->id => $r->status->value])) }},
            processing: false,
            
            open(id, currentStatus) {
                if (currentStatus === 'pending') {
                    this.openReportId = id;
                    document.body.classList.add('overflow-hidden');
                } else {
                    window.location.href = `/admin/reports/${id}`;
                }
            },
            close() { 
                this.openReportId = null;
                document.body.classList.remove('overflow-hidden');
            },
            async performAction(id, action) {
                if(this.processing) return;
                this.processing = true;
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
                        this.processing = false;
                    }
                } catch (e) {
                    alert('Connection error. Please check your network.');
                    this.processing = false;
                }
            },
            badgeClass(status) {
                if (status === 'pending') return 'bg-accent-warm/10 text-accent-warm border-accent-warm/30';
                if (status === 'resolved') return 'bg-ink/10 text-ink border-ink/30';
                return 'bg-aged text-muted border-rule';
            }
         }" 
         class="bg-paper border border-rule shadow-sm">
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-rule">
                <thead class="bg-aged/30">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left font-mono text-[10px] font-bold text-muted uppercase tracking-widest">Reporter</th>
                        <th scope="col" class="px-6 py-4 text-left font-mono text-[10px] font-bold text-muted uppercase tracking-widest">Target Content</th>
                        <th scope="col" class="px-6 py-4 text-left font-mono text-[10px] font-bold text-muted uppercase tracking-widest">Status</th>
                        <th scope="col" class="px-6 py-4 text-right font-mono text-[10px] font-bold text-muted uppercase tracking-widest">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rule">
                    @forelse ($reports as $report)
                        <tr class="hover:bg-aged/20 transition-colors cursor-pointer group" 
                            @click="open({{ $report->id }}, statuses[{{ $report->id }}])">
                            
                            {{-- Reporter Column --}}
                            <td class="px-6 py-4 whitespace-nowrap font-serif text-sm font-bold text-ink">
                                {{ $report->reporter->username ?? 'Unknown' }}
                            </td>

                            {{-- Target Content Column --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-mono text-[9px] text-muted uppercase tracking-[0.2em] mb-1">
                                        {{ class_basename($report->target_type) }}
                                    </span>
                                    <span class="text-ink font-serif text-sm line-clamp-1">
                                        @if($report->target)
                                            {{ $report->target->title ?? $report->target->body ?? $report->target->username }}
                                        @else
                                            <span class="text-accent-warm italic">Content Deleted</span>
                                        @endif
                                    </span>
                                </div>
                            </td>

                            {{-- Status Column --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-sm font-mono text-[9px] font-bold uppercase tracking-widest border"
                                      :class="badgeClass(statuses[{{ $report->id }}])"
                                      x-text="statuses[{{ $report->id }}]">
                                </span>
                            </td>

                            {{-- Action Column --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-1 font-mono text-[10px] uppercase tracking-widest transition group-hover:translate-x-1"
                                     :class="statuses[{{ $report->id }}] === 'pending' ? 'text-accent' : 'text-muted'">
                                    <span x-text="statuses[{{ $report->id }}] === 'pending' ? 'Review' : 'View'"></span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Template --}}
                        <template x-if="openReportId === {{ $report->id }}">
                            <template x-teleport="body">
                                <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-hidden" x-cloak>
                                    <div class="absolute inset-0 bg-ink/60 backdrop-blur-sm" @click.stop="close()"></div>
                                    
                                    <div class="relative bg-paper border border-rule w-full max-w-lg p-8 shadow-2xl"
                                         @click.stop
                                         x-transition:enter="ease-out duration-300"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100">
                                         
                                        <div class="mb-6 border-b border-rule pb-4">
                                            <h2 class="text-2xl font-heading font-bold text-ink leading-tight">Review Report</h2>
                                            <p class="font-mono text-[10px] text-muted uppercase tracking-widest mt-2">
                                                Flagged for: <span class="text-accent-warm font-bold">{{ $report->reason_type->value ?? 'General' }}</span>
                                            </p>
                                        </div>

                                        <div class="bg-aged/30 p-5 mb-6 border-l-2 border-rule font-serif italic text-muted text-[15px] leading-relaxed">
                                            "{{ $report->reason ?: 'No additional context provided.' }}"
                                        </div>

                                        <div class="flex flex-col sm:flex-row gap-3">
                                            <button @click="performAction({{ $report->id }}, 'dismiss')" 
                                                    :disabled="processing"
                                                    class="flex-1 py-3 bg-transparent border border-rule text-ink font-mono text-[10px] uppercase tracking-widest hover:border-ink transition-colors focus:outline-none disabled:opacity-50">
                                                <span x-text="processing ? 'Wait...' : 'Dismiss Report'"></span>
                                            </button>
                                            
                                            {{-- Warm color for destructive actions (Ban/Hide) --}}
                                            <button @click="performAction({{ $report->id }}, 'resolve')" 
                                                    :disabled="processing"
                                                    class="flex-1 py-3 bg-accent-warm text-paper font-mono text-[10px] uppercase tracking-widest hover:opacity-80 transition-opacity shadow-sm focus:outline-none disabled:opacity-50">
                                                <span x-text="processing ? 'Processing...' : '{{ $report->target_type === App\Models\User::class ? 'Resolve & Ban User' : 'Resolve & Hide Content' }}'"></span>
                                            </button>
                                        </div>

                                        <button @click="close()" class="mt-6 w-full text-center font-mono text-[10px] font-bold text-muted hover:text-ink uppercase tracking-widest transition-colors border-b border-transparent hover:border-ink pb-1 focus:outline-none">
                                            Close Window
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </template>
                        
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center font-serif italic text-muted">
                                The moderation queue is currently empty.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($reports->hasPages())
            <div class="px-6 py-4 border-t border-rule bg-aged/10">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection