<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KnowVerse - Forgot Password</title>
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

        <h1 class="text-4xl font-extrabold mb-4 tracking-tight">Reset your password</h1>
        <p class="text-lg text-gray-400 text-center max-w-sm leading-relaxed">
            Don’t worry! Enter your email and we’ll send you a link to reset your password.
        </p>
    </div>

    {{-- Right Side: Form --}}
    <div class="flex w-full md:w-1/2 justify-center items-center bg-white px-8 py-12 shadow-2xl md:shadow-none z-10 md:rounded-l-3xl">
        <div class="max-w-md w-full">

            <h2 class="text-3xl font-bold text-black mb-8">Forgot Password</h2>

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

            <form action="{{ route('password.email') }}" method="POST" class="space-y-5" novalidate>
                @csrf

                <div>
                    <label for="email" class="block text-gray-700 font-semibold mb-1.5 text-sm">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-all bg-gray-50 focus:bg-white"
                    />
                </div>

                <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-semibold py-3 rounded-lg shadow-md hover:shadow-lg focus:ring-4 focus:ring-gray-300 transition-all mt-2">
                    Send Reset Link
                </button>
            </form>

            <p class="mt-8 text-center text-gray-500 text-sm">
                Remembered your password?
                <a href="{{ route('login') }}" class="text-black font-bold hover:underline transition-colors">
                    Back to Sign In
                </a>
            </p>

        </div>
    </div>
</body>
</html>