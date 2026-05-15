@extends('layouts.app')

@section('title', 'Edit Customer')

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.customers.show', $customer) }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Customer</h1>
            <p class="mt-0.5 text-sm text-gray-500">Update details for <strong>{{ $customer->name }}</strong>.</p>
        </div>
    </div>
@endsection

@section('content')

@php
    $employmentTypes = [
        'salaried'      => 'Salaried',
        'self_employed' => 'Self Employed',
        'business'      => 'Business',
        'unemployed'    => 'Unemployed',
    ];
@endphp

<form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="space-y-5">
    @csrf
    @method('PUT')

    {{-- Personal Info --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-4">
            <h2 class="text-sm font-semibold text-gray-700">Personal Information</h2>
        </div>
        <div class="grid grid-cols-1 gap-5 px-6 py-5 sm:grid-cols-2 lg:grid-cols-3">

            <div class="lg:col-span-2">
                <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500
                              @error('name') border-red-400 bg-red-50 @enderror">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="gender" class="mb-1.5 block text-sm font-medium text-gray-700">Gender</label>
                <select id="gender" name="gender"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Select gender</option>
                    <option value="male"   {{ old('gender', $customer->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $customer->gender) === 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other"  {{ old('gender', $customer->gender) === 'other'  ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div>
                <label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Mobile Number <span class="text-red-500">*</span>
                </label>
                <input type="text" id="phone" name="phone"
                       value="{{ old('phone', $customer->phone) }}" maxlength="10"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500
                              @error('phone') border-red-400 bg-red-50 @enderror">
                @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email', $customer->email) }}"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500
                              @error('email') border-red-400 bg-red-50 @enderror">
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="dob" class="mb-1.5 block text-sm font-medium text-gray-700">Date of Birth</label>
                <input type="date" id="dob" name="dob"
                       value="{{ old('dob', $customer->dob?->format('Y-m-d')) }}"
                       max="{{ now()->subYears(18)->format('Y-m-d') }}"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

        </div>
    </div>

    {{-- KYC --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-4">
            <h2 class="text-sm font-semibold text-gray-700">KYC Details</h2>
        </div>
        <div class="grid grid-cols-1 gap-5 px-6 py-5 sm:grid-cols-2">

            <div>
                <label for="aadhaar_number" class="mb-1.5 block text-sm font-medium text-gray-700">Aadhaar Number</label>
                <input type="text" id="aadhaar_number" name="aadhaar_number"
                       value="{{ old('aadhaar_number', $customer->aadhaar_number) }}"
                       maxlength="12"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 font-mono text-sm tracking-widest focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500
                              @error('aadhaar_number') border-red-400 bg-red-50 @enderror">
                @error('aadhaar_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="pan_number" class="mb-1.5 block text-sm font-medium text-gray-700">PAN Number</label>
                <input type="text" id="pan_number" name="pan_number"
                       value="{{ old('pan_number', $customer->pan_number) }}"
                       maxlength="10" style="text-transform:uppercase"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 font-mono text-sm tracking-widest focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500
                              @error('pan_number') border-red-400 bg-red-50 @enderror">
                @error('pan_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

        </div>
    </div>

    {{-- Address --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-4">
            <h2 class="text-sm font-semibold text-gray-700">Address Details</h2>
        </div>
        <div class="grid grid-cols-1 gap-5 px-6 py-5 sm:grid-cols-2 lg:grid-cols-3">

            <div class="sm:col-span-2 lg:col-span-3">
                <label for="address" class="mb-1.5 block text-sm font-medium text-gray-700">Full Address</label>
                <textarea id="address" name="address" rows="2"
                          class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('address', $customer->address) }}</textarea>
            </div>

            <div>
                <label for="city" class="mb-1.5 block text-sm font-medium text-gray-700">City</label>
                <input type="text" id="city" name="city"
                       value="{{ old('city', $customer->city) }}"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div>
                <label for="state" class="mb-1.5 block text-sm font-medium text-gray-700">State</label>
                <select id="state" name="state"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Select state</option>
                    @foreach($states as $state)
                        <option value="{{ $state }}" {{ old('state', $customer->state) === $state ? 'selected' : '' }}>{{ $state }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="pincode" class="mb-1.5 block text-sm font-medium text-gray-700">Pincode</label>
                <input type="text" id="pincode" name="pincode"
                       value="{{ old('pincode', $customer->pincode) }}" maxlength="6"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

        </div>
    </div>

    {{-- Employment --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-4">
            <h2 class="text-sm font-semibold text-gray-700">Employment & Financial</h2>
        </div>
        <div class="grid grid-cols-1 gap-5 px-6 py-5 sm:grid-cols-2">

            <div>
                <label for="employment_type" class="mb-1.5 block text-sm font-medium text-gray-700">Employment Type</label>
                <select id="employment_type" name="employment_type"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Select type</option>
                    @foreach($employmentTypes as $value => $label)
                        <option value="{{ $value }}" {{ old('employment_type', $customer->employment_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="salary" class="mb-1.5 block text-sm font-medium text-gray-700">Monthly Salary (₹)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-medium text-gray-500">₹</span>
                    <input type="number" id="salary" name="salary"
                           value="{{ old('salary', $customer->salary) }}"
                           min="0" step="0.01"
                           class="w-full rounded-lg border border-gray-300 py-2.5 pl-8 pr-4 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

        </div>
    </div>

    {{-- Status & Notes --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-4">
            <h2 class="text-sm font-semibold text-gray-700">Status & Notes</h2>
        </div>
        <div class="grid grid-cols-1 gap-5 px-6 py-5 sm:grid-cols-2">

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Status <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-5 pt-1">
                    @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'blacklisted' => 'Blacklisted'] as $val => $lbl)
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="radio" name="status" value="{{ $val }}"
                                   {{ old('status', $customer->status) === $val ? 'checked' : '' }}
                                   class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">{{ $lbl }}</span>
                        </label>
                    @endforeach
                </div>
                @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="notes" class="mb-1.5 block text-sm font-medium text-gray-700">Internal Notes</label>
                <textarea id="notes" name="notes" rows="3"
                          class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notes', $customer->notes) }}</textarea>
            </div>

        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-3 rounded-xl border border-gray-200 bg-white px-6 py-4 shadow-sm">
        <a href="{{ route('admin.customers.show', $customer) }}"
           class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50">
            Cancel
        </a>
        <button type="submit"
                class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700">
            Save Changes
        </button>
    </div>

</form>

@endsection

@push('scripts')
<script>
    document.getElementById('pan_number').addEventListener('input', function () {
        this.value = this.value.toUpperCase();
    });
</script>
@endpush
