@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('page-header')
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Admin Dashboard</h2>
            <p class="mt-0.5 text-sm text-gray-500">
                Welcome back, <span class="font-medium text-gray-700">{{ auth()->user()->name }}</span>.
                Here's the complete system overview.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-green-500"></span>
                System Active
            </span>
            <span class="text-xs text-gray-400">{{ now()->format('d M Y, h:i A') }}</span>
        </div>
    </div>
@endsection

@section('content')

{{-- ════════════════════════════════════════════════════════════════════════════
    ROW 1 — Primary stat cards
════════════════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-6">

    {{-- Total Customers --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50">
                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <p class="mt-3 text-2xl font-bold text-gray-900">{{ number_format($stats['total_customers']) }}</p>
        <p class="text-xs font-medium text-gray-500">Total Customers</p>
        <p class="mt-0.5 text-xs text-green-600">{{ $stats['active_customers'] }} active</p>
    </div>

    {{-- Total HR --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100">
                <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
        <p class="mt-3 text-2xl font-bold text-gray-900">{{ number_format($stats['total_hr']) }}</p>
        <p class="text-xs font-medium text-gray-500">HR Managers</p>
        <p class="mt-0.5 text-xs text-gray-400">Staff members</p>
    </div>

    {{-- Total Loans --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50">
                <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
        <p class="mt-3 text-2xl font-bold text-gray-900">{{ number_format($stats['total_loans']) }}</p>
        <p class="text-xs font-medium text-gray-500">Total Loans</p>
        <p class="mt-0.5 text-xs text-gray-400">All applications</p>
    </div>

    {{-- Pending --}}
    <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100">
                <svg class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="mt-3 text-2xl font-bold text-yellow-800">{{ number_format($stats['pending_loans']) }}</p>
        <p class="text-xs font-medium text-yellow-700">Pending</p>
        <p class="mt-0.5 text-xs text-yellow-600">Awaiting review</p>
    </div>

    {{-- Approved --}}
    <div class="rounded-xl border border-green-200 bg-green-50 p-4">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="mt-3 text-2xl font-bold text-green-800">{{ number_format($stats['approved_loans']) }}</p>
        <p class="text-xs font-medium text-green-700">Approved</p>
        <p class="mt-0.5 text-xs text-green-600">+ {{ $stats['disbursed_loans'] }} disbursed</p>
    </div>

    {{-- Rejected --}}
    <div class="rounded-xl border border-red-200 bg-red-50 p-4">
        <div class="flex items-center justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100">
                <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="mt-3 text-2xl font-bold text-red-800">{{ number_format($stats['rejected_loans']) }}</p>
        <p class="text-xs font-medium text-red-700">Rejected</p>
        <p class="mt-0.5 text-xs text-red-600">Not approved</p>
    </div>

</div>

{{-- ── Amount summary strip ─────────────────────────────────────────────────── --}}
<div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white px-5 py-4">
        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-blue-900">
            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500">Total Amount Requested</p>
            <p class="text-lg font-bold text-gray-900">₹{{ number_format($stats['total_requested'] / 100000, 2) }}L</p>
        </div>
    </div>
    <div class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white px-5 py-4">
        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-green-700">
            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500">Total Amount Disbursed</p>
            <p class="text-lg font-bold text-gray-900">₹{{ number_format($stats['total_disbursed'] / 100000, 2) }}L</p>
        </div>
    </div>
    <div class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white px-5 py-4">
        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-slate-700">
            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500">Under Review</p>
            <p class="text-lg font-bold text-gray-900">{{ number_format($stats['under_review']) }} <span class="text-sm font-normal text-gray-500">loans</span></p>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════════
    ROW 2 — Charts
════════════════════════════════════════════════════════════════════════════ --}}
<div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-3">

    {{-- Monthly bar chart (spans 2 cols) --}}
    <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-5">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Monthly Loan Applications</h3>
                <p class="text-xs text-gray-400">Last 12 months — applied vs approved</p>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5 text-gray-500">
                    <span class="h-2.5 w-2.5 rounded-sm bg-slate-700"></span> Applied
                </span>
                <span class="flex items-center gap-1.5 text-gray-500">
                    <span class="h-2.5 w-2.5 rounded-sm bg-blue-500"></span> Approved
                </span>
            </div>
        </div>
        <div class="relative h-64">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    {{-- Status doughnut chart --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-900">Loan Status Breakdown</h3>
            <p class="text-xs text-gray-400">Current distribution</p>
        </div>
        <div class="relative h-48 flex items-center justify-center">
            <canvas id="statusChart"></canvas>
        </div>
        <ul class="mt-4 space-y-1.5">
            @php
                $statusColors = [
                    'Pending'      => ['bg-yellow-400',  'text-yellow-700'],
                    'Under Review' => ['bg-blue-500',    'text-blue-700'],
                    'Approved'     => ['bg-green-500',   'text-green-700'],
                    'Rejected'     => ['bg-red-500',     'text-red-700'],
                    'Disbursed'    => ['bg-slate-600',   'text-slate-700'],
                ];
                $statusCounts = [
                    'Pending'      => $stats['pending_loans'],
                    'Under Review' => $stats['under_review'],
                    'Approved'     => $stats['approved_loans'],
                    'Rejected'     => $stats['rejected_loans'],
                    'Disbursed'    => $stats['disbursed_loans'],
                ];
            @endphp
            @foreach($statusCounts as $label => $count)
                <li class="flex items-center justify-between text-xs">
                    <span class="flex items-center gap-2 text-gray-600">
                        <span class="h-2 w-2 rounded-full {{ $statusColors[$label][0] }}"></span>
                        {{ $label }}
                    </span>
                    <span class="font-semibold {{ $statusColors[$label][1] }}">{{ $count }}</span>
                </li>
            @endforeach
        </ul>
    </div>

</div>

{{-- ════════════════════════════════════════════════════════════════════════════
    ROW 3 — Recent Applications Table + HR Activity
════════════════════════════════════════════════════════════════════════════ --}}
<div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-3">

    {{-- Recent applications table (spans 2 cols) --}}
    <div class="xl:col-span-2 rounded-xl border border-gray-200 bg-white">

        {{-- Table header + search/filter --}}
        <div class="border-b border-gray-100 px-5 py-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Recent Loan Applications</h3>
                <a href="#" class="text-xs font-medium text-blue-600 hover:underline">View all loans →</a>
            </div>

            {{-- Search + filter form --}}
            <form method="GET" action="{{ route('admin.dashboard') }}" class="mt-3 flex flex-col gap-2 sm:flex-row">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search by name, phone or loan #..."
                        class="w-full rounded-lg border border-gray-300 bg-white py-2 pl-9 pr-4 text-sm text-gray-800 placeholder-gray-400 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    >
                </div>
                <select name="status"
                        onchange="this.form.submit()"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="pending"      {{ $status === 'pending'      ? 'selected' : '' }}>Pending</option>
                    <option value="under_review" {{ $status === 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="approved"     {{ $status === 'approved'     ? 'selected' : '' }}>Approved</option>
                    <option value="rejected"     {{ $status === 'rejected'     ? 'selected' : '' }}>Rejected</option>
                    <option value="disbursed"    {{ $status === 'disbursed'    ? 'selected' : '' }}>Disbursed</option>
                </select>
                @if($search || $status)
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-1 rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Clear
                    </a>
                @endif
                <button type="submit"
                        class="rounded-lg bg-blue-900 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800">
                    Search
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Loan #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Type</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">HR</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentLoans as $loan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-mono text-xs font-semibold text-blue-700">{{ $loan->loan_number }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $loan->customer->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $loan->customer->phone }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-gray-600">{{ $loan->loan_type_label }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="font-semibold text-gray-800">₹{{ number_format($loan->amount_requested) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="{{ $loan->status_badge_class }} text-xs">
                                    {{ $loan->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-gray-500">
                                    {{ $loan->assignedHR?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-gray-400">{{ $loan->applied_at->format('d M Y') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center">
                                <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No loans found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($recentLoans->hasPages())
            <div class="border-t border-gray-100 px-5 py-3">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        Showing {{ $recentLoans->firstItem() }}–{{ $recentLoans->lastItem() }}
                        of {{ $recentLoans->total() }} loans
                    </p>
                    <div class="flex items-center gap-1">
                        {{-- Previous --}}
                        @if($recentLoans->onFirstPage())
                            <span class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-300 cursor-not-allowed">← Prev</span>
                        @else
                            <a href="{{ $recentLoans->previousPageUrl() }}"
                               class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50">← Prev</a>
                        @endif

                        {{-- Page numbers --}}
                        @foreach($recentLoans->getUrlRange(max(1, $recentLoans->currentPage()-2), min($recentLoans->lastPage(), $recentLoans->currentPage()+2)) as $page => $url)
                            @if($page === $recentLoans->currentPage())
                                <span class="rounded-lg bg-blue-900 px-3 py-1.5 text-xs font-semibold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next --}}
                        @if($recentLoans->hasMorePages())
                            <a href="{{ $recentLoans->nextPageUrl() }}"
                               class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50">Next →</a>
                        @else
                            <span class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-300 cursor-not-allowed">Next →</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- Recent HR Activity feed --}}
    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-100 px-5 py-4">
            <h3 class="text-sm font-semibold text-gray-900">Recent HR Activities</h3>
            <p class="text-xs text-gray-400">Latest status updates</p>
        </div>

        <div class="divide-y divide-gray-50">
            @forelse($recentActivities as $activity)
                <div class="px-5 py-3.5">
                    <div class="flex items-start gap-3">
                        {{-- Avatar --}}
                        <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-slate-800 text-xs font-bold text-white">
                            {{ strtoupper(substr($activity->changedBy->name ?? 'S', 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-medium text-gray-800">
                                {{ $activity->changedBy->name ?? 'System' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $activity->loan->loan_number ?? '' }}
                                @if($activity->from_status)
                                    <span class="text-gray-400">{{ $activity->from_status }}</span>
                                    <span class="mx-1 text-gray-300">→</span>
                                @endif
                                <span class="font-medium text-gray-700">{{ $activity->to_status }}</span>
                            </p>
                            @if($activity->loan->customer)
                                <p class="text-xs text-gray-400">{{ $activity->loan->customer->name }}</p>
                            @endif
                            <p class="mt-0.5 text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                        {{-- Status dot --}}
                        <span class="mt-0.5 flex-shrink-0">
                            @php
                                $dot = match($activity->to_status) {
                                    'approved', 'disbursed' => 'bg-green-500',
                                    'rejected'              => 'bg-red-500',
                                    'under_review'          => 'bg-blue-500',
                                    default                 => 'bg-yellow-400',
                                };
                            @endphp
                            <span class="inline-block h-2 w-2 rounded-full {{ $dot }}"></span>
                        </span>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center px-5 py-10 text-center">
                    <svg class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="mt-2 text-xs text-gray-400">No activities yet.</p>
                </div>
            @endforelse
        </div>

        @if($recentActivities->isNotEmpty())
            <div class="border-t border-gray-100 px-5 py-3">
                <a href="#" class="text-xs font-medium text-blue-600 hover:underline">View all activity logs →</a>
            </div>
        @endif
    </div>

</div>

@endsection

{{-- ════════════════════════════════════════════════════════════════════════════
    Charts — Chart.js initialization
════════════════════════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
(function () {
    const monthlyData = @json($monthlyChart);
    const statusData  = @json($statusChart);

    // ── Monthly bar chart ─────────────────────────────────────────────────────
    const mCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(mCtx, {
        type: 'bar',
        data: {
            labels: monthlyData.labels,
            datasets: [
                {
                    label: 'Applied',
                    data: monthlyData.applied,
                    backgroundColor: '#1e293b',
                    borderRadius: 4,
                    barPercentage: 0.55,
                    categoryPercentage: 0.7,
                },
                {
                    label: 'Approved',
                    data: monthlyData.approved,
                    backgroundColor: '#3b82f6',
                    borderRadius: 4,
                    barPercentage: 0.55,
                    categoryPercentage: 0.7,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { size: 11 },
                    bodyFont: { size: 11 },
                    padding: 8,
                    cornerRadius: 6,
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 }, color: '#9ca3af' },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: { font: { size: 10 }, color: '#9ca3af', stepSize: 1 },
                },
            },
        },
    });

    // ── Status doughnut ───────────────────────────────────────────────────────
    const sCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(sCtx, {
        type: 'doughnut',
        data: {
            labels: statusData.labels,
            datasets: [{
                data: statusData.data,
                backgroundColor: ['#facc15', '#3b82f6', '#22c55e', '#ef4444', '#475569'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { size: 11 },
                    bodyFont: { size: 11 },
                    padding: 8,
                    cornerRadius: 6,
                },
            },
        },
    });
})();
</script>
@endpush
