@extends('layouts.app')

@section('title', $loan->loan_number)

@section('page-header')
    @php
        $statusConfig = [
            'pending'      => ['class' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 'dot' => 'bg-yellow-400'],
            'under_review' => ['class' => 'bg-blue-100 text-blue-800 border-blue-200',       'dot' => 'bg-blue-500'],
            'approved'     => ['class' => 'bg-green-100 text-green-800 border-green-200',    'dot' => 'bg-green-500'],
            'rejected'     => ['class' => 'bg-red-100 text-red-800 border-red-200',          'dot' => 'bg-red-500'],
            'disbursed'    => ['class' => 'bg-purple-100 text-purple-800 border-purple-200', 'dot' => 'bg-purple-500'],
        ];
        $sc = $statusConfig[$loan->status] ?? ['class' => 'bg-gray-100 text-gray-800 border-gray-200', 'dot' => 'bg-gray-400'];
    @endphp
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('hr.loans.index') }}"
           class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="font-mono text-xl font-bold text-gray-900">{{ $loan->loan_number }}</h1>
                <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $sc['class'] }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $sc['dot'] }}"></span>
                    {{ $loan->status_label }}
                </span>
            </div>
            <p class="mt-0.5 text-sm text-gray-500">
                {{ $loan->loan_type_label }}
                &bull; {{ $loan->customer->name }}
                &bull; Applied {{ $loan->applied_at?->format('d M Y') ?? '—' }}
            </p>
        </div>
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

@if($errors->any())
    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
        <p class="mb-1 text-sm font-medium text-red-800">Please fix the following:</p>
        <ul class="list-disc pl-4 text-xs text-red-700 space-y-0.5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="grid gap-5 lg:grid-cols-3">

    {{-- ═══ LEFT COLUMN ═══ --}}
    <div class="space-y-5 lg:col-span-2">

        {{-- Loan Information --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Loan Information</h2>
            </div>
            <div class="grid grid-cols-2 gap-5 p-6 sm:grid-cols-3">
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
                    <p class="mt-1 text-sm text-gray-900">{{ $loan->interest_rate ? $loan->interest_rate . '% p.a.' : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Tenure</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $loan->tenure_months }} months</p>
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
                        <p class="mt-1 rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700">{{ $loan->remarks }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Customer Information --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Customer Information</h2>
                <a href="{{ route('hr.customers.show', $loan->customer) }}"
                   class="flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-800">
                    View Profile
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-2 gap-5 p-6 sm:grid-cols-3">
                <div>
                    <p class="text-xs font-medium text-gray-500">Full Name</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $loan->customer->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Phone</p>
                    <p class="mt-1 font-mono text-sm text-gray-700">{{ $loan->customer->phone }}</p>
                </div>
                @if($loan->customer->email)
                    <div>
                        <p class="text-xs font-medium text-gray-500">Email</p>
                        <p class="mt-1 text-sm text-gray-700 break-all">{{ $loan->customer->email }}</p>
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
                @if($loan->customer->aadhaar_number)
                    <div>
                        <p class="text-xs font-medium text-gray-500">Aadhaar</p>
                        <p class="mt-1 font-mono text-sm text-gray-700">{{ $loan->customer->masked_aadhaar }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Documents --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-semibold text-gray-800">Documents</h2>
                    @php $pendingCount = $loan->documents->where('status','pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-semibold text-yellow-700">
                            {{ $pendingCount }} pending
                        </span>
                    @endif
                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                        {{ $loan->documents->count() }} total
                    </span>
                </div>
            </div>

            @if($loan->documents->isEmpty())
                <div class="flex flex-col items-center justify-center px-6 py-12">
                    <svg class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-400">No documents uploaded yet.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($loan->documents as $doc)
                        @php
                            $docBadge = match($doc->status) {
                                'verified' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                default    => 'bg-yellow-100 text-yellow-700',
                            };
                        @endphp
                        <div class="px-6 py-4">
                            <div class="flex items-start gap-4">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-slate-100">
                                    <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-sm font-medium text-gray-900">{{ $doc->document_type_label }}</p>
                                        <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $docBadge }}">
                                            {{ ucfirst($doc->status) }}
                                        </span>
                                    </div>
                                    <p class="mt-0.5 truncate text-xs text-gray-400">{{ $doc->original_name }}</p>
                                    @if($doc->remarks)
                                        <p class="mt-1 text-xs italic text-gray-500">{{ $doc->remarks }}</p>
                                    @endif
                                    @if($doc->verifiedBy && $doc->status !== 'pending')
                                        <p class="mt-0.5 text-xs text-gray-400">
                                            {{ ucfirst($doc->status) }} by {{ $doc->verifiedBy->name }}
                                            &bull; {{ $doc->verified_at?->format('d M Y, h:i A') }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex flex-shrink-0 items-center gap-2">
                                    <a href="{{ asset($doc->file_path) }}" target="_blank"
                                       class="inline-flex h-8 items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 text-xs font-medium text-gray-600 hover:bg-gray-50 hover:text-blue-600">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        View
                                    </a>
                                </div>
                            </div>

                            {{-- Verify/Reject form --}}
                            @if($doc->status === 'pending')
                                <div class="mt-3 rounded-lg border border-gray-100 bg-gray-50 p-3">
                                    <form method="POST" action="{{ route('hr.loans.verify-document', $loan) }}" id="doc-form-{{ $doc->id }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="document_id" value="{{ $doc->id }}">
                                        <input type="hidden" name="status" class="doc-status-input" value="">
                                        <div class="mb-2">
                                            <input type="text" name="remarks"
                                                   placeholder="Optional remarks for this document…"
                                                   class="w-full rounded-lg border border-gray-200 bg-white py-2 px-3 text-xs focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400">
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button"
                                                    onclick="submitDocVerify('{{ $doc->id }}', 'verified')"
                                                    class="flex items-center gap-1.5 rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 transition-colors hover:bg-green-100">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Mark Verified
                                            </button>
                                            <button type="button"
                                                    onclick="submitDocVerify('{{ $doc->id }}', 'rejected')"
                                                    class="flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition-colors hover:bg-red-100">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Reject
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Status Timeline --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Status History & Timeline</h2>
            </div>

            @if($loan->statusHistories->isEmpty())
                <div class="px-6 py-10 text-center">
                    <p class="text-sm text-gray-400">No status changes recorded yet.</p>
                </div>
            @else
                <div class="px-6 py-6">
                    <ol class="relative border-l-2 border-gray-200">
                        @foreach($loan->statusHistories->sortByDesc('created_at') as $history)
                            @php
                                $dotColor = [
                                    'pending'      => 'bg-yellow-400 border-yellow-200',
                                    'under_review' => 'bg-blue-500 border-blue-200',
                                    'approved'     => 'bg-green-500 border-green-200',
                                    'rejected'     => 'bg-red-500 border-red-200',
                                    'disbursed'    => 'bg-purple-500 border-purple-200',
                                ][$history->to_status] ?? 'bg-gray-400 border-gray-200';

                                $toLabel   = ucfirst(str_replace('_', ' ', $history->to_status));
                                $fromLabel = $history->from_status
                                    ? ucfirst(str_replace('_', ' ', $history->from_status))
                                    : null;
                            @endphp
                            <li class="mb-6 ml-6 last:mb-0">
                                <span class="absolute -left-2.5 flex h-5 w-5 items-center justify-center rounded-full border-2 {{ $dotColor }}"></span>
                                <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                                    <div class="flex flex-wrap items-center gap-2 text-sm">
                                        @if($fromLabel)
                                            <span class="font-medium text-gray-500">{{ $fromLabel }}</span>
                                            <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        @endif
                                        <span class="font-bold text-gray-900">{{ $toLabel }}</span>
                                    </div>
                                    <div class="mt-1.5 flex flex-wrap items-center gap-1.5 text-xs text-gray-500">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>{{ $history->changedBy?->name ?? 'System' }}</span>
                                        <span>&bull;</span>
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $history->created_at->format('d M Y, h:i A') }}</span>
                                        <span class="text-gray-400">({{ $history->created_at->diffForHumans() }})</span>
                                    </div>
                                    @if($history->remarks)
                                        <p class="mt-2 rounded border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700">
                                            "{{ $history->remarks }}"
                                        </p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif
        </div>

    </div>

    {{-- ═══ RIGHT COLUMN ═══ --}}
    <div class="space-y-5">

        {{-- Update Status --}}
        @if(!in_array($loan->status, ['disbursed']))
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-800">Update Status</h2>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('hr.loans.update-status', $loan) }}">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-700">New Status <span class="text-red-500">*</span></label>
                                <select name="status"
                                        id="status-select"
                                        onchange="toggleApprovalFields(this.value)"
                                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="">— Select status —</option>
                                    @foreach(['under_review' => 'Under Review', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $val => $label)
                                        <option value="{{ $val }}"
                                                {{ old('status', $loan->status) === $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-400">Admins manage Pending and Disbursed statuses.</p>
                            </div>

                            {{-- Approval fields (shown only for approved) --}}
                            <div id="approval-fields"
                                 class="{{ old('status', $loan->status) === 'approved' ? '' : 'hidden' }} space-y-3 rounded-lg border border-green-100 bg-green-50 p-4">
                                <p class="text-xs font-semibold text-green-800">Approval Details</p>
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium text-gray-700">Amount Approved (₹)</label>
                                    <input type="number" name="amount_approved"
                                           value="{{ old('amount_approved', $loan->amount_approved) }}"
                                           min="0" step="1000" placeholder="0"
                                           class="w-full rounded-lg border border-gray-300 bg-white py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium text-gray-700">Interest Rate (% p.a.)</label>
                                    <input type="number" name="interest_rate"
                                           value="{{ old('interest_rate', $loan->interest_rate) }}"
                                           min="0" max="100" step="0.01" placeholder="0.00"
                                           class="w-full rounded-lg border border-gray-300 bg-white py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-700">Remarks / Notes</label>
                                <textarea name="remarks" rows="3"
                                          placeholder="Add your review notes or reason for this status change…"
                                          class="w-full resize-none rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('remarks') }}</textarea>
                            </div>

                            <button type="submit"
                                    class="w-full rounded-lg bg-blue-600 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700 disabled:opacity-60">
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="rounded-xl border border-purple-100 bg-purple-50 p-5 text-center shadow-sm">
                <svg class="mx-auto h-8 w-8 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mt-2 text-sm font-semibold text-purple-800">Loan Disbursed</p>
                <p class="mt-1 text-xs text-purple-600">This loan has been fully disbursed. No further status updates are required.</p>
            </div>
        @endif

        {{-- EMI Details --}}
        @if($loan->emi_amount)
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-800">EMI Details</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="rounded-lg bg-blue-50 border border-blue-100 p-4 text-center">
                        <p class="text-xs text-blue-600">Monthly EMI</p>
                        <p class="mt-1 text-2xl font-bold text-blue-700">₹{{ number_format($loan->emi_amount, 2) }}</p>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-gray-500">Principal Amount</p>
                            <p class="text-sm font-semibold text-gray-800">₹{{ number_format($loan->amount_approved) }}</p>
                        </div>
                        <div class="h-px bg-gray-100"></div>
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-gray-500">Total Interest</p>
                            <p class="text-sm font-semibold text-orange-600">₹{{ number_format($loan->total_interest, 2) }}</p>
                        </div>
                        <div class="h-px bg-gray-100"></div>
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-gray-500">Total Payable</p>
                            <p class="text-sm font-bold text-gray-900">₹{{ number_format($loan->total_payable, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Key Dates --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Key Dates</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach([
                    ['label' => 'Applied',  'value' => $loan->applied_at?->format('d M Y') ?? '—',   'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['label' => 'Reviewed', 'value' => $loan->reviewed_at?->format('d M Y') ?? '—',  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Disbursed','value' => $loan->disbursed_at?->format('d M Y') ?? '—', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                ] as $date)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $date['icon'] }}"/>
                        </svg>
                        <p class="flex-1 text-xs text-gray-500">{{ $date['label'] }}</p>
                        <p class="text-xs font-semibold text-gray-800">{{ $date['value'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Quick Info --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Assigned HR</h2>
            </div>
            <div class="flex items-center gap-3 p-5">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-600">
                    {{ strtoupper(substr($loan->assignedHR?->name ?? 'HR', 0, 2)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $loan->assignedHR?->name ?? 'Unassigned' }}</p>
                    <p class="text-xs text-gray-500">{{ $loan->assignedHR?->email ?? '—' }}</p>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    function toggleApprovalFields(status) {
        const fields = document.getElementById('approval-fields');
        if (fields) fields.classList.toggle('hidden', status !== 'approved');
    }

    function submitDocVerify(docId, status) {
        const form = document.getElementById('doc-form-' + docId);
        if (!form) return;
        if (status === 'rejected') {
            const remarks = form.querySelector('[name="remarks"]').value;
            if (!remarks.trim()) {
                alert('Please add a reason for rejecting this document.');
                form.querySelector('[name="remarks"]').focus();
                return;
            }
        }
        form.querySelector('.doc-status-input').value = status;
        form.submit();
    }

    // Init on load
    document.addEventListener('DOMContentLoaded', function () {
        const sel = document.getElementById('status-select');
        if (sel) toggleApprovalFields(sel.value);
    });
</script>
@endpush
