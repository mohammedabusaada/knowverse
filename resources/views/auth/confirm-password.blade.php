<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>KnowVerse - Confirm Access</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex bg-paper text-ink font-serif antialiased">

    <div class="flex w-full justify-center items-center bg-paper px-8">
        <div class="max-w-md w-full text-center">
            <h2 class="font-heading text-4xl font-bold text-ink mb-4">Security Check</h2>
            <p class="text-muted italic mb-10 leading-relaxed">
                This is a secure area. Please confirm your password before continuing.
            </p>

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                @csrf
                <div class="text-left">
                    <label for="password" class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2">Password</label>
                    <input id="password" type="password" name="password" required
                           class="w-full px-0 py-2 border-0 border-b border-rule bg-transparent focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg" />
                </div>

                <button type="submit" class="w-full bg-ink text-paper font-mono uppercase tracking-widest text-xs py-4 hover:bg-transparent hover:text-ink border border-ink transition-all">
                    Confirm Access
                </button>
            </form>
            
            <a href="{{ url()->previous() }}" class="inline-block mt-8 font-mono text-[10px] uppercase tracking-widest text-muted hover:text-ink transition-colors">
                &larr; Go Back
            </a>
        </div>
    </div>
</body>
</html>