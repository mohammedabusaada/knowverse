<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>KnowVerse - Forgot Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;0,700;1,400;1,500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex bg-paper text-ink font-serif antialiased">

    <div class="hidden md:flex w-1/2 bg-ink text-paper flex-col justify-center items-center px-10 relative">
        <x-application-logo class="w-auto h-16 mb-8" />
        <h1 class="text-4xl font-heading font-bold mb-4 tracking-tight">Lost your key?</h1>
        <p class="text-lg text-aged italic text-center max-w-sm border-l-2 border-paper/20 pl-4 leading-relaxed">
            Forgotten paths are common. Provide your email, and we shall help you find your way back.
        </p>
    </div>

    <div class="flex w-full md:w-1/2 justify-center items-center bg-paper px-8">
        <div class="max-w-md w-full">
            <h2 class="font-heading text-4xl font-bold text-ink mb-8">Password Recovery</h2>

            @if (session('status'))
                <div class="mb-6 p-4 border border-ink bg-aged text-ink text-sm font-bold italic text-center">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-8">
                @csrf
                <div>
                    <label for="email" class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2">Registered Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-0 py-3 border-0 border-b border-rule bg-transparent focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg" />
                </div>

                <button type="submit" class="w-full bg-ink hover:bg-transparent hover:text-ink border border-ink text-paper font-mono uppercase tracking-[0.15em] text-xs py-4 transition-colors">
                    Send Recovery Link
                </button>
            </form>

            <div class="mt-12 text-center">
                <a href="{{ route('login') }}" class="font-mono text-[10px] uppercase tracking-widest text-muted hover:text-ink transition-colors border-b border-rule pb-1">
                    &larr; Return to Entrance
                </a>
            </div>
        </div>
    </div>
</body>
</html>