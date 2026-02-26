<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KnowVerse - Login</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;0,700;1,400;1,500&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex bg-paper text-ink font-serif antialiased selection:bg-accent selection:text-paper">

    {{-- Left Side: Ink Background --}}
    <div class="hidden md:flex w-1/2 bg-ink text-paper flex-col justify-center items-center px-10 py-16 relative overflow-hidden">
        {{-- Subtle texture overlay --}}
        <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'%3E%3Cfilter id=\'n\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.75\' numOctaves=\'4\' stitchTiles=\'stitch\'/%3E%3CfeColorMatrix type=\'saturate\' values=\'0\'/%3E%3C/filter%3E%3Crect width=\'400\' height=\'400\' filter=\'url(%23n)\' opacity=\'0.04\'/%3E%3C/svg%3E');"></div>
        
        <x-application-logo class="w-auto h-16 mb-8 hover:opacity-80 transition-opacity" />

        <h1 class="text-4xl md:text-5xl font-heading font-bold mb-4 tracking-tight">
            The Archive Awaits
        </h1>
        <p class="text-lg text-aged italic text-center max-w-md leading-relaxed border-l-2 border-paper/20 pl-4">
            Join the discourse and start your journey of academic discovery.
        </p>
    </div>

    {{-- Right Side: Login Form --}}
    <div class="flex w-full md:w-1/2 justify-center items-center bg-paper px-8 py-12 z-10">
        <div class="max-w-md w-full">

            <h2 class="font-heading text-4xl font-bold text-ink mb-10 text-center md:text-left">Sign in</h2>

            @if (session('status'))
                <div class="mb-6 p-4 border border-ink bg-aged text-ink text-sm font-bold italic text-center">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 border border-[#a65a38] bg-[#a65a38]/5 text-[#a65a38] text-sm">
                    <ul class="list-disc list-inside space-y-1 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-6" novalidate>
                @csrf

                <div>
                    <label for="login" class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2">
                        Email or Username
                    </label>
                    <input
                        type="text"
                        id="login"
                        name="login"
                        required
                        value="{{ old('login') }}"
                        autocomplete="username"
                        class="w-full px-0 py-3 border-0 border-b border-rule bg-transparent focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg placeholder:text-muted/50 placeholder:italic"
                        placeholder="Enter your credential..."
                    />
                </div>

                <div>
                    <label for="password" class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full px-0 py-3 border-0 border-b border-rule bg-transparent focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg placeholder:text-muted/50 placeholder:italic"
                        placeholder="••••••••"
                    />
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center text-muted cursor-pointer hover:text-ink transition-colors group">
                        <input type="checkbox" name="remember" class="h-4 w-4 text-ink border-rule rounded-sm bg-transparent focus:ring-ink" />
                        <span class="ml-2 text-sm italic font-serif group-hover:text-ink">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="font-mono text-[10px] uppercase tracking-widest text-muted hover:text-ink transition-colors">
                        Lost password?
                    </a>
                </div>

                <button type="submit" class="w-full bg-ink hover:bg-transparent hover:text-ink border border-ink text-paper font-mono uppercase tracking-[0.15em] text-xs py-4 transition-colors mt-4">
                    Enter the Verse
                </button>
            </form>

            <p class="mt-12 text-center text-muted font-serif italic text-[15px]">
                Not a member yet?
                <a href="{{ route('register') }}" class="text-ink font-bold border-b border-ink hover:text-accent transition-colors ml-1">
                    Apply for Access
                </a>
            </p>
        </div>
    </div>
</body>
</html>