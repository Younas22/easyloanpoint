<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO --}}
    <title>{{ $settings['website_name'] ?? 'EasyLoanPoint' }} — Fast & Trusted Loans in India</title>
    <meta name="description" content="Apply for personal, home, and business loans instantly online. Track your application in real-time via the EasyLoanPoint mobile app. 100% digital, fast approval.">
    <meta name="keywords" content="personal loan India, home loan, business loan, instant loan app, loan online India, fast loan approval">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">

    {{-- Open Graph --}}
    <meta property="og:type"        content="website">
    <meta property="og:title"       content="{{ $settings['website_name'] ?? 'EasyLoanPoint' }} — Fast & Trusted Loans in India">
    <meta property="og:description" content="Apply for personal, home, and business loans instantly. 100% digital process with real-time tracking.">
    <meta property="og:url"         content="{{ url('/') }}">
    @if(!empty($settings['company_logo']))
    <meta property="og:image"       content="{{ asset('public/' . $settings['company_logo']) }}">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $settings['website_name'] ?? 'EasyLoanPoint' }} — Fast & Trusted Loans">
    <meta name="twitter:description" content="Apply for personal, home, and business loans instantly. 100% digital process.">

    {{-- Favicon --}}
    @if(!empty($settings['favicon']))
        <link rel="icon" type="image/png" href="{{ asset('public/' . $settings['favicon']) }}">
    @endif

    {{-- Schema Markup --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "FinancialService",
        "name": "{{ $settings['website_name'] ?? 'EasyLoanPoint' }}",
        "description": "Fast and trusted loan services in India with real-time application tracking.",
        "url": "{{ url('/') }}",
        "areaServed": "IN",
        "serviceType": ["Easy Loan", "Personal Loan", "Home Loan", "Business Loan"]
    }
    </script>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

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
                            900: '#0D2258',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        * { font-family: 'Inter', system-ui, sans-serif; }
        html { scroll-behavior: smooth; }

        /* ── Brand colours ── */
        .gradient-hero  { background: linear-gradient(135deg, #0D2258 0%, #1457FB 58%, #0A3FD6 100%); }
        .gradient-brand { background: linear-gradient(135deg, #0D2258, #1457FB); }
        .gradient-text  {
            background: linear-gradient(135deg, #1457FB, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Card hover ── */
        .card-hover { transition: transform .25s ease, box-shadow .25s ease; }
        .card-hover:hover { transform: translateY(-6px); box-shadow: 0 24px 48px rgba(20,87,251,.13); }

        /* ── Phone mockup ── */
        .phone-shadow { filter: drop-shadow(0 28px 56px rgba(20,87,251,.28)); }
        .phone-frame  { border-radius: 2.25rem; border: 3px solid rgba(255,255,255,.92); }

        /* ── Hero glassmorphism stat card ── */
        .stat-card {
            background: rgba(255,255,255,.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,.15);
        }

        /* ── Trust badge ── */
        .trust-badge {
            background: rgba(255,255,255,.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,.2);
        }

        /* ── Section label pill ── */
        .section-label {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            color: #1457FB;
        }

        /* ── Pulsing live dot ── */
        @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.35} }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }

        /* ── Nav scroll shadow ── */
        .nav-scrolled { box-shadow: 0 4px 24px rgba(0,0,0,.08); }

        /* ── Step connector (desktop) ── */
        .step-wrap { position: relative; }
        .step-connector::after {
            content: '';
            position: absolute;
            top: 27px;
            left: calc(50% + 32px);
            width: calc(100% - 64px);
            height: 2px;
            background: linear-gradient(90deg, #1457FB 0%, #bfdbfe 100%);
        }

        @media (max-width: 639px) {
            .step-connector::after { display: none; }
        }

        /* ── Lazy image fade-in ── */
        img[loading="lazy"] { opacity: 0; transition: opacity .4s ease; }
        img[loading="lazy"].loaded { opacity: 1; }

        /* ── Download badge ── */
        .download-safe { background: linear-gradient(135deg, #ecfdf5, #d1fae5); color: #065f46; }

        /* ── Feature icon bg ── */
        .feat-icon { background: linear-gradient(135deg, #eff6ff, #dbeafe); }
    </style>
</head>
<body class="bg-slate-50 text-gray-800 antialiased">

{{-- ══════════════════════════════════════
     NAVBAR
══════════════════════════════════════ --}}
<nav id="navbar" class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 transition-shadow duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="#" class="flex items-center gap-2.5 flex-shrink-0">
                @if(!empty($settings['company_logo']))
                    <img src="{{ asset('public/' . $settings['company_logo']) }}"
                         alt="{{ $settings['company_name'] ?? 'EasyLoanPoint' }} Logo"
                         class="h-9 w-9 rounded-xl object-cover">
                @else
                    <div class="w-9 h-9 rounded-xl gradient-brand flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                        </svg>
                    </div>
                @endif
                <span class="text-lg font-bold text-gray-900 tracking-tight">{{ $settings['website_name'] ?? 'EasyLoanPoint' }}</span>
            </a>

            {{-- Desktop nav links --}}
            <div class="hidden md:flex items-center gap-7 text-sm font-medium text-gray-600">
                <a href="#features"     class="hover:text-blue-700 transition-colors duration-200">Features</a>
                <a href="#how-it-works" class="hover:text-blue-700 transition-colors duration-200">How It Works</a>
                <a href="#app"          class="hover:text-blue-700 transition-colors duration-200">App</a>
                <a href="#about"        class="hover:text-blue-700 transition-colors duration-200">About</a>
                <a href="#download"     class="hover:text-blue-700 transition-colors duration-200">Download</a>
            </div>

            <div class="flex items-center gap-2">
                {{-- CTA --}}
                <a href="#download"
                   class="inline-flex items-center gap-2 gradient-brand text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm hover:opacity-90 hover:shadow-lg hover:shadow-blue-200/50 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Get App
                </a>

                {{-- Mobile hamburger --}}
                <button id="mobileMenuBtn"
                        class="md:hidden p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                        aria-label="Open menu">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobileMenu" class="hidden md:hidden border-t border-gray-100 py-2 pb-4">
            <div class="flex flex-col gap-0.5">
                @foreach([['#features','Features'],['#how-it-works','How It Works'],['#app','App'],['#about','About'],['#download','Download']] as $link)
                <a href="{{ $link[0] }}"
                   onclick="document.getElementById('mobileMenu').classList.add('hidden')"
                   class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-lg transition-colors">
                    {{ $link[1] }}
                </a>
                @endforeach
            </div>
        </div>
    </div>
</nav>

{{-- ══════════════════════════════════════
     HERO
══════════════════════════════════════ --}}
<section class="gradient-hero text-white relative overflow-hidden">

    {{-- Decorative blobs --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute -top-28 -right-28 w-96 h-96 rounded-full opacity-10" style="background:radial-gradient(circle,#93c5fd,transparent)"></div>
        <div class="absolute -bottom-36 -left-36 w-80 h-80 rounded-full opacity-10" style="background:radial-gradient(circle,#60a5fa,transparent)"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] rounded-full opacity-5" style="background:radial-gradient(circle,#bfdbfe,transparent)"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-10 sm:pt-22 sm:pb-14 relative">

        {{-- Error alert --}}
        @if(session('error'))
        <div class="mb-6 inline-flex items-center gap-2 bg-red-500/20 border border-red-400/30 text-red-100 text-sm px-4 py-2.5 rounded-xl">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
        @endif

        <div class="max-w-4xl mx-auto text-center">

            {{-- Live trust badge --}}
            <div class="inline-flex items-center gap-2 trust-badge rounded-full px-4 py-1.5 mb-6 text-xs font-semibold tracking-widest uppercase">
                <span class="w-1.5 h-1.5 bg-green-400 rounded-full pulse-dot"></span>
                Trusted by 1,200+ Borrowers Across India
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-extrabold leading-[1.1] tracking-tight mb-5">
                @if(!empty($settings['hero_title']))
                    {!! nl2br(e($settings['hero_title'])) !!}
                @else
                    Loans Made <span style="color:#93c5fd">Simple</span><br>For Every Indian
                @endif
            </h1>

            <p class="text-base sm:text-lg text-blue-100/90 max-w-2xl mx-auto mb-8 leading-relaxed">
                @if(!empty($settings['hero_subtitle']))
                    {{ $settings['hero_subtitle'] }}
                @else
                    Apply for personal, home, or business loans in minutes. Track your status in real-time — no paperwork, no branch visits.
                @endif
            </p>

            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 justify-center mb-10">
                <a href="#download"
                   class="inline-flex items-center justify-center gap-2.5 bg-white text-blue-700 font-bold px-8 py-3.5 rounded-xl shadow-xl hover:bg-blue-50 hover:shadow-2xl transition-all duration-200 text-sm sm:text-base">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Android App
                </a>
                <a href="#how-it-works"
                   class="inline-flex items-center justify-center gap-2 border border-white/30 bg-white/10 backdrop-blur-sm text-white font-semibold px-8 py-3.5 rounded-xl hover:bg-white/20 transition-all duration-200 text-sm sm:text-base">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                    See How It Works
                </a>
            </div>

            {{-- Trust icons --}}
            <div class="flex flex-wrap items-center justify-center gap-5 sm:gap-8">
                @foreach([
                    ['M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z', '100% Online'],
                    ['M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z', 'Bank-Level Security'],
                    ['M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z', 'Approval in 24 Hrs'],
                    ['M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z', '4.8★ Rating'],
                ] as $badge)
                <div class="flex items-center gap-1.5 text-blue-200/80 text-xs font-medium">
                    <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="{{ $badge[0] }}" clip-rule="evenodd"/>
                    </svg>
                    {{ $badge[1] }}
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Stats strip --}}
    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-5">
                @foreach([
                    ['₹50L+',  'Loans Disbursed'],
                    ['1,200+', 'Happy Customers'],
                    ['24 Hrs', 'Avg. Approval Time'],
                    ['100%',   'Online & Paperless'],
                ] as $stat)
                <div class="stat-card rounded-2xl px-4 py-4 text-center">
                    <p class="text-2xl sm:text-3xl font-extrabold text-white">{{ $stat[0] }}</p>
                    <p class="text-xs text-blue-200/70 mt-1 font-medium">{{ $stat[1] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════
     FEATURES
══════════════════════════════════════ --}}
<section id="features" class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-12">
            <span class="section-label text-xs font-bold px-3 py-1.5 rounded-full mb-4 inline-block uppercase tracking-widest">Why Us</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight mb-3">
                Why Choose <span class="gradient-text">{{ $settings['website_name'] ?? 'EasyLoanPoint' }}</span>?
            </h2>
            <p class="text-gray-500 max-w-xl mx-auto text-base leading-relaxed">
                Everything you need to apply, track, and manage your loan — in one seamless platform.
            </p>
        </div>

        @php
        $features = [
            ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z',                                                                                                                              'badge' => '5 Min Apply',    'title' => 'Instant Application',    'desc' => 'Complete your loan application entirely online in under 5 minutes — zero paperwork, no branch visits required.'],
            ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                          'badge' => 'Live Updates',   'title' => 'Real-Time Tracking',     'desc' => 'Monitor every stage of your loan journey — from submission to disbursement — live from the app.'],
            ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',                                'badge' => 'Paperless',      'title' => 'Digital KYC',            'desc' => 'Upload Aadhaar, PAN, and selfie directly from your phone. Fully secure, instant digital verification.'],
            ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',                                               'badge' => '256-bit SSL',    'title' => 'Bank-Level Security',    'desc' => 'Your personal and financial data is protected with enterprise-grade encryption and secure authentication.'],
            ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'badge' => '1-on-1 Support', 'title' => 'Dedicated Loan Officer', 'desc' => 'Every application is assigned a personal loan officer who guides you step by step to approval.'],
            ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',                                                              'badge' => '3 Loan Types',   'title' => 'Flexible Loan Options',  'desc' => 'Personal, home, and business loans with flexible EMI plans tailored to your income and repayment capacity.'],
        ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($features as $f)
            <div class="group bg-white border border-gray-100 rounded-2xl p-6 card-hover cursor-default relative overflow-hidden shadow-sm">
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl pointer-events-none" style="background:linear-gradient(135deg,rgba(20,87,251,.025),rgba(20,87,251,.06))"></div>
                <div class="relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-11 h-11 feat-icon rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" style="color:#1457FB" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="section-label text-[10px] font-bold px-2.5 py-1 rounded-full flex-shrink-0">{{ $f['badge'] }}</span>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-2 group-hover:text-blue-700 transition-colors duration-200">{{ $f['title'] }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $f['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════
     HOW IT WORKS
══════════════════════════════════════ --}}
<section id="how-it-works" class="py-16 sm:py-20" style="background:linear-gradient(180deg,#f8faff 0%,#eef2ff 100%)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-12">
            <span class="section-label text-xs font-bold px-3 py-1.5 rounded-full mb-4 inline-block uppercase tracking-widest">Process</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight mb-3">Get Your Loan in 4 Steps</h2>
            <p class="text-gray-500 max-w-xl mx-auto text-base">A simple, transparent process — from application to money in your account.</p>
        </div>

        @php
        $steps = [
            ['icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4',                                                                                        'title' => 'Download App',   'desc' => 'Install the free EasyLoanPoint Android app and create your account in seconds.'],
            ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',                                'title' => 'Complete KYC',   'desc' => 'Upload Aadhaar, PAN, and selfie for instant, secure digital verification.'],
            ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',                    'title' => 'Apply for Loan', 'desc' => 'Select your loan type, enter the amount, and submit your digital application.'],
            ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',                                                              'title' => 'Get Disbursed',  'desc' => 'Approved funds are transferred directly to your bank account within hours.'],
        ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($steps as $idx => $step)
            <div class="relative flex flex-col items-center text-center step-wrap @if(!$loop->last) step-connector @endif">

                {{-- Step icon circle --}}
                <div class="relative z-10 w-14 h-14 gradient-brand rounded-2xl flex items-center justify-center shadow-lg shadow-blue-300/40 mx-auto mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $step['icon'] }}"/>
                    </svg>
                    <span class="absolute -top-2 -right-2 w-5 h-5 bg-white text-blue-700 text-[10px] font-extrabold rounded-full flex items-center justify-center border-2 border-blue-100 shadow">{{ $idx + 1 }}</span>
                </div>

                {{-- Step card --}}
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm w-full max-w-xs mx-auto">
                    <h3 class="text-base font-bold text-gray-900 mb-1.5">{{ $step['title'] }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-10">
            <a href="#download"
               class="inline-flex items-center gap-2 gradient-brand text-white font-semibold px-7 py-3.5 rounded-xl shadow-lg hover:opacity-90 hover:shadow-xl hover:shadow-blue-200/50 transition-all duration-200 text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Start Your Loan Journey
            </a>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════
     APP SHOWCASE
══════════════════════════════════════ --}}
<section id="app" class="py-16 sm:py-20 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-16 lg:items-center">

            {{-- Left: Text content --}}
            <div class="mb-14 lg:mb-0">
                <span class="section-label text-xs font-bold px-3 py-1.5 rounded-full mb-4 inline-block uppercase tracking-widest">Mobile App</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight mb-4 leading-tight">
                    Manage Your Loans<br><span class="gradient-text">From Your Phone</span>
                </h2>
                <p class="text-gray-500 mb-7 leading-relaxed text-base">
                    @if(!empty($settings['download_section']))
                        {{ $settings['download_section'] }}
                    @else
                        The EasyLoanPoint app gives you full control — apply, track, and manage loans right from your Android device. Clean, intuitive, and built for speed.
                    @endif
                </p>

                <ul class="space-y-3 mb-8">
                    @foreach([
                        'Apply and submit loan applications in minutes',
                        'Track real-time status updates at every step',
                        'Upload and manage KYC documents securely',
                        'Instant push notifications on approvals',
                        'Secure OTP-based mobile login',
                    ] as $item)
                    <li class="flex items-center gap-3 text-sm text-gray-700">
                        <div class="w-5 h-5 rounded-full gradient-brand flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>

                <a href="#download"
                   class="inline-flex items-center gap-2.5 gradient-brand text-white font-bold px-7 py-3.5 rounded-xl shadow-lg hover:opacity-90 hover:shadow-xl transition-all duration-200 text-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Free App
                </a>
            </div>

            {{-- Right: Phone screenshots --}}
            <div class="relative flex items-end justify-center gap-3 sm:gap-5 min-h-72">
                {{-- Glow bg --}}
                <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
                    <div class="absolute inset-0 rounded-3xl opacity-15" style="background:radial-gradient(ellipse at 50% 70%,#1457FB,transparent 70%)"></div>
                </div>

                {{-- Screenshot 1 --}}
                <div class="phone-shadow transform -rotate-6 hover:-rotate-2 transition-transform duration-300 flex-shrink-0 z-10 mt-10">
                    <div class="w-36 sm:w-44 phone-frame overflow-hidden shadow-2xl">
                        <img src="{{ asset('public/uploads/1.jpeg') }}"
                             alt="EasyLoanPoint App Welcome Screen"
                             loading="lazy"
                             class="w-full object-cover">
                    </div>
                </div>

                {{-- Screenshot 2 — centre elevated --}}
                <div class="phone-shadow flex-shrink-0 z-20" style="transform:scale(1.1)">
                    <div class="w-40 sm:w-52 phone-frame overflow-hidden shadow-2xl">
                        <img src="{{ asset('public/uploads/2.jpeg') }}"
                             alt="EasyLoanPoint App Dashboard"
                             loading="lazy"
                             class="w-full object-cover">
                    </div>
                </div>

                {{-- Screenshot 3 --}}
                <div class="phone-shadow transform rotate-6 hover:rotate-2 transition-transform duration-300 flex-shrink-0 z-10 mt-10">
                    <div class="w-36 sm:w-44 phone-frame overflow-hidden shadow-2xl">
                        <img src="{{ asset('public/uploads/3.jpeg') }}"
                             alt="EasyLoanPoint App Loan Status"
                             loading="lazy"
                             class="w-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════
     ABOUT  (+ contact embedded when available)
══════════════════════════════════════ --}}
@if(!empty($settings['about_section']))
<section id="about" class="py-16 sm:py-20" style="background:linear-gradient(180deg,#f8faff 0%,#eef2ff 100%)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-16 lg:items-start">

            {{-- Left: About text --}}
            <div>
                <span class="section-label text-xs font-bold px-3 py-1.5 rounded-full mb-4 inline-block uppercase tracking-widest">About Us</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight mb-5 leading-tight">
                    Building Financial Access<br><span class="gradient-text">For Every Indian</span>
                </h2>
                <div class="text-gray-600 leading-relaxed text-base whitespace-pre-line mb-7">{{ $settings['about_section'] }}</div>

                @if(!empty($settings['contact_info']))
                <div class="p-4 bg-blue-50 border border-blue-100 rounded-2xl">
                    <p class="text-sm font-semibold text-blue-800 mb-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Contact Us
                    </p>
                    <p class="text-sm text-blue-700 leading-relaxed whitespace-pre-line">{{ $settings['contact_info'] }}</p>
                </div>
                @endif
            </div>

            {{-- Right: Mission / Vision / Commitment cards --}}
            <div class="mt-10 lg:mt-0 space-y-4">
                @php
                $pillars = [
                    ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z',                                                                                                                                   'color' => '#1457FB', 'bg' => '#eff6ff', 'title' => 'Our Mission',     'text' => 'To make financial services accessible, transparent, and instant for every Indian — regardless of location or income.'],
                    ['icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', 'color' => '#059669', 'bg' => '#ecfdf5', 'title' => 'Our Vision',      'text' => 'A future where every Indian has seamless access to credit — empowering them to achieve personal and business goals.'],
                    ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'title' => 'Our Commitment', 'text' => 'Complete transparency, zero hidden charges, and dedicated support at every step of your loan journey.'],
                ];
                @endphp
                @foreach($pillars as $p)
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex gap-4 card-hover">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:{{ $p['bg'] }}">
                        <svg class="w-5 h-5" style="color:{{ $p['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $p['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 mb-1">{{ $p['title'] }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $p['text'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@else
    {{-- Standalone contact section when no about_section --}}
    @if(!empty($settings['contact_info']))
    <section id="about" class="py-14 bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-extrabold text-gray-900 mb-3">Contact Us</h2>
            <p class="text-gray-600 leading-relaxed whitespace-pre-line text-sm">{{ $settings['contact_info'] }}</p>
        </div>
    </section>
    @endif
@endif

{{-- ══════════════════════════════════════
     APK DOWNLOAD
══════════════════════════════════════ --}}
<section id="download" class="py-16 sm:py-20 gradient-hero relative overflow-hidden">

    {{-- Blobs --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute -top-16 -right-16 w-72 h-72 rounded-full opacity-10" style="background:radial-gradient(circle,#93c5fd,transparent)"></div>
        <div class="absolute -bottom-16 -left-16 w-64 h-64 rounded-full opacity-10" style="background:radial-gradient(circle,#60a5fa,transparent)"></div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative">

        {{-- Section header --}}
        <div class="text-center mb-10">
            <span class="inline-flex items-center gap-1.5 bg-white/15 border border-white/20 text-white text-xs font-bold px-4 py-1.5 rounded-full mb-4 uppercase tracking-widest">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"/>
                </svg>
                Free Download
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Download the App</h2>
            <p class="text-blue-100/80 mt-2 text-base">Available on Android · Free to install · No sign-up needed to try</p>
        </div>

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-5">

                {{-- Left: App info & download --}}
                <div class="lg:col-span-3 p-8 lg:p-10">

                    {{-- App identity --}}
                    <div class="flex items-center gap-4 mb-7">
                        <div class="w-14 h-14 gradient-brand rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200/60 flex-shrink-0">
                            @if(!empty($settings['company_logo']))
                                <img src="{{ asset('public/' . $settings['company_logo']) }}" alt="App Icon" class="w-10 h-10 rounded-xl object-cover">
                            @else
                                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-lg font-extrabold text-gray-900 truncate">{{ $settings['website_name'] ?? 'EasyLoanPoint' }}</p>
                            <p class="text-sm text-gray-500">Android App — Loan Management</p>
                        </div>
                        <span class="download-safe text-xs font-bold px-3 py-1.5 rounded-full flex-shrink-0 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Verified
                        </span>
                    </div>

                    {{-- Version info --}}
                    <div class="grid grid-cols-3 gap-3 mb-7">
                        @foreach([
                            ['Version',  $settings['apk_version'] ?? '1.0.0'],
                            ['Updated',  \Carbon\Carbon::now()->format('d M Y')],
                            ['Requires', 'Android 6+'],
                        ] as $meta)
                        <div class="bg-slate-50 rounded-xl p-3 text-center border border-gray-100">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mb-1">{{ $meta[0] }}</p>
                            <p class="font-bold text-gray-800 text-sm">{{ $meta[1] }}</p>
                        </div>
                        @endforeach
                    </div>

                    {{-- Download CTA --}}
                    <a href="{{ route('download.apk') }}"
                       class="flex items-center justify-center gap-3 gradient-brand text-white font-bold px-8 py-4 rounded-2xl shadow-lg hover:opacity-90 hover:shadow-xl hover:shadow-blue-300/30 transition-all duration-200 text-base w-full mb-3">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Android APK
                    </a>
                    <p class="text-center text-xs text-gray-400">
                        Free download &middot; Safe &amp; verified &middot; Official app by {{ $settings['company_name'] ?? 'EasyLoanPoint' }}
                    </p>
                </div>

                {{-- Right: Install steps --}}
                <div class="lg:col-span-2 bg-slate-50 p-8 lg:p-10 border-t lg:border-t-0 lg:border-l border-gray-100">
                    <h3 class="text-sm font-bold text-gray-800 mb-6 flex items-center gap-2.5">
                        <div class="w-6 h-6 gradient-brand rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        How to Install APK
                    </h3>

                    @php
                    $installSteps = [
                        ['Download the APK',        'Tap the download button and save the APK file to your device.'],
                        ['Allow Unknown Sources',   'Go to Settings → Security → Enable "Install from Unknown Sources".'],
                        ['Open the APK File',       'Open your Downloads folder and tap the APK to begin installation.'],
                        ['Install & Launch',        'Tap "Install", open the app, and register with your mobile number.'],
                    ];
                    @endphp

                    <ol class="space-y-4">
                        @foreach($installSteps as $i => $step)
                        <li class="flex gap-3">
                            <div class="w-6 h-6 gradient-brand text-white rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">{{ $i + 1 }}</div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm leading-tight mb-0.5">{{ $step[0] }}</p>
                                <p class="text-xs text-gray-500 leading-relaxed">{{ $step[1] }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ol>

                    <div class="mt-6 p-3.5 bg-amber-50 border border-amber-200 rounded-xl">
                        <p class="text-xs text-amber-700 leading-relaxed">
                            <strong>Security Note:</strong> This is the official APK by {{ $settings['company_name'] ?? 'EasyLoanPoint' }}. Always download only from this page.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════
     FOOTER
══════════════════════════════════════ --}}
<footer class="bg-gray-950 text-gray-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-6">

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 pb-8 border-b border-gray-800/70">

            {{-- Brand --}}
            <div class="sm:col-span-2">
                <div class="flex items-center gap-2.5 mb-4">
                    @if(!empty($settings['company_logo']))
                        <img src="{{ asset('public/' . $settings['company_logo']) }}"
                             alt="{{ $settings['company_name'] ?? 'EasyLoanPoint' }} Logo"
                             class="h-8 w-8 rounded-xl object-cover">
                    @else
                        <div class="w-8 h-8 rounded-xl gradient-brand flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                            </svg>
                        </div>
                    @endif
                    <span class="text-white font-bold text-lg tracking-tight">{{ $settings['website_name'] ?? 'EasyLoanPoint' }}</span>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed max-w-xs mb-5">
                    Fast, trusted, and transparent loan services for every Indian. Apply online, track in real-time, get disbursed faster.
                </p>
                {{-- Social links --}}
                <div class="flex gap-2.5">
                    @foreach([
                        ['Facebook',  'hover:bg-blue-600',  'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z'],
                        ['Twitter',   'hover:bg-sky-500',   'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z'],
                        ['Instagram', 'hover:bg-pink-600',  'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6z M2 9h4v12H2z M4 6a2 2 0 100-4 2 2 0 000 4z'],
                    ] as $social)
                    <a href="#" aria-label="{{ $social[0] }}"
                       class="w-8 h-8 bg-gray-800 {{ $social[1] }} rounded-lg flex items-center justify-center transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $social[2] }}"/>
                        </svg>
                    </a>
                    @endforeach
                    {{-- WhatsApp --}}
                    <a href="#" aria-label="WhatsApp"
                       class="w-8 h-8 bg-gray-800 hover:bg-green-600 rounded-lg flex items-center justify-center transition-colors duration-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Quick links --}}
            <div>
                <p class="text-white text-sm font-semibold mb-4">Quick Links</p>
                <ul class="space-y-2.5 text-sm">
                    @foreach([['#features','Features'],['#how-it-works','How It Works'],['#app','App'],['#about','About Us'],['#download','Download APK']] as $link)
                    <li><a href="{{ $link[0] }}" class="hover:text-white transition-colors duration-200">{{ $link[1] }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Services --}}
            <div>
                <p class="text-white text-sm font-semibold mb-4">Loan Services</p>
                <ul class="space-y-2.5 text-sm">
                    @foreach(['Personal Loan','Home Loan','Business Loan','Real-Time Tracking','Digital KYC'] as $service)
                    <li><span class="hover:text-white transition-colors duration-200 cursor-default">{{ $service }}</span></li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-5 text-xs">
            <p>© {{ date('Y') }} {{ $settings['company_name'] ?? 'EasyLoanPoint' }}. All rights reserved.</p>
            <p class="text-gray-600">Empowering India with digital finance.</p>
        </div>
    </div>
</footer>

{{-- ══ Scripts ══ --}}
<script>
    // Navbar scroll shadow
    const nav = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('nav-scrolled', window.scrollY > 10);
    }, { passive: true });

    // Mobile menu toggle
    document.getElementById('mobileMenuBtn').addEventListener('click', () => {
        document.getElementById('mobileMenu').classList.toggle('hidden');
    });

    // Lazy image fade-in
    document.querySelectorAll('img[loading="lazy"]').forEach(img => {
        if (img.complete) img.classList.add('loaded');
        else img.addEventListener('load', () => img.classList.add('loaded'));
    });
</script>

</body>
</html>
