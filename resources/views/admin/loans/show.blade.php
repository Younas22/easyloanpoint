@extends('layouts.app')

@section('title', $loan->loan_number)

@section('page-header')
    @php
        $statusClasses = [
            'pending'      => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'under_review' => 'bg-blue-100 text-blue-800 border-blue-200',
            'approved'     => 'bg-green-100 text-green-800 border-green-200',
            'rejected'     => 'bg-red-100 text-red-800 border-red-200',
            'disbursed'    => 'bg-purple-100 text-purple-800 border-purple-200',
        ];
        $sc = $statusClasses[$loan->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
    @endphp
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.loans.index') }}"
               class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="font-mono text-xl font-bold text-gray-900">{{ $loan->loan_number }}</h1>
                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">
                        {{ $loan->status_label }}
                    </span>
                </div>
                <p class="mt-0.5 text-sm text-gray-500">
                    {{ $loan->loan_type_label }} &bull;
                    {{ $loan->customer->name }} &bull;
                    Applied {{ $loan->applied_at?->format('d M Y') ?? '—' }}
                </p>
            </div>
        </div>
        <a href="{{ route('admin.loans.edit', $loan) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Loan
        </a>
    </div>
@endsection

@section('content')
<div class="grid gap-5 lg:grid-cols-3">

    {{-- Left: Loan Info + Customer + Documents --}}
    <div class="space-y-5 lg:col-span-2">

        {{-- Loan Information --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Loan Information</h2>
            </div>
            <div class="grid grid-cols-2 gap-4 p-6 sm:grid-cols-3">
                <div>
                    <p class="text-xs font-medium text-gray-500">Loan Number</p>
                    <p class="mt-1 font-mono text-sm font-semibold text-gray-900">{{ $loan->loan_number }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Loan Type</p>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $loan->loan_type_label }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Amount Requested</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">₹{{ number_format($loan->amount_requested) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Amount Approved</p>
                    <p class="mt-1 text-sm font-semibold {{ $loan->amount_approved ? 'text-green-600' : 'text-gray-400' }}">
                        {{ $loan->amount_approved ? '₹' . number_format($loan->amount_approved) : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Interest Rate</p>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ $loan->interest_rate ? $loan->interest_rate . '% p.a.' : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Tenure</p>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ $loan->tenure_months }} months
                        @if($loan->tenure_months >= 12)
                            <span class="text-gray-400">({{ round($loan->tenure_months / 12, 1) }} yrs)</span>
                        @endif
                    </p>
                </div>
                @if($loan->purpose)
                    <div class="col-span-2 sm:col-span-3">
                        <p class="text-xs font-medium text-gray-500">Purpose</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $loan->purpose }}</p>
                    </div>
                @endif
                @if($loan->remarks)
                    <div class="col-span-2 sm:col-span-3">
                        <p class="text-xs font-medium text-gray-500">Remarks</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $loan->remarks }}</p>
                    </div>
                @endif
                <div>
                    <p class="text-xs font-medium text-gray-500">Applied At</p>
                    <p class="mt-1 text-sm text-gray-700">{{ $loan->applied_at?->format('d M Y, h:i A') ?? '—' }}</p>
                </div>
                @if($loan->reviewed_at)
                    <div>
                        <p class="text-xs font-medium text-gray-500">Reviewed At</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $loan->reviewed_at->format('d M Y, h:i A') }}</p>
                    </div>
                @endif
                @if($loan->disbursed_at)
                    <div>
                        <p class="text-xs font-medium text-gray-500">Disbursed At</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $loan->disbursed_at->format('d M Y, h:i A') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Customer Information --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Customer Information</h2>
            </div>
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-slate-800 text-sm font-bold text-white">
                        {{ strtoupper(substr($loan->customer->name, 0, 2)) }}
                    </div>
                    <div class="grid flex-1 grid-cols-2 gap-3 sm:grid-cols-3">
                        <div>
                            <p class="text-xs font-medium text-gray-500">Name</p>
                            <a href="{{ route('admin.customers.show', $loan->customer) }}"
                               class="mt-1 block text-sm font-semibold text-blue-600 hover:underline">
                                {{ $loan->customer->name }}
                            </a>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500">Phone</p>
                            <p class="mt-1 font-mono text-sm text-gray-700">{{ $loan->customer->phone }}</p>
                        </div>
                        @if($loan->customer->email)
                            <div>
                                <p class="text-xs font-medium text-gray-500">Email</p>
                                <p class="mt-1 text-sm text-gray-700">{{ $loan->customer->email }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-xs font-medium text-gray-500">Employment</p>
                            <p class="mt-1 text-sm text-gray-700">{{ $loan->customer->employment_label }}</p>
                        </div>
                        @if($loan->customer->salary)
                            <div>
                                <p class="text-xs font-medium text-gray-500">Monthly Income</p>
                                <p class="mt-1 text-sm font-semibold text-gray-700">₹{{ number_format($loan->customer->salary) }}</p>
                            </div>
                        @endif
                        @if($loan->customer->city || $loan->customer->state)
                            <div>
                                <p class="text-xs font-medium text-gray-500">Location</p>
                                <p class="mt-1 text-sm text-gray-700">{{ implode(', ', array_filter([$loan->customer->city, $loan->customer->state])) }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Documents --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">
                    Documents
                    <span class="ml-1.5 rounded-full bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">
                        {{ $loan->documents->count() }}
                    </span>
                </h2>
                <button type="button" onclick="toggleUploadForm()"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-slate-700">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Upload Document
                </button>
            </div>

            {{-- Upload Form --}}
            <div id="upload-form" class="hidden border-b border-gray-100 bg-gray-50 px-6 py-4">
                <form method="POST" action="{{ route('admin.loans.upload-document', $loan) }}"
                      enctype="multipart/form-data" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    @csrf
                    <div class="flex-1">
                        <label class="mb-1 block text-xs font-medium text-gray-600">Document Type</label>
                        <select name="document_type"
                                class="w-full rounded-lg border border-gray-300 py-2 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            @foreach(['aadhaar' => 'Aadhaar Card', 'pan' => 'PAN Card', 'selfie' => 'Selfie', 'income_proof' => 'Income Proof', 'bank_statement' => 'Bank Statement', 'other' => 'Other'] as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="mb-1 block text-xs font-medium text-gray-600">File (JPG, PNG, PDF — max 5MB)</label>
                        <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf"
                               class="w-full rounded-lg border border-gray-300 py-1.5 px-3 text-sm file:mr-2 file:rounded file:border-0 file:bg-blue-600 file:px-2 file:py-1 file:text-xs file:font-semibold file:text-white">
                    </div>
                    <button type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Upload
                    </button>
                </form>
            </div>

            {{-- Documents List --}}
            @if($loan->documents->isEmpty())
                <div class="px-6 py-10 text-center">
                    <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No documents uploaded yet.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($loan->documents as $doc)
                        <div class="flex items-center gap-4 px-6 py-4">
                            {{-- Icon --}}
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-slate-100">
                                @if($doc->isImage())
                                    <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $doc->document_type_label }}</p>
                                <p class="truncate text-xs text-gray-400">{{ $doc->original_name }}</p>
                                @if($doc->verified_at)
                                    <p class="text-xs text-gray-400">
                                        Verified by {{ $doc->verifiedBy?->name }} on {{ $doc->verified_at->format('d M Y') }}
                                    </p>
                                @endif
                                @if($doc->remarks)
                                    <p class="text-xs text-gray-500 italic">{{ $doc->remarks }}</p>
                                @endif
                            </div>

                            {{-- Status --}}
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $doc->status_badge_class }}">
                                {{ ucfirst($doc->status) }}
                            </span>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="{{ asset($doc->file_path) }}" target="_blank"
                                   class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition-colors hover:bg-gray-50 hover:text-blue-600">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.loans.verify-document', [$loan, $doc]) }}"
                                      class="flex items-center gap-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" class="doc-status-input" value="verified">
                                    <button type="button"
                                            onclick="verifyDoc(this, 'verified')"
                                            class="inline-flex h-7 px-2 items-center justify-center rounded-lg border border-green-200 bg-green-50 text-xs font-semibold text-green-700 transition-colors hover:bg-green-100">
                                        Verify
                                    </button>
                                    <button type="button"
                                            onclick="verifyDoc(this, 'rejected')"
                                            class="inline-flex h-7 px-2 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-xs font-semibold text-red-700 transition-colors hover:bg-red-100">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Status Timeline --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Status History</h2>
            </div>
            @if($loan->statusHistories->isEmpty())
                <div class="px-6 py-8 text-center text-sm text-gray-400">No status changes recorded.</div>
            @else
                <div class="px-6 py-5">
                    <ol class="relative border-l border-gray-200">
                        @foreach($loan->statusHistories as $history)
                            @php
                                $dotColors = [
                                    'pending'      => 'bg-yellow-400',
                                    'under_review' => 'bg-blue-500',
                                    'approved'     => 'bg-green-500',
                                    'rejected'     => 'bg-red-500',
                                    'disbursed'    => 'bg-purple-500',
                                ];
                                $dot = $dotColors[$history->to_status] ?? 'bg-gray-400';
                            @endphp
                            <li class="mb-6 ml-5 last:mb-0">
                                <span class="absolute -left-2 flex h-4 w-4 items-center justify-center rounded-full {{ $dot }} ring-4 ring-white"></span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        @if($history->from_status)
                                            <span class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $history->from_status)) }}</span>
                                            <svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        @endif
                                        <span class="text-sm font-semibold text-gray-800">{{ ucfirst(str_replace('_', ' ', $history->to_status)) }}</span>
                                    </div>
                                    <p class="mt-0.5 text-xs text-gray-400">
                                        By {{ $history->changedBy?->name ?? 'System' }} &bull;
                                        {{ $history->created_at->format('d M Y, h:i A') }}
                                    </p>
                                    @if($history->remarks)
                                        <p class="mt-1 rounded bg-gray-50 px-2 py-1 text-xs text-gray-600">{{ $history->remarks }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif
        </div>

    </div>

    {{-- Right: Status Update + EMI + HR --}}
    <div class="space-y-5">

        {{-- Update Status --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Update Status</h2>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.loans.update-status', $loan) }}" id="status-form">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">New Status</label>
                            <select name="status" id="status-select" onchange="toggleApprovalFields(this.value)"
                                    class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                @foreach(['pending' => 'Pending', 'under_review' => 'Under Review', 'approved' => 'Approved', 'rejected' => 'Rejected', 'disbursed' => 'Disbursed'] as $val => $label)
                                    <option value="{{ $val }}" {{ $loan->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="approval-fields" class="{{ in_array($loan->status, ['approved', 'under_review']) ? '' : 'hidden' }} space-y-3">
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-700">Amount Approved (₹)</label>
                                <input type="number" name="amount_approved"
                                       value="{{ old('amount_approved', $loan->amount_approved) }}"
                                       min="0" step="1000" placeholder="e.g. 450000"
                                       class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-700">Interest Rate (% p.a.)</label>
                                <input type="number" name="interest_rate"
                                       value="{{ old('interest_rate', $loan->interest_rate) }}"
                                       min="0" max="100" step="0.01" placeholder="e.g. 12.5"
                                       class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Remarks</label>
                            <textarea name="remarks" rows="3" placeholder="Add a note about this status change…"
                                      class="w-full resize-none rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                        </div>

                        <button type="submit"
                                class="w-full rounded-lg bg-blue-600 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- EMI Details --}}
        @if($loan->emi_amount)
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-800">EMI Details</h2>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">Monthly EMI</p>
                        <p class="text-lg font-bold text-blue-600">₹{{ number_format($loan->emi_amount, 2) }}</p>
                    </div>
                    <div class="h-px bg-gray-100"></div>
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">Principal</p>
                        <p class="text-sm font-semibold text-gray-800">₹{{ number_format($loan->amount_approved) }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">Total Interest</p>
                        <p class="text-sm font-semibold text-orange-600">₹{{ number_format($loan->total_interest, 2) }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">Total Payable</p>
                        <p class="text-sm font-bold text-gray-900">₹{{ number_format($loan->total_payable, 2) }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">Tenure</p>
                        <p class="text-sm text-gray-700">{{ $loan->tenure_months }} months</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">Rate p.a.</p>
                        <p class="text-sm text-gray-700">{{ $loan->interest_rate }}%</p>
                    </div>

                    {{-- EMI Schedule Toggle --}}
                    <button type="button" onclick="toggleEmiSchedule()"
                            class="w-full rounded-lg border border-gray-200 py-2 text-xs font-semibold text-gray-600 transition-colors hover:bg-gray-50">
                        View EMI Schedule
                    </button>

                    <div id="emi-schedule" class="hidden max-h-72 overflow-y-auto">
                        @php
                            $balance = (float) $loan->amount_approved;
                            $r = $loan->interest_rate / 12 / 100;
                            $emiAmt = $loan->emi_amount;
                        @endphp
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500">
                                    <th class="px-2 py-1.5 text-left">Month</th>
                                    <th class="px-2 py-1.5 text-right">EMI</th>
                                    <th class="px-2 py-1.5 text-right">Interest</th>
                                    <th class="px-2 py-1.5 text-right">Principal</th>
                                    <th class="px-2 py-1.5 text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @for($m = 1; $m <= min($loan->tenure_months, 60); $m++)
                                    @php
                                        $interest  = round($balance * $r, 2);
                                        $principal = round($emiAmt - $interest, 2);
                                        $balance   = max(0, round($balance - $principal, 2));
                                    @endphp
                                    <tr>
                                        <td class="px-2 py-1.5 text-gray-500">{{ $m }}</td>
                                        <td class="px-2 py-1.5 text-right text-gray-700">₹{{ number_format($emiAmt, 0) }}</td>
                                        <td class="px-2 py-1.5 text-right text-orange-600">₹{{ number_format($interest, 0) }}</td>
                                        <td class="px-2 py-1.5 text-right text-blue-600">₹{{ number_format($principal, 0) }}</td>
                                        <td class="px-2 py-1.5 text-right font-medium text-gray-800">₹{{ number_format($balance, 0) }}</td>
                                    </tr>
                                @endfor
                                @if($loan->tenure_months > 60)
                                    <tr>
                                        <td colspan="5" class="px-2 py-2 text-center text-gray-400">
                                            ... and {{ $loan->tenure_months - 60 }} more months
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Assigned HR --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Assigned HR</h2>
            </div>
            <div class="p-6">
                @if($loan->assignedHR)
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                            {{ strtoupper(substr($loan->assignedHR->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $loan->assignedHR->name }}</p>
                            <p class="text-xs text-gray-400">{{ $loan->assignedHR->email }}</p>
                            @if($loan->assignedHR->phone)
                                <p class="font-mono text-xs text-gray-400">{{ $loan->assignedHR->phone }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-400">No HR assigned to this loan.</p>
                @endif

                {{-- Quick reassign --}}
                <form method="POST" action="{{ route('admin.loans.update', $loan) }}" class="mt-4">
                    @csrf
                    @method('PUT')
                    {{-- Pass all current values so update doesn't overwrite --}}
                    <input type="hidden" name="loan_type"        value="{{ $loan->loan_type }}">
                    <input type="hidden" name="amount_requested" value="{{ $loan->amount_requested }}">
                    <input type="hidden" name="tenure_months"    value="{{ $loan->tenure_months }}">
                    <input type="hidden" name="amount_approved"  value="{{ $loan->amount_approved }}">
                    <input type="hidden" name="interest_rate"    value="{{ $loan->interest_rate }}">
                    <input type="hidden" name="purpose"          value="{{ $loan->purpose }}">
                    <input type="hidden" name="remarks"          value="{{ $loan->remarks }}">
                    <label class="mb-1 block text-xs font-medium text-gray-600">Reassign HR</label>
                    <div class="flex gap-2">
                        <select name="assigned_hr_id"
                                class="flex-1 rounded-lg border border-gray-300 py-2 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">— Remove Assignment —</option>
                            @foreach($hrUsers as $hr)
                                <option value="{{ $hr->id }}" {{ $loan->assigned_hr_id == $hr->id ? 'selected' : '' }}>
                                    {{ $hr->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit"
                                class="rounded-lg bg-slate-800 px-3 py-2 text-xs font-semibold text-white transition-colors hover:bg-slate-700">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleApprovalFields(status) {
        const fields = document.getElementById('approval-fields');
        if (status === 'approved' || status === 'under_review') {
            fields.classList.remove('hidden');
        } else {
            fields.classList.add('hidden');
        }
    }

    function toggleUploadForm() {
        document.getElementById('upload-form').classList.toggle('hidden');
    }

    function toggleEmiSchedule() {
        document.getElementById('emi-schedule').classList.toggle('hidden');
    }

    function verifyDoc(btn, status) {
        const form = btn.closest('form');
        form.querySelector('.doc-status-input').value = status;
        form.submit();
    }
</script>
@endpush
