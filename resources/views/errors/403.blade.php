@extends('layouts.app')

@section('title', 'Restricted Access')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 animate-[fadeUp_0.8s_ease_both]">
    <div class="text-center max-w-2xl relative">
        {{-- Background Giant Number --}}
        <div class="absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 -z-10 select-none pointer-events-none">
            <span class="font-heading text-[15rem] md:text-[20rem] font-black text-accent-warm opacity-10 leading-none">
                403
            </span>
        </div>

        <div class="w-16 h-16 mx-auto mb-6 text-accent-warm relative z-10 bg-paper rounded-full flex items-center justify-center border border-accent-warm/20">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>

        <h1 class="font-heading text-4xl md:text-5xl font-bold text-ink mb-6 tracking-tight relative z-10">
            Restricted Section
        </h1>
        
        <p class="font-serif text-lg text-muted italic mb-12 leading-relaxed relative z-10 bg-paper/50 backdrop-blur-sm py-2">
            Your current academic standing or permissions do not grant you access to this restricted area of the platform.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 relative z-10">
            <a href="{{ url()->previous() }}" class="w-full sm:w-auto px-8 py-3 bg-transparent border border-rule text-ink font-mono text-xs uppercase tracking-widest hover:border-ink transition-colors">
                &larr; Go Back
            </a>
            <a href="{{ route('home') }}" class="w-full sm:w-auto px-8 py-3 bg-ink text-paper font-mono text-xs uppercase tracking-widest hover:opacity-80 transition-opacity shadow-sm">
                Return Home
            </a>
        </div>
    </div>
</div>
@endsection