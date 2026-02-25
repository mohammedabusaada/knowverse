@extends('admin.layouts.app')

@section('header', 'Platform Overview')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    
    {{-- Top Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Total Posts</p>
                <span class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg">
                    <x-icons.pencil class="w-5 h-5" />
                </span>
            </div>
            <p class="mt-4 text-3xl font-black text-gray-900 dark:text-white">{{ number_format($totalPosts) }}</p>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Total Comments</p>
                <span class="p-2 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-lg">
                    <x-icons.chat class="w-5 h-5" />
                </span>
            </div>
            <p class="mt-4 text-3xl font-black text-gray-900 dark:text-white">{{ number_format($totalComments) }}</p>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Registered Users</p>
                <span class="p-2 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-lg">
                    <x-icons.user class="w-5 h-5" />
                </span>
            </div>
            <p class="mt-4 text-3xl font-black text-gray-900 dark:text-white">{{ number_format($totalUsers) }}</p>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Pending Reports</p>
                <span class="p-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </span>
            </div>
            <p class="mt-4 text-3xl font-black text-gray-900 dark:text-white">{{ number_format($pendingReports) }}</p>
        </div>

    </div>

    {{-- Quick Actions and Moderation Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        {{-- Quick Actions --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.reports.index') }}" class="flex items-center justify-between p-4 rounded-xl border border-gray-100 dark:border-gray-800 hover:border-black dark:hover:border-white transition group">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg group-hover:bg-black group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-black transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2z"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white">Review Reports</p>
                            <p class="text-xs text-gray-500">Handle flagged content and users</p>
                        </div>
                    </div>
                    <span class="text-gray-400 group-hover:text-black dark:group-hover:text-white">&rarr;</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between p-4 rounded-xl border border-gray-100 dark:border-gray-800 hover:border-black dark:hover:border-white transition group">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg group-hover:bg-black group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-black transition">
                            <x-icons.user class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white">Manage Users</p>
                            <p class="text-xs text-gray-500">View, edit, or ban accounts</p>
                        </div>
                    </div>
                    <span class="text-gray-400 group-hover:text-black dark:group-hover:text-white">&rarr;</span>
                </a>
            </div>
        </div>

        {{-- Moderation Summary --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm flex flex-col justify-center">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 text-center">Moderation Efficiency</h3>
            <p class="text-sm text-gray-500 text-center mb-8">Summary of resolved vs dismissed reports</p>
            
            <div class="flex items-center justify-center gap-12">
                <div class="text-center">
                    <p class="text-5xl font-black text-green-500 mb-2">{{ number_format($resolvedReports) }}</p>
                    <span class="px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-xs font-bold uppercase tracking-widest">Resolved</span>
                </div>
                <div class="w-px h-20 bg-gray-200 dark:bg-gray-700"></div>
                <div class="text-center">
                    <p class="text-5xl font-black text-gray-400 dark:text-gray-600 mb-2">{{ number_format($dismissedReports) }}</p>
                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded-full text-xs font-bold uppercase tracking-widest">Dismissed</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection