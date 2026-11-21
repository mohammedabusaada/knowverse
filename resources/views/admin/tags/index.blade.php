@extends('admin.layouts.app')

@section('header', 'Manage Topics')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
        showModal: false,
        isEdit: false,
        formAction: '{{ route('admin.tags.store') }}',
        tagName: '',

        openCreate() {
            this.isEdit = false;
            this.tagName = '';
            this.formAction = '{{ route('admin.tags.store') }}';
            this.showModal = true;
        },

        openEdit(id, name) {
            this.isEdit = true;
            this.tagName = name;
            this.formAction = '/admin/tags/' + id;
            this.showModal = true;
        }
    }">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 text-green-700 border border-green-200 rounded-md font-medium">
            {{ session('success') }}
        </div>
    @endif
    @error('name')
        <div class="p-4 bg-red-50 text-red-700 border border-red-200 rounded-md font-medium">
            {{ $message }}
        </div>
    @enderror

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white dark:bg-neutral-900 p-4 rounded-lg border border-neutral-200 dark:border-neutral-800 shadow-sm">
        <form method="GET" action="{{ route('admin.tags.index') }}" class="w-full sm:w-96 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-neutral-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="search" aria-label="Search topics" value="{{ request('search') }}" placeholder="Search topics..."
                   class="block w-full pl-10 pr-3 py-2 border border-neutral-200 dark:border-neutral-700 rounded-md bg-neutral-50 dark:bg-neutral-950 text-neutral-900 dark:text-white focus:ring-2 focus:ring-black dark:focus:ring-white focus:border-transparent transition-all sm:text-sm">
        </form>

        <button @click="openCreate()" class="w-full sm:w-auto px-6 py-2 bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200 text-white font-bold rounded-md shadow-md transition-all flex items-center justify-center gap-2">
            <span>+</span> Create Topic
        </button>
    </div>

    {{-- Data Table --}}
    <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                <thead class="bg-neutral-50 dark:bg-neutral-950/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-bold text-neutral-500 uppercase tracking-wider">Topic Name</th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-bold text-neutral-500 uppercase tracking-wider">Slug</th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-bold text-neutral-500 uppercase tracking-wider">Posts Count</th>
                        <th scope="col" class="px-6 py-4 text-right text-sm font-bold text-neutral-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                    @forelse($tags as $tag)
                        <tr class="hover:bg-neutral-50/50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-neutral-900 dark:text-white">
                                #{{ $tag->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $tag->slug }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text">
                                {{ number_format($tag->posts_count) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="openEdit({{ $tag->id }}, @js($tag->name))" class="min-w-11 min-h-11 inline-flex items-center justify-center text-muted hover:text-text transition" title="Edit Topic">
                                        <x-icons.pencil class="w-5 h-5" />
                                    </button>

                                    <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" onsubmit="return confirm('Delete this topic? This will remove the topic from all associated posts.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="min-w-11 min-h-11 inline-flex items-center justify-center text-muted hover:text-text transition" title="Delete Topic">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-neutral-500">
                                No topics found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tags->hasPages())
            <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-950/50">
                {{ $tags->onEachSide(1)->links('pagination.academic') }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    <div x-show="showModal" @keydown.escape.window="showModal = false" role="dialog" aria-modal="true" aria-label="Topic editor" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-hidden" x-cloak>
        <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-sm" @click="showModal = false"></div>

        <div class="relative bg-white dark:bg-neutral-900 rounded-lg w-full max-w-md p-8 shadow-2xl border border-neutral-200 dark:border-neutral-800 overflow-hidden"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <h2 class="text-2xl font-black text-neutral-900 dark:text-white mb-6" x-text="isEdit ? 'Edit Topic' : 'Create New Topic'"></h2>

            <form :action="formAction" method="POST">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="mb-6">
                    <label for="topic-name" class="block text-sm font-bold text-neutral-700 dark:text-neutral-300 mb-2">Topic Name</label>
                    <input type="text" id="topic-name" name="name" x-model="tagName" required placeholder="e.g. Science"
                           class="w-full px-4 py-3 rounded-md border border-neutral-300 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-950 text-neutral-900 dark:text-white focus:ring-2 focus:ring-black dark:focus:ring-white transition">
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="showModal = false" class="flex-1 py-3 bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 rounded-md font-bold hover:bg-neutral-200 dark:hover:bg-neutral-700 transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-3 bg-black dark:bg-white text-white dark:text-black rounded-md font-bold hover:bg-neutral-800 dark:hover:bg-neutral-200 shadow-md transition">
                        Save Topic
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
