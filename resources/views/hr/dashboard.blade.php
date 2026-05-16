@extends('layouts.app')

@section('title', 'HR Dashboard')
@section('page-title', 'Dashboard')

@section('page-header')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">HR Dashboard</h2>
            <p class="mt-0.5 text-sm text-gray-500">
                Welcome back, <span class="font-semibold text-blue-900">{{ auth()->user()->name }}</span>.
                Today is {{ now()->format('l, d F Y') }}.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($unreadNotifications > 0)
                <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                    {{ $unreadNotifications }} unread alert{{ $unreadNotifications > 1 ? 's' : '' }}
                </span>
            @endif
            <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                HR Manager
            </span>
        </div>
    </div>
@endsection

@section('content')

{{-- ══════════════════════════════════════════════════════════════════
     QUICK ACTIONS BAR
══════════════════════════════════════════════════════════════════ --}}
<div class="mb-6 flex flex-wrap gap-2">
    <a href="{{ route('hr.loans.index') }}"
       class="inline-flex items-center gap-2 rounded-lg border border-blue-900 bg-blue-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-800">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Manage Loans
    </a>
    <a href="{{ route('hr.loans.index', ['status' => 'pending']) }}"
       class="inline-flex items-center gap-2 rounded-lg border border-amber-500 bg-amber-50 px-4 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Pending Review
        @if($stats['pending_loans'] > 0)
            <span class="rounded-full bg-amber-500 px-1.5 py-0.5 text-xs font-bold text-white">{{ $stats['pending_loans'] }}</span>
        @endif
    </a>
    @if($stats['pending_documents'] > 0)
        <a href="{{ route('hr.loans.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-orange-400 bg-orange-50 px-4 py-2 text-xs font-semibold text-orange-700 transition hover:bg-orange-100">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
            </svg>
            Verify Documents
            <span class="rounded-full bg-orange-500 px-1.5 py-0.5 text-xs font-bold text-white">{{ $stats['pending_documents'] }}</span>
        </a>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════════
     PRIMARY STAT CARDS — Row 1 (4 cards)
══════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 gap-4 xl:grid-cols-4">

    {{-- Assigned Customers --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Assigned Customers</p>
                <p class="mt-2 text-3xl font-black text-gray-900">{{ $stats['assigned_customers'] }}</p>
                <p class="mt-1 text-xs text-gray-400">Active assignments</p>
            </div>
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border border-blue-100 bg-blue-50">
                <svg class="h-5 w-5 text-blue-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <div class="mt-4 border-t border-gray-100 pt-3">
            <span class="text-xs text-gray-400">This month: <span class="font-semibold text-gray-700">{{ $stats['this_month_loans'] }} applications</span></span>
        </div>
    </div>

    {{-- Pending Loans --}}
    <div class="rounded-xl border border-amber-200 bg-white p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Pending Loans</p>
                <p class="mt-2 text-3xl font-black text-gray-900">{{ $stats['pending_loans'] }}</p>
                <p class="mt-1 text-xs text-gray-400">Awaiting review</p>
            </div>
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border border-amber-100 bg-amber-50">
                <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="mt-4 border-t border-gray-100 pt-3">
            <span class="text-xs text-gray-400">Under review: <span class="font-semibold text-gray-700">{{ $stats['under_review'] }}</span></span>
        </div>
    </div>

    {{-- Approved Loans --}}
    <div class="rounded-xl border border-emerald-200 bg-white p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Approved Loans</p>
                <p class="mt-2 text-3xl font-black text-gray-900">{{ $stats['approved_loans'] }}</p>
                <p class="mt-1 text-xs text-gray-400">Successfully approved</p>
            </div>
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border border-emerald-100 bg-emerald-50">
                <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="mt-4 border-t border-gray-100 pt-3">
            <span class="text-xs text-gray-400">This month: <span class="font-semibold text-emerald-700">{{ $stats['this_month_approved'] }} approved</span></span>
        </div>
    </div>

    {{-- Rejected Loans --}}
    <div class="rounded-xl border border-red-200 bg-white p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Rejected Loans</p>
                <p class="mt-2 text-3xl font-black text-gray-900">{{ $stats['rejected_loans'] }}</p>
                <p class="mt-1 text-xs text-gray-400">Not approved</p>
            </div>
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border border-red-100 bg-red-50">
                <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="mt-4 border-t border-gray-100 pt-3">
            <span class="text-xs text-gray-400">Disbursed: <span class="font-semibold text-blue-700">{{ $stats['disbursed_loans'] }}</span></span>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════
     SECONDARY STAT CARDS — Row 2 (3 cards)
══════════════════════════════════════════════════════════════════ --}}
<div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">

    {{-- Under Review --}}
    <div class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white px-5 py-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-indigo-100 bg-indigo-50">
            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Under Review</p>
            <p class="text-2xl font-black text-gray-900">{{ $stats['under_review'] }}</p>
        </div>
    </div>

    {{-- Disbursed --}}
    <div class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white px-5 py-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-teal-100 bg-teal-50">
            <svg class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Disbursed</p>
            <p class="text-2xl font-black text-gray-900">{{ $stats['disbursed_loans'] }}</p>
        </div>
    </div>

    {{-- Pending Documents --}}
    <div class="flex items-center gap-4 rounded-xl border {{ $stats['pending_documents'] > 0 ? 'border-orange-300 bg-orange-50' : 'border-gray-200 bg-white' }} px-5 py-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-orange-100 bg-orange-50">
            <svg class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Docs Pending</p>
            <p class="text-2xl font-black {{ $stats['pending_documents'] > 0 ? 'text-orange-600' : 'text-gray-900' }}">{{ $stats['pending_documents'] }}</p>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════
     MAIN CONTENT — Two-column layout
══════════════════════════════════════════════════════════════════ --}}
<div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">

    {{-- LEFT: Recent Loan Applications (2/3) --}}
    <div class="xl:col-span-2">
        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Recent Loan Applications</h3>
                    <p class="mt-0.5 text-xs text-gray-400">Latest 8 applications from your customers</p>
                </div>
                <a href="{{ route('hr.loans.index') }}"
                   class="inline-flex items-center gap-1 text-xs font-semibold text-blue-900 hover:underline">
                    View all
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            @if($recentLoans->isEmpty())
                <div class="flex flex-col items-center justify-center px-5 py-12 text-center">
                    <svg class="h-10 w-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="mt-3 text-sm font-medium text-gray-500">No loan applications yet</p>
                    <p class="mt-1 text-xs text-gray-400">Applications from your assigned customers will appear here.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-widest text-gray-400">
                                <th class="px-5 py-3">Loan No.</th>
                                <th class="px-5 py-3">Customer</th>
                                <th class="px-5 py-3">Type</th>
                                <th class="px-5 py-3 text-right">Amount</th>
                                <th class="px-5 py-3 text-center">Status</th>
                                <th class="px-5 py-3 text-right">Applied</th>
                                <th class="px-5 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentLoans as $loan)
                                <tr class="transition hover:bg-gray-50/60">
                                    <td class="px-5 py-3.5">
                                        <span class="font-mono text-xs font-semibold text-blue-900">{{ $loan->loan_number }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="font-semibold text-gray-900">{{ $loan->customer->name ?? '—' }}</div>
                                        <div class="text-xs text-gray-400">{{ $loan->customer->phone ?? '' }}</div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-xs text-gray-600">{{ $loan->loan_type_label ?? ucfirst($loan->loan_type) }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="font-semibold text-gray-900">₹{{ number_format($loan->amount_requested) }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        @php
                                            $badge = match($loan->status) {
                                                'pending'      => 'bg-amber-100 text-amber-700 border-amber-200',
                                                'under_review' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                                'approved'     => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                'rejected'     => 'bg-red-100 text-red-600 border-red-200',
                                                'disbursed'    => 'bg-teal-100 text-teal-700 border-teal-200',
                                                default        => 'bg-gray-100 text-gray-600 border-gray-200',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $badge }}">
                                            {{ $loan->status_label ?? ucfirst(str_replace('_', ' ', $loan->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right text-xs text-gray-400">
                                        {{ $loan->applied_at ? $loan->applied_at->format('d M Y') : '—' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <a href="{{ route('hr.loans.show', $loan) }}"
                                           class="inline-flex items-center gap-1 rounded-md border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-800 transition hover:bg-blue-100">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- RIGHT: Notifications (1/3) --}}
    <div class="flex flex-col gap-4">

        {{-- Notifications --}}
        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-gray-900">Recent Notifications</h3>
                    @if($unreadNotifications > 0)
                        <span class="rounded-full bg-blue-900 px-2 py-0.5 text-xs font-bold text-white">{{ $unreadNotifications }}</span>
                    @endif
                </div>
            </div>

            @if($recentNotifications->isEmpty())
                <div class="flex flex-col items-center justify-center px-5 py-8 text-center">
                    <svg class="h-8 w-8 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="mt-2 text-xs text-gray-400">No notifications yet.</p>
                </div>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach($recentNotifications as $notif)
                        <li class="flex gap-3 px-4 py-3.5 {{ $notif->is_read ? '' : 'bg-blue-50/40' }}">
                            <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                                        {{ $notif->type === 'loan_status' ? 'bg-blue-100 text-blue-600' :
                                           ($notif->type === 'assignment' ? 'bg-purple-100 text-purple-600' :
                                           ($notif->type === 'document' ? 'bg-orange-100 text-orange-500' : 'bg-gray-100 text-gray-500')) }}">
                                @if($notif->type === 'loan_status')
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @elseif($notif->type === 'assignment')
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                @elseif($notif->type === 'document')
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                @else
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-900 leading-tight">{{ $notif->title }}</p>
                                <p class="mt-0.5 text-xs text-gray-500 leading-relaxed line-clamp-2">{{ $notif->message }}</p>
                                <p class="mt-1 text-xs text-gray-400">{{ $notif->created_at->diffForHumans() }}</p>
                            </div>
                            @if(!$notif->is_read)
                                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-blue-500"></span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Pending Documents Alert --}}
        @if($stats['pending_documents'] > 0)
            <div class="rounded-xl border border-orange-300 bg-orange-50 px-5 py-4">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-orange-800">Documents Awaiting Verification</p>
                        <p class="mt-0.5 text-xs text-orange-700">
                            <span class="font-bold">{{ $stats['pending_documents'] }}</span> document{{ $stats['pending_documents'] > 1 ? 's' : '' }} need{{ $stats['pending_documents'] === 1 ? 's' : '' }} your review.
                        </p>
                        <a href="{{ route('hr.loans.index') }}" class="mt-2 inline-block text-xs font-semibold text-orange-700 underline hover:text-orange-900">
                            Review now →
                        </a>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════
     MONTHLY STATS — Last 6 months
══════════════════════════════════════════════════════════════════ --}}
<div class="mt-6 rounded-xl border border-gray-200 bg-white">
    <div class="border-b border-gray-100 px-5 py-4">
        <h3 class="text-sm font-bold text-gray-900">Monthly Performance</h3>
        <p class="mt-0.5 text-xs text-gray-400">Loan statistics over the last 6 months</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-widest text-gray-400">
                    <th class="px-5 py-3">Month</th>
                    <th class="px-5 py-3 text-center">Total Applications</th>
                    <th class="px-5 py-3 text-center">Approved</th>
                    <th class="px-5 py-3 text-center">Rejected</th>
                    <th class="px-5 py-3 text-center">Approval Rate</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($monthlyStats as $month)
                    @php
                        $rate = $month['total'] > 0 ? round(($month['approved'] / $month['total']) * 100) : 0;
                        $isCurrentMonth = \Illuminate\Support\Str::contains($month['label'], now()->format('M Y'));
                    @endphp
                    <tr class="{{ $isCurrentMonth ? 'bg-blue-50/40' : 'hover:bg-gray-50/60' }} transition">
                        <td class="px-5 py-3 font-semibold text-gray-900">
                            {{ $month['label'] }}
                            @if($isCurrentMonth)
                                <span class="ml-1.5 rounded-full bg-blue-100 px-1.5 py-0.5 text-xs font-semibold text-blue-700">Current</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center font-semibold text-gray-900">{{ $month['total'] }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="font-semibold text-emerald-700">{{ $month['approved'] }}</span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="font-semibold text-red-600">{{ $month['rejected'] }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-full rounded-full bg-blue-900 transition-all" style="width: {{ $rate }}%"></div>
                                </div>
                                <span class="w-9 text-right text-xs font-semibold text-gray-700">{{ $rate }}%</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════
     ASSIGNED CUSTOMERS GRID
══════════════════════════════════════════════════════════════════ --}}
<div class="mt-6 rounded-xl border border-gray-200 bg-white">
    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
        <div>
            <h3 class="text-sm font-bold text-gray-900">My Assigned Customers</h3>
            <p class="mt-0.5 text-xs text-gray-400">Recently assigned — showing latest 6</p>
        </div>
    </div>

    @if($recentCustomers->isEmpty())
        <div class="flex flex-col items-center justify-center px-5 py-12 text-center">
            <svg class="h-10 w-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <p class="mt-3 text-sm font-medium text-gray-500">No customers assigned yet</p>
            <p class="mt-1 text-xs text-gray-400">Your admin will assign customers to you.</p>
        </div>
    @else
        <div class="grid grid-cols-1 divide-y divide-gray-100 sm:grid-cols-2 sm:divide-y-0 xl:grid-cols-3">
            @foreach($recentCustomers as $customer)
                @php
                    $latestLoan = $customer->loans->first();
                    $statusBadge = match($customer->status ?? 'active') {
                        'active'      => 'bg-emerald-100 text-emerald-700',
                        'inactive'    => 'bg-gray-100 text-gray-500',
                        'blacklisted' => 'bg-red-100 text-red-600',
                        default       => 'bg-gray-100 text-gray-500',
                    };
                @endphp
                <div class="border-b border-r-0 border-gray-100 p-5 last:border-b-0 sm:border-r xl:border-b
                            {{ $loop->index % 2 === 0 && !$loop->last ? 'sm:border-r' : '' }}
                            transition hover:bg-gray-50/50">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-blue-100 bg-blue-900 text-sm font-black text-white">
                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm leading-tight">{{ $customer->name }}</p>
                                <p class="text-xs text-gray-400">{{ $customer->phone }}</p>
                            </div>
                        </div>
                        <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusBadge }}">
                            {{ ucfirst($customer->status ?? 'active') }}
                        </span>
                    </div>

                    <div class="mt-3 flex items-center justify-between">
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <span>
                                <span class="font-semibold text-gray-900">{{ $customer->loans_count }}</span>
                                loan{{ $customer->loans_count !== 1 ? 's' : '' }}
                            </span>
                            @if($customer->employment_type)
                                <span class="text-gray-300">·</span>
                                <span>{{ ucfirst(str_replace('_', ' ', $customer->employment_type)) }}</span>
                            @endif
                        </div>
                        @if($latestLoan)
                            @php
                                $loanBadge = match($latestLoan->status) {
                                    'pending'      => 'bg-amber-100 text-amber-700',
                                    'under_review' => 'bg-indigo-100 text-indigo-700',
                                    'approved'     => 'bg-emerald-100 text-emerald-700',
                                    'rejected'     => 'bg-red-100 text-red-600',
                                    'disbursed'    => 'bg-teal-100 text-teal-700',
                                    default        => 'bg-gray-100 text-gray-500',
                                };
                            @endphp
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $loanBadge }}">
                                {{ $latestLoan->status_label ?? ucfirst(str_replace('_', ' ', $latestLoan->status)) }}
                            </span>
                        @endif
                    </div>

                    <div class="mt-3 border-t border-gray-100 pt-3 text-xs text-gray-400">
                        Assigned {{ $customer->created_at->diffForHumans() }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
