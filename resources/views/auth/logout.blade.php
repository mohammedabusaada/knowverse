<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>KnowVerse - Departure</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex bg-paper text-ink font-serif antialiased">
    <div class="hidden md:flex w-1/2 bg-ink text-paper flex-col justify-center items-center px-10 relative">
        <x-application-logo class="w-auto h-16 mb-8" />
        <h1 class="text-4xl font-heading font-bold mb-4">Farewell</h1>
        <p class="text-lg text-aged italic text-center max-w-sm">The archive remains. Your contributions are preserved until your next visit.</p>
    </div>

    <div class="flex w-full md:w-1/2 justify-center items-center bg-paper px-8">
        <div class="max-w-md w-full text-center md:text-left">
            <h2 class="font-heading text-4xl font-bold text-ink mb-6">Session Ended</h2>
            <p class="text-muted italic mb-10 leading-relaxed">To safeguard your academic records, we have secured your account. You have been successfully logged out.</p>

            <div class="space-y-4">
                <a href="{{ route('login') }}" class="block text-center w-full bg-ink text-paper font-mono uppercase tracking-widest text-xs py-4 hover:bg-transparent hover:text-ink border border-ink transition-all">
                    Re-enter the Verse
                </a>
                <a href="/" class="block text-center w-full bg-transparent border border-rule text-muted font-mono uppercase tracking-widest text-xs py-4 hover:border-ink hover:text-ink transition-all">
                    Back to Public View
                </a>
            </div>
        </div>
    </div>
</body>
</html>