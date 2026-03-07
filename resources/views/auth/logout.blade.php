<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>KnowVerse - Logged Out</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex bg-paper text-ink font-serif antialiased">
    <div class="hidden md:flex w-1/2 bg-ink text-paper flex-col justify-center items-center px-10 relative">
        <a href="/" class="font-heading font-bold text-3xl tracking-[0.08em] uppercase text-paper hover:opacity-80 transition-opacity mb-8 block">KnowVerse</a>
        <h1 class="text-4xl font-heading font-bold mb-4">See You Soon</h1>
        <p class="text-lg text-aged italic text-center max-w-sm">Thank you for visiting. We look forward to your next contribution.</p>
    </div>

    <div class="flex w-full md:w-1/2 justify-center items-center bg-paper px-8">
        <div class="max-w-md w-full text-center md:text-left">
            <h2 class="font-heading text-4xl font-bold text-ink mb-6">Successfully Logged Out</h2>
            <p class="text-muted italic mb-10 leading-relaxed">Your account is secure. You have been successfully logged out of the system.</p>

            <div class="space-y-4">
                <a href="{{ route('login') }}" class="block text-center w-full bg-ink text-paper font-mono uppercase tracking-widest text-xs py-4 hover:bg-transparent hover:text-ink border border-ink transition-all">
                    Log In Again
                </a>
                <a href="/" class="block text-center w-full bg-transparent border border-rule text-muted font-mono uppercase tracking-widest text-xs py-4 hover:border-ink hover:text-ink transition-all">
                    Return to Homepage
                </a>
            </div>
        </div>
    </div>
</body>
</html>