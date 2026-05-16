@extends('layouts.app')

@section('title', 'Loan Applications')

@section('page-header')
    <div>
        <h1 class="text-xl font-bold text-gray-900">Loan Applications</h1>
        <p class="mt-0.5 text-sm text-gray-500">Loans assigned to you for review and processing.</p>
    </div>
@endsection

@section('content')

{{-- Flash Messages --}}
@if(session('success'))
    <div class="mb-5 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
        <svg class="h-4 w-4 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-5 flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <svg class="h-4 w-4 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{ session('error') }}
    </div>
@endif

{{-- Stats --}}
<div class="mb-5 grid grid-cols-3 gap-3 sm:grid-cols-6">
    @foreach([
        ['label' => 'Total',        'value' => $stats['total'],        'color' => 'text-slate-800',  'bg' => 'bg-slate-50',  'border' => 'border-slate-200', 'key' => ''],
        ['label' => 'Pending',      'value' => $stats['pending'],      'color' => 'text-yellow-700', 'bg' => 'bg-yellow-50', 'border' => 'border-yellow-200','key' => 'pending'],
        ['label' => 'Under Review', 'value' => $stats['under_review'], 'color' => 'text-blue-700',   'bg' => 'bg-blue-50',   'border' => 'border-blue-200',  'key' => 'under_review'],
        ['label' => 'Approved',     'value' => $stats['approved'],     'color' => 'text-green-700',  'bg' => 'bg-green-50',  'border' => 'border-green-200', 'key' => 'approved'],
        ['label' => 'Rejected',     'value' => $stats['rejected'],     'color' => 'text-red-700',    'bg' => 'bg-red-50',    'border' => 'border-red-200',   'key' => 'rejected'],
        ['label' => 'Disbursed',    'value' => $stats['disbursed'],    'color' => 'text-purple-700', 'bg' => 'bg-purple-50', 'border' => 'border-purple-200','key' => 'disbursed'],
    ] as $stat)
        @php $isActive = request('status') === $stat['key'] && ($stat['key'] !== '' || !request('status')); @endphp
        <a href="{{ route('hr.loans.index', array_merge(request()->except(['status','page']), $stat['key'] ? ['status' => $stat['key']] : [])) }}"
           class="rounded-xl border px-4 py-3 shadow-sm transition-all hover:shadow
                  {{ request('status') === $stat['key'] || ($stat['key'] === '' && !request('status'))
                      ? $stat['bg'] . ' ' . $stat['border'] . ' ring-2 ring-offset-1 ring-current'
                      : 'border-gray-200 bg-white' }}">
            <p class="text-xs font-medium text-gray-500">{{ $stat['label'] }}</p>
            <p class="mt-1 text-xl font-bold {{ $stat['color'] }}">{{ number_format($stat['value']) }}</p>
        </a>
    @endforeach
</div>

{{-- Filters --}}
<div class="mb-5 rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-3">
        <p class="text-sm font-semibold text-gray-700">Filter & Search</p>
        @if(request()->hasAny(['search','status','loan_type','date_from','date_to']))
            <a href="{{ route('hr.loans.index') }}"
               class="flex items-center gap-1.5 text-xs font-medium text-red-500 hover:text-red-700">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Clear all filters
            </a>
        @endif
    </div>
    <form method="GET" action="{{ route('hr.loans.index') }}" class="p-5">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">

            {{-- Search --}}
            <div class="lg:col-span-2">
                <label class="mb-1.5 block text-xs font-medium text-gray-600">Search</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Loan number, customer name or phone…"
                           class="w-full rounded-lg border border-gray-300 py-2.5 pl-9 pr-4 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-600">Status</label>
                <select name="status"
                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Status</option>
                    @foreach(['pending' => 'Pending', 'under_review' => 'Under Review', 'approved' => 'Approved', 'rejected' => 'Rejected', 'disbursed' => 'Disbursed'] as $val => $label)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Loan Type --}}
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-600">Loan Type</label>
                <select name="loan_type"
                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach(['personal' => 'Personal', 'home' => 'Home', 'business' => 'Business', 'vehicle' => 'Vehicle', 'education' => 'Education'] as $val => $label)
                        <option value="{{ $val }}" {{ request('loan_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Submit --}}
            <div class="flex items-end">
                <button type="submit"
                        class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700">
                    Apply Filters
                </button>
            </div>
        </div>

        {{-- Date Range --}}
        <div class="mt-4 grid grid-cols-1 gap-4 border-t border-gray-100 pt-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-600">Applied From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-600">Applied To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
        </div>
    </form>
</div>

{{-- Active filter badges --}}
@if(request()->hasAny(['search','status','loan_type','date_from','date_to']))
    <div class="mb-4 flex flex-wrap items-center gap-2">
        <span class="text-xs text-gray-500">Active filters:</span>
        @if(request('search'))
            <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-800">
                Search: "{{ request('search') }}"
                <a href="{{ route('hr.loans.index', request()->except(['search','page'])) }}" class="ml-0.5 hover:text-blue-600">×</a>
            </span>
        @endif
        @if(request('status'))
            @php $statusLabels = ['pending'=>'Pending','under_review'=>'Under Review','approved'=>'Approved','rejected'=>'Rejected','disbursed'=>'Disbursed']; @endphp
            <span class="inline-flex items-center gap-1 rounded-full bg-purple-100 px-2.5 py-1 text-xs font-medium text-purple-800">
                Status: {{ $statusLabels[request('status')] ?? request('status') }}
                <a href="{{ route('hr.loans.index', request()->except(['status','page'])) }}" class="ml-0.5 hover:text-purple-600">×</a>
            </span>
        @endif
        @if(request('loan_type'))
            <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-2.5 py-1 text-xs font-medium text-orange-800">
                Type: {{ ucfirst(request('loan_type')) }}
                <a href="{{ route('hr.loans.index', request()->except(['loan_type','page'])) }}" class="ml-0.5 hover:text-orange-600">×</a>
            </span>
        @endif
        @if(request('date_from') || request('date_to'))
            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-800">
                Date: {{ request('date_from') ?: '…' }} → {{ request('date_to') ?: '…' }}
                <a href="{{ route('hr.loans.index', request()->except(['date_from','date_to','page'])) }}" class="ml-0.5 hover:text-gray-600">×</a>
            </span>
        @endif
    </div>
@endif

{{-- Table --}}
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-3">
        <p class="text-sm font-semibold text-gray-700">
            Applications
            <span class="ml-1.5 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                {{ $loans->total() }}
            </span>
        </p>
        <p class="text-xs text-gray-400">
            Showing {{ $loans->firstItem() ?? 0 }}–{{ $loans->lastItem() ?? 0 }} of {{ $loans->total() }}
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">#</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Loan No.</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Customer</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Applied</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Docs</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($loans as $loan)
                    @php
                        $statusConfig = [
                            'pending'      => ['class' => 'bg-yellow-100 text-yellow-800', 'label' => 'Pending'],
                            'under_review' => ['class' => 'bg-blue-100 text-blue-800',   'label' => 'Under Review'],
                            'approved'     => ['class' => 'bg-green-100 text-green-800', 'label' => 'Approved'],
                            'rejected'     => ['class' => 'bg-red-100 text-red-800',     'label' => 'Rejected'],
                            'disbursed'    => ['class' => 'bg-purple-100 text-purple-800','label' => 'Disbursed'],
                        ];
                        $sc  = $statusConfig[$loan->status]['class']  ?? 'bg-gray-100 text-gray-800';
                        $sl  = $statusConfig[$loan->status]['label']  ?? ucfirst($loan->status);
                        $pendingDocs = $loan->pending_docs_count ?? 0;
                        $totalDocs   = $loan->documents_count ?? 0;
                    @endphp
                    <tr class="transition-colors hover:bg-gray-50/70">
                        <td class="whitespace-nowrap px-5 py-4 text-xs text-gray-400">
                            {{ ($loans->currentPage() - 1) * $loans->perPage() + $loop->iteration }}
                        </td>
                        <td class="whitespace-nowrap px-5 py-4">
                            <a href="{{ route('hr.loans.show', $loan) }}"
                               class="font-mono text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                {{ $loan->loan_number }}
                            </a>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-900">{{ $loan->customer->name }}</p>
                            <p class="text-xs text-gray-400">{{ $loan->customer->phone }}</p>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-gray-600 capitalize">
                            {{ $loan->loan_type_label }}
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            <p class="font-semibold text-gray-900">₹{{ number_format($loan->amount_requested) }}</p>
                            @if($loan->amount_approved)
                                <p class="text-xs text-green-600">₹{{ number_format($loan->amount_approved) }} approved</p>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-center">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $sc }}">
                                {{ $sl }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-xs text-gray-500">
                            {{ $loan->applied_at?->format('d M Y') ?? '—' }}
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-center">
                            <span title="{{ $pendingDocs }} pending"
                                  class="inline-flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold
                                         {{ $pendingDocs > 0 ? 'bg-yellow-400 text-white' : ($totalDocs > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500') }}">
                                {{ $totalDocs }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            <a href="{{ route('hr.loans.show', $loan) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-600 transition-colors hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Review
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-5 py-20 text-center">
                            <div class="mx-auto max-w-xs">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100">
                                    <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="mt-3 text-sm font-semibold text-gray-700">No loans found</p>
                                <p class="mt-1 text-xs text-gray-400">
                                    @if(request()->hasAny(['search','status','loan_type','date_from','date_to']))
                                        No loans match your current filters. <a href="{{ route('hr.loans.index') }}" class="text-blue-600 hover:underline">Clear filters</a>
                                    @else
                                        Contact your admin to get loan assignments.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($loans->hasPages())
        <div class="border-t border-gray-100 bg-gray-50 px-5 py-3">
            {{ $loans->links() }}
        </div>
    @endif
</div>

@endsection
