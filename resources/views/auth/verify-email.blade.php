<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>KnowVerse - Verify Email</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex bg-paper text-ink font-serif antialiased">

    <div class="hidden md:flex w-1/2 bg-ink text-paper flex-col justify-center items-center px-10 relative">
        <x-application-logo class="w-auto h-16 mb-8" />
        <h1 class="text-4xl font-heading font-bold mb-4 tracking-tight">Final Step</h1>
        <p class="text-lg text-aged italic text-center max-w-sm border-l-2 border-paper/20 pl-4">
            A verification link has been dispatched to your inbox. Please confirm it to begin.
        </p>
    </div>

    <div class="flex w-full md:w-1/2 justify-center items-center bg-paper px-8">
        <div class="max-w-md w-full">
            <h2 class="font-heading text-4xl font-bold text-ink mb-6">Verify Your Email</h2>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 border border-ink bg-aged text-ink text-sm font-bold italic">
                    A fresh link has been sent to your email address.
                </div>
            @endif

            <p class="text-muted italic mb-8 leading-relaxed">
                Before we proceed, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive it, we will gladly send you another.
            </p>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full bg-ink text-paper font-mono uppercase tracking-widest text-xs py-4 hover:bg-transparent hover:text-ink border border-ink transition-all">
                    Resend Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-8 text-center">
                @csrf
                <button type="submit" class="font-mono text-[10px] uppercase tracking-widest text-muted hover:text-ink transition-colors border-b border-rule pb-1">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</body>
</html>