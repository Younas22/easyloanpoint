@extends('layouts.app')

@section('title', 'Change Password')
@section('page-title', 'Change Password')

@section('content')
<div class="mx-auto max-w-2xl">
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-4">
            <h2 class="text-base font-semibold text-gray-900">Change Password</h2>
            <p class="text-sm text-gray-500">Ensure your account uses a strong password.</p>
        </div>

        <form action="{{ route('profile.password.update') }}" method="POST" class="px-6 py-5 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input type="password" name="current_password"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500
                              @error('current_password') border-red-400 @enderror">
                @error('current_password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" name="password"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500
                              @error('password') border-red-400 @enderror">
                @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
