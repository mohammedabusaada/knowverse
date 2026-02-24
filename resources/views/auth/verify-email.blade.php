<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KnowVerse - Verify Email</title>
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

        <h1 class="text-4xl font-extrabold mb-4 tracking-tight">Verify your email</h1>
        <p class="text-lg text-gray-400 text-center max-w-sm leading-relaxed">
            A verification link has been sent to your email address. Please check your inbox.
        </p>
    </div>

    {{-- Right Side: Form --}}
    <div class="flex w-full md:w-1/2 justify-center items-center bg-white px-8 py-12 shadow-2xl md:shadow-none z-10 md:rounded-l-3xl">
        <div class="max-w-md w-full">

            <h2 class="text-3xl font-bold text-black mb-6">Verify Your Email</h2>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200">
                    A new verification link has been sent to the email address you provided.
                </div>
            @endif

            <p class="text-gray-600 text-sm leading-relaxed mb-8">
                Before proceeding, please check your email for a verification link.  
                If you didn’t receive the email, click the button below to request another one.
            </p>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-semibold py-3 rounded-lg shadow-md hover:shadow-lg focus:ring-4 focus:ring-gray-300 transition-all">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-6 text-center">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-black font-bold transition-colors text-sm">
                    Log Out
                </button>
            </form>

        </div>
    </div>
</body>
</html>