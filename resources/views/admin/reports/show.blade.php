@extends('admin.layouts.app')

@section('header', 'Report Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-[fadeUp_0.8s_ease_both]">

    {{-- Back Link --}}
    <div>
        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 font-mono text-[10px] uppercase tracking-widest text-muted hover:text-accent transition-colors border-b border-transparent hover:border-accent pb-1">
            &larr; Back to Queue
        </a>
    </div>

    {{-- Header Info --}}
    <div class="flex items-center justify-between border-b border-rule pb-6">
        <h1 class="text-3xl font-heading font-bold text-ink">
            Report Details #{{ $report->id }}
        </h1>
        
        <span class="inline-flex items-center px-3 py-1 rounded-sm font-mono text-[10px] font-bold uppercase tracking-widest border
            @if($report->status === \App\Enums\ReportStatus::PENDING)
                bg-accent-warm/10 text-accent-warm border-accent-warm/30
            @elseif($report->status === \App\Enums\ReportStatus::RESOLVED)
                bg-ink/10 text-ink border-ink/30
            @else
                bg-aged text-muted border-rule
            @endif">
            {{ $report->status->value }}
        </span>
    </div>

    {{-- Main Document Card --}}
    <div class="bg-paper border border-rule shadow-sm p-6 sm:p-10 space-y-10">

        {{-- Reporter Info --}}
        <div>
            <h3 class="font-mono text-[10px] text-muted uppercase tracking-[0.2em] mb-4 border-b border-rule pb-2">Reported By</h3>
            <div class="flex items-center gap-4">
                <x-user-avatar :user="$report->reporter" size="sm" class="grayscale" />
                <p class="font-serif text-base font-bold text-ink">
                    <a href="{{ route('admin.users.show', $report->reporter) }}" class="hover:text-accent transition-colors">
                        {{ $report->reporter?->username ?? 'Unknown User' }} 
                    </a>
                    <span class="text-muted font-mono text-[10px] tracking-widest ml-2">(ID: {{ $report->reporter_id }})</span>
                </p>
            </div>
        </div>

        {{-- Target Content Preview --}}
        <div>
            <h3 class="font-mono text-[10px] text-muted uppercase tracking-[0.2em] mb-4 border-b border-rule pb-2">Target Subject</h3>

            @if($report->target)
                <div class="border border-rule p-6 bg-aged/30 rounded-sm">
                    @if($report->target_type === \App\Models\Post::class)
                        <p class="font-heading font-bold text-ink mb-3 text-xl">{{ $report->target->title }}</p>
                        <div class="font-serif text-[15px] text-muted line-clamp-4 prose dark:prose-invert">
                            {{ strip_tags($report->target->body) }}
                        </div>
                        <div class="mt-5 pt-4 border-t border-rule font-mono text-[9px] uppercase tracking-widest text-muted">
                            <strong>Type:</strong> Discussion &bull; <a href="{{ route('admin.users.show', $report->target->user_id) }}" class="hover:text-accent">Author ID: {{ $report->target->user_id }}</a>
                        </div>

                    @elseif($report->target_type === \App\Models\Comment::class)
                        <p class="font-heading font-bold text-ink mb-3">Response on a Discussion</p>
                        <div class="font-serif text-[15px] text-muted line-clamp-4 prose dark:prose-invert border-l-2 border-rule pl-4 italic">
                            {{ strip_tags($report->target->body) }}
                        </div>
                        <div class="mt-5 pt-4 border-t border-rule font-mono text-[9px] uppercase tracking-widest text-muted">
                            <strong>Type:</strong> Response &bull; <a href="{{ route('admin.users.show', $report->target->user_id) }}" class="hover:text-accent">Author ID: {{ $report->target->user_id }}</a>
                        </div>

                    @elseif($report->target_type === \App\Models\User::class)
                        <div class="flex items-center gap-5">
                            <x-user-avatar :user="$report->target" size="lg" />
                            <div>
                                <a href="{{ route('admin.users.show', $report->target) }}" class="font-heading font-bold text-ink hover:text-accent transition-colors text-xl block">{{ $report->target->display_name }}</a>
                                <p class="font-mono text-xs text-muted mt-1">{{ '@' . $report->target->username }}</p>
                            </div>
                        </div>
                        <div class="mt-5 pt-4 border-t border-rule font-mono text-[9px] uppercase tracking-widest text-muted">
                            <strong>Type:</strong> Scholar Profile
                        </div>
                    @endif
                </div>
            @else
                <div class="flex items-center gap-3 p-5 bg-accent-warm/5 border border-accent-warm/30 text-accent-warm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span class="font-serif italic text-[15px]">Target content was deleted or is no longer available in the database.</span>
                </div>
            @endif
        </div>

        {{-- Reason for reporting --}}
        <div>
            <h3 class="font-mono text-[10px] text-muted uppercase tracking-[0.2em] mb-4 border-b border-rule pb-2">Violation Reason</h3>
            
            <div class="mb-4">
                <span class="px-3 py-1 bg-ink text-paper rounded-sm font-mono text-[10px] font-bold uppercase tracking-widest">
                    {{ str_replace('_', ' ', $report->reason_type->value) }}
                </span>
            </div>

            @if($report->reason)
                <div class="font-serif text-[15px] text-ink italic bg-aged/20 p-5 border-l-4 border-accent-warm">
                    "{{ $report->reason }}"
                </div>
            @else
                <p class="font-serif text-[15px] text-muted italic">No additional details were provided by the reporter.</p>
            @endif
        </div>

        {{-- Metadata / Audit Trail --}}
        <div class="bg-aged/10 p-6 border border-rule">
            <h3 class="font-mono text-[10px] text-ink font-bold uppercase tracking-[0.2em] mb-4">Audit Trail</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 font-mono text-[10px] uppercase tracking-widest text-muted">
                <div>
                    <span class="block opacity-50 mb-1">Submitted At</span>
                    <span class="text-ink">{{ $report->created_at->format('M d, Y - H:i A') }}</span>
                </div>
                @if($report->resolved_by)
                    <div>
                        <span class="block opacity-50 mb-1">Resolved By</span>
                        <span class="text-ink font-bold">{{ $report->resolver->username ?? 'Unknown Admin' }}</span>
                    </div>
                    <div>
                        <span class="block opacity-50 mb-1">Resolved At</span>
                        <span class="text-ink">{{ $report->resolved_at->format('M d, Y - H:i A') }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Action Buttons --}}
        @if($report->status === \App\Enums\ReportStatus::PENDING)
            <div x-data="{ 
                processing: false,
                async handleAction(url) {
                    if (!confirm('Are you sure? This action cannot be undone.')) return;
                    this.processing = true;
                    try {
                        const response = await fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        if (response.ok) {
                            window.location.href = '{{ route('admin.reports.index') }}';
                        } else {
                            alert('Action failed. Please try again.');
                            this.processing = false;
                        }
                    } catch (e) {
                        alert('Connection error. Please check your network.');
                        this.processing = false;
                    }
                }
            }" class="pt-8 mt-4 border-t border-rule flex flex-col sm:flex-row gap-4">

                <button @click="handleAction('{{ route('admin.reports.dismiss', $report) }}')"
                        :disabled="processing"
                        class="flex-1 py-3 bg-transparent border border-rule text-ink font-mono text-[10px] uppercase tracking-widest hover:border-ink transition-colors focus:outline-none disabled:opacity-50">
                    <span x-text="processing ? 'Processing...' : 'Dismiss Report'"></span>
                </button>

                <button @click="handleAction('{{ route('admin.reports.resolve', $report) }}')"
                        :disabled="processing"
                        class="flex-1 py-3 bg-accent-warm text-paper font-mono text-[10px] uppercase tracking-widest hover:opacity-80 transition-opacity shadow-sm focus:outline-none disabled:opacity-50">
                    <span x-text="processing ? 'Processing...' : '{{ $report->target_type === App\Models\User::class ? 'Ban Scholar' : 'Hide Content' }}'"></span>
                </button>
            </div>
        @else
            <div class="pt-6 mt-6 border-t border-rule flex items-center gap-3 text-muted bg-aged/20 p-4 font-serif italic text-[15px]">
                <svg class="w-5 h-5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p>This report has already been processed and is closed.</p>
            </div>
        @endif

    </div>
</div>
@endsection