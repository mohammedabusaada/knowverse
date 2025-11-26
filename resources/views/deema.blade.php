@extends('home')
@section('home')




<body class="bg-stone-50 text-gray-900 min-h-screen flex flex-col">

    <!-- Navbar -->

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto px-6 py-10">

       <!-- Greeting -->


        <!-- User Welcome Card -->


        <!-- Trending Discussions -->
        <section id="trending" class="mb-12">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-2xl font-bold">Trending Discussions</h2>
                <a href="#" class="text-sm text-gray-600 hover:underline">View All</a>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                @foreach ([1,2,3] as $i)
                <article class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-lg transition">
                    <h3 class="font-semibold text-lg mb-2">
                        AI and Academic Integrity: Balancing Innovation with Ethics
                    </h3>

                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        Exploring how universities can adapt AI tools while maintaining fair academic evaluation systems.
                    </p>

                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <span>▲ 245 • 56 replies</span>
                        <span>3 days ago</span>
                    </div>
                </article>
                @endforeach
            </div>
        </section>

        <!-- Recent Posts -->
        <section id="recent" class="mb-12">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-2xl font-bold">Recent Posts</h2>
                <a href="#" class="text-sm text-gray-600 hover:underline">View All</a>
            </div>

            <div class="space-y-4">
                @for ($i = 0; $i < 4; $i++)
                <div class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition">

                    <h3 class="font-semibold mb-1">Open Data Policies and Their Impact on Global Research</h3>

                    <p class="text-gray-600 text-sm line-clamp-2">
                        Discussing how open data initiatives enhance collaboration while challenging privacy frameworks.
                    </p>

                    <div class="mt-2 flex justify-between text-xs text-gray-500">
                        <span>By R. Haddad • 2h ago</span>
                        <span>▲ 78 replies</span>
                    </div>

                </div>
                @endfor
            </div>
        </section>
@endsection
