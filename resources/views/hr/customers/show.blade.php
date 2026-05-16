@extends('layouts.app')

@section('title', $customer->name . ' — Customer Profile')
@section('page-title', 'Customer Profile')

@section('page-header')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('hr.customers.index') }}"
               class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition hover:border-gray-300 hover:text-gray-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $customer->name }}</h2>
                <p class="mt-0.5 text-sm text-gray-500">Customer profile &amp; loan history</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @php
                $statusCls = match($customer->status) {
                    'active'      => 'border-green-200 bg-green-50 text-green-700',
                    'inactive'    => 'border-gray-200 bg-gray-50 text-gray-600',
                    'blacklisted' => 'border-red-200 bg-red-50 text-red-700',
                    default       => 'border-gray-200 bg-gray-50 text-gray-600',
                };
            @endphp
            <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold {{ $statusCls }}">
                <span class="h-1.5 w-1.5 rounded-full
                    @if($customer->status === 'active') bg-green-500
                    @elseif($customer->status === 'blacklisted') bg-red-500
                    @else bg-gray-400 @endif"></span>
                {{ $customer->status_label }}
            </span>
            @if($assignment)
                <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800">
                    Assigned {{ $assignment->created_at->format('d M Y') }}
                </span>
            @endif
        </div>
    </div>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-4 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- ── Left Column: Profile + Docs + Remarks ─────────────────────────── --}}
    <div class="space-y-6 lg:col-span-1">

        {{-- Profile Card --}}
        <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
            <div class="flex flex-col items-center border-b border-gray-100 px-6 py-6 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-900 text-xl font-bold text-white">
                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                </div>
                <h3 class="mt-3 text-base font-bold text-gray-900">{{ $customer->name }}</h3>
                <p class="mt-0.5 text-sm text-gray-500">{{ $customer->employment_label }}</p>
                <div class="mt-3 flex gap-2">
                    <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                        {{ $customer->loans_count ?? $customer->loans->count() }} Loans
                    </span>
                </div>
            </div>
            <div class="divide-y divide-gray-50 px-5 py-4">
                @php
                    $fields = [
                        ['label' => 'Mobile',   'value' => $customer->phone    ?: '—', 'mono' => true],
                        ['label' => 'Email',    'value' => $customer->email    ?: '—', 'mono' => false],
                        ['label' => 'City',     'value' => $customer->city     ?: '—', 'mono' => false],
                        ['label' => 'State',    'value' => $customer->state    ?: '—', 'mono' => false],
                        ['label' => 'Pincode',  'value' => $customer->pincode  ?: '—', 'mono' => true],
                        ['label' => 'Gender',   'value' => $customer->gender   ? ucfirst($customer->gender) : '—', 'mono' => false],
                        ['label' => 'DOB',      'value' => $customer->dob      ? $customer->dob->format('d M Y') : '—', 'mono' => false],
                        ['label' => 'Salary',   'value' => $customer->salary   ? '₹' . number_format($customer->salary, 0) : '—', 'mono' => false],
                        ['label' => 'Aadhaar',  'value' => $customer->masked_aadhaar, 'mono' => true],
                        ['label' => 'PAN',      'value' => $customer->pan_number ?: '—', 'mono' => true],
                    ];
                @endphp
                @foreach($fields as $field)
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-xs text-gray-500">{{ $field['label'] }}</span>
                        <span class="text-xs font-medium {{ $field['mono'] ? 'font-mono' : '' }} text-gray-800">
                            {{ $field['value'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Documents Card --}}
        <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h4 class="text-sm font-bold text-gray-900">Uploaded Documents</h4>
                @php $totalDocs = $customer->loans->sum(fn($l) => $l->documents->count()); @endphp
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600">{{ $totalDocs }}</span>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($customer->loans as $loan)
                    @foreach($loan->documents as $doc)
                        <div class="flex items-center gap-3 px-5 py-3">
                            {{-- Icon --}}
                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-gray-100 bg-gray-50">
                                @if($doc->isImage())
                                    <svg class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @else
                                    <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-semibold text-gray-800">{{ $doc->document_type_label }}</p>
                                <p class="text-xs text-gray-400">{{ $loan->loan_number }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                @php
                                    $docCls = match($doc->status) {
                                        'verified' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default    => 'bg-yellow-100 text-yellow-700',
                                    };
                                @endphp
                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $docCls }}">
                                    {{ ucfirst($doc->status) }}
                                </span>
                                <a href="{{ asset('uploads/' . $doc->file_path) }}"
                                   target="_blank"
                                   class="flex h-6 w-6 items-center justify-center rounded text-gray-400 transition hover:text-blue-600"
                                   title="View Document">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @empty
                    <div class="px-5 py-8 text-center">
                        <p class="text-xs text-gray-400">No documents uploaded yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Add Remark --}}
        <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h4 class="text-sm font-bold text-gray-900">Add Remark / Note</h4>
                <p class="mt-0.5 text-xs text-gray-500">Notes are appended with your name and timestamp.</p>
            </div>
            <form action="{{ route('hr.customers.remark', $customer) }}" method="POST" class="p-5">
                @csrf
                <textarea name="remark"
                          rows="4"
                          placeholder="Write your remark or note here…"
                          class="w-full resize-none rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 @error('remark') border-red-400 @enderror"></textarea>
                @error('remark')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <button type="submit"
                        class="mt-3 w-full rounded-lg border border-blue-900 bg-blue-900 py-2 text-sm font-semibold text-white transition hover:bg-blue-800">
                    Add Remark
                </button>
            </form>
        </div>
    </div>

    {{-- ── Right Column: Loans + Notes Timeline ───────────────────────────── --}}
    <div class="space-y-6 lg:col-span-2">

        {{-- Loan Applications --}}
        <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h4 class="text-sm font-bold text-gray-900">Loan Applications</h4>
                <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">
                    {{ $customer->loans->count() }}
                </span>
            </div>

            @if($customer->loans->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">No loan applications</p>
                    <p class="mt-1 text-xs text-gray-400">This customer has not applied for any loans yet.</p>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($customer->loans as $loan)
                        <div class="p-5">
                            {{-- Loan Header --}}
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-sm font-bold text-gray-900">{{ $loan->loan_number }}</span>
                                        @php
                                            $lCls = match($loan->status) {
                                                'pending'      => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                'under_review' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'approved'     => 'bg-green-100 text-green-800 border-green-200',
                                                'rejected'     => 'bg-red-100 text-red-800 border-red-200',
                                                'disbursed'    => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                                default        => 'bg-gray-100 text-gray-700 border-gray-200',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-semibold {{ $lCls }}">
                                            {{ $loan->status_label }}
                                        </span>
                                    </div>
                                    <p class="mt-0.5 text-xs text-gray-500">{{ $loan->loan_type_label }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900">₹{{ number_format($loan->amount_requested, 0) }}</p>
                                    <p class="text-xs text-gray-400">Applied: {{ $loan->applied_at->format('d M Y') }}</p>
                                </div>
                            </div>

                            {{-- Loan Details Grid --}}
                            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                                @php
                                    $loanDetails = [
                                        ['label' => 'Approved Amt', 'value' => $loan->amount_approved ? '₹' . number_format($loan->amount_approved, 0) : '—'],
                                        ['label' => 'Interest',     'value' => $loan->interest_rate   ? $loan->interest_rate . '%'  : '—'],
                                        ['label' => 'Tenure',       'value' => $loan->tenure_months   ? $loan->tenure_months . ' mo' : '—'],
                                        ['label' => 'Monthly EMI',  'value' => $loan->emi_amount      ? '₹' . number_format($loan->emi_amount, 0) : '—'],
                                    ];
                                @endphp
                                @foreach($loanDetails as $detail)
                                    <div class="rounded-lg bg-gray-50 px-3 py-2.5">
                                        <p class="text-xs text-gray-400">{{ $detail['label'] }}</p>
                                        <p class="mt-0.5 text-sm font-semibold text-gray-800">{{ $detail['value'] }}</p>
                                    </div>
                                @endforeach
                            </div>

                            @if($loan->purpose)
                                <p class="mt-3 text-xs text-gray-500">
                                    <span class="font-medium text-gray-700">Purpose:</span> {{ $loan->purpose }}
                                </p>
                            @endif

                            @if($loan->remarks)
                                <p class="mt-1 text-xs text-gray-500">
                                    <span class="font-medium text-gray-700">Remarks:</span> {{ $loan->remarks }}
                                </p>
                            @endif

                            {{-- Status History --}}
                            @if($loan->statusHistories->isNotEmpty())
                                <div class="mt-4">
                                    <p class="mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status History</p>
                                    <div class="space-y-1.5">
                                        @foreach($loan->statusHistories->take(4) as $history)
                                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                                <span class="h-1.5 w-1.5 rounded-full bg-gray-300 flex-shrink-0"></span>
                                                <span class="font-medium text-gray-700">{{ $history->status_label ?? ucfirst($history->status) }}</span>
                                                <span>—</span>
                                                <span>{{ $history->created_at->format('d M Y, h:i A') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Quick action --}}
                            <div class="mt-4">
                                <a href="{{ route('hr.loans.show', $loan) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:border-blue-300 hover:text-blue-700">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View Full Loan Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Notes / Remarks Timeline --}}
        <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h4 class="text-sm font-bold text-gray-900">Remarks &amp; Notes History</h4>
            </div>
            <div class="p-5">
                @if($customer->notes)
                    @php
                        $remarkLines = array_filter(
                            preg_split('/\n\n+/', trim($customer->notes))
                        );
                    @endphp
                    <div class="space-y-3">
                        @foreach(array_reverse($remarkLines) as $remark)
                            @php
                                preg_match('/^\[(.+?) — (.+?)\]:\s*(.+)$/s', trim($remark), $m);
                                $time    = $m[1] ?? null;
                                $author  = $m[2] ?? null;
                                $text    = $m[3] ?? trim($remark);
                            @endphp
                            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                                @if($time && $author)
                                    <div class="mb-2 flex items-center gap-2">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-900 text-xs font-bold text-white">
                                            {{ strtoupper(substr($author, 0, 1)) }}
                                        </div>
                                        <span class="text-xs font-semibold text-gray-700">{{ $author }}</span>
                                        <span class="text-xs text-gray-400">·</span>
                                        <span class="text-xs text-gray-400">{{ $time }}</span>
                                    </div>
                                @endif
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $text }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center py-8 text-center">
                        <svg class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No remarks added yet.</p>
                        <p class="text-xs text-gray-400">Use the form on the left to add your first note.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection
