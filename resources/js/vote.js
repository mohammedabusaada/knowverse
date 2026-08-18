document.addEventListener("alpine:init", () => {
    Alpine.data("voteComponent", ({ id, type, initialScore, initialVote }) => ({
        score: initialScore,
        vote: initialVote, // 1, -1, or 0 (0 means null/retracted)
        scoreFlashClass: "",
        loading: false,

        async voteAction(value) {
            if (this.loading) return;
            this.loading = true;

            // Toggle logic: If the user clicks the same vote again, retract it (send 0)
            let sendValue = (this.vote === value) ? 0 : value;

            const formData = new FormData();
            formData.append("type", type);
            formData.append("id", id);
            formData.append("value", sendValue);

            try {
                const res = await fetch("/vote", {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    }
                });

                const data = await res.json().catch(() => null);

                // Guard failures (voting on your own content, or not having enough
                // reputation to downvote) return HTTP 403 with { success:false, error }.
                // Surface that message to the user instead of failing silently.
                if (!res.ok || !data || data.success === false) {
                    const message = (data && data.error)
                        ? data.error
                        : 'Unable to register your vote. Please try again.';
                    if (window.showToast) window.showToast(message, 'error');
                    return;
                }

                // Update Alpine's reactive state with the fresh database counts
                this.vote = data.user_vote;
                this.score = data.score;

                // Trigger UI micro-interaction (Flash effect)
                this.flash(sendValue);

            } catch (error) {
                console.error('Vote action failed:', error);
                if (window.showToast) window.showToast('Network error — your vote was not saved.', 'error');
            } finally {
                this.loading = false;
            }
        },

        flash(value) {
            // Apply different flash colors based on the vote direction
            const flashColor = value === 1 ? 'text-ink' : (value === -1 ? 'text-accent-warm' : 'text-muted');
            
            // Add scale and color temporarily
            this.scoreFlashClass = `scale-125 ${flashColor} transition-transform duration-200`;
            
            // Remove after 200ms to return to normal state
            setTimeout(() => {
                this.scoreFlashClass = "transition-transform duration-200";
            }, 200);
        }
    }));
});