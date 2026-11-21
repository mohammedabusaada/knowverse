export function searchSuggestions(initialValue) {
    return {
        q: initialValue ?? '', open: false, loading: false, error: '', requestId: 0,
        results: { posts: [], users: [], tags: [] },
        async fetch() {
            const id = ++this.requestId;
            const query = this.q.trim();
            this.error = '';
            this.open = false;
            if (query.length < 2) { this.loading = false; return; }
            this.loading = true;
            try {
                const response = await fetch(`/search/suggestions?q=${encodeURIComponent(query)}`);
                if (!response.ok) throw new Error('Search failed');
                const data = await response.json();
                if (id !== this.requestId || query !== this.q.trim()) return;
                this.results = { posts: data.posts ?? [], users: data.users ?? [], tags: data.tags ?? [] };
                this.open = true;
            } catch {
                if (id === this.requestId && query === this.q.trim()) {
                    this.error = 'Suggestions are unavailable. Press Enter to search.';
                }
            } finally { if (id === this.requestId) this.loading = false; }
        }
    };
}
