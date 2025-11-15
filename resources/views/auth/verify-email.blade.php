<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KnowVerse - Verify Email</title>
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

        <h1 class="text-4xl font-extrabold mb-4 tracking-tight">Verify your email</h1>

        <p class="text-lg text-gray-700 text-center max-w-sm leading-relaxed">
            A verification link has been sent to your email address.
            Please check your inbox and click the link to activate your account.
        </p>
    </div>

    <!-- Right Section -->
    <div class="flex w-full md:w-1/2 justify-center items-center bg-white px-8 py-12">
        <div class="max-w-md w-full bg-white p-10 rounded-3xl shadow-2xl border border-gray-100 hover:shadow-gray-300/40 transition-shadow duration-300 text-center">

            <h2 class="text-3xl font-bold text-gray-900 mb-4">Verify Your Email</h2>

            {{-- Notification --}}
            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800 text-sm shadow-sm">
                    A new verification link has been sent to your email address.
                </div>
            @endif

            <p class="text-gray-700 text-sm leading-relaxed mb-6">
                Before proceeding, please check your email for a verification link.  
                If you didn’t receive the email, you can request another one below.
            </p>

            <!-- Resend Link -->
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button
                    type="submit"
                    class="w-full bg-[#1a1a1a] hover:bg-gray-900 text-white font-semibold py-3 rounded-full shadow-md hover:shadow-lg focus:ring-4 focus:ring-gray-300 transition"
                >
                    Resend Verification Email
                </button>
            </form>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="mt-6">
                @csrf
                <button
                    type="submit"
                    class="text-gray-600 hover:text-gray-900 font-medium hover:underline transition"
                >
                    Log Out
                </button>
            </form>

        </div>
    </div>

</body>
</html>
