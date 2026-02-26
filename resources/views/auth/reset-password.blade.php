<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>KnowVerse - Reset Password</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex bg-paper text-ink font-serif antialiased">

    <div class="hidden md:flex w-1/2 bg-ink text-paper flex-col justify-center items-center px-10 relative">
        <x-application-logo class="w-auto h-16 mb-8" />
        <h1 class="text-4xl font-heading font-bold mb-4">New Credentials</h1>
        <p class="text-lg text-aged italic text-center max-w-sm">
            Update your access key to return to your research.
        </p>
    </div>

    <div class="flex w-full md:w-1/2 justify-center items-center bg-paper px-8 py-12">
        <div class="max-w-md w-full">
            <h2 class="font-heading text-4xl font-bold text-ink mb-10">Reset Password</h2>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label for="email" class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required
                           class="w-full px-0 py-2 border-0 border-b border-rule bg-transparent focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg" />
                </div>

                <div>
                    <label for="password" class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2">New Password</label>
                    <input id="password" type="password" name="password" required
                           class="w-full px-0 py-2 border-0 border-b border-rule bg-transparent focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg" />
                </div>

                <div>
                    <label for="password_confirmation" class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2">Confirm New Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="w-full px-0 py-2 border-0 border-b border-rule bg-transparent focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg" />
                </div>

                <button type="submit" class="w-full bg-ink text-paper font-mono uppercase tracking-widest text-xs py-4 hover:bg-transparent hover:text-ink border border-ink transition-all mt-4">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</body>
</html>