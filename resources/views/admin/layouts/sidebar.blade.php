<div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-gray-900/80 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" x-cloak></div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-black text-gray-300 transition-transform duration-300 ease-in-out lg:static lg:flex lg:flex-col lg:shrink-0 shadow-2xl lg:shadow-none">

    <div class="h-16 flex items-center px-6 border-b border-gray-800 bg-black">
        <a href="{{ route('admin.dashboard') }}" class="text-xl font-extrabold text-white tracking-tight flex items-center gap-3">
            <span class="w-8 h-8 bg-white text-black flex items-center justify-center rounded-lg text-lg">K</span>
            Admin Panel
        </a>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <p class="px-4 text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Menu</p>
        
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-white text-black shadow-md' : 'hover:bg-gray-900 hover:text-white' }}">
            <x-icons.chart class="w-5 h-5" />
            <span>Overview</span>
        </a>

        {{-- Visible to ADMINS ONLY --}}
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all {{ request()->routeIs('admin.users.*') ? 'bg-white text-black shadow-md' : 'hover:bg-gray-900 hover:text-white' }}">
                <x-icons.user class="w-5 h-5" />
                <span>Users</span>
            </a>

            <a href="{{ route('admin.tags.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all {{ request()->routeIs('admin.tags.*') ? 'bg-white text-black shadow-md' : 'hover:bg-gray-900 hover:text-white' }}">
                <x-icons.tag class="w-5 h-5" />
                <span>Tags</span>
            </a>
        @endif

        {{-- Visible to ADMINS and MODERATORS --}}
        <a href="{{ route('admin.reports.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-white text-black shadow-md' : 'hover:bg-gray-900 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
            <span>Reports</span>
            
            @php $pendingCount = \App\Models\Report::pending()->count(); @endphp
            @if($pendingCount > 0)
                <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">{{ $pendingCount }}</span>
            @endif
        </a>
    </nav>

    <div class="p-4 border-t border-gray-800">
        <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-900 hover:text-white transition-all text-sm font-medium">
            <x-icons.home class="w-5 h-5 opacity-70" />
            <span>Back to KnowVerse</span>
        </a>
    </div>
</aside>