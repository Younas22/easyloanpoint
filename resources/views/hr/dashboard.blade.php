@extends('layouts.app')

@section('title', 'HR Dashboard')
@section('page-title', 'Dashboard')

@section('page-header')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">HR Dashboard</h2>
            <p class="mt-0.5 text-sm text-gray-500">Welcome, {{ auth()->user()->name }}. Manage your assigned customers.</p>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            HR Manager
        </span>
    </div>
@endsection

@section('content')

    {{-- ── Stats cards ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Assigned Customers</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['assigned_customers'] }}</p>
                    <p class="mt-1 text-xs text-gray-400">Under my care</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Pending Loans</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['pending_loans'] }}</p>
                    <p class="mt-1 text-xs text-gray-400">Awaiting review</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-50">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Approved Loans</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['approved_loans'] }}</p>
                    <p class="mt-1 text-xs text-gray-400">Successfully approved</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Rejected Loans</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['rejected_loans'] }}</p>
                    <p class="mt-1 text-xs text-gray-400">Not approved</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50">
                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ── My assigned customers placeholder ───────────────────────── --}}
    <div class="mt-6 rounded-xl border border-gray-200 bg-white">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <h3 class="text-sm font-semibold text-gray-900">My Assigned Customers</h3>
            <a href="#" class="text-xs font-medium text-blue-600 hover:underline">View all</a>
        </div>

        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center px-5 py-12 text-center">
            <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <p class="mt-2 text-sm text-gray-500">No customers assigned yet.</p>
            <p class="text-xs text-gray-400">Your admin will assign customers to you.</p>
        </div>
    </div>

    {{-- ── Recent loan applications ─────────────────────────────────── --}}
    <div class="mt-6 rounded-xl border border-gray-200 bg-white">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <h3 class="text-sm font-semibold text-gray-900">Recent Loan Applications</h3>
            <a href="#" class="text-xs font-medium text-blue-600 hover:underline">View all loans</a>
        </div>
        <div class="flex flex-col items-center justify-center px-5 py-12 text-center">
            <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="mt-2 text-sm text-gray-500">No loan applications found.</p>
            <p class="text-xs text-gray-400">Loan applications from your customers will appear here.</p>
        </div>
    </div>

@endsection
