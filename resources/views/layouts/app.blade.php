<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — EasyLoanPoint</title>
<link rel="stylesheet" href="{{ asset('public/build/assets/app-CM0Fw6HD.css') }}">
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="h-full bg-gray-50 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar --}}
        <x-sidebar />

        {{-- Main wrapper --}}
        <div class="flex flex-1 flex-col overflow-hidden">

            {{-- Header --}}
            <x-header />

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto bg-gray-50 px-6 py-6">
                {{-- Page heading --}}
                @hasSection('page-header')
                    <div class="mb-6">
                        @yield('page-header')
                    </div>
                @endif

                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="mb-4 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5a1 1 0 112 0v-4a1 1 0 11-2 0v4zm1-8a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Main slot --}}
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="border-t border-gray-200 bg-white px-6 py-3">
                <p class="text-xs text-gray-400">
                    &copy; {{ date('Y') }} EasyLoanPoint. All rights reserved.
                    <span class="mx-2 text-gray-300">|</span>
                    <span class="text-gray-400">v1.0.0</span>
                </p>
            </footer>

        </div>
    </div>

    <script src="{{ asset('public/build/assets/app-Baybx7-I.js') }}"></script>
    @stack('scripts')
</body>
</html>
