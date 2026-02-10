document.addEventListener("alpine:init", () => {
    Alpine.data("savePost", (initialState, postId) => ({
        isSaved: initialState,
        loading: false,

        async toggle() {
            if (this.loading) return;
            this.loading = true;

            // 1. Optimistic UI Update (Change visuals immediately before server responds)
            this.isSaved = !this.isSaved;

            try {
                const res = await fetch(`/posts/${postId}/save`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (!res.ok) throw new Error('Request failed');

                const data = await res.json();
                
                // 2. Sync with Server Truth
                this.isSaved = data.saved;
                
                // Optional: Dispatch event for Toast notification
                // window.dispatchEvent(new CustomEvent('notify', { detail: { message: data.message } }));

            } catch (error) {
                console.error(error);
                // Revert UI on error
                this.isSaved = !this.isSaved;
            } finally {
                this.loading = false;
            }
        }
    }));
});