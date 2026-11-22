<div x-data="{
    copied: false,
    async share() {
        try {
            await navigator.clipboard.writeText(window.location.href);
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        } catch { window.showToast?.('Could not copy the link. Copy it from the address bar.', 'error'); }
    }
}" class="relative inline-block">
    <button @click="share" 
            class="inline-flex items-center gap-2 px-4 py-1.5 bg-paper border border-rule text-ink font-mono text-xs uppercase tracking-widest hover:bg-aged transition-colors shadow-sm focus:outline-none">
        
        <svg x-show="!copied" class="w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
        </svg>
        
        <svg x-show="copied" class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        
        <span aria-live="polite" x-text="copied ? 'Link Copied' : 'Share'"></span>
    </button>
</div>