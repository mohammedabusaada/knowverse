<div
    x-data="{
        followed: @js($followed),
        loading: false,
        toggle() {
            this.loading = true;
            setTimeout(() => {
                this.followed = !this.followed;
                this.loading = false;
            }, 800);
        }
    }"
>
<button
    @click="toggle"
    :disabled="loading"
    class="min-w-[140px] flex items-center justify-center gap-2 px-6 py-2.5 rounded-full text-sm font-semibold tracking-wide uppercase transition-all duration-300 shadow-md hover:shadow-lg disabled:opacity-50 disabled:shadow-none disabled:cursor-not-allowed border-2 transform active:scale-[0.98]"
    :class="followed
        ? 'bg-gradient-to-r from-blue-700 to-indigo-700 text-white border-transparent hover:from-blue-800 hover:to-indigo-800'
        : 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white border-transparent hover:from-blue-700 hover:to-indigo-700'"
>
    <svg x-show="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-30" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
    </svg>

    <span x-text="loading ? 'Processing...' : (followed ? 'Following' : 'Follow')"></span>
</button>
</div>
