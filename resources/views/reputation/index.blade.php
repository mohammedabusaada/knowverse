@extends('layouts.app')

{{-- 
    Academic Reputation Ledger
    ---------------------------
    This view serves as the formal record of a scholar's influence within the KnowVerse.
    It balances transparency with privacy by showing aggregate standing publicly 
    while restricting granular interaction logs to the account owner.
--}}

@section('profile-content')
    <div class="max-w-4xl py-6 animate-[fadeUp_0.8s_ease_both]">
        <div class="flex flex-col gap-8">
            
            {{-- Section Header: Defines the context of Scholarly Standing --}}
            <header class="border-b border-rule pb-4">
                <h1 class="font-heading text-3xl font-bold text-ink mb-2">
                    Scholarly Standing
                </h1>
                <p class="font-serif text-[15px] text-muted italic">
                    A comprehensive record of academic influence and peer-evaluated contributions within the KnowVerse community.
                </p>
            </header>

            {{-- 1. Quantitative Standing Widget --}}
            {{-- This component remains public to showcase the scholar's rank and total reputation --}}
            <x-profile.reputation-widget :user="$user" />

            {{-- 2. Granular Activity Ledger --}}
            {{-- 
                Privacy Logic: 
                Granular credit/debit history is treated as sensitive personal data.
                Only the account holder can audit the specific sources of their reputation points.
            --}}
            @if($isPrivate)
                <div class="py-20 text-center border border-dashed border-rule bg-aged/10 rounded-sm mt-4">
                    <div class="flex justify-center mb-5">
                        {{-- Security Icon representing restricted access --}}
                        <svg class="w-10 h-10 text-muted opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="font-serif text-lg text-ink font-bold mb-1">Restricted Ledger</h3>
                    <p class="font-serif text-sm text-muted italic">
                        Detailed interaction logs are strictly confidential and accessible only to the authenticated scholar.
                    </p>
                </div>
            @else
                {{-- Detailed Activity History for the Owner --}}
                <div class="space-y-6 mt-8">
                    <div class="flex items-center justify-between border-b border-rule pb-2">
                        <h2 class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted font-bold">
                            Ledger of Contributions
                        </h2>
                        <span class="font-mono text-[9px] text-muted uppercase tracking-widest">Merits & Retractions</span>
                    </div>
                    
                    {{-- 
                        Data Presentation Table: 
                        Injects the chronological history of reputation adjustments.
                    --}}
                    @include('reputation.partials.table', ['history' => $history])
                </div>
            @endif

        </div>
    </div>
@endsection

@section('content')
    {{-- 
        Structural Layout Wrapper 
        ------------------------
        Integrates the reputation view within the global profile navigation framework.
    --}}
    @include('profile._layout', ['user' => $user])
@endsection