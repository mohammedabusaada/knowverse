@extends('admin.layouts.app')

@section('header', 'Platform Overview')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto animate-[fadeUp_0.8s_ease_both]">
    
    {{-- Top Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        {{-- Total Discussions --}}
        <div class="bg-paper border border-rule rounded-sm p-6 shadow-sm hover:border-ink transition-colors">
            <div class="flex items-center justify-between">
                <p class="font-mono text-[10px] uppercase tracking-widest text-muted font-bold">Total Discussions</p>
                <span class="p-2 bg-aged text-ink rounded-sm">
                    <x-icons.pencil class="w-4 h-4" />
                </span>
            </div>
            <p class="mt-4 text-3xl font-heading font-black text-ink">{{ number_format($totalPosts) }}</p>
        </div>

        {{-- Total Responses --}}
        <div class="bg-paper border border-rule rounded-sm p-6 shadow-sm hover:border-ink transition-colors">
            <div class="flex items-center justify-between">
                <p class="font-mono text-[10px] uppercase tracking-widest text-muted font-bold">Total Responses</p>
                <span class="p-2 bg-aged text-ink rounded-sm">
                    <x-icons.chat class="w-4 h-4" />
                </span>
            </div>
            <p class="mt-4 text-3xl font-heading font-black text-ink">{{ number_format($totalComments) }}</p>
        </div>

        {{-- Registered Scholars --}}
        <div class="bg-paper border border-rule rounded-sm p-6 shadow-sm hover:border-ink transition-colors">
            <div class="flex items-center justify-between">
                <p class="font-mono text-[10px] uppercase tracking-widest text-muted font-bold">Registered Scholars</p>
                <span class="p-2 bg-aged text-ink rounded-sm">
                    <x-icons.user class="w-4 h-4" />
                </span>
            </div>
            <p class="mt-4 text-3xl font-heading font-black text-ink">{{ number_format($totalUsers) }}</p>
        </div>

        {{-- Pending Reports (Highlighted Warning) --}}
        <div class="bg-paper border border-rule rounded-sm p-6 shadow-sm hover:border-accent-warm transition-colors">
            <div class="flex items-center justify-between">
                <p class="font-mono text-[10px] uppercase tracking-widest text-muted font-bold">Pending Reports</p>
                <span class="p-2 bg-accent-warm/10 text-accent-warm rounded-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </span>
            </div>
            <p class="mt-4 text-3xl font-heading font-black text-accent-warm">{{ number_format($pendingReports) }}</p>
        </div>

    </div>

    {{-- Quick Actions and Moderation Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        {{-- Quick Actions --}}
        <div class="bg-paper border border-rule rounded-sm p-6 shadow-sm">
            <h3 class="font-mono text-[10px] uppercase tracking-widest text-muted mb-5 border-b border-rule pb-2">Quick Actions</h3>
            <div class="space-y-3">
                
                {{-- Accessible to both Admin & Moderator --}}
                <a href="{{ route('admin.reports.index') }}" class="flex items-center justify-between p-4 border border-rule hover:border-ink transition-colors group">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-aged text-ink group-hover:bg-ink group-hover:text-paper transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2z"></path></svg>
                        </div>
                        <div>
                            <p class="font-heading font-bold text-ink">Review Reports</p>
                            <p class="font-serif text-sm italic text-muted">Handle flagged content and users</p>
                        </div>
                    </div>
                    <span class="text-muted group-hover:text-ink transition-colors">&rarr;</span>
                </a>

                {{-- STRICTLY RESTRICTED TO ADMINS ONLY --}}
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between p-4 border border-rule hover:border-ink transition-colors group">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-aged text-ink group-hover:bg-ink group-hover:text-paper transition-colors">
                                <x-icons.user class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="font-heading font-bold text-ink">Manage Scholars</p>
                                <p class="font-serif text-sm italic text-muted">View, edit, or suspend accounts</p>
                            </div>
                        </div>
                        <span class="text-muted group-hover:text-ink transition-colors">&rarr;</span>
                    </a>

                    <a href="{{ route('admin.tags.index') }}" class="flex items-center justify-between p-4 border border-rule hover:border-ink transition-colors group">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-aged text-ink group-hover:bg-ink group-hover:text-paper transition-colors">
                                <x-icons.tag class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="font-heading font-bold text-ink">Manage Topics</p>
                                <p class="font-serif text-sm italic text-muted">Organize system classifications</p>
                            </div>
                        </div>
                        <span class="text-muted group-hover:text-ink transition-colors">&rarr;</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- Moderation Summary --}}
        <div class="bg-paper border border-rule rounded-sm p-6 shadow-sm flex flex-col justify-center relative overflow-hidden">
            <h3 class="font-mono text-[10px] uppercase tracking-widest text-muted mb-2 text-center relative z-10">Moderation Efficiency</h3>
            <p class="font-serif text-[15px] italic text-muted text-center mb-8 relative z-10">Summary of resolved vs dismissed reports</p>
            
            <div class="flex items-center justify-center gap-12 relative z-10">
                {{-- Resolved --}}
                <div class="text-center">
                    <p class="text-5xl font-heading font-black text-ink mb-3">{{ number_format($resolvedReports) }}</p>
                    <span class="px-3 py-1 bg-ink text-paper text-[9px] font-mono uppercase tracking-widest border border-ink">Resolved</span>
                </div>
                
                {{-- Divider --}}
                <div class="w-px h-16 bg-rule"></div>
                
                {{-- Dismissed --}}
                <div class="text-center">
                    <p class="text-5xl font-heading font-black text-muted mb-3">{{ number_format($dismissedReports) }}</p>
                    <span class="px-3 py-1 bg-transparent text-muted border border-rule text-[9px] font-mono uppercase tracking-widest">Dismissed</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection