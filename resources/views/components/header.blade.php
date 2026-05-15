<header class="flex h-16 flex-shrink-0 items-center justify-between border-b border-gray-200 bg-white px-6">

    {{-- Left: mobile hamburger + page title --}}
    <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100 lg:hidden">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div>
            <h1 class="text-base font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
            <p class="text-xs text-gray-400">{{ now()->format('l, d M Y') }}</p>
        </div>
    </div>

    {{-- Right: notifications + user menu --}}
    <div class="flex items-center gap-2">

        {{-- Notification bell --}}
        <x-notification-bell />

        {{-- User dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition-colors hover:bg-gray-100">
                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <span class="hidden font-medium text-gray-700 sm:block">{{ auth()->user()->name }}</span>
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open"
                 x-cloak
                 @click.outside="open = false"
                 class="absolute right-0 top-full z-50 mt-1 w-48 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg">
                <div class="border-b border-gray-100 px-4 py-3">
                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-gray-500">{{ auth()->user()->email }}</p>
                    <span class="mt-1 inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">
                        {{ auth()->user()->role_label }}
                    </span>
                </div>
                <div class="py-1">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Profile</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Change Password</a>
                </div>
                <div class="border-t border-gray-100 py-1">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</header>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
</script>
