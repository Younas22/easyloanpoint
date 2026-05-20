@php
    $user     = auth()->user();
    $isAdmin  = $user->isAdmin();
    $current  = request()->route()->getName();

    $adminNav = [
        ['route' => 'admin.dashboard',        'label' => 'Dashboard',        'icon' => 'grid',       'prefix' => 'admin.dashboard'],
        ['route' => 'admin.hr.index',         'label' => 'HR Management',    'icon' => 'users',      'prefix' => 'admin.hr'],
        ['route' => 'admin.customers.index',  'label' => 'Customers',        'icon' => 'user-group', 'prefix' => 'admin.customers'],
        ['route' => 'admin.loans.index',      'label' => 'Loan Applications','icon' => 'document',   'prefix' => 'admin.loans'],
        ['route' => 'admin.loan-types.index', 'label' => 'Loan Types',       'icon' => 'tag',        'prefix' => 'admin.loan-types'],
        ['route' => 'admin.assignments.index', 'label' => 'Assignments',      'icon' => 'link',       'prefix' => 'admin.assignments'],
        ['route' => 'notifications.index',   'label' => 'Notifications',    'icon' => 'bell',       'prefix' => 'notifications'],
        ['route' => 'admin.reports.index',    'label' => 'Reports',          'icon' => 'chart',      'prefix' => 'admin.reports'],
        ['route' => 'admin.settings.index',   'label' => 'Settings',         'icon' => 'cog',        'prefix' => 'admin.settings'],
        ['route' => 'admin.logs.index',       'label' => 'Activity Logs',    'icon' => 'list',       'prefix' => 'admin.logs'],
    ];

    $hrNav = [
        ['route' => 'hr.dashboard',        'label' => 'Dashboard',        'icon' => 'grid',       'prefix' => 'hr.dashboard'],
        ['route' => 'hr.customers.index',  'label' => 'My Customers',     'icon' => 'user-group', 'prefix' => 'hr.customers'],
        ['route' => 'hr.loans.index',      'label' => 'Loan Applications','icon' => 'document',   'prefix' => 'hr.loans'],
        ['route' => 'notifications.index', 'label' => 'Notifications',    'icon' => 'bell',       'prefix' => 'notifications'],
        ['route' => 'profile.show',        'label' => 'Profile',          'icon' => 'user',       'prefix' => 'profile'],
    ];

    $navItems = $isAdmin ? $adminNav : $hrNav;
@endphp

{{-- Mobile overlay --}}
<div id="sidebar-overlay"
     class="fixed inset-0 z-20 bg-black/60 lg:hidden hidden"
     onclick="toggleSidebar()">
</div>

{{-- Sidebar --}}
<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-slate-900 transition-transform duration-300 -translate-x-full lg:relative lg:translate-x-0 lg:flex lg:flex-shrink-0">

    {{-- Logo --}}
    <div class="flex h-16 items-center gap-3 border-b border-slate-700 px-5">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600">
            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold leading-none text-white">EasyLoanPoint</p>
            <p class="mt-0.5 text-xs text-slate-400">{{ $isAdmin ? 'Admin Panel' : 'HR Panel' }}</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4">

        <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-widest text-slate-500">
            {{ $isAdmin ? 'Administration' : 'HR Menu' }}
        </p>

        <ul class="space-y-0.5">
            @foreach($navItems as $item)
                @php
                    $isActive = str_starts_with($current ?? '', $item['prefix']);
                @endphp
                <li>
                    <a href="{{ route($item['route']) }}"
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors
                              {{ $isActive
                                  ? 'bg-blue-600 text-white'
                                  : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

                        {{-- Icon --}}
                        <span class="flex h-5 w-5 flex-shrink-0 items-center justify-center">
                            @if($item['icon'] === 'grid')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                            @elseif($item['icon'] === 'users')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            @elseif($item['icon'] === 'user-group')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            @elseif($item['icon'] === 'document')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            @elseif($item['icon'] === 'link')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            @elseif($item['icon'] === 'chart')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            @elseif($item['icon'] === 'cog')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            @elseif($item['icon'] === 'list')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                            @elseif($item['icon'] === 'user')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            @elseif($item['icon'] === 'tag')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            @elseif($item['icon'] === 'bell')
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            @endif
                        </span>

                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    {{-- User profile at bottom --}}
    <div class="border-t border-slate-700 p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-slate-700 text-sm font-bold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-slate-400">{{ auth()->user()->role_label }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        title="Logout"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition-colors hover:bg-slate-800 hover:text-red-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

</aside>
