@extends('layouts.app')

@section('title', $customer->name . ' — Customer Profile')

@section('page-header')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.index') }}"
               class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-xl font-bold text-gray-900">{{ $customer->name }}</h1>
                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold
                                 {{ $customer->status === 'active'      ? 'bg-green-100 text-green-700' : '' }}
                                 {{ $customer->status === 'inactive'    ? 'bg-yellow-100 text-yellow-700' : '' }}
                                 {{ $customer->status === 'blacklisted' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ $customer->status_label }}
                    </span>
                </div>
                <p class="mt-0.5 text-sm text-gray-500">
                    Customer since {{ $customer->created_at->format('d M Y') }}
                    @if($customer->creator) &bull; Added by {{ $customer->creator->name }} @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.customers.edit', $customer) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <button onclick="document.getElementById('assign-modal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-blue-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Assign HR
            </button>
        </div>
    </div>
@endsection

@section('content')

    {{-- Stats Row --}}
    <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        @php
            $totalLoans    = $customer->loans->count();
            $activeLoans   = $customer->loans->whereNotIn('status', ['rejected'])->count();
            $totalAmount   = $customer->loans->sum('amount_requested');
            $approvedAmt   = $customer->loans->whereIn('status', ['approved', 'disbursed'])->sum('amount_approved');
        @endphp
        <div class="rounded-xl border border-gray-200 bg-white px-4 py-3.5 shadow-sm">
            <p class="text-xs font-medium text-gray-500">Total Loans</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ $totalLoans }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white px-4 py-3.5 shadow-sm">
            <p class="text-xs font-medium text-gray-500">Active Loans</p>
            <p class="mt-1 text-2xl font-bold text-blue-600">{{ $activeLoans }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white px-4 py-3.5 shadow-sm">
            <p class="text-xs font-medium text-gray-500">Amount Applied</p>
            <p class="mt-1 text-xl font-bold text-slate-800">₹{{ number_format($totalAmount) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white px-4 py-3.5 shadow-sm">
            <p class="text-xs font-medium text-gray-500">Amount Approved</p>
            <p class="mt-1 text-xl font-bold text-green-600">₹{{ number_format($approvedAmt) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

        {{-- ── LEFT COLUMN (main content) ───────────────────── --}}
        <div class="space-y-5 lg:col-span-2">

            {{-- Personal Information --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-700">Personal Information</h2>
                </div>
                <div class="grid grid-cols-1 gap-x-8 gap-y-4 px-6 py-5 sm:grid-cols-2">
                    @php
                        $fields = [
                            'Mobile'     => $customer->phone,
                            'Email'      => $customer->email ?? '—',
                            'Gender'     => $customer->gender ? ucfirst($customer->gender) : '—',
                            'DOB'        => $customer->dob ? $customer->dob->format('d M Y') : '—',
                        ];
                    @endphp
                    @foreach($fields as $label => $value)
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-400">{{ $label }}</p>
                            <p class="mt-1 text-sm font-medium text-gray-800">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- KYC Documents --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-700">KYC Documents</h2>
                </div>
                <div class="grid grid-cols-1 gap-5 px-6 py-5 sm:grid-cols-2">

                    {{-- Aadhaar --}}
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                                <svg class="h-4 w-4 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500">Aadhaar Card</p>
                                <p class="mt-0.5 font-mono text-sm font-semibold text-gray-800">
                                    {{ $customer->masked_aadhaar }}
                                </p>
                            </div>
                        </div>
                        @if($customer->aadhaar_number)
                            <span class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-green-600">
                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Verified
                            </span>
                        @else
                            <span class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-gray-400">Not provided</span>
                        @endif
                    </div>

                    {{-- PAN --}}
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100">
                                <svg class="h-4 w-4 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500">PAN Card</p>
                                <p class="mt-0.5 font-mono text-sm font-semibold tracking-widest text-gray-800">
                                    {{ $customer->pan_number ?? '—' }}
                                </p>
                            </div>
                        </div>
                        @if($customer->pan_number)
                            <span class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-green-600">
                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Verified
                            </span>
                        @else
                            <span class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-gray-400">Not provided</span>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Loan History --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-700">Loan History
                        <span class="ml-1.5 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">
                            {{ $customer->loans->count() }}
                        </span>
                    </h2>
                </div>
                @if($customer->loans->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Loan #</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Applied</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">HR</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($customer->loans as $loan)
                                    <tr class="hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-5 py-3 font-mono text-xs font-semibold text-blue-700">
                                            {{ $loan->loan_number }}
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-3 text-gray-700">
                                            {{ $loan->loan_type_label }}
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-3 text-right font-semibold text-gray-800">
                                            ₹{{ number_format($loan->amount_requested) }}
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-3 text-center">
                                            @php
                                                $badge = match($loan->status) {
                                                    'pending'      => 'bg-yellow-100 text-yellow-700',
                                                    'under_review' => 'bg-blue-100 text-blue-700',
                                                    'approved'     => 'bg-green-100 text-green-700',
                                                    'rejected'     => 'bg-red-100 text-red-700',
                                                    'disbursed'    => 'bg-slate-800 text-white',
                                                    default        => 'bg-gray-100 text-gray-600',
                                                };
                                            @endphp
                                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $badge }}">
                                                {{ $loan->status_label }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-3 text-gray-500 text-xs">
                                            {{ $loan->applied_at->format('d M Y') }}
                                        </td>
                                        <td class="px-5 py-3 text-gray-500 text-xs">
                                            {{ $loan->assignedHR?->name ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-10 text-center">
                        <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-400">No loans found for this customer.</p>
                    </div>
                @endif
            </div>

        </div>

        {{-- ── RIGHT COLUMN (sidebar info) ──────────────────── --}}
        <div class="space-y-5">

            {{-- Address & Employment --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-700">Address & Employment</h2>
                </div>
                <div class="space-y-4 px-6 py-5">
                    @if($customer->address || $customer->city)
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Address</p>
                            <p class="mt-1 text-sm text-gray-700">
                                {{ $customer->address }}
                                @if($customer->city || $customer->state)
                                    <br>{{ implode(', ', array_filter([$customer->city, $customer->state, $customer->pincode])) }}
                                @endif
                            </p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Employment</p>
                        <p class="mt-1 text-sm font-medium text-gray-700">{{ $customer->employment_label }}</p>
                    </div>
                    @if($customer->salary)
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Monthly Salary</p>
                            <p class="mt-1 text-sm font-bold text-gray-800">₹{{ number_format($customer->salary) }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Current HR Assignment --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-700">HR Assignment</h2>
                </div>
                <div class="px-6 py-5">
                    @if($customer->assignments->first())
                        @php $latest = $customer->assignments->first(); @endphp
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">
                                {{ strtoupper(substr($latest->hr->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $latest->hr->name }}</p>
                                <p class="text-xs text-gray-400">{{ $latest->hr->email }}</p>
                            </div>
                        </div>
                        @if($latest->notes)
                            <p class="mt-3 rounded-lg bg-blue-50 px-3 py-2 text-xs text-blue-700">{{ $latest->notes }}</p>
                        @endif
                        <p class="mt-3 text-xs text-gray-400">
                            Assigned {{ $latest->created_at->diffForHumans() }}
                            by {{ $latest->assignedBy?->name }}
                        </p>
                    @else
                        <div class="rounded-lg border border-dashed border-gray-200 py-6 text-center">
                            <svg class="mx-auto h-7 w-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <p class="mt-2 text-xs text-gray-400">No HR assigned yet</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Assignment History --}}
            @if($customer->assignments->count() > 1)
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-sm font-semibold text-gray-700">Assignment History</h2>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @foreach($customer->assignments->skip(1)->take(5) as $assignment)
                            <li class="px-6 py-3">
                                <p class="text-sm font-medium text-gray-700">{{ $assignment->hr->name }}</p>
                                <p class="text-xs text-gray-400">{{ $assignment->created_at->format('d M Y, h:i A') }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Notes --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-700">Internal Notes</h2>
                </div>
                <div class="px-6 py-5">
                    <textarea id="customer-notes" rows="4"
                              placeholder="Add internal notes about this customer…"
                              class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ $customer->notes }}</textarea>
                    <div class="mt-2.5 flex items-center justify-between">
                        <span id="note-msg" class="text-xs text-green-600 hidden">Saved.</span>
                        <button type="button" onclick="saveNote()"
                                class="ml-auto rounded-lg bg-slate-800 px-4 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-slate-900">
                            Save Note
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Assign HR Modal ────────────────────────────────────── --}}
    <div id="assign-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('assign-modal').classList.add('hidden')"></div>
            <div class="relative z-10 w-full max-w-md rounded-2xl border border-gray-200 bg-white shadow-2xl">

                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h3 class="text-base font-bold text-gray-900">Assign HR Member</h3>
                    <button onclick="document.getElementById('assign-modal').classList.add('hidden')"
                            class="flex h-7 w-7 items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.customers.assign-hr', $customer) }}" class="px-6 py-5 space-y-4">
                    @csrf

                    <div>
                        <label for="hr_id" class="mb-1.5 block text-sm font-medium text-gray-700">
                            Select HR Member <span class="text-red-500">*</span>
                        </label>
                        <select name="hr_id" id="hr_id" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">— Choose HR —</option>
                            @foreach($hrs as $hr)
                                <option value="{{ $hr->id }}"
                                        {{ $customer->assignments->first()?->hr_id === $hr->id ? 'selected' : '' }}>
                                    {{ $hr->name }} ({{ $hr->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('hr_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="assign_notes" class="mb-1.5 block text-sm font-medium text-gray-700">Notes (optional)</label>
                        <textarea name="notes" id="assign_notes" rows="3"
                                  placeholder="Assignment reason or instructions…"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button"
                                onclick="document.getElementById('assign-modal').classList.add('hidden')"
                                class="flex-1 rounded-lg border border-gray-300 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 rounded-lg bg-blue-600 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                            Assign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function saveNote() {
        const notes = document.getElementById('customer-notes').value;
        const msg   = document.getElementById('note-msg');

        fetch('{{ route('admin.customers.update-note', $customer) }}', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ notes }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.status) {
                msg.classList.remove('hidden');
                setTimeout(() => msg.classList.add('hidden'), 2500);
            }
        });
    }

    // Open assign modal if validation error happened
    @if($errors->has('hr_id'))
        document.getElementById('assign-modal').classList.remove('hidden');
    @endif
</script>
@endpush
