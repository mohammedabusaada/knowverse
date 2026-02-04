<div class="fixed top-0 left-0 w-full h-1 z-50">
    <div
        x-data="{
            percent: 0,
            updateProgress() {
                let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
                let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                this.percent = height ? (winScroll / height) * 100 : 0;
            }
        }"
        x-init="updateProgress(); window.addEventListener('scroll', () => updateProgress())"
        :style="'width: ' + percent + '%'"
        class="h-full bg-blue-600/80 dark:bg-blue-500/80 transition-all duration-150"
    ></div>
</div>