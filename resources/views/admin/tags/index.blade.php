@extends('admin.layouts.app')

@section('header', 'Manage Tags')

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
        <div class="p-4 bg-green-50 text-green-700 border border-green-200 rounded-xl font-medium">
            {{ session('success') }}
        </div>
    @endif
    @error('name')
        <div class="p-4 bg-red-50 text-red-700 border border-red-200 rounded-xl font-medium">
            {{ $message }}
        </div>
    @enderror

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
        <form method="GET" action="{{ route('admin.tags.index') }}" class="w-full sm:w-96 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tags..." 
                   class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white focus:ring-2 focus:ring-black dark:focus:ring-white focus:border-transparent transition-all sm:text-sm">
        </form>
        
        <button @click="openCreate()" class="w-full sm:w-auto px-6 py-2 bg-black hover:bg-gray-800 dark:bg-white dark:text-black dark:hover:bg-gray-200 text-white font-bold rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
            <span>+</span> Create Tag
        </button>
    </div>

    {{-- Data Table --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-950/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tag Name</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Slug</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Posts Count</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse($tags as $tag)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900 dark:text-white">
                                #{{ $tag->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $tag->slug }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                {{ number_format($tag->posts_count) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="openEdit({{ $tag->id }}, '{{ addslashes($tag->name) }}')" class="text-gray-400 hover:text-blue-600 transition" title="Edit Tag">
                                        <x-icons.pencil class="w-5 h-5" />
                                    </button>

                                    <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" onsubmit="return confirm('Delete this tag? This will remove the tag from all associated posts.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition" title="Delete Tag">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                No tags found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($tags->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/50">
                {{ $tags->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-hidden" x-cloak>
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showModal = false"></div>
        
        <div class="relative bg-white dark:bg-gray-900 rounded-3xl w-full max-w-md p-8 shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-6" x-text="isEdit ? 'Edit Tag' : 'Create New Tag'"></h2>

            <form :action="formAction" method="POST">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tag Name</label>
                    <input type="text" name="name" x-model="tagName" required placeholder="e.g. Science"
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white focus:ring-2 focus:ring-black dark:focus:ring-white transition">
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="showModal = false" class="flex-1 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-3 bg-black dark:bg-white text-white dark:text-black rounded-xl font-bold hover:bg-gray-800 dark:hover:bg-gray-200 shadow-md transition">
                        Save Tag
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection