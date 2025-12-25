@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Page Title --}}
    <h1 class="text-2xl font-bold mb-6">
        Admin Dashboard
    </h1>

    {{-- Statistics Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <div class="p-5 bg-white rounded shadow">
            <h2 class="text-sm text-gray-500">Total Posts</h2>
            <p class="text-3xl font-semibold">{{ $totalPosts }}</p>
        </div>

        <div class="p-5 bg-white rounded shadow">
            <h2 class="text-sm text-gray-500">Total Comments</h2>
            <p class="text-3xl font-semibold">{{ $totalComments }}</p>
        </div>

        <div class="p-5 bg-white rounded shadow">
            <h2 class="text-sm text-gray-500">Total Users</h2>
            <p class="text-3xl font-semibold">{{ $totalUsers }}</p>
        </div>

        <div class="p-5 bg-white rounded shadow">
            <h2 class="text-sm text-gray-500">Total Reports</h2>
            <p class="text-3xl font-semibold">{{ $totalReports }}</p>
        </div>

        <div class="p-5 bg-white rounded shadow">
            <h2 class="text-sm text-gray-500">Pending Reports</h2>
            <p class="text-3xl font-semibold text-red-600">
                {{ $pendingReports }}
            </p>
        </div>

    </div>

    {{-- Admin Quick Actions --}}
    <div class="mt-10">
        <h2 class="text-xl font-semibold mb-4">
            Admin Actions
        </h2>

        <div class="flex flex-wrap gap-4">
            <a href="#"
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Manage Posts
            </a>

            <a href="#"
               class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Manage Users
            </a>

            <a href="#"
               class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Review Reports
            </a>
        </div>
    </div>

</div>
@endsection
