@extends('layouts.app')

@section('profile-content')
    <div class="max-w-4xl py-6 animate-[fadeUp_0.4s_ease_both]">
        
        <x-activity.filters :user="$user" :type="request('type', 'all')" />

        <div class="flex flex-col border-t border-rule mt-2">
            @forelse ($activities as $activity)
                <x-activity.item :activity="$activity" />
            @empty
                <div class="py-16 text-center border border-dashed border-rule bg-aged/10 rounded-sm mt-4">
                    <span class="block text-2xl mb-2 opacity-50 text-muted">✦</span>
                    <h3 class="font-heading text-xl font-bold text-ink mb-1">No activity yet</h3>
                    <p class="font-serif text-muted italic text-[15px]">
                        This scholar hasn't performed any public actions in this category yet.
                    </p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $activities->links() }}
        </div>
        
    </div>
@endsection

@section('content')
    @include('profile._layout', ['user' => $user])
@endsection