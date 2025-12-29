@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            Reports Moderation
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Review user reports and take appropriate actions
        </p>
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
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $report->reporter->username ?? 'Unknown' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ class_basename($report->reportable_type) }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $report->reason }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if($report->status === 'pending')
                                    bg-yellow-100 text-yellow-700
                                @elseif($report->status === 'resolved')
                                    bg-green-100 text-green-700
                                @else
                                    bg-red-100 text-red-700
                                @endif">
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if($report->status === 'pending')
                                <div class="flex justify-center gap-2">
                                    <form method="POST" action="{{ route('admin.reports.review', $report) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            class="px-4 py-1.5 text-sm rounded-lg bg-green-600 text-white hover:bg-green-700 transition">
                                            Resolve
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.reports.dismiss', $report) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            class="px-4 py-1.5 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                                            Dismiss
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-sm text-gray-400">No actions</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            No reports available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="grid gap-4 md:hidden">
        @forelse ($reports as $report)
            <div class="bg-white shadow rounded-xl p-4 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-gray-800">
                        {{ $report->reporter->username ?? 'Unknown' }}
                    </span>
                    <span class="text-xs px-2 py-1 rounded-full
                        @if($report->status === 'pending')
                            bg-yellow-100 text-yellow-700
                        @elseif($report->status === 'resolved')
                            bg-green-100 text-green-700
                        @else
                            bg-red-100 text-red-700
                        @endif">
                        {{ ucfirst($report->status) }}
                    </span>
                </div>

                <div class="text-sm text-gray-600">
                    <strong>Target:</strong> {{ class_basename($report->reportable_type) }}
                </div>

                <div class="text-sm text-gray-600">
                    <strong>Reason:</strong> {{ $report->reason }}
                </div>

                @if($report->status === 'pending')
                    <div class="flex gap-2 pt-2">
                        <form method="POST" action="{{ route('admin.reports.review', $report) }}" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button
                                class="w-full py-2 text-sm rounded-lg bg-green-600 text-white hover:bg-green-700">
                                Resolve
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.reports.dismiss', $report) }}" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button
                                class="w-full py-2 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700">
                                Dismiss
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center text-gray-500 py-10">
                No reports available
            </div>
        @endforelse
    </div>

</div>
@endsection
