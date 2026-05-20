@extends('layouts.app')

@section('title', 'Edit Loan — ' . $loan->loan_number)

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.loans.show', $loan) }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Loan</h1>
            <p class="mt-0.5 font-mono text-sm text-gray-500">{{ $loan->loan_number }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('admin.loans.update', $loan) }}" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Customer Info (read-only) --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-semibold text-gray-800">Customer</h2>
            <div class="flex items-center gap-3 rounded-lg bg-gray-50 px-4 py-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-800 text-xs font-bold text-white">
                    {{ strtoupper(substr($loan->customer->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $loan->customer->name }}</p>
                    <p class="text-xs text-gray-500">{{ $loan->customer->phone }}</p>
                </div>
            </div>
        </div>

        {{-- Loan Details --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-semibold text-gray-800">Loan Details</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Loan Type <span class="text-red-500">*</span>
                    </label>
                    <select name="loan_type"
                            class="w-full rounded-lg border @error('loan_type') border-red-400 @else border-gray-300 @enderror py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @foreach(['personal' => 'Personal Loan', 'home' => 'Home Loan', 'business' => 'Business Loan', 'vehicle' => 'Vehicle Loan', 'education' => 'Education Loan'] as $val => $label)
                            <option value="{{ $val }}" {{ old('loan_type', $loan->loan_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('loan_type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Amount Requested (₹) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="amount_requested"
                           value="{{ old('amount_requested', $loan->amount_requested) }}"
                           min="1000" max="10000000" step="1000"
                           class="w-full rounded-lg border @error('amount_requested') border-red-400 @else border-gray-300 @enderror py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    @error('amount_requested')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Amount Approved (₹)</label>
                    <input type="number" name="amount_approved"
                           value="{{ old('amount_approved', $loan->amount_approved) }}"
                           min="0" max="10000000" step="1000"
                           class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Interest Rate (% p.a.)</label>
                    <input type="number" name="interest_rate"
                           value="{{ old('interest_rate', $loan->interest_rate) }}"
                           min="0" max="100" step="0.01"
                           placeholder="e.g. 12.5"
                           class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Repayment Days <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="repayment_days"
                           value="{{ old('repayment_days', $loan->repayment_days ?: 6) }}"
                           min="1" max="3650"
                           class="w-full rounded-lg border @error('repayment_days') border-red-400 @else border-gray-300 @enderror py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-400">Return date = Applied date + repayment days</p>
                    @error('repayment_days')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Assign HR</label>
                    <select name="assigned_hr_id"
                            class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">— No Assignment —</option>
                        @foreach($hrUsers as $hr)
                            <option value="{{ $hr->id }}"
                                {{ old('assigned_hr_id', $loan->assigned_hr_id) == $hr->id ? 'selected' : '' }}>
                                {{ $hr->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Purpose</label>
                    <input type="text" name="purpose"
                           value="{{ old('purpose', $loan->purpose) }}"
                           maxlength="500"
                           class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Remarks</label>
                    <textarea name="remarks" rows="3" maxlength="1000"
                              class="w-full resize-none rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('remarks', $loan->remarks) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700">
                Save Changes
            </button>
            <a href="{{ route('admin.loans.show', $loan) }}"
               class="rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
