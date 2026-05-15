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
    <div class="flex items-center gap-3">
        <a href="{{ route('hr.loans.index') }}"
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
                {{ $loan->loan_type_label }} &bull; {{ $loan->customer->name }} &bull;
                Applied {{ $loan->applied_at?->format('d M Y') ?? '—' }}
            </p>
        </div>
    </div>
@endsection

@section('content')
<div class="grid gap-5 lg:grid-cols-3">

    {{-- Left --}}
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
                        <p class="mt-1 text-sm text-gray-700">{{ $loan->remarks }}</p>
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
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                    <div>
                        <p class="text-xs font-medium text-gray-500">Name</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $loan->customer->name }}</p>
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
                    @if($loan->customer->aadhaar_number)
                        <div>
                            <p class="text-xs font-medium text-gray-500">Aadhaar</p>
                            <p class="mt-1 font-mono text-sm text-gray-700">{{ $loan->customer->masked_aadhaar }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Documents --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">
                    Documents
                    @php $pendingCount = $loan->documents->where('status','pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="ml-1.5 rounded-full bg-yellow-100 px-1.5 py-0.5 text-xs font-semibold text-yellow-700">
                            {{ $pendingCount }} pending
                        </span>
                    @endif
                </h2>
            </div>

            @if($loan->documents->isEmpty())
                <div class="px-6 py-10 text-center">
                    <p class="text-sm text-gray-400">No documents uploaded.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($loan->documents as $doc)
                        <div class="flex items-start gap-4 px-6 py-4">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-slate-100">
                                <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $doc->document_type_label }}</p>
                                <p class="truncate text-xs text-gray-400">{{ $doc->original_name }}</p>
                                @if($doc->remarks)
                                    <p class="text-xs italic text-gray-500">{{ $doc->remarks }}</p>
                                @endif
                            </div>
                            <div class="flex flex-shrink-0 items-center gap-2">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $doc->status_badge_class }}">
                                    {{ ucfirst($doc->status) }}
                                </span>
                                <a href="{{ asset($doc->file_path) }}" target="_blank"
                                   class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-blue-600">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                                @if($doc->status === 'pending')
                                    <form method="POST" action="{{ route('hr.loans.verify-document', $loan) }}" class="flex gap-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="document_id" value="{{ $doc->id }}">
                                        <input type="hidden" name="status" class="doc-status-val" value="verified">
                                        <button type="button" onclick="verifyHR(this,'verified')"
                                                class="rounded-lg border border-green-200 bg-green-50 px-2 py-1 text-xs font-semibold text-green-700 hover:bg-green-100">
                                            Verify
                                        </button>
                                        <button type="button" onclick="verifyHR(this,'rejected')"
                                                class="rounded-lg border border-red-200 bg-red-50 px-2 py-1 text-xs font-semibold text-red-700 hover:bg-red-100">
                                            Reject
                                        </button>
                                    </form>
                                @endif
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
                                $dot = [
                                    'pending'      => 'bg-yellow-400',
                                    'under_review' => 'bg-blue-500',
                                    'approved'     => 'bg-green-500',
                                    'rejected'     => 'bg-red-500',
                                    'disbursed'    => 'bg-purple-500',
                                ][$history->to_status] ?? 'bg-gray-400';
                            @endphp
                            <li class="mb-5 ml-5 last:mb-0">
                                <span class="absolute -left-2 flex h-4 w-4 items-center justify-center rounded-full {{ $dot }} ring-4 ring-white"></span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        @if($history->from_status)
                                            <span class="text-xs text-gray-500">{{ ucfirst(str_replace('_',' ',$history->from_status)) }}</span>
                                            <svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        @endif
                                        <span class="text-sm font-semibold text-gray-800">{{ ucfirst(str_replace('_',' ',$history->to_status)) }}</span>
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

    {{-- Right --}}
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
                                <label class="mb-1.5 block text-xs font-medium text-gray-700">New Status</label>
                                <select name="status" onchange="toggleHRApproval(this.value)"
                                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    @foreach(['under_review' => 'Under Review', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $val => $label)
                                        <option value="{{ $val }}" {{ $loan->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-400">Admins handle Pending and Disbursed statuses.</p>
                            </div>

                            <div id="hr-approval-fields"
                                 class="{{ $loan->status === 'approved' ? '' : 'hidden' }} space-y-3">
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium text-gray-700">Amount Approved (₹)</label>
                                    <input type="number" name="amount_approved"
                                           value="{{ $loan->amount_approved }}"
                                           min="0" step="1000"
                                           class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium text-gray-700">Interest Rate (% p.a.)</label>
                                    <input type="number" name="interest_rate"
                                           value="{{ $loan->interest_rate }}"
                                           min="0" max="100" step="0.01"
                                           class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-700">Remarks</label>
                                <textarea name="remarks" rows="3" placeholder="Add your review notes…"
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
        @endif

        {{-- EMI Details --}}
        @if($loan->emi_amount)
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-800">EMI Details</h2>
                </div>
                <div class="space-y-3 p-6">
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
                </div>
            </div>
        @endif

        {{-- Key Dates --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-800">Key Dates</h2>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="flex items-center justify-between px-6 py-3">
                    <p class="text-xs text-gray-500">Applied</p>
                    <p class="text-xs font-medium text-gray-800">{{ $loan->applied_at?->format('d M Y') ?? '—' }}</p>
                </div>
                <div class="flex items-center justify-between px-6 py-3">
                    <p class="text-xs text-gray-500">Reviewed</p>
                    <p class="text-xs font-medium text-gray-800">{{ $loan->reviewed_at?->format('d M Y') ?? '—' }}</p>
                </div>
                <div class="flex items-center justify-between px-6 py-3">
                    <p class="text-xs text-gray-500">Disbursed</p>
                    <p class="text-xs font-medium text-gray-800">{{ $loan->disbursed_at?->format('d M Y') ?? '—' }}</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleHRApproval(status) {
        const fields = document.getElementById('hr-approval-fields');
        fields.classList.toggle('hidden', status !== 'approved');
    }

    function verifyHR(btn, status) {
        const form = btn.closest('form');
        form.querySelector('.doc-status-val').value = status;
        form.submit();
    }
</script>
@endpush
