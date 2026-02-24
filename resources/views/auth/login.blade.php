<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KnowVerse - Login</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>

<body class="min-h-screen flex bg-gray-50">

    {{-- Left Side: Bold Black --}}
    <div class="hidden md:flex w-1/2 bg-black text-white flex-col justify-center items-center px-10 py-16">
        <x-application-logo class="w-auto h-16 mb-8 hover:scale-105 transform transition duration-300" />

        <h1 class="text-4xl font-extrabold mb-4 tracking-tight">
            Welcome to KnowVerse
        </h1>
        <p class="text-lg text-gray-400 text-center max-w-sm leading-relaxed">
            Join us and start your journey of learning and discovery.
        </p>
    </div>

    {{-- Right Side: Login Form --}}
    <div class="flex w-full md:w-1/2 justify-center items-center bg-white px-8 py-12 shadow-2xl md:shadow-none z-10 md:rounded-l-3xl">
        <div class="max-w-md w-full">

            <h2 class="text-3xl font-bold text-black mb-8">Sign in</h2>

            @if (session('status'))
                <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5" novalidate>
                @csrf

                <div>
                    <label for="login" class="block text-gray-700 font-semibold mb-1.5 text-sm">
                        Email or Username
                    </label>
                    <input
                        type="text"
                        id="login"
                        name="login"
                        required
                        value="{{ old('login') }}"
                        autocomplete="username"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-all bg-gray-50 focus:bg-white"
                    />
                </div>

                <div>
                    <label for="password" class="block text-gray-700 font-semibold mb-1.5 text-sm">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-all bg-gray-50 focus:bg-white"
                    />
                </div>

                <div class="flex items-center justify-between text-sm mt-2 pb-2">
                    <label class="flex items-center text-gray-600 cursor-pointer hover:text-black">
                        <input type="checkbox" name="remember" class="h-4 w-4 text-black border-gray-300 rounded mr-2 focus:ring-black" />
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="text-gray-500 hover:text-black font-medium transition">
                        Forgot password?
                    </a>
                </div>

                <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-semibold py-3 rounded-lg shadow-md hover:shadow-lg focus:ring-4 focus:ring-gray-300 transition-all">
                    Sign In
                </button>
            </form>

            <p class="mt-8 text-center text-gray-500 text-sm">
                Don’t have an account?
                <a href="{{ route('register') }}" class="text-black font-bold hover:underline transition-colors">
                    Create one
                </a>
            </p>
        </div>
    </div>
</body>
</html>