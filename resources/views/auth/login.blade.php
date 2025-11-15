<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KnowVerse - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        * {
            transition: all 0.25s ease-in-out;
        }
    </style>
</head>

<body class="min-h-screen flex bg-gradient-to-br from-gray-50 via-white to-gray-100">

    <!-- Left Section -->
    <div class="hidden md:flex w-1/2 bg-gradient-to-br from-[#d3cdc7] to-[#bfb9b3] text-gray-900 flex-col justify-center items-center px-10 py-16 shadow-inner">
        <img src="{{ asset('logo.jpg') }}" alt="KnowVerse Logo"
             class="w-32 mb-6 rounded-2xl shadow-xl hover:scale-105 transform transition duration-300" />

        <h1 class="text-4xl font-extrabold mb-4 tracking-tight">
            Welcome to <span class="text-gray-800">KnowVerse</span>
        </h1>

        <p class="text-lg text-gray-700 text-center max-w-sm leading-relaxed">
            Join us and start your journey of learning and discovery.
        </p>
    </div>

    <!-- Right Section -->
    <div class="flex w-full md:w-1/2 justify-center items-center bg-white px-8 py-12">
        <div class="max-w-md w-full bg-white p-10 rounded-3xl shadow-2xl border border-gray-100 hover:shadow-gray-300/40 transition-shadow duration-300">

            <h2 class="text-3xl font-bold text-center text-gray-900 mb-8">Sign in to your account</h2>

            {{-- Flash Messages --}}
            @if (session('status'))
                <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800 text-sm text-center shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 text-sm shadow-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-6" novalidate>
                @csrf

                <!-- Email or Username -->
                <div>
                    <label for="login" class="block text-gray-700 font-semibold mb-2">
                        Email or Username
                    </label>

                    <input
                        type="text"
                        id="login"
                        name="login"
                        required
                        placeholder="you@example.com or your-username"
                        value="{{ old('login') }}"
                        autocomplete="username"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-gray-400 placeholder-gray-400"
                    />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        autocomplete="current-password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-gray-400 placeholder-gray-400"
                    />
                </div>

                <!-- Remember / Forgot -->
                <div class="flex items-center justify-between text-sm mt-2">
                    <label class="flex items-center text-gray-700 cursor-pointer">
                        <input type="checkbox" name="remember"
                               class="h-4 w-4 text-gray-700 border-gray-300 rounded mr-2 focus:ring-gray-400" />
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}"
                       class="text-gray-600 hover:text-gray-900 hover:underline font-medium transition">
                        Forgot password?
                    </a>
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="w-full bg-[#1a1a1a] hover:bg-gray-900 text-white font-semibold py-3
                               rounded-full shadow-md hover:shadow-lg focus:ring-4 focus:ring-gray-300
                               transition-transform hover:-translate-y-0.5">
                    Sign In
                </button>
            </form>

            <p class="mt-8 text-center text-gray-600 text-sm">
                Don’t have an account?
                <a href="{{ route('register') }}"
                   class="text-gray-900 font-semibold hover:underline hover:text-black transition-colors">
                    Create one
                </a>
            </p>
        </div>
    </div>
</body>

</html>
