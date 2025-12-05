document.addEventListener("alpine:init", () => {

    Alpine.data("voteComponent", ({ id, type, initialScore, initialVote }) => ({
        score: initialScore,
        vote: initialVote, // 1, -1, or null
        scoreFlashClass: "",
        loading: false,

        async voteAction(value) {

            if (this.loading) return;
            this.loading = true;

            // Same vote clicked → unvote
            let sendValue = (this.vote === value) ? 0 : value;

            const formData = new FormData();
            formData.append("type", type);
            formData.append("id", id);
            formData.append("value", sendValue);

            const res = await fetch("/vote", {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                }
            });

            this.loading = false;
            if (!res.ok) return;

            const data = await res.json();

            this.vote = data.user_vote;
            this.score = data.score;

            this.flash();
        },

        flash() {
            this.scoreFlashClass = "scale-125 text-blue-600 dark:text-blue-300";
            setTimeout(() => {
                this.scoreFlashClass = "";
            }, 200);
        }
    }));

});
