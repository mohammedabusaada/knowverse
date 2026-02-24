<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KnowVerse - Confirm Password</title>
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

        <h1 class="text-4xl font-extrabold mb-4 tracking-tight">Confirm Your Password</h1>
        <p class="text-lg text-gray-400 text-center max-w-sm leading-relaxed">
            For your security, please confirm your password before continuing.
        </p>
    </div>

    {{-- Right Side: Form --}}
    <div class="flex w-full md:w-1/2 justify-center items-center bg-white px-8 py-12 shadow-2xl md:shadow-none z-10 md:rounded-l-3xl">
        <div class="max-w-md w-full">

            <h2 class="text-3xl font-bold text-black mb-8">Confirm Password</h2>

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

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="password" class="block text-gray-700 font-semibold mb-1.5 text-sm">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-all bg-gray-50 focus:bg-white"
                    />
                </div>

                <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-semibold py-3 rounded-lg shadow-md hover:shadow-lg focus:ring-4 focus:ring-gray-300 transition-all mt-2">
                    Confirm Password
                </button>
            </form>

            <div class="mt-8 text-center">
                <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-black font-bold transition-colors text-sm">
                    &larr; Back
                </a>
            </div>

        </div>
    </div>
</body>
</html>