@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">

    {{-- Page Title --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            Admin Dashboard
        </h1>
        <p class="text-gray-500 mt-1">
            Overview of platform activity & moderation
        </p>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Posts --}}
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Total Posts</p>
            <p class="mt-2 text-3xl font-bold text-gray-800">
                {{ $totalPosts }}
            </p>
        </div>

        {{-- Comments --}}
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-indigo-500">
            <p class="text-sm text-gray-500">Total Comments</p>
            <p class="mt-2 text-3xl font-bold text-gray-800">
                {{ $totalComments }}
            </p>
        </div>

        {{-- Users --}}
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Total Users</p>
            <p class="mt-2 text-3xl font-bold text-gray-800">
                {{ $totalUsers }}
            </p>
        </div>

        {{-- Total Reports --}}
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-gray-400">
            <p class="text-sm text-gray-500">Total Reports</p>
            <p class="mt-2 text-3xl font-bold text-gray-800">
                {{ $totalReports }}
            </p>
        </div>

        {{-- Pending Reports --}}
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500">Pending Reports</p>
            <p class="mt-2 text-3xl font-bold text-yellow-600">
                {{ $pendingReports }}
            </p>
        </div>

        {{-- Resolved Reports --}}
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-600">
            <p class="text-sm text-gray-500">Resolved Reports</p>
            <p class="mt-2 text-3xl font-bold text-green-600">
                {{ $resolvedReports }}
            </p>
        </div>

        {{-- Dismissed Reports --}}
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-red-600">
            <p class="text-sm text-gray-500">Dismissed Reports</p>
            <p class="mt-2 text-3xl font-bold text-red-600">
                {{ $dismissedReports }}
            </p>
        </div>

    </div>

    {{-- Admin Actions --}}
    <div class="mt-12">
        <h2 class="text-xl font-semibold mb-4">
            Admin Actions
        </h2>

        <div class="flex flex-wrap gap-4">
    <a href="{{ route('posts.index') }}"
       class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
        View Posts
    </a>

    <a href="{{ route('admin.users.index') }}"
       class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition">
        Manage Users
    </a>

    <a href="{{ route('admin.reports.index') }}"
       class="px-5 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
        Review Reports
    </a>
</div>

    </div>

</div>
@endsection
