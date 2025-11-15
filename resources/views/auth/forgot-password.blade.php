<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KnowVerse - Forgot Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; }
        * { transition: all 0.25s ease-in-out; }
    </style>
</head>

<body class="min-h-screen flex bg-gradient-to-br from-gray-50 via-white to-gray-100">

    <!-- Left Section -->
    <div class="hidden md:flex w-1/2 bg-gradient-to-br from-[#d3cdc7] to-[#bfb9b3] text-gray-900 flex-col justify-center items-center px-10 py-16 shadow-inner">
        <img src="{{ asset('logo.jpg') }}" alt="KnowVerse Logo"
             class="w-32 mb-6 rounded-2xl shadow-xl hover:scale-105 transform transition duration-300" />

        <h1 class="text-4xl font-extrabold mb-4 tracking-tight">Reset your password</h1>

        <p class="text-lg text-gray-700 text-center max-w-sm leading-relaxed">
            Don’t worry! Enter your email and we’ll send you a link to reset your password.
        </p>
    </div>

    <!-- Right Section -->
    <div class="flex w-full md:w-1/2 justify-center items-center bg-white px-8 py-12">
        <div class="max-w-md w-full bg-white p-10 rounded-3xl shadow-2xl border border-gray-100 hover:shadow-gray-300/40 transition-shadow duration-300">

            <h2 class="text-3xl font-bold text-center text-gray-900 mb-8">Forgot Password</h2>

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

            <form action="{{ route('password.email') }}" method="POST" class="space-y-6" novalidate>
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-gray-700 font-semibold mb-2">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="you@example.com"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 placeholder-gray-400"
                    />
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full bg-[#1a1a1a] hover:bg-gray-900 text-white font-semibold py-3 rounded-full shadow-md hover:shadow-lg focus:ring-4 focus:ring-gray-300 transition-transform hover:-translate-y-0.5"
                >
                    Send Reset Link
                </button>
            </form>

            <p class="mt-8 text-center text-gray-600 text-sm">
                Remembered your password?
                <a href="{{ route('login') }}" class="text-gray-900 font-semibold hover:underline hover:text-black transition-colors">
                    Back to Sign In
                </a>
            </p>

        </div>
    </div>

</body>
</html>
