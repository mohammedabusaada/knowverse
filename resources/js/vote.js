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
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!res.ok) throw new Error('Network response was not ok');

                const data = await res.json();

                // Validation Guard: If the backend rejected the vote
                if (data.success === false) {
                    console.warn(data.error);
                    return;
                }

                // Update Alpine's reactive state with the fresh database counts
                this.vote = data.user_vote;
                this.score = data.score;

                // Trigger UI micro-interaction (Flash effect)
                this.flash(sendValue);

            } catch (error) {
                console.error('Vote action failed:', error);
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