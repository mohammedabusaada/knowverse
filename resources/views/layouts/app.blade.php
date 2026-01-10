<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
    x-init="
        if (darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    "
    x-bind:class="{ 'dark': darkMode }"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KnowVerse') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Prism.js -->
    <link href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism.min.css" rel="stylesheet" id="prism-light">
    <link href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism-okaidia.min.css" rel="stylesheet" id="prism-dark" disabled>
    <script src="https://cdn.jsdelivr.net/npm/prismjs/prism.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs/plugins/autoloader/prism-autoloader.min.js" defer></script>

    <!-- Brand Styling -->
    <style>
        .brand-name {
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #000;
        }
        .dark .brand-name {
            color: #fff;
        }
    </style>
</head>

<body class="font-sans antialiased
             bg-gradient-to-br from-gray-100 to-gray-200
             dark:from-gray-900 dark:to-gray-950
             text-gray-900 dark:text-gray-100
             min-h-screen">

    <!-- Global Navigation -->
    <x-nav-bar />

    <!-- Page Header -->
    @isset($header)
        <header class="bg-white/80 dark:bg-gray-800/80
                       backdrop-blur
                       border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @yield('content')
    </main>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.effect(() => {
                const isDark = document.documentElement.classList.contains('dark');
                document.getElementById('prism-light').disabled = isDark;
                document.getElementById('prism-dark').disabled = !isDark;
            });
        });
    </script>

</body>
</html>
