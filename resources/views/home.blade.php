<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyLoanPoint — Fast & Trusted Loans in India</title>
    <meta name="description" content="Apply for personal loans, home loans, and business loans instantly with EasyLoanPoint. Track your application in real-time via our mobile app.">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        html { scroll-behavior: smooth; }
        .gradient-hero { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #0ea5e9 100%); }
        .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

{{-- ═══════════════════════════════════════════════════════════
     NAVBAR
════════════════════════════════════════════════════════════ --}}
<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-700 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-900">EasyLoan<span class="text-blue-700">Point</span></span>
            </div>

            {{-- Nav links --}}
            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                <a href="#features" class="hover:text-blue-700 transition-colors">Features</a>
                <a href="#how-it-works" class="hover:text-blue-700 transition-colors">How it Works</a>
                <a href="#download" class="hover:text-blue-700 transition-colors">Download App</a>
            </div>

            {{-- CTA --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}"
                   class="text-sm font-medium text-blue-700 hover:text-blue-800 transition-colors">
                    Staff Login
                </a>
                <a href="#download"
                   class="hidden sm:inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                    Get App
                </a>
            </div>
        </div>
    </div>
</nav>

{{-- ═══════════════════════════════════════════════════════════
     HERO
════════════════════════════════════════════════════════════ --}}
<section class="gradient-hero text-white py-20 sm:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        @if(session('error'))
            <div class="mb-8 inline-flex items-center gap-2 bg-red-100 text-red-800 text-sm px-4 py-2 rounded-full">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <span class="inline-block bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full mb-6 tracking-wider uppercase">
            Fast • Trusted • Transparent
        </span>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
            Loans Made Simple<br>
            <span class="text-blue-200">For Every Indian</span>
        </h1>
        <p class="text-lg sm:text-xl text-blue-100 max-w-2xl mx-auto mb-10 leading-relaxed">
            Apply for personal, home, or business loans in minutes.
            Track your application status in real-time — right from your phone.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#download"
               class="inline-flex items-center justify-center gap-2 bg-white text-blue-700 font-bold px-8 py-4 rounded-xl shadow-lg hover:bg-blue-50 transition-colors text-base">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download Android App
            </a>
            <a href="#how-it-works"
               class="inline-flex items-center justify-center gap-2 border-2 border-white/60 text-white font-semibold px-8 py-4 rounded-xl hover:bg-white/10 transition-colors text-base">
                Learn How It Works
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     STATS STRIP
════════════════════════════════════════════════════════════ --}}
<section class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <p class="text-3xl font-extrabold text-blue-700">₹50L+</p>
                <p class="text-sm text-gray-500 mt-1">Loans Disbursed</p>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-blue-700">1,200+</p>
                <p class="text-sm text-gray-500 mt-1">Happy Customers</p>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-blue-700">24 hrs</p>
                <p class="text-sm text-gray-500 mt-1">Avg. Approval Time</p>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-blue-700">100%</p>
                <p class="text-sm text-gray-500 mt-1">Online Process</p>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     FEATURES
════════════════════════════════════════════════════════════ --}}
<section id="features" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">Why Choose EasyLoanPoint?</h2>
            <p class="text-lg text-gray-500 max-w-2xl mx-auto">
                Everything you need to apply, track, and manage your loan — in one place.
            </p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

            @php
            $features = [
                ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => 'Instant Application', 'desc' => 'Fill out your loan application online in under 5 minutes. No paperwork, no branch visits.'],
                ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Real-Time Tracking', 'desc' => 'Track every step of your loan journey — from submission to disbursement — via our mobile app.'],
                ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'Digital KYC', 'desc' => 'Upload Aadhaar, PAN, and selfie directly from your phone. Secure and fully digital verification.'],
                ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'title' => 'Bank-Level Security', 'desc' => 'Your data is protected with enterprise-grade encryption and secure API authentication.'],
                ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Dedicated HR Support', 'desc' => 'Every application is assigned a dedicated loan officer who guides you through the process.'],
                ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'title' => 'Multiple Loan Types', 'desc' => 'Personal loans, home loans, business loans — choose what fits your needs with flexible EMI options.'],
            ];
            @endphp

            @foreach($features as $f)
            <div class="bg-white rounded-2xl p-7 shadow-sm border border-gray-100 card-hover">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $f['title'] }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed">{{ $f['desc'] }}</p>
            </div>
            @endforeach

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     HOW IT WORKS
════════════════════════════════════════════════════════════ --}}
<section id="how-it-works" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">How It Works</h2>
            <p class="text-lg text-gray-500 max-w-xl mx-auto">Get your loan approved in 4 simple steps.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @php
            $steps = [
                ['num' => '01', 'title' => 'Download App', 'desc' => 'Install the EasyLoanPoint Android app on your phone.'],
                ['num' => '02', 'title' => 'Register & KYC', 'desc' => 'Create your account and complete Aadhaar + PAN verification.'],
                ['num' => '03', 'title' => 'Apply for Loan', 'desc' => 'Fill out the loan application form and submit your documents.'],
                ['num' => '04', 'title' => 'Get Disbursed', 'desc' => 'Approved funds are transferred directly to your bank account.'],
            ];
            @endphp
            @foreach($steps as $step)
            <div class="text-center">
                <div class="w-14 h-14 bg-blue-700 text-white rounded-2xl flex items-center justify-center text-xl font-extrabold mx-auto mb-4">
                    {{ $step['num'] }}
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $step['title'] }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     APK DOWNLOAD SECTION
════════════════════════════════════════════════════════════ --}}
<section id="download" class="py-20 gradient-hero">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">

                {{-- Left: Info --}}
                <div class="p-10 lg:p-12">
                    <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full mb-6 uppercase tracking-wider">
                        Android App
                    </span>
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-4 leading-tight">
                        Download the<br>EasyLoanPoint App
                    </h2>
                    <p class="text-gray-500 text-sm leading-relaxed mb-8">
                        Manage your loan applications, upload documents, and track approval status — all from your Android phone. Free to download.
                    </p>

                    {{-- App Meta --}}
                    <div class="flex flex-wrap gap-4 mb-8">
                        <div class="bg-gray-50 rounded-xl px-4 py-3 text-center">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Version</p>
                            <p class="font-bold text-gray-800 text-sm">1.0.0</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 text-center">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Updated</p>
                            <p class="font-bold text-gray-800 text-sm">{{ \Carbon\Carbon::now()->format('d M Y') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 text-center">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Platform</p>
                            <p class="font-bold text-gray-800 text-sm">Android 6.0+</p>
                        </div>
                    </div>

                    {{-- Download Button --}}
                    <a href="{{ route('download.apk') }}"
                       class="inline-flex items-center gap-3 bg-blue-700 hover:bg-blue-800 text-white font-bold px-8 py-4 rounded-xl shadow-lg transition-colors text-base w-full sm:w-auto justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Android APK
                    </a>
                    <p class="text-xs text-gray-400 mt-3">Free download · No sign-up required to install</p>
                </div>

                {{-- Right: Install Instructions --}}
                <div class="bg-gray-50 p-10 lg:p-12 border-t lg:border-t-0 lg:border-l border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        How to Install APK on Android
                    </h3>
                    <ol class="space-y-5">
                        @php
                        $steps = [
                            ['title' => 'Download the APK', 'desc' => 'Tap the "Download Android APK" button and save the file to your phone.'],
                            ['title' => 'Allow Unknown Sources', 'desc' => 'Go to Settings → Security → Enable "Install from Unknown Sources" (or "Install Unknown Apps" on Android 8+).'],
                            ['title' => 'Open the APK File', 'desc' => 'Navigate to your Downloads folder and tap "EasyLoanPoint.apk" to begin installation.'],
                            ['title' => 'Install & Launch', 'desc' => 'Tap "Install" and wait for it to complete. Open the app and register with your mobile number.'],
                        ];
                        @endphp
                        @foreach($steps as $i => $step)
                        <li class="flex gap-4">
                            <div class="w-7 h-7 bg-blue-700 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                                {{ $i + 1 }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm mb-0.5">{{ $step['title'] }}</p>
                                <p class="text-xs text-gray-500 leading-relaxed">{{ $step['desc'] }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ol>

                    <div class="mt-8 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                        <p class="text-xs text-amber-700 leading-relaxed">
                            <strong>Note:</strong> This is a direct APK download outside the Play Store. The app is safe and developed by the EasyLoanPoint team. You can re-enable "Unknown Sources" restrictions after installation.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     FOOTER
════════════════════════════════════════════════════════════ --}}
<footer class="bg-gray-900 text-gray-400 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-700 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-white font-bold text-lg">EasyLoan<span class="text-blue-400">Point</span></span>
            </div>
            <nav class="flex flex-wrap gap-6 text-sm">
                <a href="#features" class="hover:text-white transition-colors">Features</a>
                <a href="#how-it-works" class="hover:text-white transition-colors">How it Works</a>
                <a href="#download" class="hover:text-white transition-colors">Download App</a>
                <a href="{{ route('login') }}" class="hover:text-white transition-colors">Staff Login</a>
            </nav>
            <p class="text-sm text-center">© {{ date('Y') }} EasyLoanPoint. All rights reserved.</p>
        </div>
    </div>
</footer>

</body>
</html>
