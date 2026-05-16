@extends('layouts.app')

@section('title', 'Customer Management')
@section('page-title', 'Customer Management')

@section('page-header')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Customer Management</h1>
            <p class="mt-0.5 text-sm text-gray-500">Manage all customers, KYC details and loan history.</p>
        </div>
        <a href="{{ route('admin.customers.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add Customer
        </a>
    </div>
@endsection

@section('content')

    {{-- Filters --}}
    <div class="mb-5 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.customers.index') }}"
              class="flex flex-col gap-3 lg:flex-row lg:items-end">

            {{-- Search --}}
            <div class="flex-1">
                <label class="mb-1 block text-xs font-medium text-gray-600">Search</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Name, email, phone, Aadhaar or PAN…"
                           class="w-full rounded-lg border border-gray-300 py-2.5 pl-9 pr-4 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Status --}}
            <div class="sm:w-40">
                <label class="mb-1 block text-xs font-medium text-gray-600">Status</label>
                <select name="status"
                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="active"      {{ request('status') === 'active'      ? 'selected' : '' }}>Active</option>
                    <option value="inactive"    {{ request('status') === 'inactive'    ? 'selected' : '' }}>Inactive</option>
                    <option value="blacklisted" {{ request('status') === 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
                </select>
            </div>

            {{-- Employment --}}
            <div class="sm:w-44">
                <label class="mb-1 block text-xs font-medium text-gray-600">Employment</label>
                <select name="employment_type"
                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Types</option>
                    <option value="salaried"      {{ request('employment_type') === 'salaried'      ? 'selected' : '' }}>Salaried</option>
                    <option value="self_employed" {{ request('employment_type') === 'self_employed' ? 'selected' : '' }}>Self Employed</option>
                    <option value="business"      {{ request('employment_type') === 'business'      ? 'selected' : '' }}>Business</option>
                    <option value="unemployed"    {{ request('employment_type') === 'unemployed'    ? 'selected' : '' }}>Unemployed</option>
                </select>
            </div>

            {{-- State --}}
            <div class="sm:w-44">
                <label class="mb-1 block text-xs font-medium text-gray-600">State</label>
                <select name="state"
                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All States</option>
                    @foreach($states as $state)
                        <option value="{{ $state }}" {{ request('state') === $state ? 'selected' : '' }}>
                            {{ $state }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'employment_type', 'state']))
                    <a href="{{ route('admin.customers.index') }}"
                       class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Stats bar --}}
    <div class="mb-5 grid grid-cols-2 gap-4 sm:grid-cols-4">
        @php
            $total       = $customers->total();
            $activeCount = \App\Models\Customer::where('status', 'active')->count();
            $blacklisted = \App\Models\Customer::where('status', 'blacklisted')->count();
            $withLoans   = \App\Models\Customer::has('loans')->count();
        @endphp
        @foreach([
            ['label' => 'Total Customers', 'value' => $total,       'color' => 'text-blue-600'],
            ['label' => 'Active',          'value' => $activeCount, 'color' => 'text-green-600'],
            ['label' => 'Blacklisted',     'value' => $blacklisted, 'color' => 'text-red-600'],
            ['label' => 'With Loans',      'value' => $withLoans,   'color' => 'text-slate-700'],
        ] as $stat)
            <div class="rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
                <p class="text-xs font-medium text-gray-500">{{ $stat['label'] }}</p>
                <p class="mt-1 text-2xl font-bold {{ $stat['color'] }}">{{ number_format($stat['value']) }}</p>
            </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">#</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Customer</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Mobile</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Location</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Employment</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Assigned HR</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Loans</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($customers as $customer)
                        <tr class="transition-colors hover:bg-gray-50">
                            <td class="whitespace-nowrap px-5 py-4 text-gray-400 text-xs">
                                {{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}
                            </td>

                            {{-- Customer --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.customers.show', $customer) }}"
                                           class="font-semibold text-gray-900 hover:text-blue-600 hover:underline">
                                            {{ $customer->name }}
                                        </a>
                                        <p class="text-xs text-gray-400">{{ $customer->email ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Mobile --}}
                            <td class="whitespace-nowrap px-5 py-4 font-mono text-sm text-gray-700">
                                {{ $customer->phone }}
                            </td>

                            {{-- Location --}}
                            <td class="px-5 py-4 text-gray-600">
                                @if($customer->city || $customer->state)
                                    <span class="block text-sm">{{ $customer->city }}</span>
                                    <span class="text-xs text-gray-400">{{ $customer->state }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Employment --}}
                            <td class="px-5 py-4 text-gray-600">
                                <span class="block text-sm">{{ $customer->employment_label }}</span>
                                @if($customer->salary)
                                    <span class="text-xs text-gray-400">₹{{ number_format($customer->salary) }}/mo</span>
                                @endif
                            </td>

                            {{-- Assigned HR --}}
                            <td class="px-5 py-4">
                                @if($customer->currentAssignment?->hr)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                        {{ $customer->currentAssignment->hr->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">Unassigned</span>
                                @endif
                            </td>

                            {{-- Loans --}}
                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full
                                             {{ $customer->loans->count() > 0 ? 'bg-slate-800 text-white' : 'bg-gray-100 text-gray-500' }}
                                             text-xs font-bold">
                                    {{ $customer->loans->count() }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <button type="button"
                                        onclick="toggleStatus({{ $customer->id }}, this)"
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold transition-colors
                                               {{ $customer->status === 'active'      ? 'bg-green-100 text-green-700 hover:bg-green-200' : '' }}
                                               {{ $customer->status === 'inactive'    ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : '' }}
                                               {{ $customer->status === 'blacklisted' ? 'bg-red-100 text-red-700 cursor-not-allowed' : '' }}">
                                    <span class="h-1.5 w-1.5 rounded-full
                                                 {{ $customer->status === 'active'      ? 'bg-green-500' : '' }}
                                                 {{ $customer->status === 'inactive'    ? 'bg-yellow-500' : '' }}
                                                 {{ $customer->status === 'blacklisted' ? 'bg-red-500' : '' }}">
                                    </span>
                                    {{ $customer->status_label }}
                                </button>
                            </td>

                            {{-- Actions --}}
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.customers.show', $customer) }}"
                                       title="View Profile"
                                       class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.customers.edit', $customer) }}"
                                       title="Edit"
                                       class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:border-gray-300 hover:bg-gray-50">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button type="button"
                                            onclick="confirmDelete({{ $customer->id }}, '{{ addslashes($customer->name) }}')"
                                            title="Delete"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-red-200 bg-white text-red-500 transition-colors hover:bg-red-50">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-14 text-center">
                                <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <p class="mt-3 text-sm font-medium text-gray-500">No customers found</p>
                                <p class="mt-1 text-xs text-gray-400">Try adjusting your search or filter criteria.</p>
                                <a href="{{ route('admin.customers.create') }}"
                                   class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                    Add First Customer
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="border-t border-gray-100 bg-gray-50 px-5 py-3">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <form id="delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

@endsection

@push('scripts')
<script>
    function confirmDelete(id, name) {
        if (typeof Swal === 'undefined') {
            if (!confirm(`Delete "${name}"? This cannot be undone.`)) return;
            submitDelete(id);
            return;
        }
        Swal.fire({
            title: 'Delete Customer?',
            html: `<p class="text-gray-600">Deleting <strong>${name}</strong> will also remove all their loan records.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
        }).then(r => { if (r.isConfirmed) submitDelete(id); });
    }

    function submitDelete(id) {
        const form = document.getElementById('delete-form');
        form.action = `{{ url('admin/customers') }}/${id}`;
        form.submit();
    }

    function toggleStatus(id, btn) {
        if (btn.classList.contains('cursor-not-allowed')) return;

        fetch(`{{ url('admin/customers') }}/${id}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (!data.status) return;
            const s = data.new_status;
            const dot = btn.querySelector('span');

            btn.className = `inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold transition-colors ` +
                (s === 'active'
                    ? 'bg-green-100 text-green-700 hover:bg-green-200'
                    : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200');
            dot.className = `h-1.5 w-1.5 rounded-full ${s === 'active' ? 'bg-green-500' : 'bg-yellow-500'}`;
            btn.lastChild.textContent = s === 'active' ? ' Active' : ' Inactive';
        });
    }
</script>
@endpush
