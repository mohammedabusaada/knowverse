<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KnowVerse - Logged Out</title>
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

        <h1 class="text-4xl font-extrabold mb-4 tracking-tight">See you soon!</h1>
        <p class="text-lg text-gray-400 text-center max-w-sm leading-relaxed">
            You have been successfully logged out. Come back soon to continue your learning journey.
        </p>
    </div>

    {{-- Right Side: Form --}}
    <div class="flex w-full md:w-1/2 justify-center items-center bg-white px-8 py-12 shadow-2xl md:shadow-none z-10 md:rounded-l-3xl">
        <div class="max-w-md w-full">

            <h2 class="text-3xl font-bold text-black mb-4">You’re Logged Out</h2>
            <p class="text-gray-600 text-sm mb-8 leading-relaxed">
                Your session has ended. To protect your account, close this window or sign in again.
            </p>

            <div class="flex flex-col space-y-4">
                <a href="{{ route('login') }}" class="block text-center w-full bg-black hover:bg-gray-800 text-white font-semibold py-3 rounded-lg shadow-md hover:shadow-lg transition-all">
                    Sign In Again
                </a>

                <a href="{{ route('register') }}" class="block text-center w-full bg-white border border-gray-300 text-gray-800 hover:bg-gray-50 font-semibold py-3 rounded-lg shadow-sm transition-all">
                    Create New Account
                </a>
            </div>

            <p class="mt-8 text-sm text-center text-gray-500">
                Having trouble?
                <a href="{{ route('password.request') }}" class="font-bold text-black hover:underline transition-colors">
                    Reset your password
                </a>
            </p>

        </div>
    </div>
</body>
</html>