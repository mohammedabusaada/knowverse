@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('admin.reports.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            ← Back to reports
        </a>

        <h1 class="mt-2 text-3xl font-bold text-gray-800">
            Report #{{ $report->id }}
        </h1>
    </div>

    {{-- Status Badge --}}
    <div class="mb-6">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
            @if($report->status === \App\Enums\ReportStatus::PENDING)
                bg-yellow-100 text-yellow-700
            @elseif($report->status === \App\Enums\ReportStatus::RESOLVED)
                bg-green-100 text-green-700
            @else
                bg-red-100 text-red-700
            @endif">
            {{ ucfirst($report->status->value) }}
        </span>
    </div>

    {{-- Main Card --}}
    <div class="bg-white shadow rounded-xl p-6 space-y-6">

        {{-- Reporter Info --}}
        <div>
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Reported by</h3>
            <p class="text-gray-800">
                {{ $report->reporter?->username ?? 'Unknown user' }} (ID: {{ $report->reporter_id }})
            </p>
        </div>

        {{-- Target Content Preview --}}
        <div>
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Reported content</h3>

            @if($report->target)
                @if($report->target_type === \App\Models\Post::class)
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <p class="font-semibold text-gray-800 mb-1">Post: {{ $report->target->title }}</p>
                        <p class="text-sm text-gray-600">{{ $report->target->body }}</p>
                        <div class="mt-2 pt-2 border-t text-xs text-gray-400">Author ID: {{ $report->target->user_id }}</div>
                    </div>
                @elseif($report->target_type === \App\Models\Comment::class)
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <p class="font-semibold text-gray-800 mb-1">Comment</p>
                        <p class="text-sm text-gray-600">{{ $report->target->body }}</p>
                        <div class="mt-2 pt-2 border-t text-xs text-gray-400">Author ID: {{ $report->target->user_id }}</div>
                    </div>
                @elseif($report->target_type === \App\Models\User::class)
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <p class="font-semibold text-gray-800">User Profile: {{ $report->target->username }}</p>
                    </div>
                @endif
            @else
                <p class="text-sm text-red-500 italic p-4 bg-red-50 rounded-lg border border-red-100">
                    Target content was deleted or is unavailable.
                </p>
            @endif
        </div>

        {{-- Reason for reporting --}}
        <div>
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Report reason</h3>
            <span class="px-2 py-0.5 bg-red-50 text-red-600 rounded text-xs font-bold border border-red-100">
                {{ $report->reason_type->value }}
            </span>

            @if($report->reason)
                <p class="mt-2 text-sm text-gray-600 italic bg-gray-50 p-3 rounded-lg border-l-4 border-gray-300">
                    "{{ $report->reason }}"
                </p>
            @endif
        </div>

        {{-- Metadata --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-600 border-t pt-4">
            <div><strong>Submitted:</strong> {{ $report->created_at->format('Y-m-d H:i') }}</div>
            @if($report->resolved_by)
                <div><strong>Resolved by:</strong> {{ $report->resolver->username }}</div>
                <div><strong>Resolved at:</strong> {{ $report->resolved_at->format('Y-m-d H:i') }}</div>
            @endif
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
            }" class="pt-4 flex gap-3">

                <button @click="handleAction('{{ route('admin.reports.resolve', $report) }}')"
                        :disabled="processing"
                        class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition font-semibold disabled:opacity-50 shadow-md">
                    {{-- 💡 تغيير النص بناءً على نوع الهدف --}}
                    <span x-text="processing ? 'Processing...' : '{{ $report->target_type === App\Models\User::class ? 'Resolve & Ban User' : 'Resolve & Hide Content' }}'"></span>
                </button>

                <button @click="handleAction('{{ route('admin.reports.dismiss', $report) }}')"
                        :disabled="processing"
                        class="px-5 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300 transition font-semibold disabled:opacity-50 border border-gray-300">
                    <span x-text="processing ? 'Wait...' : 'Dismiss Report'"></span>
                </button>
            </div>
        @else
            <div class="pt-4 border-t">
                <p class="text-sm text-gray-500 italic">This report has already been processed and is closed.</p>
            </div>
        @endif

    </div>
</div>
@endsection