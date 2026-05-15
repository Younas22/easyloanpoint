@extends('layouts.app')

@section('title', 'Customer Assignments')

@section('page-header')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Customer Assignments</h1>
        <p class="mt-0.5 text-sm text-gray-500">Assign and manage HR executives for customers</p>
    </div>
    <div class="flex items-center gap-2 text-xs text-gray-500">
        <span class="flex items-center gap-1">
            <span class="inline-block h-2.5 w-2.5 rounded-full bg-green-400"></span> 1–5 customers
        </span>
        <span class="flex items-center gap-1">
            <span class="inline-block h-2.5 w-2.5 rounded-full bg-yellow-400"></span> 6–10 customers
        </span>
        <span class="flex items-center gap-1">
            <span class="inline-block h-2.5 w-2.5 rounded-full bg-red-400"></span> 11+ customers
        </span>
    </div>
</div>
@endsection

@section('content')

{{-- ── HR Workload Cards ─────────────────────────────────────────────────────── --}}
<div class="mb-6">
    <h2 class="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-400">HR Workload Overview</h2>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
        @forelse($hrUsers as $hr)
            @php
                $count  = $hr->active_customers_count;
                $ring   = $count === 0 ? 'border-gray-200'
                        : ($count <= 5  ? 'border-green-300'
                        : ($count <= 10 ? 'border-yellow-300'
                        :                 'border-red-300'));
                $badge  = $count === 0 ? 'bg-gray-100 text-gray-500'
                        : ($count <= 5  ? 'bg-green-100 text-green-700'
                        : ($count <= 10 ? 'bg-yellow-100 text-yellow-700'
                        :                 'bg-red-100 text-red-700'));
            @endphp
            <button type="button"
                    onclick="filterByHr({{ $hr->id }})"
                    class="group rounded-xl border-2 {{ $ring }} bg-white p-4 text-center shadow-sm transition-all hover:shadow-md focus:outline-none {{ request('hr_id') == $hr->id ? 'ring-2 ring-blue-500 ring-offset-2' : '' }}">
                <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 text-sm font-bold text-white group-hover:bg-slate-700">
                    {{ strtoupper(substr($hr->name, 0, 2)) }}
                </div>
                <p class="truncate text-sm font-semibold text-gray-800">{{ $hr->name }}</p>
                <span class="mt-1.5 inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $badge }}">
                    {{ $count }} {{ Str::plural('customer', $count) }}
                </span>
            </button>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white px-6 py-8 text-center text-sm text-gray-400">
                No active HR executives found. Add HR staff from HR Management.
            </div>
        @endforelse
    </div>
</div>

{{-- ── Filter Bar ───────────────────────────────────────────────────────────── --}}
<div class="mb-4 rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
    <form method="GET" action="{{ route('admin.assignments.index') }}"
          class="flex flex-wrap items-center gap-3">
        <div class="flex-1" style="min-width:200px">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name, phone or email..."
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm transition focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <div style="min-width:160px">
            <select name="filter"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">All Customers</option>
                <option value="assigned"   {{ request('filter') === 'assigned'   ? 'selected' : '' }}>Assigned Only</option>
                <option value="unassigned" {{ request('filter') === 'unassigned' ? 'selected' : '' }}>Unassigned Only</option>
            </select>
        </div>
        <div style="min-width:160px">
            <select name="hr_id"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">All HR Executives</option>
                @foreach($hrUsers as $hr)
                    <option value="{{ $hr->id }}" {{ request('hr_id') == $hr->id ? 'selected' : '' }}>
                        {{ $hr->name }} ({{ $hr->active_customers_count }})
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            Filter
        </button>
        @if(request()->hasAny(['search', 'filter', 'hr_id']))
            <a href="{{ route('admin.assignments.index') }}"
               class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                Clear
            </a>
        @endif
        <span class="ml-auto text-xs text-gray-400">{{ $customers->total() }} customer(s)</span>
    </form>
</div>

{{-- ── Customer Table ───────────────────────────────────────────────────────── --}}
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Contact</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Active Loans</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Assigned HR</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Since</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($customers as $customer)
                    @php $ca = $customer->currentAssignment; @endphp
                    <tr class="transition-colors hover:bg-gray-50" id="row-{{ $customer->id }}">

                        {{-- Customer --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700">
                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-gray-900">{{ $customer->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $customer->masked_aadhaar }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Contact --}}
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-800">{{ $customer->phone }}</p>
                            <p class="text-xs text-gray-400 truncate max-w-[140px]">{{ $customer->email ?? '—' }}</p>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium
                                {{ $customer->status === 'active'      ? 'bg-green-100 text-green-700'  : '' }}
                                {{ $customer->status === 'inactive'    ? 'bg-gray-100  text-gray-600'   : '' }}
                                {{ $customer->status === 'blacklisted' ? 'bg-red-100   text-red-700'    : '' }}">
                                {{ $customer->status_label }}
                            </span>
                        </td>

                        {{-- Active Loans --}}
                        <td class="px-4 py-3">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full
                                         {{ $customer->active_loans_count > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-400' }}
                                         text-xs font-bold">
                                {{ $customer->active_loans_count }}
                            </span>
                        </td>

                        {{-- Assigned HR --}}
                        <td class="px-4 py-3">
                            @if($ca?->hr)
                                <div class="flex items-center gap-2">
                                    <div class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-slate-800 text-xs font-bold text-white">
                                        {{ strtoupper(substr($ca->hr->name, 0, 2)) }}
                                    </div>
                                    <span class="text-sm text-gray-800">{{ $ca->hr->name }}</span>
                                </div>
                            @else
                                <span class="inline-flex rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-600">
                                    Unassigned
                                </span>
                            @endif
                        </td>

                        {{-- Since --}}
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $ca ? $ca->created_at->format('d M Y') : '—' }}
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($ca?->hr)
                                    <button type="button"
                                            onclick="openReassignModal({{ $customer->id }}, @js($customer->name), @js($ca->hr->name))"
                                            class="rounded-lg border border-yellow-200 bg-yellow-50 px-2.5 py-1.5 text-xs font-medium text-yellow-700 transition-colors hover:bg-yellow-100">
                                        Reassign
                                    </button>
                                @else
                                    <button type="button"
                                            onclick="openAssignModal({{ $customer->id }}, @js($customer->name))"
                                            class="rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-xs font-medium text-blue-700 transition-colors hover:bg-blue-100">
                                        Assign
                                    </button>
                                @endif
                                <button type="button"
                                        onclick="openHistoryModal({{ $customer->id }}, @js($customer->name))"
                                        class="rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1.5 text-xs font-medium text-gray-600 transition-colors hover:bg-gray-100">
                                    History
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">
                            No customers found matching your filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($customers->hasPages())
        <div class="border-t border-gray-100 bg-gray-50 px-4 py-3">
            {{ $customers->links() }}
        </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- ASSIGN MODAL                                                               --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
<div id="assign-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl">

        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <h3 class="text-base font-bold text-gray-900">Assign HR Executive</h3>
            <button type="button" onclick="closeModal('assign-modal')"
                    class="text-gray-400 transition hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-4 px-6 py-5">
            <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3">
                <p class="text-xs font-medium text-blue-500">Assigning Customer</p>
                <p id="assign-customer-name" class="mt-0.5 text-sm font-bold text-blue-900"></p>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    HR Executive <span class="text-red-500">*</span>
                </label>
                <select id="assign-hr-select"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm transition focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">— Select HR Executive —</option>
                    @foreach($hrUsers as $hr)
                        <option value="{{ $hr->id }}">
                            {{ $hr->name }} &nbsp;·&nbsp; {{ $hr->active_customers_count }} {{ Str::plural('assigned', $hr->active_customers_count) }}
                        </option>
                    @endforeach
                </select>
                <p id="assign-hr-error" class="mt-1 hidden text-xs text-red-600"></p>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Notes <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <textarea id="assign-notes" rows="3"
                          placeholder="Add any notes about this assignment..."
                          class="w-full resize-none rounded-lg border border-gray-300 px-3 py-2 text-sm transition focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
            </div>
        </div>

        <div class="flex gap-3 border-t border-gray-200 px-6 py-4">
            <button type="button" onclick="closeModal('assign-modal')"
                    class="flex-1 rounded-lg border border-gray-300 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                Cancel
            </button>
            <button type="button" onclick="submitAssign()" id="assign-btn"
                    class="flex-1 rounded-lg bg-blue-600 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                Assign
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- REASSIGN MODAL                                                             --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
<div id="reassign-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl">

        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <h3 class="text-base font-bold text-gray-900">Reassign HR Executive</h3>
            <button type="button" onclick="closeModal('reassign-modal')"
                    class="text-gray-400 transition hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-4 px-6 py-5">
            <div class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3">
                <p class="text-xs font-medium text-yellow-500">Reassigning Customer</p>
                <p id="reassign-customer-name" class="mt-0.5 text-sm font-bold text-yellow-900"></p>
                <p class="mt-1 text-xs text-yellow-700">
                    Currently assigned to: <span id="reassign-current-hr" class="font-bold"></span>
                </p>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    New HR Executive <span class="text-red-500">*</span>
                </label>
                <select id="reassign-hr-select"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm transition focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">— Select HR Executive —</option>
                    @foreach($hrUsers as $hr)
                        <option value="{{ $hr->id }}">
                            {{ $hr->name }} &nbsp;·&nbsp; {{ $hr->active_customers_count }} {{ Str::plural('assigned', $hr->active_customers_count) }}
                        </option>
                    @endforeach
                </select>
                <p id="reassign-hr-error" class="mt-1 hidden text-xs text-red-600"></p>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Reason for Reassignment <span class="text-red-500">*</span>
                </label>
                <textarea id="reassign-notes" rows="3"
                          placeholder="Explain why you are reassigning this customer..."
                          class="w-full resize-none rounded-lg border border-gray-300 px-3 py-2 text-sm transition focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                <p id="reassign-notes-error" class="mt-1 hidden text-xs text-red-600"></p>
            </div>
        </div>

        <div class="flex gap-3 border-t border-gray-200 px-6 py-4">
            <button type="button" onclick="closeModal('reassign-modal')"
                    class="flex-1 rounded-lg border border-gray-300 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                Cancel
            </button>
            <button type="button" onclick="submitReassign()" id="reassign-btn"
                    class="flex-1 rounded-lg bg-yellow-500 py-2.5 text-sm font-medium text-white transition hover:bg-yellow-600 disabled:cursor-not-allowed disabled:opacity-60">
                Reassign
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- HISTORY MODAL                                                              --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
<div id="history-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
    <div class="flex max-h-[90vh] w-full max-w-lg flex-col rounded-2xl bg-white shadow-2xl">

        <div class="flex flex-shrink-0 items-center justify-between border-b border-gray-200 px-6 py-4">
            <div>
                <h3 class="text-base font-bold text-gray-900">Assignment History</h3>
                <p id="history-customer-label" class="text-xs text-gray-500"></p>
            </div>
            <button type="button" onclick="closeModal('history-modal')"
                    class="text-gray-400 transition hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div id="history-body" class="flex-1 overflow-y-auto px-6 py-5">
            <div class="flex items-center justify-center gap-2 py-10 text-sm text-gray-400">
                <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10"/>
                </svg>
                Loading history…
            </div>
        </div>
    </div>
</div>

{{-- ── Toast ────────────────────────────────────────────────────────────────── --}}
<div id="toast" class="pointer-events-none fixed bottom-6 right-6 z-50 hidden">
    <div id="toast-box"
         class="flex items-center gap-3 rounded-xl px-5 py-3.5 text-sm font-medium text-white shadow-xl">
        <span id="toast-icon" class="flex-shrink-0"></span>
        <span id="toast-msg"></span>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
    const STORE_URL  = '{{ route("admin.assignments.store") }}';
    const HIST_BASE  = '{{ url("admin/assignments") }}';

    let activeCustomerId = null;

    // ── Modal control ─────────────────────────────────────────────────────────
    function openModal(id) {
        const el = document.getElementById(id);
        el.classList.remove('hidden');
        el.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        const el = document.getElementById(id);
        el.classList.add('hidden');
        el.classList.remove('flex');
        document.body.style.overflow = '';
    }

    window.closeModal = closeModal;

    // Close on backdrop click
    ['assign-modal', 'reassign-modal', 'history-modal'].forEach(id => {
        document.getElementById(id).addEventListener('click', function (e) {
            if (e.target === this) closeModal(id);
        });
    });

    // ── Assign modal ──────────────────────────────────────────────────────────
    window.openAssignModal = function (customerId, customerName) {
        activeCustomerId = customerId;
        document.getElementById('assign-customer-name').textContent = customerName;
        document.getElementById('assign-hr-select').value = '';
        document.getElementById('assign-notes').value = '';
        clearError('assign-hr-error');
        openModal('assign-modal');
    };

    window.submitAssign = async function () {
        clearError('assign-hr-error');

        const hrId = document.getElementById('assign-hr-select').value;
        if (!hrId) { showError('assign-hr-error', 'Please select an HR executive.'); return; }

        await ajaxAssign('assign-btn', 'Assign…', {
            customer_id: activeCustomerId,
            hr_id: hrId,
            notes: document.getElementById('assign-notes').value,
        }, 'assign-modal');
    };

    // ── Reassign modal ────────────────────────────────────────────────────────
    window.openReassignModal = function (customerId, customerName, currentHrName) {
        activeCustomerId = customerId;
        document.getElementById('reassign-customer-name').textContent = customerName;
        document.getElementById('reassign-current-hr').textContent   = currentHrName;
        document.getElementById('reassign-hr-select').value = '';
        document.getElementById('reassign-notes').value = '';
        clearError('reassign-hr-error');
        clearError('reassign-notes-error');
        openModal('reassign-modal');
    };

    window.submitReassign = async function () {
        clearError('reassign-hr-error');
        clearError('reassign-notes-error');

        const hrId  = document.getElementById('reassign-hr-select').value;
        const notes = document.getElementById('reassign-notes').value.trim();
        let ok = true;

        if (!hrId)  { showError('reassign-hr-error',    'Please select an HR executive.'); ok = false; }
        if (!notes) { showError('reassign-notes-error', 'Reason for reassignment is required.'); ok = false; }
        if (!ok) return;

        await ajaxAssign('reassign-btn', 'Reassigning…', {
            customer_id: activeCustomerId,
            hr_id: hrId,
            notes: notes,
        }, 'reassign-modal');
    };

    // shared fetch
    async function ajaxAssign(btnId, loadingText, payload, modalId) {
        const btn = document.getElementById(btnId);
        const origText = btn.textContent;
        btn.disabled = true;
        btn.textContent = loadingText;

        try {
            const res  = await fetch(STORE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept':       'application/json',
                },
                body: JSON.stringify(payload),
            });
            const data = await res.json();

            if (data.status) {
                closeModal(modalId);
                toast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1300);
            } else if (data.errors) {
                const map = { hr_id: btnId.replace('-btn', '-hr-error'), notes: btnId.replace('-btn', '-notes-error') };
                Object.entries(data.errors).forEach(([k, v]) => {
                    const elId = map[k];
                    if (elId) showError(elId, v[0]);
                });
            } else {
                toast(data.message || 'Something went wrong.', 'error');
            }
        } catch (err) {
            toast('Network error. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = origText;
        }
    }

    // ── History modal ─────────────────────────────────────────────────────────
    window.openHistoryModal = async function (customerId, customerName) {
        document.getElementById('history-customer-label').textContent = customerName;
        document.getElementById('history-body').innerHTML = spinner();
        openModal('history-modal');

        try {
            const res  = await fetch(`${HIST_BASE}/${customerId}/history`, {
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            });
            const data = await res.json();

            if (data.status && data.data.history.length > 0) {
                renderTimeline(data.data.history);
            } else {
                document.getElementById('history-body').innerHTML =
                    '<p class="py-10 text-center text-sm text-gray-400">No assignment history found.</p>';
            }
        } catch {
            document.getElementById('history-body').innerHTML =
                '<p class="py-10 text-center text-sm text-red-500">Failed to load history.</p>';
        }
    };

    function renderTimeline(history) {
        const items = history.map((item, i) => {
            const isLast   = i === history.length - 1;
            const dotColor = item.is_active ? 'bg-green-600' : 'bg-gray-300';
            const badge    = item.is_active
                ? '<span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Current</span>'
                : '<span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500">Previous</span>';
            const noteHtml = item.notes
                ? `<p class="mt-1.5 rounded bg-gray-50 px-2 py-1 text-xs text-gray-600 italic">"${escHtml(item.notes)}"</p>`
                : '';

            return `
            <div class="flex gap-4">
                <div class="flex flex-col items-center flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full ${dotColor} text-xs font-bold text-white">
                        ${escHtml(item.hr_name.substring(0, 2).toUpperCase())}
                    </div>
                    ${!isLast ? '<div class="mt-1 w-px flex-1 bg-gray-200 min-h-[20px]"></div>' : ''}
                </div>
                <div class="flex-1 pb-5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">${escHtml(item.hr_name)}</p>
                            <p class="text-xs text-gray-400">By ${escHtml(item.assigned_by)} · ${escHtml(item.assigned_at)}</p>
                            ${noteHtml}
                        </div>
                        ${badge}
                    </div>
                </div>
            </div>`;
        }).join('');

        document.getElementById('history-body').innerHTML =
            `<div class="space-y-0">${items}</div>`;
    }

    // ── Filter by HR workload card ─────────────────────────────────────────────
    window.filterByHr = function (hrId) {
        const url = new URL(window.location.href);
        if (url.searchParams.get('hr_id') == hrId) {
            url.searchParams.delete('hr_id');
        } else {
            url.searchParams.set('hr_id', hrId);
        }
        window.location.href = url.toString();
    };

    // ── Helpers ───────────────────────────────────────────────────────────────
    function showError(id, msg) {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = msg;
        el.classList.remove('hidden');
    }

    function clearError(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = '';
        el.classList.add('hidden');
    }

    function spinner() {
        return `<div class="flex items-center justify-center gap-2 py-10 text-sm text-gray-400">
            <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10"/>
            </svg>Loading history…</div>`;
    }

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    let toastTimer = null;
    function toast(message, type) {
        const el  = document.getElementById('toast');
        const box = document.getElementById('toast-box');
        const ico = document.getElementById('toast-icon');
        const msg = document.getElementById('toast-msg');

        msg.textContent = message;

        if (type === 'success') {
            box.className = 'flex items-center gap-3 rounded-xl px-5 py-3.5 text-sm font-medium text-white shadow-xl bg-green-700';
            ico.innerHTML = '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
        } else {
            box.className = 'flex items-center gap-3 rounded-xl px-5 py-3.5 text-sm font-medium text-white shadow-xl bg-red-700';
            ico.innerHTML = '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
        }

        el.classList.remove('hidden');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => el.classList.add('hidden'), 3500);
    }
})();
</script>
@endpush
