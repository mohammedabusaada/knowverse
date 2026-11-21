<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KnowVerse') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('layouts.includes.theme-init')
</head>

<body class="font-serif text-ink bg-aged/30 antialiased min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
    
    {{-- Application Logo --}}
    <div>
        <a href="/">
            <h1 class="font-heading text-4xl font-bold tracking-tight text-ink">KnowVerse.</h1>
        </a>
    </div>

    {{-- Authentication Form Container --}}
    <div class="w-full sm:max-w-md mt-8 px-8 py-10 bg-paper border border-rule shadow-xl rounded-sm">
        {{ $slot }}
    </div>

</body>
</html>