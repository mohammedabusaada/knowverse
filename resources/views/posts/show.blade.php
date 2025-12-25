@extends('layouts.app')

@section('content')
{{-- Reading Progress Bar --}}
<div class="fixed top-0 left-0 w-full h-1 z-50">
    <div x-data="{ 
            percent: 0, 
            updateProgress() { 
                let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
                let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                this.percent = (winScroll / height) * 100;
            } 
         }"
         x-init="window.addEventListener('scroll', () => updateProgress())"
         :style="'width: ' + percent + '%'"
         class="h-full bg-blue-500 transition-all duration-150">
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8 lg:flex lg:gap-8">
    
    <div class="lg:w-3/4">
        
        <div class="mb-6">
            <nav class="flex text-xs text-gray-500 uppercase tracking-widest mb-4">
                <a href="{{ route('posts.index') }}" class="hover:text-blue-500">Posts</a>
                <span class="mx-2">/</span>
                <span class="text-gray-400">Discussion</span>
            </nav>
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white leading-tight">
                {{ $post->title }}
            </h1>
        </div>

        <div class="flex gap-4 md:gap-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-4 md:p-6 shadow-sm">
            
            <div class="flex flex-col items-center pt-2">
                <x-post-vote :post="$post" />
                
                <button title="Bookmark" class="mt-4 text-gray-400 hover:text-yellow-500 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-hidden">
                <div class="flex items-center gap-2 text-sm mb-6 border-b dark:border-gray-700 pb-4">
                    <x-user-avatar :src="$post->user->profile_picture_url" size="xs" />
                    
                    {{-- User Hover Card --}}
                    <x-user-hover-card :user="$post->user" />

                    <span class="text-gray-400">•</span>
                    <span class="text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
                    
                    {{-- Reading Time Estimate --}}
                    @php
                        $wordCount = str_word_count(strip_tags($post->body));
                        $minutes = ceil($wordCount / 200); // Average reading speed
                    @endphp
                    <span class="text-gray-400">•</span>
                    <span class="text-gray-400 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $minutes }} min read
                    </span>
                    
                    <div class="ml-auto flex items-center gap-3">
                        <span class="hidden sm:flex items-center gap-1 text-gray-400"><x-icons.eye class="w-4 h-4"/> {{ number_format($post->view_count) }}</span>
                        
                        {{-- Share Button --}}
                        <div x-data="{ 
                                copied: false, 
                                share() { 
                                    navigator.clipboard.writeText(window.location.href); 
                                    this.copied = true; 
                                    setTimeout(() => this.copied = false, 2000); 
                                } 
                            }" class="relative">
                            <button @click="share" class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/30 text-gray-600 dark:text-gray-400 hover:text-blue-600 rounded-lg transition font-bold text-xs">
                                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                <svg x-show="copied" class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="copied ? 'Copied!' : 'Share'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                @if ($post->image)
                    <div class="mb-6">
                        <img src="{{ asset('storage/'.$post->image) }}" class="rounded-xl w-full object-cover max-h-[500px]">
                    </div>
                @endif

                <article class="prose prose-lg dark:prose-invert max-w-none mb-8 leading-relaxed">
                    <x-markdown :text="$post->body" />
                </article>

                <div class="flex items-center justify-between pt-4 border-t dark:border-gray-700">
                    @if ($post->tags->count())
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->tags as $tag)
                                <a href="/search?q={{ $tag->name }}&type=tags" class="px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold rounded-full hover:bg-blue-100 transition">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Admin Actions --}}
                    @can('update', $post)
                    <div class="flex gap-2">
                        <a href="{{ route('posts.edit', $post) }}" class="text-xs font-bold text-gray-500 hover:text-blue-500 uppercase">Edit</a>
                        <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete post?');">
                            @csrf @method('DELETE')
                            <button class="text-xs font-bold text-gray-500 hover:text-red-500 uppercase">Delete</button>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Discussion Section --}}
        <div class="mt-8">
            <h2 class="text-xl font-bold dark:text-white mb-6 flex items-center gap-2">
                <x-icons.chat class="w-6 h-6" />
                Discussion ({{ $post->allComments()->count() }})
            </h2>

            @auth
                <div class="mb-8 bg-gray-50 dark:bg-gray-900/50 p-6 rounded-2xl border border-gray-200 dark:border-gray-700">
                    <form action="{{ route('comments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $post->id }}">
                        <x-textarea name="body" rows="4" class="w-full border-gray-300 focus:ring-blue-500" placeholder="What are your thoughts?" required></x-textarea>
                        <div class="flex justify-end mt-4">
                            <x-button class="bg-blue-600 hover:bg-blue-700 rounded-full px-6">Post Comment</x-button>
                        </div>
                    </form>
                </div>
            @else
                <div class="p-6 text-center bg-gray-50 dark:bg-gray-800 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-700 mb-8">
                    <p class="text-gray-600 dark:text-gray-400">Want to join the conversation? <a href="{{ route('login') }}" class="text-blue-600 font-bold">Log in</a> to comment.</p>
                </div>
            @endauth

            <div class="space-y-6">
                @foreach ($comments as $comment)
                    <x-comment :comment="$comment" />
                @endforeach
            </div>
        </div>
    </div>

    <div class="hidden lg:block lg:w-1/4">
        <div class="sticky top-24 space-y-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4 text-xs uppercase tracking-widest">About the Author</h3>
                <div class="flex items-center gap-3 mb-4">
                    <x-user-avatar :src="$post->user->profile_picture_url" size="lg" />
                    <div>
                        <p class="font-bold dark:text-white leading-tight">{{ $post->user->display_name }}</p>
                        <p class="text-xs text-gray-500">{{ '@'.$post->user->username }}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 mb-4">
                    {{ $post->user->bio ?? 'This user hasn\'t shared a bio yet.' }}
                </p>
                <a href="{{ route('profiles.show', $post->user->username) }}" class="block text-center py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl text-sm font-bold transition">
                    View Profile
                </a>
            </div>

            <div class="p-4 text-[11px] text-gray-500 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700">
                <h4 class="font-bold uppercase tracking-wider mb-2 text-gray-700 dark:text-gray-300">Rules</h4>
                <ul class="list-disc ml-4 space-y-1">
                    <li>Be respectful.</li>
                    <li>No spam.</li>
                    <li>Cite sources.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection