@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 animate-[fadeUp_0.8s_ease_both]">
    <div class="text-center max-w-2xl relative">
        {{-- Background Giant Number --}}
        <div class="absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 -z-10 select-none pointer-events-none">
            <span class="font-heading text-[15rem] md:text-[20rem] font-black text-rule opacity-30 leading-none">
                404
            </span>
        </div>

        {{-- Content --}}
        <h1 class="font-heading text-4xl md:text-5xl font-bold text-ink mb-6 tracking-tight relative z-10 uppercase">
            Lost in the Discourse
        </h1>
        
        <p class="font-serif text-lg text-muted italic mb-12 leading-relaxed relative z-10 bg-paper/50 backdrop-blur-sm py-2">
            The discussion, scholar, or topic you are seeking has drifted out of reach or never entered the platform.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 relative z-10">
            <a href="{{ route('home') }}" class="w-full sm:w-auto px-8 py-3 bg-ink text-paper font-mono text-xs uppercase tracking-widest hover:bg-transparent hover:text-ink border border-ink transition-all shadow-sm">
                Return to Entrance
            </a>
            <a href="{{ route('posts.index') }}" class="w-full sm:w-auto px-8 py-3 bg-transparent border border-rule text-ink font-mono text-xs uppercase tracking-widest hover:border-ink transition-colors">
                Explore Discussions
            </a>
        </div>
    </div>
</div>
@endsection