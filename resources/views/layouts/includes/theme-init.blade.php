<script>
    try {
        const theme = localStorage.getItem('theme');
        document.documentElement.classList.toggle('dark', theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches));
    } catch { /* The default light theme remains usable without local storage. */ }
</script>
