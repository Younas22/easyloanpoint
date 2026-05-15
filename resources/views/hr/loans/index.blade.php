@extends('layouts.app')

@section('title', 'My Loan Applications')

@section('page-header')
    <div>
        <h1 class="text-xl font-bold text-gray-900">Loan Applications</h1>
        <p class="mt-0.5 text-sm text-gray-500">Loans assigned to you for review and processing.</p>
    </div>
@endsection

@section('content')

    {{-- Stats --}}
    <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-5">
        @foreach([
            ['label' => 'Total',        'value' => $stats['total'],        'color' => 'text-slate-800'],
            ['label' => 'Pending',      'value' => $stats['pending'],      'color' => 'text-yellow-700'],
            ['label' => 'Under Review', 'value' => $stats['under_review'], 'color' => 'text-blue-700'],
            ['label' => 'Approved',     'value' => $stats['approved'],     'color' => 'text-green-700'],
            ['label' => 'Disbursed',    'value' => $stats['disbursed'],    'color' => 'text-purple-700'],
        ] as $stat)
            <div class="rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
                <p class="text-xs font-medium text-gray-500">{{ $stat['label'] }}</p>
                <p class="mt-1 text-2xl font-bold {{ $stat['color'] }}">{{ number_format($stat['value']) }}</p>
            </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="mb-5 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('hr.loans.index') }}"
              class="flex flex-col gap-3 sm:flex-row sm:items-end">

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

            <div class="flex gap-2">
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700">Filter</button>
                @if(request()->hasAny(['search', 'status', 'loan_type']))
                    <a href="{{ route('hr.loans.index') }}"
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
                        <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($loans as $loan)
                        @php
                            $sc = [
                                'pending'      => 'bg-yellow-100 text-yellow-800',
                                'under_review' => 'bg-blue-100 text-blue-800',
                                'approved'     => 'bg-green-100 text-green-800',
                                'rejected'     => 'bg-red-100 text-red-800',
                                'disbursed'    => 'bg-purple-100 text-purple-800',
                            ][$loan->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <tr class="transition-colors hover:bg-gray-50">
                            <td class="whitespace-nowrap px-5 py-4 text-xs text-gray-400">
                                {{ ($loans->currentPage() - 1) * $loans->perPage() + $loop->iteration }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <a href="{{ route('hr.loans.show', $loan) }}"
                                   class="font-mono text-sm font-semibold text-blue-600 hover:underline">
                                    {{ $loan->loan_number }}
                                </a>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-gray-900">{{ $loan->customer->name }}</p>
                                <p class="text-xs text-gray-400">{{ $loan->customer->phone }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-gray-600">{{ $loan->loan_type_label }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <p class="font-semibold text-gray-900">₹{{ number_format($loan->amount_requested) }}</p>
                                @if($loan->amount_approved)
                                    <p class="text-xs text-green-600">₹{{ number_format($loan->amount_approved) }}</p>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $sc }}">
                                    {{ $loan->status_label }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-xs text-gray-500">
                                {{ $loan->applied_at?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                @php
                                    $docCount      = $loan->documents_count ?? 0;
                                    $pendingDocs   = $loan->documents->where('status', 'pending')->count();
                                @endphp
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full {{ $pendingDocs > 0 ? 'bg-yellow-400 text-white' : 'bg-gray-100 text-gray-600' }} text-xs font-bold">
                                    {{ $loan->documents->count() }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <a href="{{ route('hr.loans.show', $loan) }}"
                                   class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-16 text-center">
                                <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-3 text-sm font-medium text-gray-500">No loans assigned to you yet</p>
                                <p class="mt-1 text-xs text-gray-400">Contact your admin to get loan assignments.</p>
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
