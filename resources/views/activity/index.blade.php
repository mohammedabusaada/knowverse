@extends('layouts.app')

@section('content')
    @include('profile._layout', ['user' => $user])

    @section('profile-content')
        <div class="max-w-4xl mx-auto py-10">
            @include('activity.partials.filters')

            <div class="space-y-8 mt-6">
                {{-- Group activities by date --}}
                @forelse ($activities->groupBy(fn($item) => $item->created_at->format('Y-m-d')) as $date => $dayActivities)
                    <section>
                        <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            {{ \Carbon\Carbon::parse($date)->isToday() ? 'Today' : (\Carbon\Carbon::parse($date)->isYesterday() ? 'Yesterday' : \Carbon\Carbon::parse($date)->format('F j, Y')) }}
                        </h3>

                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 divide-y dark:divide-gray-700 shadow-sm">
                            @foreach ($dayActivities as $activity)
                                @include('activity._item', ['activity' => $activity])
                            @endforeach
                        </div>
                    </section>
                @empty
                    <x-empty-state 
                        title="No Activity" 
                        message="No activity recorded for this user yet." 
                    />
                @endforelse
            </div>

            <div class="mt-8">
                {{ $activities->links() }}
            </div>
        </div>
    @endsection
@endsection