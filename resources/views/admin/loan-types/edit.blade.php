@extends('layouts.app')

@section('title', 'Edit Loan Type')

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.loan-types.index') }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white hover:bg-gray-50 transition-colors">
            <svg class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit — {{ $loanType->name }}</h1>
            <p class="mt-0.5 text-sm text-gray-500">Update this loan product.</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="mx-auto max-w-xl">
        <form method="POST" action="{{ route('admin.loan-types.update', $loanType) }}"
              class="space-y-5 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Loan Type Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $loanType->name) }}"
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('name') border-red-400 @enderror"
                       required>
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                <textarea name="description" rows="3"
                          class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('description') border-red-400 @enderror">{{ old('description', $loanType->description) }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Loan Amount (₹) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount', $loanType->amount) }}" min="1" step="0.01"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('amount') border-red-400 @enderror"
                           required>
                    @error('amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Repayment Days <span class="text-red-500">*</span></label>
                    <input type="number" name="repayment_days" value="{{ old('repayment_days', $loanType->repayment_days) }}" min="1" max="3650"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('repayment_days') border-red-400 @enderror"
                           required>
                    @error('repayment_days') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="active"   {{ old('status', $loanType->status) === 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $loanType->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $loanType->sort_order) }}" min="0"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            @if($loanType->loans()->exists())
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-700">
                    <strong>Note:</strong> This loan type has {{ $loanType->loans()->count() }} existing application(s). Changing the amount or repayment days will only affect new applications.
                </div>
            @endif

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.loan-types.index') }}"
                   class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
@endsection
