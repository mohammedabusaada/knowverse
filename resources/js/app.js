import './bootstrap';
import Alpine from 'alpinejs';
import EasyMDE from "easymde";
import './vote.js';

window.Alpine = Alpine;
window.EasyMDE = EasyMDE;

// 1. Global Keyboard Shortcuts
window.addEventListener('keydown', (e) => {
    // CMD/CTRL + K: Focus Search
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        const searchInput = document.querySelector('input[name="q"], input[type="search"]');
        if (searchInput) {
            e.preventDefault();
            e.stopPropagation();
            searchInput.focus();
            searchInput.select();
        }
    }
});

// 2. Alpine Data Components
document.addEventListener('alpine:init', () => {
    
    // Search Tabs Component
    Alpine.data('searchTabs', (initial) => ({
        tab: initial ?? 'posts',

        switchTab(tab) {
            this.tab = tab;
            const url = new URL(window.location);
            url.searchParams.set('type', tab);
            window.location.href = url.toString(); 
        },

        init() {
            window.addEventListener('popstate', () => {
                const params = new URLSearchParams(window.location.search);
                this.tab = params.get('type') || 'posts';
            });

            window.addEventListener('keydown', (e) => {
                // Alt + 1, 2, or 3 to switch tabs
                if (e.altKey && ['1', '2', '3'].includes(e.key)) {
                    e.preventDefault();
                    const tabs = ['posts', 'users', 'tags'];
                    const targetTab = tabs[parseInt(e.key) - 1];
                    if (targetTab && targetTab !== this.tab) {
                        this.switchTab(targetTab);
                    }
                }
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.querySelector('input[name="q"]')?.focus();
    }
            });
        }
    }));

    // Live Search Suggestions Component
    Alpine.data('searchSuggestions', (initialValue) => ({
        q: initialValue ?? '',
        open: false,
        loading: false,
        results: { posts: [], users: [], tags: [] },

        fetch() {
            if (this.q.length < 2) {
                this.open = false;
                return;
            }

            this.loading = true;
            fetch(`/search/suggestions?q=${encodeURIComponent(this.q)}`)
                .then(res => res.json())
                .then(data => {
                    this.results = data;
                    this.open = true;
                })
                .catch(err => console.error("Search error:", err))
                .finally(() => this.loading = false);
        }
    }));
});

Alpine.start();