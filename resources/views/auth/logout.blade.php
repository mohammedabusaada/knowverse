<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KnowVerse - Logged Out</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      body { font-family: 'Poppins', sans-serif; }
      * { transition: all 0.25s ease-in-out; }
    </style>
  </head>

  <body class="min-h-screen flex bg-gradient-to-br from-gray-50 via-white to-gray-100">

    <!-- القسم الأيسر -->
    <div class="hidden md:flex w-1/2 bg-gradient-to-br from-[#d3cdc7] to-[#bfb9b3] text-gray-900 flex-col justify-center items-center px-10 py-16 shadow-inner">
      <img src="{{ asset('logo.jpg') }}" alt="KnowVerse Logo"
           class="w-32 mb-6 rounded-2xl shadow-xl hover:scale-105 transform transition duration-300" />
      <h1 class="text-4xl font-extrabold mb-4 tracking-tight">See you soon!</h1>
      <p class="text-lg text-gray-700 text-center max-w-sm leading-relaxed">
        You have been successfully logged out. Come back soon to continue your learning journey.
      </p>
    </div>

    <!-- القسم الأيمن -->
    <div class="flex w-full md:w-1/2 justify-center items-center bg-white px-8 py-12">
      <div class="max-w-md w-full bg-white p-10 rounded-3xl shadow-2xl border border-gray-100 hover:shadow-gray-300/40 transition-shadow duration-300 text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">You’re Logged Out</h2>

        <p class="text-gray-600 mb-8">
          Your session has ended for security reasons or you logged out manually.
        </p>

        <div class="flex flex-col space-y-4">
          <a
            href="{{ route('login') }}"
            class="block w-full bg-[#1a1a1a] hover:bg-gray-900 text-white font-semibold py-3 rounded-full shadow-md hover:shadow-lg focus:ring-4 focus:ring-gray-300 transition-transform hover:-translate-y-0.5"
          >
            Sign In Again
          </a>

          <a
            href="{{ route('register') }}"
            class="block w-full border border-gray-300 text-gray-800 hover:bg-gray-100 font-semibold py-3 rounded-full shadow-sm transition"
          >
            Create New Account
          </a>
        </div>

        <p class="mt-8 text-sm text-gray-500">
          Having trouble? <a href="{{ route('password.request') }}" class="font-medium text-gray-800 hover:underline">Reset your password</a>
        </p>
      </div>
    </div>

  </body>
</html>
