@extends('layouts.app')

@section('title', 'Edit HR Member')

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.hr.index') }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-50 hover:text-gray-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit HR Member</h1>
            <p class="mt-0.5 text-sm text-gray-500">Update details for <strong>{{ $hr->name }}</strong>.</p>
        </div>
    </div>
@endsection

@section('content')

<div class="mx-auto max-w-2xl">
    <form method="POST"
          action="{{ route('admin.hr.update', $hr) }}"
          enctype="multipart/form-data"
          class="rounded-xl border border-gray-200 bg-white shadow-sm">

        @csrf
        @method('PUT')

        <div class="border-b border-gray-100 px-6 py-4">
            <h2 class="text-sm font-semibold text-gray-700">Personal Information</h2>
        </div>

        <div class="space-y-5 px-6 py-6">

            {{-- Profile Image --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Profile Image</label>
                <div class="flex items-center gap-4">
                    <div id="img-preview"
                         class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full border-2 border-gray-200 bg-gray-50 overflow-hidden">
                        @if($hr->profile_image)
                            <img src="{{ asset($hr->profile_image) }}"
                                 alt="{{ $hr->name }}"
                                 class="h-full w-full object-cover">
                        @else
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file"
                               name="profile_image"
                               id="profile_image"
                               accept="image/jpg,image/jpeg,image/png"
                               class="hidden"
                               onchange="previewImage(this)">
                        <label for="profile_image"
                               class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Change Image
                        </label>
                        <p class="mt-1.5 text-xs text-gray-400">JPG, JPEG or PNG. Max 2MB. Leave blank to keep current.</p>
                    </div>
                </div>
                @error('profile_image')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Name --}}
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', $hr->name) }}"
                       placeholder="Enter full name"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500
                              @error('name') border-red-400 bg-red-50 @enderror">
                @error('name')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email', $hr->email) }}"
                       placeholder="hr@example.com"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500
                              @error('email') border-red-400 bg-red-50 @enderror">
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Phone Number <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="phone"
                       name="phone"
                       value="{{ old('phone', $hr->phone) }}"
                       placeholder="10-digit mobile number"
                       maxlength="10"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500
                              @error('phone') border-red-400 bg-red-50 @enderror">
                @error('phone')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password (optional on edit) --}}
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Change Password (optional)</p>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="Leave blank to keep current"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500
                                      @error('password') border-red-400 bg-red-50 @enderror">
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               placeholder="Re-enter new password"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Status <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="1"
                               {{ old('status', $hr->status ? '1' : '0') === '1' ? 'checked' : '' }}
                               class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="0"
                               {{ old('status', $hr->status ? '1' : '0') === '0' ? 'checked' : '' }}
                               class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Inactive</span>
                    </label>
                </div>
                @error('status')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="flex items-center justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4">
            <a href="{{ route('admin.hr.index') }}"
               class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700">
                Save Changes
            </button>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
    function previewImage(input) {
        const preview = document.getElementById('img-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
