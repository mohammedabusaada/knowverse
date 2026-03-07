<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KnowVerse - Register</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;0,700;1,400;1,500&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex bg-paper text-ink font-serif antialiased selection:bg-accent selection:text-paper">

    {{-- Left Side --}}
    <div class="hidden md:flex w-1/2 bg-ink text-paper flex-col justify-center items-center px-10 py-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'%3E%3Cfilter id=\'n\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.75\' numOctaves=\'4\' stitchTiles=\'stitch\'/%3E%3CfeColorMatrix type=\'saturate\' values=\'0\'/%3E%3C/filter%3E%3Crect width=\'400\' height=\'400\' filter=\'url(%23n)\' opacity=\'0.04\'/%3E%3C/svg%3E');"></div>
        
        {{-- Text Logo --}}
        <a href="/" class="font-heading font-bold text-6xl tracking-[0.1em] uppercase text-paper hover:opacity-80 transition-all duration-300 mb-12 block border-b-4 border-paper/10 pb-4">
            KnowVerse
        </a>
        
        {{-- Main Heading --}}
        <h1 class="text-4xl font-heading font-bold mb-4 tracking-tight text-aged uppercase text-center">
            Create Account
        </h1>
        
        <p class="text-lg font-serif text-aged italic text-center max-w-md leading-relaxed border-l-2 border-paper/20 pl-4">
            Join our scholars today and begin your contribution to the collective wisdom of KnowVerse.
        </p>
    </div>

    {{-- Right Side: Register Form --}}
    <div class="flex w-full md:w-1/2 justify-center items-center bg-paper px-8 py-12 z-10">
        <div class="max-w-md w-full">
            <h2 class="font-heading text-4xl font-bold text-ink mb-10 text-center md:text-left">Sign Up</h2>

            @if ($errors->any())
                <div class="mb-6 p-4 border border-[#a65a38] bg-[#a65a38]/5 text-[#a65a38] text-sm italic font-medium">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-6" novalidate>
                @csrf

                {{-- Full Name Field --}}
                <div>
                    <label for="full_name" class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2">
                        Full Name <span class="text-[9px] lowercase opacity-50">(Optional)</span>
                    </label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" 
                           class="w-full px-0 py-2 border-0 border-b border-rule bg-transparent focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg placeholder:text-muted/30 placeholder:italic"
                           placeholder="Enter your full name..." />
                </div>

                {{-- Username Field --}}
                <div>
                    <label for="username" class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required
                           class="w-full px-0 py-2 border-0 border-b border-rule bg-transparent focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg placeholder:text-muted/30 placeholder:italic"
                           placeholder="Choose a username..." />
                </div>

                {{-- Email Field --}}
                <div>
                    <label for="email" class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-0 py-2 border-0 border-b border-rule bg-transparent focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg placeholder:text-muted/30 placeholder:italic"
                           placeholder="your@email.com" />
                </div>

                {{-- Password Grid --}}
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2">Password</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-0 py-2 border-0 border-b border-rule bg-transparent focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg" />
                    </div>
                    <div>
                        <label for="password_confirmation" class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-2">Confirm</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full px-0 py-2 border-0 border-b border-rule bg-transparent focus:ring-0 focus:border-ink transition-colors text-ink font-serif text-lg" />
                    </div>
                </div>

                <button type="submit" class="w-full bg-ink hover:bg-transparent hover:text-ink border border-ink text-paper font-mono uppercase tracking-[0.15em] text-xs py-4 transition-colors mt-6">
                    Create Account
                </button>
            </form>

            <p class="mt-10 text-center text-muted font-serif italic text-[15px]">
                Already have an account?
                <a href="{{ route('login') }}" class="text-ink font-bold border-b border-ink hover:text-accent transition-colors ml-1">
                    Sign In
                </a>
            </p>
        </div>
    </div>
</body>
</html>