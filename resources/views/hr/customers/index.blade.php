@extends('layouts.app')

@section('title', 'My Customers')
@section('page-title', 'My Customers')

@section('page-header')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">My Customers</h2>
            <p class="mt-0.5 text-sm text-gray-500">Customers assigned to you — manage, track and update loan status.</p>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            {{ $stats['total'] }} Assigned
        </span>
    </div>
@endsection

@section('content')

{{-- ── Stats ─────────────────────────────────────────────────────────────── --}}
<div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
    @php
        $statCards = [
            ['label' => 'Total Assigned',  'value' => $stats['total'],      'color' => 'blue',   'icon' => 'users'],
            ['label' => 'Active',          'value' => $stats['active'],     'color' => 'green',  'icon' => 'check'],
            ['label' => 'With Loans',      'value' => $stats['with_loans'], 'color' => 'indigo', 'icon' => 'document'],
            ['label' => 'Pending Review',  'value' => $stats['pending'],    'color' => 'amber',  'icon' => 'clock'],
        ];
    @endphp
    @foreach($statCards as $card)
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500">{{ $card['label'] }}</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg
                    @if($card['color'] === 'blue') bg-blue-100 text-blue-700
                    @elseif($card['color'] === 'green') bg-green-100 text-green-700
                    @elseif($card['color'] === 'indigo') bg-indigo-100 text-indigo-700
                    @else bg-amber-100 text-amber-700 @endif">
                    @if($card['icon'] === 'users')
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    @elseif($card['icon'] === 'check')
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($card['icon'] === 'document')
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @else
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- ── Search & Filters ──────────────────────────────────────────────────── --}}
<div class="mb-4 rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
        {{-- Search --}}
        <div class="flex-1">
            <label class="mb-1 block text-xs font-medium text-gray-600">Search Customer</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input id="searchInput"
                       type="text"
                       value="{{ request('search') }}"
                       placeholder="Name, mobile or email…"
                       class="w-full rounded-lg border border-gray-200 bg-gray-50 py-2 pl-9 pr-3 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500"/>
            </div>
        </div>

        {{-- Customer Status Filter --}}
        <div class="sm:w-44">
            <label class="mb-1 block text-xs font-medium text-gray-600">Customer Status</label>
            <select id="statusFilter" class="w-full rounded-lg border border-gray-200 bg-gray-50 py-2 px-3 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="active"      {{ request('status') === 'active'      ? 'selected' : '' }}>Active</option>
                <option value="inactive"    {{ request('status') === 'inactive'    ? 'selected' : '' }}>Inactive</option>
                <option value="blacklisted" {{ request('status') === 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
            </select>
        </div>

        {{-- Loan Status Filter --}}
        <div class="sm:w-44">
            <label class="mb-1 block text-xs font-medium text-gray-600">Loan Status</label>
            <select id="loanStatusFilter" class="w-full rounded-lg border border-gray-200 bg-gray-50 py-2 px-3 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">All Loans</option>
                <option value="pending"      {{ request('loan_status') === 'pending'      ? 'selected' : '' }}>Pending</option>
                <option value="under_review" {{ request('loan_status') === 'under_review' ? 'selected' : '' }}>Under Review</option>
                <option value="approved"     {{ request('loan_status') === 'approved'     ? 'selected' : '' }}>Approved</option>
                <option value="rejected"     {{ request('loan_status') === 'rejected'     ? 'selected' : '' }}>Rejected</option>
                <option value="disbursed"    {{ request('loan_status') === 'disbursed'    ? 'selected' : '' }}>Disbursed</option>
            </select>
        </div>

        {{-- Clear --}}
        <button id="clearFilters"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 transition hover:border-gray-300 hover:bg-gray-50">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Clear
        </button>
    </div>
</div>

{{-- ── Table Container ───────────────────────────────────────────────────── --}}
<div class="rounded-xl border border-gray-100 bg-white shadow-sm">

    {{-- Loading overlay --}}
    <div id="tableLoader" class="hidden">
        <div class="flex items-center justify-center py-12">
            <svg class="h-6 w-6 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span class="ml-2 text-sm text-gray-500">Searching…</span>
        </div>
    </div>

    <div id="tableContent">
        @include('hr.customers._table', ['customers' => $customers])
    </div>

    <div id="paginationWrapper" class="border-t border-gray-100 px-4 py-3">
        {{ $customers->links() }}
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const searchInput      = document.getElementById('searchInput');
    const statusFilter     = document.getElementById('statusFilter');
    const loanStatusFilter = document.getElementById('loanStatusFilter');
    const clearBtn         = document.getElementById('clearFilters');
    const tableContent     = document.getElementById('tableContent');
    const tableLoader      = document.getElementById('tableLoader');
    const paginationWrapper = document.getElementById('paginationWrapper');

    let debounceTimer;

    function fetchCustomers(page) {
        const params = new URLSearchParams({
            search:      searchInput.value,
            status:      statusFilter.value,
            loan_status: loanStatusFilter.value,
        });
        if (page) params.set('page', page);

        tableLoader.classList.remove('hidden');
        tableContent.classList.add('opacity-40', 'pointer-events-none');

        fetch('{{ route('hr.customers.index') }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            tableContent.innerHTML = data.html;
            paginationWrapper.innerHTML = data.pagination;
            bindPaginationLinks();
        })
        .finally(() => {
            tableLoader.classList.add('hidden');
            tableContent.classList.remove('opacity-40', 'pointer-events-none');
        });
    }

    function bindPaginationLinks() {
        paginationWrapper.querySelectorAll('a[href]').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const url   = new URL(link.href);
                const page  = url.searchParams.get('page');
                fetchCustomers(page);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    }

    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => fetchCustomers(), 380);
    });

    statusFilter.addEventListener('change',     () => fetchCustomers());
    loanStatusFilter.addEventListener('change', () => fetchCustomers());

    clearBtn.addEventListener('click', () => {
        searchInput.value      = '';
        statusFilter.value     = '';
        loanStatusFilter.value = '';
        fetchCustomers();
    });

    bindPaginationLinks();
})();
</script>
@endpush
