@extends('layouts.app')

@section('content')
<div class="w-full px-4 py-6">

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Reports Moderation</h1>
        <p class="mt-1 text-gray-500">Review user reports and take appropriate actions</p>
    </div>

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white shadow-lg rounded-xl overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Reporter</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Target</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Reason</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Status</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse ($reports as $report)
                    <tr class="hover:bg-gray-50 transition cursor-pointer"
    data-report-id="{{ $report->id }}"
    onclick="openModal(this)">

                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $report->reporter->username ?? 'Unknown' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-700 truncate max-w-xs">
                            @if($report->target)
                                @if($report->target_type === \App\Models\Post::class)
                                    Post: {{ Str::limit($report->target->title, 50) }}
                                @elseif($report->target_type === \App\Models\Comment::class)
                                    Comment: {{ Str::limit($report->target->body, 50) }}
                                @elseif($report->target_type === \App\Models\User::class)
                                    User: {{ $report->target->username }}
                                @endif
                            @else
                                Deleted
                            @endif
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-xs">
                            {{ $report->reason }}
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if($report->status->value === 'pending') bg-yellow-100 text-yellow-700
                                @elseif($report->status->value === 'reviewed') bg-green-100 text-green-700
                                @else bg-red-100 text-red-700
                                @endif">
                                {{ ucfirst($report->status->value) }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4 text-center">
                            @if($report->status->value === 'pending')
                                <div class="flex justify-center gap-2">
                                    <form method="POST" action="{{ route('admin.reports.review', $report) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="px-4 py-1.5 text-sm rounded-lg bg-green-600 text-white hover:bg-green-700 transition">
                                            Resolve
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.reports.dismiss', $report) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="px-4 py-1.5 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                                            Dismiss
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-sm text-gray-400">No actions</span>
                            @endif
                        </td>
                    </tr>

                    {{-- Modal --}}
                    <div id="modal-{{ $report->id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-xl w-11/12 max-w-lg p-6 relative">
                            <button data-report-id="{{ $report->id }}"
        onclick="closeModal(this)">
&times;</button>

                            <h2 class="text-xl font-bold text-gray-800 mb-4">Report Details</h2>

                            <p><strong>Reporter:</strong> {{ $report->reporter->username ?? 'Unknown' }}</p>

                            <p><strong>Target:</strong>
                                @if($report->target)
                                    @if($report->target_type === \App\Models\Post::class)
                                        Post: {{ $report->target->title }}
                                    @elseif($report->target_type === \App\Models\Comment::class)
                                        Comment: {{ $report->target->body }}
                                    @elseif($report->target_type === \App\Models\User::class)
                                        User: {{ $report->target->username }}
                                    @endif
                                @else
                                    Deleted
                                @endif
                            </p>

                            <p><strong>Reason:</strong> {{ $report->reason }}</p>

                            <p>
                                <strong>Status:</strong>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if($report->status->value === 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($report->status->value === 'reviewed') bg-green-100 text-green-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ ucfirst($report->status->value) }}
                                </span>
                            </p>

                            @if($report->status->value === 'pending')
                                <div class="mt-4 flex gap-2">
                                    <form method="POST" action="{{ route('admin.reports.review', $report) }}" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button class="w-full py-2 text-white bg-green-600 rounded hover:bg-green-700 transition">
                                            Resolve
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.reports.dismiss', $report) }}" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button class="w-full py-2 text-white bg-red-600 rounded hover:bg-red-700 transition">
                                            Dismiss
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            No reports available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="mt-4 px-6">
            {{ $reports->links() }}
        </div>
    </div>

</div>

{{-- Modal Scripts --}}
<script>
    function openModal(id) {
        const modal = document.getElementById('modal-' + id);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal(id) {
        const modal = document.getElementById('modal-' + id);
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection
