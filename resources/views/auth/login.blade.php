@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<div class="flex min-h-screen">

    {{-- ── Left panel: branding ──────────────────────────────────── --}}
    <div class="hidden w-1/2 flex-col justify-between bg-slate-900 p-12 lg:flex">

        {{-- Logo --}}
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-xl font-bold text-white">EasyLoanPoint</span>
        </div>

        {{-- Centre: headline --}}
        <div>
            <h2 class="text-4xl font-bold leading-tight text-white">
                Simplifying Loans,<br>
                <span class="text-blue-400">Empowering Lives.</span>
            </h2>
            <p class="mt-4 max-w-sm text-slate-400 leading-relaxed">
                A complete loan management solution designed for modern Indian finance companies.
                Manage customers, track applications, and disburse loans — all in one place.
            </p>

            {{-- Feature bullets --}}
            <ul class="mt-8 space-y-3">
                @foreach(['Instant loan application tracking', 'Aadhaar & PAN verification ready', 'Role-based access for Admin & HR', 'Real-time notifications & reports'] as $feature)
                    <li class="flex items-center gap-3 text-sm text-slate-300">
                        <span class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-blue-600/20">
                            <svg class="h-3 w-3 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        {{ $feature }}
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Bottom tagline --}}
        <p class="text-xs text-slate-600">
            &copy; {{ date('Y') }} EasyLoanPoint. All rights reserved.
        </p>
    </div>

    {{-- ── Right panel: login form ────────────────────────────────── --}}
    <div class="flex w-full flex-col items-center justify-center bg-white px-6 py-12 lg:w-1/2 lg:px-16">

        {{-- Mobile logo --}}
        <div class="mb-8 flex items-center gap-3 lg:hidden">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-lg font-bold text-slate-900">EasyLoanPoint</span>
        </div>

        <div class="w-full max-w-sm">

            {{-- Heading --}}
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Welcome back</h1>
                <p class="mt-1 text-sm text-gray-500">Sign in to your account to continue</p>
            </div>

            {{-- Flash messages --}}
            @if(session('error'))
                <div class="mb-5 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Login form --}}
            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Email Address
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="admin@gmail.com"
                        autocomplete="email"
                        required
                        class="block w-full rounded-lg border px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 outline-none transition-colors
                               {{ $errors->has('email') ? 'border-red-400 bg-red-50 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-gray-300 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500' }}"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <div class="mb-1.5 flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <a href="#" class="text-xs font-medium text-blue-600 hover:text-blue-500">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                            class="block w-full rounded-lg border px-4 py-2.5 pr-10 text-sm text-gray-900 placeholder-gray-400 outline-none transition-colors
                                   {{ $errors->has('password') ? 'border-red-400 bg-red-50 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-gray-300 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500' }}"
                        >
                        <button type="button"
                                onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg id="eye-icon" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="flex items-center gap-2">
                    <input type="checkbox"
                           id="remember"
                           name="remember"
                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="remember" class="text-sm text-gray-600">Keep me signed in</label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Sign In to Dashboard
                </button>
            </form>

            {{-- Divider --}}
            <div class="mt-8 border-t border-gray-100 pt-6">
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-center">
                        <p class="text-xs font-semibold text-gray-500">Admin Login</p>
                        <p class="mt-0.5 text-xs text-gray-400">admin@gmail.com</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-center">
                        <p class="text-xs font-semibold text-gray-500">HR Login</p>
                        <p class="mt-0.5 text-xs text-gray-400">hr@easyloanpoint.com</p>
                    </div>
                </div>
                <p class="mt-3 text-center text-xs text-gray-400">Default password: <span class="font-mono font-semibold text-gray-600">12345678</span></p>
            </div>

        </div>
    </div>

</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>
@endsection
