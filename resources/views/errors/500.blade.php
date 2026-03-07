@extends('layouts.app')

@section('title', 'System Anomaly')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 animate-[fadeUp_0.8s_ease_both]">
    <div class="text-center max-w-2xl relative">
        <div class="absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 -z-10 select-none pointer-events-none">
            <span class="font-heading text-[15rem] md:text-[20rem] font-black text-rule opacity-30 leading-none">
                500
            </span>
        </div>

        <h1 class="font-heading text-4xl md:text-5xl font-bold text-ink mb-6 tracking-tight relative z-10">
            System Anomaly
        </h1>
        
        <p class="font-serif text-lg text-muted italic mb-12 leading-relaxed relative z-10 bg-paper/50 backdrop-blur-sm py-2">
            Our infrastructure encountered an unexpected paradox while processing your request. The moderators have been notified and are working on a resolution.
        </p>

        <div class="relative z-10">
            <a href="{{ route('home') }}" class="inline-block px-8 py-3 bg-ink text-paper font-mono text-xs uppercase tracking-widest hover:opacity-80 transition-opacity shadow-sm">
                Return to Safety
            </a>
        </div>
    </div>
</div>
@endsection