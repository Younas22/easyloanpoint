@extends('layouts.app')

@section('title', 'Create Loan Application')

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.loans.index') }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">New Loan Application</h1>
            <p class="mt-0.5 text-sm text-gray-500">Create a new loan application for a customer.</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('admin.loans.store') }}"
          class="space-y-5">
        @csrf

        {{-- Customer Selection --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-semibold text-gray-800">Customer Details</h2>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Customer <span class="text-red-500">*</span>
                </label>
                <select name="customer_id"
                        class="w-full rounded-lg border @error('customer_id') border-red-400 @else border-gray-300 @enderror py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">— Select Customer —</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}"
                            {{ (old('customer_id', $selected?->id) == $customer->id) ? 'selected' : '' }}>
                            {{ $customer->name }} ({{ $customer->phone }})
                        </option>
                    @endforeach
                </select>
                @error('customer_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
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
                        <option value="">— Select Type —</option>
                        @foreach(['personal' => 'Personal Loan', 'home' => 'Home Loan', 'business' => 'Business Loan', 'vehicle' => 'Vehicle Loan', 'education' => 'Education Loan'] as $val => $label)
                            <option value="{{ $val }}" {{ old('loan_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
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
                    <input type="number" name="amount_requested" value="{{ old('amount_requested') }}"
                           min="1000" max="10000000" step="1000"
                           placeholder="e.g. 500000"
                           class="w-full rounded-lg border @error('amount_requested') border-red-400 @else border-gray-300 @enderror py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    @error('amount_requested')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Tenure (Months) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="tenure_months" value="{{ old('tenure_months', 12) }}"
                           min="1" max="360"
                           class="w-full rounded-lg border @error('tenure_months') border-red-400 @else border-gray-300 @enderror py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    @error('tenure_months')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Assign HR</label>
                    <select name="assigned_hr_id"
                            class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">— No Assignment —</option>
                        @foreach($hrUsers as $hr)
                            <option value="{{ $hr->id }}" {{ old('assigned_hr_id') == $hr->id ? 'selected' : '' }}>
                                {{ $hr->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Purpose of Loan</label>
                    <input type="text" name="purpose" value="{{ old('purpose') }}"
                           placeholder="Brief description of loan purpose"
                           maxlength="500"
                           class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Remarks</label>
                    <textarea name="remarks" rows="3" placeholder="Internal remarks (optional)"
                              maxlength="1000"
                              class="w-full resize-none rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                    class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700">
                Create Loan Application
            </button>
            <a href="{{ route('admin.loans.index') }}"
               class="rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
