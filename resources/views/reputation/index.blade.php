@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto py-10">

    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">
        Reputation History — {{ $user->display_name }}
    </h1>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 
                rounded-2xl shadow-sm p-6">

        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-gray-300 dark:border-gray-700">
                    <th class="py-2">Date</th>
                    <th class="py-2">Action</th>
                    <th class="py-2">Change</th>
                    <th class="py-2">Source</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($history as $entry)
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="py-2 text-gray-600 dark:text-gray-400">
                            {{ $entry->created_at->diffForHumans() }}
                        </td>

                        <td class="py-2">
                            {{ str_replace('_', ' ', $entry->action) }}
                        </td>

                        <td class="py-2 font-bold 
                            {{ $entry->delta > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $entry->delta > 0 ? '+' : '' }}{{ $entry->delta }}
                        </td>

                        <td class="py-2">
                            @if ($entry->source)
                                @if ($entry->source_type === \App\Models\Post::class)
                                    <a href="{{ route('posts.show', $entry->source) }}"
                                       class="text-blue-600 dark:text-blue-400 hover:underline">
                                        View Post
                                    </a>
                                @elseif ($entry->source_type === \App\Models\Comment::class)
                                    <a href="{{ route('posts.show', $entry->source->post_id) }}#comment-{{ $entry->source_id }}"
                                       class="text-blue-600 dark:text-blue-400 hover:underline">
                                        View Comment
                                    </a>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6">
            {{ $history->links() }}
        </div>

    </div>

</div>

@endsection
