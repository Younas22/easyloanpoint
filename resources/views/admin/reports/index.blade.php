@extends('layouts.app')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@section('content')
<div x-data="reportsApp()" x-cloak>

    {{-- ── Loading overlay ──────────────────────────────────────────────────── --}}
    <div x-show="loading"
         class="fixed inset-0 z-50 flex items-center justify-center bg-white/70 backdrop-blur-sm">
        <div class="flex flex-col items-center gap-3">
            <svg class="h-8 w-8 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            <span class="text-sm font-medium text-gray-600">Loading report…</span>
        </div>
    </div>

    {{-- ── Page header ───────────────────────────────────────────────────────── --}}
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Reports & Analytics</h1>
            <p class="mt-0.5 text-sm text-gray-500" x-text="periodLabel">{{ $filters['label'] }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">

            {{-- Export dropdown --}}
            <div class="relative" x-data="{ exportOpen: false }">
                <button @click="exportOpen = !exportOpen" @click.outside="exportOpen = false"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                    <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="exportOpen" x-cloak
                     class="absolute right-0 top-full z-30 mt-1 w-48 rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
                    <a :href="buildExportUrl('loans')"
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Loans CSV
                    </a>
                    <a :href="buildExportUrl('customers')"
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Customers CSV
                    </a>
                    <a :href="buildExportUrl('hr')"
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        HR Report CSV
                    </a>
                    <a :href="buildExportUrl('financial')"
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="h-4 w-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Financial CSV
                    </a>
                </div>
            </div>

            {{-- Print --}}
            <a :href="buildPrintUrl()" target="_blank"
               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </a>
        </div>
    </div>

    {{-- ── Filter bar ─────────────────────────────────────────────────────────── --}}
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-4">

            {{-- Period quick buttons --}}
            <div class="flex flex-wrap gap-2">
                <span class="self-center text-xs font-semibold uppercase tracking-wide text-gray-400 mr-1">Period:</span>
                @foreach(['today' => 'Today', 'this_week' => 'This Week', 'this_month' => 'This Month', 'custom' => 'Custom Range'] as $val => $label)
                    <button @click="range = '{{ $val }}'"
                            :class="range === '{{ $val }}'
                                ? 'bg-slate-900 text-white border-slate-900'
                                : 'bg-white text-gray-600 border-gray-300 hover:border-slate-900 hover:text-slate-900'"
                            class="rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- Custom date range --}}
            <div class="flex flex-wrap items-end gap-3" x-show="range === 'custom'" x-transition>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">From</label>
                    <input type="date" x-model="dateFrom"
                           class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">To</label>
                    <input type="date" x-model="dateTo"
                           class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            {{-- Additional filters + search + apply --}}
            <div class="flex flex-wrap items-end gap-3">

                {{-- Search --}}
                <div class="flex-1 min-w-40">
                    <label class="mb-1 block text-xs font-medium text-gray-500">Search Loans</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" x-model="search" @keydown.enter="applyFilters()" placeholder="Loan #, name, phone…"
                               class="w-full rounded-lg border border-gray-300 pl-9 pr-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                {{-- Status --}}
                <div class="min-w-36">
                    <label class="mb-1 block text-xs font-medium text-gray-500">Status</label>
                    <select x-model="status"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="under_review">Under Review</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="disbursed">Disbursed</option>
                    </select>
                </div>

                {{-- Loan type --}}
                <div class="min-w-36">
                    <label class="mb-1 block text-xs font-medium text-gray-500">Loan Type</label>
                    <select x-model="loanType"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">All Types</option>
                        @foreach($loanTypes as $type)
                            <option value="{{ $type }}">{{ ucwords(str_replace('_',' ',$type)) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- HR --}}
                <div class="min-w-40">
                    <label class="mb-1 block text-xs font-medium text-gray-500">HR Manager</label>
                    <select x-model="hrId"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">All HR</option>
                        @foreach($hrUsers as $hr)
                            <option value="{{ $hr->id }}">{{ $hr->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Apply + Reset --}}
                <div class="flex gap-2">
                    <button @click="applyFilters()"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        Apply
                    </button>
                    <button @click="resetFilters()"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Loan Summary — 6 cards ─────────────────────────────────────────────── --}}
    <div class="mb-5">
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-widest text-gray-400">Loan Summary</h2>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">

            @php
            $loanCards = [
                ['key' => 'loan_total',        'label' => 'Total Loans',   'color' => 'bg-slate-900', 'text' => 'text-white'],
                ['key' => 'loan_pending',       'label' => 'Pending',       'color' => 'bg-amber-50 border border-amber-200', 'text' => 'text-amber-700'],
                ['key' => 'loan_under_review',  'label' => 'Under Review',  'color' => 'bg-blue-50 border border-blue-200',  'text' => 'text-blue-700'],
                ['key' => 'loan_approved',      'label' => 'Approved',      'color' => 'bg-green-50 border border-green-200','text' => 'text-green-700'],
                ['key' => 'loan_rejected',      'label' => 'Rejected',      'color' => 'bg-red-50 border border-red-200',    'text' => 'text-red-700'],
                ['key' => 'loan_disbursed',     'label' => 'Disbursed',     'color' => 'bg-purple-50 border border-purple-200','text' => 'text-purple-700'],
            ];
            @endphp

            @foreach($loanCards as $card)
            <div class="rounded-xl p-4 {{ $card['color'] }}">
                <p class="text-xs font-medium {{ $card['text'] === 'text-white' ? 'text-slate-300' : 'text-gray-500' }} uppercase tracking-wide">{{ $card['label'] }}</p>
                <p class="mt-1.5 text-2xl font-bold {{ $card['text'] }}"
                   x-text="fmt(summary.{{ $card['key'] }})">{{ number_format($summary[$card['key']]) }}</p>
                <p class="mt-1 text-xs {{ $card['text'] === 'text-white' ? 'text-slate-400' : 'text-gray-400' }}">applications</p>
            </div>
            @endforeach

        </div>
    </div>

    {{-- ── Customer + Financial — 2 columns ──────────────────────────────────── --}}
    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Customer stats --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                    <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-gray-900">Customer Summary</h2>
            </div>
            <div class="grid grid-cols-2 gap-3">
                @foreach([
                    ['key' => 'cust_new',      'label' => 'New (Period)',  'icon_color' => 'text-blue-600'],
                    ['key' => 'cust_active',   'label' => 'Active Total',  'icon_color' => 'text-green-600'],
                    ['key' => 'cust_inactive', 'label' => 'Inactive Total','icon_color' => 'text-red-500'],
                    ['key' => 'cust_total',    'label' => 'All Customers', 'icon_color' => 'text-gray-700'],
                ] as $c)
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">{{ $c['label'] }}</p>
                    <p class="mt-1 text-xl font-bold text-gray-900"
                       x-text="fmt(summary.{{ $c['key'] }})">{{ number_format($summary[$c['key']]) }}</p>
                </div>
                @endforeach
            </div>
            <div class="mt-3 flex items-center gap-2 rounded-lg bg-slate-900 px-3 py-2">
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-xs text-slate-300">Active HR Managers:</span>
                <span class="ml-auto text-sm font-bold text-white" x-text="summary.hr_total">{{ $summary['hr_total'] }}</span>
            </div>
        </div>

        {{-- Financial stats --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100">
                    <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-gray-900">Financial Summary</h2>
            </div>
            <div class="space-y-2">
                @foreach([
                    ['key' => 'fin_total_req', 'label' => 'Total Requested', 'bar_color' => 'bg-slate-900'],
                    ['key' => 'fin_approved',  'label' => 'Approved Amount', 'bar_color' => 'bg-green-600'],
                    ['key' => 'fin_pending',   'label' => 'Pending Amount',  'bar_color' => 'bg-amber-500'],
                    ['key' => 'fin_disbursed', 'label' => 'Disbursed Amount','bar_color' => 'bg-purple-600'],
                ] as $f)
                <div class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 px-3 py-2.5">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full {{ $f['bar_color'] }}"></span>
                        <span class="text-xs text-gray-600">{{ $f['label'] }}</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900"
                          x-text="fmtAmt(summary.{{ $f['key'] }})">₹{{ number_format($summary[$f['key']], 2) }}</span>
                </div>
                @endforeach
            </div>

            {{-- Approval rate bar --}}
            <div class="mt-3 rounded-lg border border-gray-100 bg-gray-50 p-3">
                <div class="mb-1.5 flex items-center justify-between">
                    <span class="text-xs text-gray-500">Approval Rate</span>
                    <span class="text-xs font-semibold text-gray-900"
                          x-text="approvalRate + '%'">
                        {{ $summary['loan_total'] > 0 ? round(($summary['loan_approved'] + $summary['loan_disbursed']) / $summary['loan_total'] * 100, 1) : 0 }}%
                    </span>
                </div>
                <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-200">
                    <div class="h-full rounded-full bg-green-600 transition-all duration-500"
                         :style="'width:' + approvalRate + '%'"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Monthly Trend Chart ─────────────────────────────────────────────────── --}}
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Monthly Loan Trend</h2>
                <p class="text-xs text-gray-400">Last 12 months — all data</p>
            </div>
            <div class="flex items-center gap-4 text-xs text-gray-500">
                <span class="flex items-center gap-1"><span class="inline-block h-2 w-3 rounded-sm bg-slate-900"></span> Applied</span>
                <span class="flex items-center gap-1"><span class="inline-block h-2 w-3 rounded-sm bg-green-600"></span> Approved</span>
                <span class="flex items-center gap-1"><span class="inline-block h-2 w-3 rounded-sm bg-purple-600"></span> Disbursed</span>
            </div>
        </div>
        <div class="h-72">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    {{-- ── Status Pie + HR Chart ───────────────────────────────────────────────── --}}
    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Status distribution --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-1 text-sm font-semibold text-gray-900">Loan Status Distribution</h2>
            <p class="mb-4 text-xs text-gray-400">Based on current filters</p>
            <div class="h-64">
                <canvas id="statusChart"></canvas>
            </div>
            <template x-if="summary.loan_total === 0">
                <div class="mt-2 rounded-lg bg-gray-50 py-8 text-center text-sm text-gray-400">No loan data in selected period</div>
            </template>
        </div>

        {{-- HR Performance chart --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-1 text-sm font-semibold text-gray-900">HR Performance Chart</h2>
            <p class="mb-4 text-xs text-gray-400">Loans by HR manager in selected period</p>
            <div class="h-64">
                <canvas id="hrChart"></canvas>
            </div>
            <template x-if="hrStats.length === 0">
                <div class="mt-2 rounded-lg bg-gray-50 py-8 text-center text-sm text-gray-400">No HR data available</div>
            </template>
        </div>
    </div>

    {{-- ── HR Performance Table ────────────────────────────────────────────────── --}}
    <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">HR Performance</h2>
                <p class="text-xs text-gray-400" x-text="hrStats.length + ' HR managers'">{{ count($hrStats) }} HR managers</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">HR Manager</th>
                        <th class="px-4 py-3 text-center">Customers</th>
                        <th class="px-4 py-3 text-center">Total Loans</th>
                        <th class="px-4 py-3 text-center">Approved</th>
                        <th class="px-4 py-3 text-center">Rejected</th>
                        <th class="px-4 py-3 text-center">Pending</th>
                        <th class="px-4 py-3 text-center">Disbursed</th>
                        <th class="px-4 py-3 text-right">Approved Amt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="hr in hrStats" :key="hr.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white"
                                         x-text="hr.initials"></div>
                                    <span class="font-medium text-gray-900" x-text="hr.name"></span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center font-medium text-gray-700" x-text="hr.assigned_customers"></td>
                            <td class="px-4 py-3 text-center font-semibold text-gray-900" x-text="hr.total_loans"></td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-medium text-green-700" x-text="hr.approved"></span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-medium text-red-600" x-text="hr.rejected"></span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-medium text-amber-600" x-text="hr.pending"></span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-medium text-purple-600" x-text="hr.disbursed"></span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900" x-text="fmtAmt(hr.approved_amount)"></td>
                        </tr>
                    </template>
                    <template x-if="hrStats.length === 0">
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-400">No HR performance data for selected period.</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Loan Details Table ──────────────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-2 border-b border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Loan Details</h2>
                <p class="text-xs text-gray-400">
                    Showing <span x-text="loanRows.from">{{ $loanRows['from'] }}</span>–<span x-text="loanRows.to">{{ $loanRows['to'] }}</span>
                    of <span x-text="loanRows.total">{{ $loanRows['total'] }}</span> records
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Loan #</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-left">HR</th>
                        <th class="px-4 py-3 text-left">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="(row, i) in loanRows.data" :key="i">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3 font-mono text-xs font-semibold text-blue-700" x-text="row.loan_number"></td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900" x-text="row.customer_name"></div>
                                <div class="text-xs text-gray-400" x-text="row.customer_phone"></div>
                            </td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.loan_type_label"></td>
                            <td class="px-4 py-3 text-right">
                                <div class="font-semibold text-gray-900" x-text="fmtAmt(row.amount)"></div>
                                <div class="text-xs text-gray-400" x-show="row.amount_approved > 0">
                                    Appr: <span x-text="fmtAmt(row.amount_approved)"></span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span :class="statusBadgeClass(row.status)" x-text="row.status_label"></span>
                            </td>
                            <td class="px-4 py-3 text-gray-600" x-text="row.hr_name"></td>
                            <td class="px-4 py-3 text-gray-500" x-text="row.applied_at"></td>
                        </tr>
                    </template>
                    <template x-if="loanRows.data.length === 0">
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-400">
                                No loan records found for the selected filters.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex flex-col items-center justify-between gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row"
             x-show="loanRows.last_page > 1">
            <p class="text-xs text-gray-500">
                Page <span x-text="loanRows.current_page"></span> of <span x-text="loanRows.last_page"></span>
            </p>
            <div class="flex items-center gap-1">
                <button @click="loadPage(loanRows.current_page - 1)"
                        :disabled="loanRows.current_page <= 1"
                        :class="loanRows.current_page <= 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100'"
                        class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 transition-colors">
                    ← Prev
                </button>

                <template x-for="page in paginationPages" :key="page">
                    <button @click="page !== '…' && loadPage(page)"
                            :class="page === loanRows.current_page
                                ? 'bg-slate-900 text-white border-slate-900'
                                : page === '…' ? 'cursor-default text-gray-400 border-transparent' : 'border-gray-200 text-gray-600 hover:bg-gray-100'"
                            class="rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors"
                            x-text="page">
                    </button>
                </template>

                <button @click="loadPage(loanRows.current_page + 1)"
                        :disabled="loanRows.current_page >= loanRows.last_page"
                        :class="loanRows.current_page >= loanRows.last_page ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100'"
                        class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 transition-colors">
                    Next →
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function reportsApp() {
    return {
        // ── State ─────────────────────────────────────────────────────────────
        loading:     false,
        range:       @json($filters['range']),
        dateFrom:    @json($filters['date_from']),
        dateTo:      @json($filters['date_to']),
        status:      @json($filters['status']),
        loanType:    @json($filters['loan_type']),
        hrId:        @json($filters['hr_id']),
        search:      @json($filters['search']),
        periodLabel: @json($filters['label']),

        summary:  @json($summary),
        hrStats:  @json($hrStats),
        loanRows: @json($loanRows),
        monthly:  @json($monthly),

        charts: { monthly: null, status: null, hr: null },

        // ── Lifecycle ─────────────────────────────────────────────────────────
        init() {
            this.$nextTick(() => {
                this.initMonthlyChart();
                this.initStatusChart();
                this.initHrChart();
            });
        },

        // ── Computed ──────────────────────────────────────────────────────────
        get approvalRate() {
            if (!this.summary.loan_total) return 0;
            return Math.min(100, Math.round(
                (this.summary.loan_approved + this.summary.loan_disbursed) / this.summary.loan_total * 100 * 10
            ) / 10);
        },

        get filterParams() {
            const p = {
                range:     this.range,
                date_from: this.dateFrom,
                date_to:   this.dateTo,
                status:    this.status,
                loan_type: this.loanType,
                hr_id:     this.hrId,
                search:    this.search,
            };
            return p;
        },

        get paginationPages() {
            const cur  = this.loanRows.current_page;
            const last = this.loanRows.last_page;
            if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1);
            const pages = [1];
            if (cur > 3)       pages.push('…');
            for (let i = Math.max(2, cur - 1); i <= Math.min(last - 1, cur + 1); i++) pages.push(i);
            if (cur < last - 2) pages.push('…');
            pages.push(last);
            return pages;
        },

        // ── Actions ───────────────────────────────────────────────────────────
        async applyFilters(page = 1) {
            this.loading = true;
            try {
                const { data } = await axios.get('{{ route("admin.reports.ajax") }}', {
                    params: { ...this.filterParams, page }
                });
                this.summary     = data.summary;
                this.hrStats     = data.hrStats;
                this.loanRows    = data.loanRows;
                this.periodLabel = data.periodLabel;
                this.updateStatusChart();
                this.updateHrChart();
            } catch (e) {
                console.error('Report filter error:', e);
            } finally {
                this.loading = false;
            }
        },

        loadPage(page) {
            if (page < 1 || page > this.loanRows.last_page) return;
            this.applyFilters(page);
        },

        resetFilters() {
            this.range    = 'this_month';
            this.dateFrom = '';
            this.dateTo   = '';
            this.status   = '';
            this.loanType = '';
            this.hrId     = '';
            this.search   = '';
            this.applyFilters();
        },

        buildExportUrl(type) {
            const params = new URLSearchParams({ ...this.filterParams, export_type: type });
            return '{{ route("admin.reports.export") }}?' + params.toString();
        },

        buildPrintUrl() {
            const params = new URLSearchParams(this.filterParams);
            return '{{ route("admin.reports.print") }}?' + params.toString();
        },

        // ── Format helpers ────────────────────────────────────────────────────
        fmt(n) {
            return new Intl.NumberFormat('en-IN').format(Math.round(n || 0));
        },

        fmtAmt(n) {
            return '₹' + new Intl.NumberFormat('en-IN', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(Math.round(n || 0));
        },

        statusBadgeClass(status) {
            const map = {
                pending:      'badge-pending',
                under_review: 'badge-review',
                approved:     'badge-approved',
                rejected:     'badge-rejected',
                disbursed:    'badge-disbursed',
            };
            return 'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ' + (map[status] || 'bg-gray-100 text-gray-700');
        },

        // ── Chart init ────────────────────────────────────────────────────────
        initMonthlyChart() {
            const ctx = document.getElementById('monthlyChart');
            if (!ctx || !window.Chart) return;
            this.charts.monthly = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: this.monthly.labels,
                    datasets: [
                        {
                            label: 'Applied',
                            data: this.monthly.applied,
                            backgroundColor: 'rgb(15,23,42)',
                            borderRadius: 4,
                            borderSkipped: false,
                        },
                        {
                            label: 'Approved',
                            data: this.monthly.approved,
                            backgroundColor: 'rgb(22,163,74)',
                            borderRadius: 4,
                            borderSkipped: false,
                        },
                        {
                            label: 'Disbursed',
                            data: this.monthly.disbursed,
                            backgroundColor: 'rgb(147,51,234)',
                            borderRadius: 4,
                            borderSkipped: false,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { mode: 'index', intersect: false },
                    },
                    scales: {
                        x: { grid: { display: false }, border: { display: false } },
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, color: '#9ca3af' },
                            grid: { color: '#f3f4f6' },
                            border: { display: false },
                        },
                    },
                }
            });
        },

        initStatusChart() {
            const ctx = document.getElementById('statusChart');
            if (!ctx || !window.Chart) return;
            this.charts.status = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: this.summary.pie_labels,
                    datasets: [{
                        data: this.summary.pie_data,
                        backgroundColor: [
                            'rgb(217,119,6)',
                            'rgb(37,99,235)',
                            'rgb(22,163,74)',
                            'rgb(220,38,38)',
                            'rgb(147,51,234)',
                        ],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 14, usePointStyle: true, pointStyleWidth: 8, font: { size: 11 } }
                        },
                    },
                }
            });
        },

        initHrChart() {
            const ctx = document.getElementById('hrChart');
            if (!ctx || !window.Chart) return;
            this.charts.hr = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: this.hrStats.map(h => h.name),
                    datasets: [
                        { label: 'Approved', data: this.hrStats.map(h => h.approved), backgroundColor: 'rgb(22,163,74)',   borderRadius: 3 },
                        { label: 'Rejected', data: this.hrStats.map(h => h.rejected), backgroundColor: 'rgb(220,38,38)',   borderRadius: 3 },
                        { label: 'Pending',  data: this.hrStats.map(h => h.pending),  backgroundColor: 'rgb(217,119,6)',   borderRadius: 3 },
                        { label: 'Disbursed',data: this.hrStats.map(h => h.disbursed),backgroundColor: 'rgb(147,51,234)',  borderRadius: 3 },
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 10, usePointStyle: true, pointStyleWidth: 8, font: { size: 11 } } },
                        tooltip: { mode: 'index', intersect: false },
                    },
                    scales: {
                        x: { beginAtZero: true, stacked: true, ticks: { precision: 0, color: '#9ca3af' }, grid: { color: '#f3f4f6' }, border: { display: false } },
                        y: { stacked: true, grid: { display: false }, border: { display: false }, ticks: { color: '#6b7280' } },
                    },
                }
            });
        },

        // ── Chart update ──────────────────────────────────────────────────────
        updateStatusChart() {
            if (!this.charts.status) return;
            this.charts.status.data.datasets[0].data = this.summary.pie_data;
            this.charts.status.update();
        },

        updateHrChart() {
            if (!this.charts.hr) return;
            this.charts.hr.data.labels                = this.hrStats.map(h => h.name);
            this.charts.hr.data.datasets[0].data      = this.hrStats.map(h => h.approved);
            this.charts.hr.data.datasets[1].data      = this.hrStats.map(h => h.rejected);
            this.charts.hr.data.datasets[2].data      = this.hrStats.map(h => h.pending);
            this.charts.hr.data.datasets[3].data      = this.hrStats.map(h => h.disbursed);
            this.charts.hr.update();
        },
    };
}
</script>
@endpush
