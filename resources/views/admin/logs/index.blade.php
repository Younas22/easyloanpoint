@extends('layouts.app')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')

@section('content')

{{-- ── Stats Row ─────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-6">

    @php
    $statCards = [
        ['label' => 'Total Logs',    'value' => number_format($stats['total']),  'icon' => 'list',  'color' => 'blue'],
        ['label' => 'Today',         'value' => number_format($stats['today']),  'icon' => 'clock', 'color' => 'green'],
        ['label' => 'This Week',     'value' => number_format($stats['week']),   'icon' => 'cal',   'color' => 'indigo'],
        ['label' => 'Unique Users',  'value' => number_format($stats['users']),  'icon' => 'user',  'color' => 'slate'],
    ];
    @endphp

    @foreach($statCards as $card)
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ $card['value'] }}</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg
                @if($card['color']==='blue')   bg-blue-100 text-blue-600
                @elseif($card['color']==='green')  bg-green-100 text-green-600
                @elseif($card['color']==='indigo') bg-indigo-100 text-indigo-600
                @else bg-slate-100 text-slate-600 @endif">
                @if($card['icon']==='list')
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                @elseif($card['icon']==='clock')
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @elseif($card['icon']==='cal')
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                @else
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Filter Bar ────────────────────────────────────────────────────────── --}}
<div class="mb-5 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    <form id="filter-form" class="flex flex-wrap gap-3 items-end">

        {{-- Search --}}
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-slate-600 mb-1">Search</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                </span>
                <input type="text" name="search" id="search-input"
                       value="{{ $filters['search'] }}"
                       placeholder="User, description, IP..."
                       class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
        </div>

        {{-- Role --}}
        <div class="min-w-36">
            <label class="block text-xs font-medium text-slate-600 mb-1">Role</label>
            <select name="role" class="w-full rounded-lg border border-slate-300 bg-white py-2 px-3 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">All Roles</option>
                <option value="admin"    {{ $filters['role']==='admin'    ? 'selected' : '' }}>Admin</option>
                <option value="hr"       {{ $filters['role']==='hr'       ? 'selected' : '' }}>HR</option>
                <option value="customer" {{ $filters['role']==='customer' ? 'selected' : '' }}>Customer</option>
            </select>
        </div>

        {{-- Action --}}
        <div class="min-w-44">
            <label class="block text-xs font-medium text-slate-600 mb-1">Activity Type</label>
            <select name="action" class="w-full rounded-lg border border-slate-300 bg-white py-2 px-3 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">All Activities</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}" {{ $filters['action']===$act ? 'selected' : '' }}>
                        {{ ucwords(str_replace('_', ' ', $act)) }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Date From --}}
        <div class="min-w-36">
            <label class="block text-xs font-medium text-slate-600 mb-1">From</label>
            <input type="date" name="date_from" value="{{ $filters['date_from'] }}"
                   class="w-full rounded-lg border border-slate-300 bg-white py-2 px-3 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>

        {{-- Date To --}}
        <div class="min-w-36">
            <label class="block text-xs font-medium text-slate-600 mb-1">To</label>
            <input type="date" name="date_to" value="{{ $filters['date_to'] }}"
                   class="w-full rounded-lg border border-slate-300 bg-white py-2 px-3 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>

        {{-- Buttons --}}
        <div class="flex gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                Filter
            </button>
            <a href="{{ route('admin.logs.index') }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                Clear
            </a>
        </div>

    </form>
</div>

{{-- ── Log Table (AJAX Target) ──────────────────────────────────────────── --}}
<div id="logs-container">
    @include('admin.logs._table', ['logs' => $logs, 'filters' => $filters])
</div>

@endsection

@push('scripts')
<script>
(function () {
    const form      = document.getElementById('filter-form');
    const container = document.getElementById('logs-container');
    const ajaxUrl   = '{{ route('admin.logs.ajax') }}';

    function fetchLogs(params) {
        container.style.opacity = '0.5';
        container.style.pointerEvents = 'none';

        fetch(ajaxUrl + '?' + params, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            container.innerHTML = data.html;
            container.style.opacity = '1';
            container.style.pointerEvents = 'auto';
            bindPagination();
        });
    }

    function bindPagination() {
        container.querySelectorAll('a[data-ajax]').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const url    = new URL(this.href);
                const params = new URLSearchParams(new FormData(form));
                url.searchParams.forEach((v, k) => params.set(k, v));
                history.pushState(null, '', '?' + params.toString());
                fetchLogs(params.toString());
            });
        });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const params = new URLSearchParams(new FormData(form));
        history.pushState(null, '', '?' + params.toString());
        fetchLogs(params.toString());
    });

    // Debounce search input
    let timer;
    document.getElementById('search-input').addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(() => form.dispatchEvent(new Event('submit')), 400);
    });

    // Auto-submit on select change
    form.querySelectorAll('select, input[type=date]').forEach(el => {
        el.addEventListener('change', () => form.dispatchEvent(new Event('submit')));
    });

    bindPagination();
})();
</script>
@endpush
