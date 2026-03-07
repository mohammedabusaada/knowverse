import './bootstrap';
import Alpine from 'alpinejs';
import EasyMDE from "easymde";
import './vote.js';
import './save-post.js';
import './echo';

import hljs from 'highlight.js';
import { marked } from 'marked';
import renderMathInElement from 'katex/dist/contrib/auto-render.js';

window.hljs = hljs;
window.marked = marked;
window.EasyMDE = EasyMDE;
window.renderMathInElement = renderMathInElement;


window.Alpine = Alpine;

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

// 3. Real-Time Notifications (Laravel Echo & Reverb)
const userId = document.querySelector('meta[name="user-id"]')?.content;

// Helper function to create a clickable, beautiful Toast notification
const showBeautifulToast = (message, url) => {
    // Create an anchor tag instead of a div so it's clickable
    const toast = document.createElement('a');
    toast.href = url;
    toast.className = 'fixed bottom-8 right-8 z-50 flex items-center gap-3 px-5 py-3 rounded-sm bg-ink text-paper border border-rule shadow-xl transition-all duration-300 transform translate-y-4 opacity-0 hover:scale-105 hover:shadow-2xl';
    toast.innerHTML = `
        <span class="font-mono text-accent-warm font-bold">✦</span>
        <span class="font-serif text-sm tracking-wide">${message}</span>
    `;
    document.body.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-4', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    });

    // Animate out and remove after 5 seconds
    setTimeout(() => {
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-4', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
};

if (window.Echo && userId) {
    console.log("Echo is ready! Listening for user ID: " + userId);
    
    window.Echo.private(`notifications.${userId}`)
        .listen('RealTimeNotification', (e) => {
            console.log('New Real-Time Notification Arrived!', e);
            
            // 1. Show the interactive clickable Toast
            showBeautifulToast(e.message, e.url);

            // 2. Increment the notification badge
            window.dispatchEvent(new CustomEvent('realtime-notification'));

            // 3. Prepend the new clickable notification to the dropdown
            const list = document.getElementById('notification-list');
            const emptyMsg = document.getElementById('empty-notifications-msg');
            
            if (list) {
                if (emptyMsg) emptyMsg.remove(); 
                
                // Wrapped in <a> tag pointing to e.url
                const itemHtml = `
                    <a href="${e.url}" class="block px-5 py-4 border-b border-rule hover:bg-aged/10 transition-colors bg-accent/5">
                        <div class="flex gap-4">
                            <div class="mt-1 text-accent">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-serif text-[14px] text-ink font-bold leading-snug">
                                    ${e.message}
                                </p>
                                <p class="mt-1 font-mono text-[9px] uppercase tracking-widest text-accent">
                                    Just now
                                </p>
                            </div>
                        </div>
                    </a>
                `;
                list.insertAdjacentHTML('afterbegin', itemHtml);
            }
        });
}