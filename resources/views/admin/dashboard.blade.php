@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('page-header')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Admin Dashboard</h2>
            <p class="mt-0.5 text-sm text-gray-500">Welcome back, {{ auth()->user()->name }}. Here's the system overview.</p>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
            System Active
        </span>
    </div>
@endsection

@section('content')

    {{-- ── Stats cards ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Total Customers --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Customers</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['total_customers']) }}</p>
                    <p class="mt-1 text-xs text-gray-400">Registered in system</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- HR Managers --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">HR Managers</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['total_hr']) }}</p>
                    <p class="mt-1 text-xs text-gray-400">Active HR staff</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                    <svg class="h-6 w-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Loans --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Loans</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">0</p>
                    <p class="mt-1 text-xs text-gray-400">All applications</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-50">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active Users --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Active Users</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['active_users']) }}</p>
                    <p class="mt-1 text-xs text-gray-400">System-wide</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Second row ───────────────────────────────────────────────── --}}
    <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-3">

        {{-- Loan status summary --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 lg:col-span-2">
            <div class="mb-5 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Loan Pipeline</h3>
                <a href="#" class="text-xs font-medium text-blue-600 hover:underline">View all</a>
            </div>

            <div class="space-y-3">
                @foreach([
                    ['label' => 'Pending',      'count' => 0, 'color' => 'bg-yellow-400', 'pct' => 0],
                    ['label' => 'Under Review',  'count' => 0, 'color' => 'bg-blue-500',   'pct' => 0],
                    ['label' => 'Approved',      'count' => 0, 'color' => 'bg-green-500',  'pct' => 0],
                    ['label' => 'Rejected',      'count' => 0, 'color' => 'bg-red-500',    'pct' => 0],
                    ['label' => 'Disbursed',     'count' => 0, 'color' => 'bg-slate-600',  'pct' => 0],
                ] as $row)
                    <div class="flex items-center gap-3">
                        <span class="w-24 flex-shrink-0 text-xs text-gray-500">{{ $row['label'] }}</span>
                        <div class="flex-1 overflow-hidden rounded-full bg-gray-100" style="height:6px">
                            <div class="{{ $row['color'] }} rounded-full" style="width:{{ $row['pct'] }}%; height:6px"></div>
                        </div>
                        <span class="w-6 text-right text-xs font-semibold text-gray-700">{{ $row['count'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <h3 class="mb-4 text-sm font-semibold text-gray-900">Quick Actions</h3>
            <div class="space-y-2">
                @foreach([
                    ['label' => 'Add New HR',         'icon' => 'user-plus', 'color' => 'text-blue-600 bg-blue-50'],
                    ['label' => 'Add Customer',        'icon' => 'user-add',  'color' => 'text-green-600 bg-green-50'],
                    ['label' => 'View All Loans',      'icon' => 'doc',       'color' => 'text-yellow-600 bg-yellow-50'],
                    ['label' => 'Generate Report',     'icon' => 'chart',     'color' => 'text-purple-600 bg-purple-50'],
                    ['label' => 'System Settings',     'icon' => 'cog',       'color' => 'text-gray-600 bg-gray-100'],
                ] as $action)
                    <a href="#"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-50">
                        <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg {{ $action['color'] }}">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                        </span>
                        {{ $action['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ── Recent activity placeholder ─────────────────────────────── --}}
    <div class="mt-6 rounded-xl border border-gray-200 bg-white">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <h3 class="text-sm font-semibold text-gray-900">Recent Activity</h3>
            <a href="#" class="text-xs font-medium text-blue-600 hover:underline">View logs</a>
        </div>
        <div class="flex flex-col items-center justify-center px-5 py-12 text-center">
            <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="mt-2 text-sm text-gray-500">No activity recorded yet.</p>
            <p class="text-xs text-gray-400">Activity will appear here as the system is used.</p>
        </div>
    </div>

@endsection
