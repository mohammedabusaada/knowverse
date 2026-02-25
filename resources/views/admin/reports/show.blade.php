@extends('admin.layouts.app')

@section('header', 'Report Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Back Link --}}
    <div>
        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Reports
        </a>
    </div>

    {{-- Header Info --}}
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Report #{{ $report->id }}
        </h1>
        
        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider border
            @if($report->status === \App\Enums\ReportStatus::PENDING)
                bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800
            @elseif($report->status === \App\Enums\ReportStatus::RESOLVED)
                bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-800
            @else
                bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-700
            @endif">
            {{ $report->status->value }}
        </span>
    </div>

    {{-- Main Card --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm rounded-2xl p-6 sm:p-8 space-y-8">

        {{-- Reporter Info --}}
        <div>
            <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3 border-b border-gray-100 dark:border-gray-800 pb-2">Reported By</h3>
            <div class="flex items-center gap-3">
                <x-user-avatar :src="$report->reporter?->profile_picture_url" size="sm" />
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $report->reporter?->username ?? 'Unknown user' }} 
                    <span class="text-gray-400 font-normal ml-1">(ID: {{ $report->reporter_id }})</span>
                </p>
            </div>
        </div>

        {{-- Target Content Preview --}}
        <div>
            <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3 border-b border-gray-100 dark:border-gray-800 pb-2">Target Content</h3>

            @if($report->target)
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 bg-gray-50 dark:bg-gray-950/50">
                    @if($report->target_type === \App\Models\Post::class)
                        <p class="font-bold text-gray-900 dark:text-white mb-2 text-lg">{{ $report->target->title }}</p>
                        <div class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3 prose dark:prose-invert">
                            {{ strip_tags($report->target->body) }}
                        </div>
                        <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-800 text-xs text-gray-500">
                            <strong>Type:</strong> Post &bull; <strong>Author ID:</strong> {{ $report->target->user_id }}
                        </div>

                    @elseif($report->target_type === \App\Models\Comment::class)
                        <p class="font-bold text-gray-900 dark:text-white mb-2">Comment on a Post</p>
                        <div class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3 prose dark:prose-invert">
                            {{ strip_tags($report->target->body) }}
                        </div>
                        <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-800 text-xs text-gray-500">
                            <strong>Type:</strong> Comment &bull; <strong>Author ID:</strong> {{ $report->target->user_id }}
                        </div>

                    @elseif($report->target_type === \App\Models\User::class)
                        <div class="flex items-center gap-4">
                            <x-user-avatar :src="$report->target->profile_picture_url" size="lg" />
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white text-lg">{{ $report->target->display_name }}</p>
                                <p class="text-sm text-gray-500">{{ '@' . $report->target->username }}</p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-800 text-xs text-gray-500">
                            <strong>Type:</strong> User Profile
                        </div>
                    @endif
                </div>
            @else
                <div class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span class="text-sm font-medium">Target content was deleted or is no longer available in the database.</span>
                </div>
            @endif
        </div>

        {{-- Reason for reporting --}}
        <div>
            <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3 border-b border-gray-100 dark:border-gray-800 pb-2">Violation Reason</h3>
            
            <div class="mb-3">
                <span class="px-2.5 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 rounded-md text-xs font-bold border border-red-200 dark:border-red-800 uppercase tracking-wider">
                    {{ str_replace('_', ' ', $report->reason_type->value) }}
                </span>
            </div>

            @if($report->reason)
                <div class="text-sm text-gray-700 dark:text-gray-300 italic bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border-l-4 border-gray-300 dark:border-gray-600">
                    "{{ $report->reason }}"
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 italic">No additional context provided by the reporter.</p>
            @endif
        </div>

        {{-- Metadata / Audit Trail --}}
        <div class="bg-gray-50 dark:bg-gray-950/50 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
            <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3">Audit Trail</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span class="block text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 mb-1">Submitted At</span>
                    {{ $report->created_at->format('M d, Y - H:i A') }}
                </div>
                @if($report->resolved_by)
                    <div>
                        <span class="block text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 mb-1">Resolved By</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $report->resolver->username ?? 'Unknown' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 mb-1">Resolved At</span>
                        {{ $report->resolved_at->format('M d, Y - H:i A') }}
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
                        alert('Error connecting to the server.');
                        this.processing = false;
                    }
                }
            }" class="pt-6 mt-6 border-t border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row gap-3">

                <button @click="handleAction('{{ route('admin.reports.resolve', $report) }}')"
                        :disabled="processing"
                        class="flex-1 px-6 py-3 rounded-xl bg-red-600 dark:bg-red-500 text-white hover:bg-red-700 dark:hover:bg-red-600 transition font-bold disabled:opacity-50 shadow-md focus:outline-none focus:ring-2 focus:ring-red-500/50">
                    {{-- Change text based on target type --}}
                    <span x-text="processing ? 'Processing...' : '{{ $report->target_type === App\Models\User::class ? 'Resolve & Ban User' : 'Resolve & Hide Content' }}'"></span>
                </button>

                <button @click="handleAction('{{ route('admin.reports.dismiss', $report) }}')"
                        :disabled="processing"
                        class="flex-1 px-6 py-3 rounded-xl bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition font-bold disabled:opacity-50 focus:outline-none">
                    <span x-text="processing ? 'Wait...' : 'Dismiss Report'"></span>
                </button>
            </div>
        @else
            <div class="pt-6 mt-6 border-t border-gray-200 dark:border-gray-800 flex items-center gap-3 text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm font-medium">This report has already been processed and is closed.</p>
            </div>
        @endif

    </div>
</div>
@endsection