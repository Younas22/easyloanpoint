@extends('layouts.app')

@section('title', 'Loan Applications')

@section('page-header')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Loan Applications</h1>
            <p class="mt-0.5 text-sm text-gray-500">Manage, review and process all loan applications.</p>
        </div>
        <a href="{{ route('admin.loans.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Loan
        </a>
    </div>
@endsection

@section('content')

    {{-- Stats --}}
    <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
        @foreach([
            ['label' => 'Total',        'value' => $stats['total'],        'color' => 'text-slate-800',  'bg' => 'bg-slate-50',   'border' => 'border-slate-200'],
            ['label' => 'Pending',      'value' => $stats['pending'],      'color' => 'text-yellow-700', 'bg' => 'bg-yellow-50',  'border' => 'border-yellow-200'],
            ['label' => 'Under Review', 'value' => $stats['under_review'], 'color' => 'text-blue-700',   'bg' => 'bg-blue-50',    'border' => 'border-blue-200'],
            ['label' => 'Approved',     'value' => $stats['approved'],     'color' => 'text-green-700',  'bg' => 'bg-green-50',   'border' => 'border-green-200'],
            ['label' => 'Rejected',     'value' => $stats['rejected'],     'color' => 'text-red-700',    'bg' => 'bg-red-50',     'border' => 'border-red-200'],
            ['label' => 'Disbursed',    'value' => $stats['disbursed'],    'color' => 'text-purple-700', 'bg' => 'bg-purple-50',  'border' => 'border-purple-200'],
        ] as $stat)
            <div class="rounded-xl border {{ $stat['border'] }} {{ $stat['bg'] }} px-4 py-3">
                <p class="text-xs font-medium text-gray-500">{{ $stat['label'] }}</p>
                <p class="mt-1 text-2xl font-bold {{ $stat['color'] }}">{{ number_format($stat['value']) }}</p>
            </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="mb-5 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.loans.index') }}"
              class="flex flex-col gap-3 lg:flex-row lg:items-end">

            <div class="flex-1">
                <label class="mb-1 block text-xs font-medium text-gray-600">Search</label>
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

            <div class="sm:w-40">
                <label class="mb-1 block text-xs font-medium text-gray-600">Status</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Status</option>
                    @foreach(['pending' => 'Pending', 'under_review' => 'Under Review', 'approved' => 'Approved', 'rejected' => 'Rejected', 'disbursed' => 'Disbursed'] as $val => $label)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:w-44">
                <label class="mb-1 block text-xs font-medium text-gray-600">Loan Type</label>
                <select name="loan_type" class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach(['personal' => 'Personal', 'home' => 'Home', 'business' => 'Business', 'vehicle' => 'Vehicle', 'education' => 'Education'] as $val => $label)
                        <option value="{{ $val }}" {{ request('loan_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:w-48">
                <label class="mb-1 block text-xs font-medium text-gray-600">Assigned HR</label>
                <select name="hr_id" class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All HR</option>
                    @foreach($hrUsers as $hr)
                        <option value="{{ $hr->id }}" {{ request('hr_id') == $hr->id ? 'selected' : '' }}>{{ $hr->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'loan_type', 'hr_id']))
                    <a href="{{ route('admin.loans.index') }}"
                       class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">#</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Loan No.</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Customer</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Assigned HR</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Applied</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Return Date</th>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($loans as $loan)
                        @php
                            $statusMap = [
                                'pending'      => ['pill' => 'bg-yellow-100 text-yellow-700', 'dot' => 'bg-yellow-500'],
                                'under_review' => ['pill' => 'bg-blue-100 text-blue-700',    'dot' => 'bg-blue-500'],
                                'approved'     => ['pill' => 'bg-green-100 text-green-700',  'dot' => 'bg-green-500'],
                                'rejected'     => ['pill' => 'bg-red-100 text-red-700',      'dot' => 'bg-red-500'],
                                'disbursed'    => ['pill' => 'bg-purple-100 text-purple-700','dot' => 'bg-purple-500'],
                                'closed'       => ['pill' => 'bg-gray-100 text-gray-600',   'dot' => 'bg-gray-500'],
                            ];
                            $sm = $statusMap[$loan->status] ?? ['pill' => 'bg-gray-100 text-gray-600', 'dot' => 'bg-gray-400'];
                        @endphp
                        <tr class="transition-colors hover:bg-gray-50">
                            <td class="whitespace-nowrap px-5 py-4 text-xs text-gray-400">
                                {{ ($loans->currentPage() - 1) * $loans->perPage() + $loop->iteration }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <a href="{{ route('admin.loans.show', $loan) }}"
                                   class="font-mono text-sm font-semibold text-blue-600 hover:underline">
                                    {{ $loan->loan_number }}
                                </a>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                                        {{ strtoupper(substr($loan->customer->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $loan->customer->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $loan->customer->phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-gray-600">{{ $loan->loan_type_label }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <p class="font-semibold text-gray-900">₹{{ number_format($loan->amount_requested) }}</p>
                                @if($loan->amount_approved)
                                    <p class="text-xs text-green-600">Approved: ₹{{ number_format($loan->amount_approved) }}</p>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $sm['pill'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $sm['dot'] }}"></span>
                                    {{ $loan->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if($loan->assignedHR)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                        {{ $loan->assignedHR->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">Unassigned</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-xs text-gray-500">
                                {{ $loan->applied_at?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-xs">
                                @if($loan->return_date)
                                    @if($loan->is_overdue)
                                        <span class="font-semibold text-red-600">{{ $loan->return_date->format('d M Y') }}</span>
                                        <span class="block text-red-400">{{ now()->diffForHumans($loan->return_date, true) }} overdue</span>
                                    @else
                                        <span class="text-gray-700">{{ $loan->return_date->format('d M Y') }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.loans.show', $loan) }}" title="View"
                                       class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.loans.edit', $loan) }}" title="Edit"
                                       class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:border-gray-300 hover:bg-gray-50">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @if(!in_array($loan->status, ['approved', 'disbursed']))
                                        <button type="button"
                                                onclick="confirmDelete({{ $loan->id }}, '{{ $loan->loan_number }}')"
                                                title="Delete"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-red-200 bg-white text-red-500 transition-colors hover:bg-red-50">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-16 text-center">
                                <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-3 text-sm font-medium text-gray-500">No loan applications found</p>
                                <p class="mt-1 text-xs text-gray-400">Try adjusting your filters or create a new loan application.</p>
                                <a href="{{ route('admin.loans.create') }}"
                                   class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                    Create New Loan
                                </a>
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

    <form id="delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

@endsection

@push('scripts')
<script>
    function confirmDelete(id, loanNo) {
        if (!confirm(`Delete loan ${loanNo}? This cannot be undone.`)) return;
        const form = document.getElementById('delete-form');
        form.action = `{{ url('admin/loans') }}/${id}`;
        form.submit();
    }
</script>
@endpush
