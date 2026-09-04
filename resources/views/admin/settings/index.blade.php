@extends('layouts.app')

@section('title', 'System Settings')

@section('page-header')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">System Settings</h1>
            <p class="mt-0.5 text-sm text-gray-500">Manage all application settings from one place.</p>
        </div>
    </div>
@endsection

@section('content')

{{-- Toast Notification --}}
<div id="toast"
     class="fixed top-5 right-5 z-50 hidden max-w-sm rounded-xl border px-5 py-4 shadow-lg transition-all duration-300">
    <div class="flex items-start gap-3">
        <span id="toast-icon" class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full">
            <svg id="toast-icon-ok"  class="h-3.5 w-3.5 hidden" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <svg id="toast-icon-err" class="h-3.5 w-3.5 hidden" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
        </span>
        <p id="toast-msg" class="text-sm font-medium leading-snug"></p>
    </div>
</div>

<div class="flex gap-6 flex-col xl:flex-row">

    {{-- ── Left: Vertical Tab Nav ─────────────────────────────────────────── --}}
    <div class="xl:w-56 flex-shrink-0">
        <nav class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <p class="px-4 pt-4 pb-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Settings</p>
            @php
                $tabs = [
                    ['id' => 'general',       'label' => 'General',        'icon' => 'globe',     'show' => true],
                    ['id' => 'loan',          'label' => 'Loan',           'icon' => 'currency',  'show' => false],
                    ['id' => 'bank',          'label' => 'Payment Bank',   'icon' => 'bank',      'show' => true],
                    ['id' => 'apk',           'label' => 'APK / App',      'icon' => 'device',    'show' => true],
                    ['id' => 'smtp',          'label' => 'SMTP Email',     'icon' => 'mail',      'show' => false],
                    ['id' => 'sms',           'label' => 'SMS / OTP',      'icon' => 'chat',      'show' => true],
                    ['id' => 'notifications', 'label' => 'Notifications',  'icon' => 'bell',      'show' => false],
                    ['id' => 'homepage',      'label' => 'Homepage',       'icon' => 'home',      'show' => true],
                    ['id' => 'security',      'label' => 'Security',       'icon' => 'shield',    'show' => false],
                    ['id' => 'system',        'label' => 'System Tools',   'icon' => 'terminal',  'show' => auth()->user()->isSuperAdmin()],
                ];
            @endphp
            <ul class="pb-3">
                @php $firstVisible = true; @endphp
                @foreach($tabs as $tab)
                    <li @if(!($tab['show'] ?? true)) style="display:none" @endif>
                        <button type="button"
                                data-tab="{{ $tab['id'] }}"
                                onclick="switchTab('{{ $tab['id'] }}')"
                                class="tab-btn group flex w-full items-center gap-3 px-4 py-2.5 text-sm font-medium transition-colors
                                       {{ ($tab['show'] ?? true) && $firstVisible ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        @if(($tab['show'] ?? true) && $firstVisible) @php $firstVisible = false; @endphp @endif
                            <span class="flex h-5 w-5 flex-shrink-0 items-center justify-center">
                                @if($tab['icon'] === 'globe')
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                @elseif($tab['icon'] === 'currency')
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @elseif($tab['icon'] === 'bank')
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                                @elseif($tab['icon'] === 'device')
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                @elseif($tab['icon'] === 'mail')
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                @elseif($tab['icon'] === 'chat')
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                @elseif($tab['icon'] === 'bell')
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                @elseif($tab['icon'] === 'home')
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                @elseif($tab['icon'] === 'shield')
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                @elseif($tab['icon'] === 'terminal')
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @endif
                            </span>
                            {{ $tab['label'] }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>

    {{-- ── Right: Tab Panels ───────────────────────────────────────────────── --}}
    <div class="flex-1 min-w-0">

        {{-- ════ TAB 1 — General Settings ════ --}}
        <div id="panel-general" class="tab-panel">
            <form class="settings-form" action="{{ route('admin.settings.update', 'general') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-gray-900">General Settings</h2>
                        <p class="text-sm text-gray-500">Basic information about your company and website.</p>
                    </div>
                    <div class="px-6 py-6 space-y-5">

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label class="s-label">Website Name <span class="text-red-500">*</span></label>
                                <input type="text" name="website_name" value="{{ $settings['website_name'] ?? '' }}"
                                       class="s-input" placeholder="EasyLoanPoint" required>
                            </div>
                            <div>
                                <label class="s-label">Company Name <span class="text-red-500">*</span></label>
                                <input type="text" name="company_name" value="{{ $settings['company_name'] ?? '' }}"
                                       class="s-input" placeholder="EasyLoanPoint Pvt Ltd" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label class="s-label">Support Email <span class="text-red-500">*</span></label>
                                <input type="email" name="support_email" value="{{ $settings['support_email'] ?? '' }}"
                                       class="s-input" placeholder="support@company.com" required>
                            </div>
                            <div>
                                <label class="s-label">Support Phone <span class="text-red-500">*</span></label>
                                <input type="text" name="support_phone" value="{{ $settings['support_phone'] ?? '' }}"
                                       class="s-input" placeholder="+91 9999999999" required>
                            </div>
                        </div>

                        <div>
                            <label class="s-label">Address</label>
                            <textarea name="address" rows="3" class="s-input resize-none"
                                      placeholder="Company full address…">{{ $settings['address'] ?? '' }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- Company Logo --}}
                            <div>
                                <label class="s-label">Company Logo</label>
                                <div class="mt-1 flex items-center gap-4">
                                    <div class="flex h-16 w-32 flex-shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 overflow-hidden">
                                        @if(!empty($settings['company_logo']))
                                            <img id="preview-logo" src="{{ asset('public/' . $settings['company_logo']) }}" class="h-full w-full object-contain" alt="Logo">
                                        @else
                                            <img id="preview-logo" src="" class="h-full w-full object-contain hidden" alt="Logo">
                                            <span id="preview-logo-ph" class="text-xs text-gray-400">No logo</span>
                                        @endif
                                    </div>
                                    <div>
                                        <input type="file" name="company_logo" id="inp-logo" accept="image/*" class="hidden"
                                               onchange="previewImg(this,'preview-logo','preview-logo-ph')">
                                        <label for="inp-logo" class="cursor-pointer rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            Choose File
                                        </label>
                                        <p class="mt-1 text-xs text-gray-400">PNG, JPG, SVG — max 2 MB</p>
                                    </div>
                                </div>
                            </div>
                            {{-- Favicon --}}
                            <div>
                                <label class="s-label">Favicon</label>
                                <div class="mt-1 flex items-center gap-4">
                                    <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 overflow-hidden">
                                        @if(!empty($settings['favicon']))
                                            <img id="preview-favicon" src="{{ asset('public/' . $settings['favicon']) }}" class="h-full w-full object-contain" alt="Favicon">
                                        @else
                                            <img id="preview-favicon" src="" class="h-full w-full object-contain hidden" alt="Favicon">
                                            <span id="preview-favicon-ph" class="text-xs text-gray-400">None</span>
                                        @endif
                                    </div>
                                    <div>
                                        <input type="file" name="favicon" id="inp-favicon" accept="image/*,.ico" class="hidden"
                                               onchange="previewImg(this,'preview-favicon','preview-favicon-ph')">
                                        <label for="inp-favicon" class="cursor-pointer rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            Choose File
                                        </label>
                                        <p class="mt-1 text-xs text-gray-400">ICO, PNG — max 512 KB</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="border-t border-gray-100 px-6 py-4">
                        <button type="submit" class="s-btn-save">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Save General Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ════ TAB 2 — Loan Settings ════ --}}
        <div id="panel-loan" class="tab-panel hidden">
            <form class="settings-form" action="{{ route('admin.settings.update', 'loan') }}" method="POST">
                @csrf
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-gray-900">Loan Settings</h2>
                        <p class="text-sm text-gray-500">Default loan parameters applied to all applications.</p>
                    </div>
                    <div class="px-6 py-6 space-y-5">

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label class="s-label">Minimum Loan Amount (₹) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-500">₹</span>
                                    <input type="number" name="min_loan_amount" value="{{ $settings['min_loan_amount'] ?? '10000' }}"
                                           class="s-input pl-7" min="1" step="1000" required>
                                </div>
                            </div>
                            <div>
                                <label class="s-label">Maximum Loan Amount (₹) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-500">₹</span>
                                    <input type="number" name="max_loan_amount" value="{{ $settings['max_loan_amount'] ?? '500000' }}"
                                           class="s-input pl-7" min="1" step="1000" required>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label class="s-label">Interest Rate (% p.a.) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" name="interest_rate" value="{{ $settings['interest_rate'] ?? '12' }}"
                                           class="s-input pr-8" min="0" max="100" step="0.01" required>
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">%</span>
                                </div>
                            </div>
                            <div>
                                <label class="s-label">Processing Fee (%) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" name="processing_fee" value="{{ $settings['processing_fee'] ?? '2' }}"
                                           class="s-input pr-8" min="0" max="100" step="0.01" required>
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">%</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="s-label">Loan Duration Options (months) <span class="text-red-500">*</span></label>
                            <input type="text" name="loan_duration_options"
                                   value="{{ $settings['loan_duration_options'] ?? '3,6,12,18,24,36' }}"
                                   class="s-input" placeholder="3,6,12,18,24,36" required>
                            <p class="mt-1 text-xs text-gray-400">Comma-separated months, e.g. 3,6,12,24</p>
                        </div>

                        <div class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                            <strong>Note:</strong> These values are the system defaults. Loan officers can override per application.
                        </div>

                    </div>
                    <div class="border-t border-gray-100 px-6 py-4">
                        <button type="submit" class="s-btn-save">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Save Loan Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ════ TAB 3 — Payment Bank Settings ════ --}}
        <div id="panel-bank" class="tab-panel hidden">
            <form class="settings-form" action="{{ route('admin.settings.update', 'bank') }}" method="POST">
                @csrf
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-gray-900">Payment Bank Details</h2>
                        <p class="text-sm text-gray-500">These bank details are shown to customers in the app for loan repayment.</p>
                    </div>
                    <div class="px-6 py-6 space-y-5">

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label class="s-label">Bank Name <span class="text-red-500">*</span></label>
                                <input type="text" name="payment_bank_name" value="{{ $settings['payment_bank_name'] ?? '' }}"
                                       class="s-input" placeholder="e.g. HDFC Bank" required>
                            </div>
                            <div>
                                <label class="s-label">Account Holder Name <span class="text-red-500">*</span></label>
                                <input type="text" name="payment_holder_name" value="{{ $settings['payment_holder_name'] ?? '' }}"
                                       class="s-input" placeholder="Name as per bank records" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label class="s-label">Account Number <span class="text-red-500">*</span></label>
                                <input type="text" name="payment_account_number" value="{{ $settings['payment_account_number'] ?? '' }}"
                                       class="s-input" placeholder="Enter account number" required>
                            </div>
                            <div>
                                <label class="s-label">IFSC Code <span class="text-red-500">*</span></label>
                                <input type="text" name="payment_ifsc_code" value="{{ $settings['payment_ifsc_code'] ?? '' }}"
                                       class="s-input" placeholder="e.g. HDFC0001234" maxlength="11" style="text-transform:uppercase" required>
                                <p class="mt-1 text-xs text-gray-400">Format: 4 letters + 0 + 6 alphanumeric (e.g. HDFC0001234)</p>
                            </div>
                        </div>

                        <div class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                            <strong>Note:</strong> Customers will see these details in the app when they tap "Re Payment". After they upload a screenshot, you will approve it in the loan details to close the loan.
                        </div>

                    </div>
                    <div class="border-t border-gray-100 px-6 py-4">
                        <button type="submit" class="s-btn-save">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Save Bank Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ════ TAB 3 — APK / App Settings ════ --}}
        <div id="panel-apk" class="tab-panel hidden">
            <form class="settings-form" action="{{ route('admin.settings.update', 'apk') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-gray-900">APK / App Settings</h2>
                        <p class="text-sm text-gray-500">Manage the Android APK download and force-update policy.</p>
                    </div>
                    <div class="px-6 py-6 space-y-5">

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label class="s-label">APK Version <span class="text-red-500">*</span></label>
                                <input type="text" name="apk_version"
                                       value="{{ $settings['apk_version'] ?? '1.0.0' }}"
                                       class="s-input" placeholder="1.0.0"
                                       pattern="\d+\.\d+\.\d+" title="Format: major.minor.patch" required>
                                <p class="mt-1 text-xs text-gray-400">Format: major.minor.patch (e.g. 1.2.0)</p>
                            </div>
                            <div class="flex flex-col justify-end">
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3 h-[46px]">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Force Update</p>
                                        <p class="text-xs text-gray-400">Require all users to update</p>
                                    </div>
                                    <label class="s-toggle">
                                        <input type="checkbox" name="force_update" value="1"
                                               {{ ($settings['force_update'] ?? '0') === '1' ? 'checked' : '' }}>
                                        <span class="s-track"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="s-label">Upload New APK</label>
                            <div class="mt-1 rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-8 text-center hover:border-blue-400 hover:bg-blue-50 transition-colors">
                                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">
                                    <label for="apk-input" class="cursor-pointer font-medium text-blue-600 hover:underline">Click to upload</label>
                                    or drag &amp; drop
                                </p>
                                <p class="text-xs text-gray-400 mt-1">APK files only — max 100 MB</p>
                                <input id="apk-input" type="file" name="apk_file" accept=".apk"
                                       class="hidden" onchange="showApkName(this)">
                                <p id="apk-filename" class="mt-2 hidden text-sm font-medium text-blue-700"></p>
                            </div>
                        </div>

                        @if(!empty($settings['apk_file']))
                            <div class="flex items-center justify-between rounded-lg border border-green-200 bg-green-50 px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-green-800">Current APK Uploaded</p>
                                        <p class="text-xs text-green-600">v{{ $settings['apk_version'] ?? '—' }}</p>
                                    </div>
                                </div>
                                <a href="{{ asset('public/' . $settings['apk_file']) }}" download
                                   class="inline-flex items-center gap-2 rounded-lg border border-green-300 bg-white px-3 py-1.5 text-sm font-medium text-green-700 hover:bg-green-50">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download APK
                                </a>
                            </div>
                        @endif

                    </div>
                    <div class="border-t border-gray-100 px-6 py-4">
                        <button type="submit" class="s-btn-save">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Save APK Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ════ TAB 4 — SMTP Email Settings ════ --}}
        <div id="panel-smtp" class="tab-panel hidden">
            <form class="settings-form" action="{{ route('admin.settings.update', 'smtp') }}" method="POST">
                @csrf
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-gray-900">SMTP Email Settings</h2>
                        <p class="text-sm text-gray-500">Configure the outgoing mail server for system emails.</p>
                    </div>
                    <div class="px-6 py-6 space-y-5">

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                            <div class="sm:col-span-2">
                                <label class="s-label">Mail Host <span class="text-red-500">*</span></label>
                                <input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? '' }}"
                                       class="s-input" placeholder="smtp.gmail.com" required>
                            </div>
                            <div>
                                <label class="s-label">Port <span class="text-red-500">*</span></label>
                                <input type="number" name="mail_port" value="{{ $settings['mail_port'] ?? '587' }}"
                                       class="s-input" min="1" max="65535" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label class="s-label">Username</label>
                                <input type="text" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}"
                                       class="s-input" placeholder="your@email.com" autocomplete="off">
                            </div>
                            <div>
                                <label class="s-label">Password</label>
                                <div class="relative">
                                    <input type="password" name="mail_password" id="smtp-pass"
                                           value="{{ $settings['mail_password'] ?? '' }}"
                                           class="s-input pr-10" autocomplete="new-password" placeholder="••••••••">
                                    <button type="button" onclick="togglePwd('smtp-pass')"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                            <div>
                                <label class="s-label">Encryption <span class="text-red-500">*</span></label>
                                <select name="mail_encryption" class="s-input" required>
                                    @foreach(['tls' => 'TLS','ssl' => 'SSL','starttls' => 'STARTTLS','none' => 'None'] as $v => $l)
                                        <option value="{{ $v }}" {{ ($settings['mail_encryption'] ?? 'tls') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="s-label">From Name <span class="text-red-500">*</span></label>
                                <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? '' }}"
                                       class="s-input" placeholder="EasyLoanPoint" required>
                            </div>
                        </div>

                        <div>
                            <label class="s-label">From Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="mail_from_email" value="{{ $settings['mail_from_email'] ?? '' }}"
                                   class="s-input" placeholder="no-reply@company.com" required>
                        </div>

                    </div>
                    <div class="border-t border-gray-100 px-6 py-4">
                        <button type="submit" class="s-btn-save">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Save SMTP Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ════ TAB 5 — SMS / OTP Settings ════ --}}
        <div id="panel-sms" class="tab-panel hidden">
            <form class="settings-form" action="{{ route('admin.settings.update', 'sms') }}" method="POST">
                @csrf
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-gray-900">SMS / OTP Settings</h2>
                        <p class="text-sm text-gray-500">Configure SMS gateway credentials for OTP delivery.</p>
                    </div>
                    <div class="px-6 py-6 space-y-5">

                        <div class="flex items-center justify-between rounded-xl border border-gray-200 px-5 py-4">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Enable OTP Verification</p>
                                <p class="text-xs text-gray-400 mt-0.5">Require SMS OTP during customer login and registration</p>
                            </div>
                            <label class="s-toggle">
                                <input type="checkbox" name="otp_enabled" value="1"
                                       {{ ($settings['otp_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                <span class="s-track"></span>
                            </label>
                        </div>

                        <div class="rounded-lg border border-gray-100 bg-gray-50 px-5 py-4 space-y-4">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">SMS API Credentials</p>
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="s-label">API Key</label>
                                    <input type="text" name="sms_api_key" value="{{ $settings['sms_api_key'] ?? '' }}"
                                           class="s-input" placeholder="Your SMS API Key" autocomplete="off">
                                </div>
                                <div>
                                    <label class="s-label">API Secret</label>
                                    <div class="relative">
                                        <input type="password" name="sms_api_secret" id="sms-secret"
                                               value="{{ $settings['sms_api_secret'] ?? '' }}"
                                               class="s-input pr-10" autocomplete="new-password" placeholder="••••••••">
                                        <button type="button" onclick="togglePwd('sms-secret')"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="sm:w-64">
                                <label class="s-label">Sender ID</label>
                                <input type="text" name="sms_sender_id" value="{{ $settings['sms_sender_id'] ?? 'ELOAN' }}"
                                       class="s-input" placeholder="ELOAN" maxlength="20">
                                <p class="mt-1 text-xs text-gray-400">Max 6 chars — DLT registered sender ID</p>
                            </div>
                        </div>

                    </div>
                    <div class="border-t border-gray-100 px-6 py-4">
                        <button type="submit" class="s-btn-save">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Save SMS Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ════ TAB 6 — Notification Settings ════ --}}
        <div id="panel-notifications" class="tab-panel hidden">
            <form class="settings-form" action="{{ route('admin.settings.update', 'notifications') }}" method="POST">
                @csrf
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-gray-900">Notification Settings</h2>
                        <p class="text-sm text-gray-500">Control which channels are used to deliver notifications.</p>
                    </div>
                    <div class="px-6 py-6 space-y-4">
                        @php
                            $nChannels = [
                                ['key' => 'email_notifications', 'label' => 'Email Notifications', 'desc' => 'Send updates, loan alerts, and assignments via email', 'icon' => 'mail'],
                                ['key' => 'push_notifications',  'label' => 'Push Notifications',  'desc' => 'In-app push alerts to mobile app users', 'icon' => 'bell'],
                                ['key' => 'sms_notifications',   'label' => 'SMS Notifications',   'desc' => 'SMS alerts for critical loan status changes', 'icon' => 'chat'],
                            ];
                        @endphp
                        @foreach($nChannels as $ch)
                            <div class="flex items-center justify-between rounded-xl border border-gray-200 px-5 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-blue-50">
                                        @if($ch['icon'] === 'mail')
                                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        @elseif($ch['icon'] === 'bell')
                                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                        @else
                                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">{{ $ch['label'] }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $ch['desc'] }}</p>
                                    </div>
                                </div>
                                <label class="s-toggle">
                                    <input type="checkbox" name="{{ $ch['key'] }}" value="1"
                                           {{ ($settings[$ch['key']] ?? '1') === '1' ? 'checked' : '' }}>
                                    <span class="s-track"></span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-gray-100 px-6 py-4">
                        <button type="submit" class="s-btn-save">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Save Notification Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ════ TAB 7 — Homepage Settings ════ --}}
        <div id="panel-homepage" class="tab-panel hidden">
            <form class="settings-form" action="{{ route('admin.settings.update', 'homepage') }}" method="POST">
                @csrf
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-gray-900">Website Homepage Settings</h2>
                        <p class="text-sm text-gray-500">Customize content displayed on the public-facing homepage.</p>
                    </div>
                    <div class="px-6 py-6 space-y-5">
                        <div>
                            <label class="s-label">Hero Title <span class="text-red-500">*</span></label>
                            <input type="text" name="hero_title" value="{{ $settings['hero_title'] ?? '' }}"
                                   class="s-input" placeholder="Fast &amp; Easy Loans in India" required>
                        </div>
                        <div>
                            <label class="s-label">Hero Subtitle</label>
                            <input type="text" name="hero_subtitle" value="{{ $settings['hero_subtitle'] ?? '' }}"
                                   class="s-input" placeholder="Get your loan approved in minutes">
                        </div>
                        <div>
                            <label class="s-label">Download App Section</label>
                            <textarea name="download_section" rows="3" class="s-input resize-none"
                                      placeholder="Describe the app download section…">{{ $settings['download_section'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="s-label">About Section</label>
                            <textarea name="about_section" rows="5" class="s-input resize-none"
                                      placeholder="About your company and services…">{{ $settings['about_section'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="s-label">Contact Info</label>
                            <textarea name="contact_info" rows="4" class="s-input resize-none"
                                      placeholder="Address, phone, email shown on homepage…">{{ $settings['contact_info'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 px-6 py-4">
                        <button type="submit" class="s-btn-save">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Save Homepage Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ════ TAB 8 — Security Settings ════ --}}
        <div id="panel-security" class="tab-panel hidden">
            <form class="settings-form" action="{{ route('admin.settings.update', 'security') }}" method="POST">
                @csrf
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-gray-900">Security Settings</h2>
                        <p class="text-sm text-gray-500">Session control, login limits, and password policy.</p>
                    </div>
                    <div class="px-6 py-6 space-y-5">

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label class="s-label">Session Timeout (minutes) <span class="text-red-500">*</span></label>
                                <input type="number" name="session_timeout" value="{{ $settings['session_timeout'] ?? '120' }}"
                                       class="s-input" min="5" max="1440" required>
                                <p class="mt-1 text-xs text-gray-400">5–1440 min (max 24 h)</p>
                            </div>
                            <div>
                                <label class="s-label">Login Attempt Limit <span class="text-red-500">*</span></label>
                                <input type="number" name="login_attempt_limit" value="{{ $settings['login_attempt_limit'] ?? '5' }}"
                                       class="s-input" min="1" max="20" required>
                                <p class="mt-1 text-xs text-gray-400">Failed attempts before lockout</p>
                            </div>
                        </div>

                        <div class="rounded-lg border border-gray-100 bg-gray-50 px-5 py-4 space-y-4">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Password Policy</p>
                            <div class="sm:w-56">
                                <label class="s-label">Minimum Length <span class="text-red-500">*</span></label>
                                <input type="number" name="password_min_length" value="{{ $settings['password_min_length'] ?? '8' }}"
                                       class="s-input" min="6" max="32" required>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Require Uppercase Letter</p>
                                        <p class="text-xs text-gray-400">At least one uppercase letter (A–Z)</p>
                                    </div>
                                    <label class="s-toggle">
                                        <input type="checkbox" name="password_uppercase" value="1"
                                               {{ ($settings['password_uppercase'] ?? '1') === '1' ? 'checked' : '' }}>
                                        <span class="s-track"></span>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Require Number</p>
                                        <p class="text-xs text-gray-400">At least one numeric digit (0–9)</p>
                                    </div>
                                    <label class="s-toggle">
                                        <input type="checkbox" name="password_numbers" value="1"
                                               {{ ($settings['password_numbers'] ?? '1') === '1' ? 'checked' : '' }}>
                                        <span class="s-track"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-yellow-100 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                            <strong>Note:</strong> Session timeout changes apply to new sessions only. Existing sessions are unaffected.
                        </div>

                    </div>
                    <div class="border-t border-gray-100 px-6 py-4">
                        <button type="submit" class="s-btn-save">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Save Security Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ════ TAB 9 — System Tools (super_admin only) ════ --}}
        @if(auth()->user()->isSuperAdmin())
        <div id="panel-system" class="tab-panel hidden">
            <div class="space-y-6">

                <div class="rounded-lg border border-yellow-100 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                    <strong>Caution:</strong> These run real commands on the server (database migrations, composer, cache).
                    Use on a live site only when you know what a command does. Composer install can take a while and may
                    time out on some hosts — if it fails, run it via SSH/terminal instead.
                </div>

                {{-- Migrations --}}
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-gray-900">Database Migrations</h2>
                        <p class="text-sm text-gray-500">Apply new database changes after uploading updated code.</p>
                    </div>
                    <div class="px-6 py-6 space-y-5">
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 px-5 py-4">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Run All Pending Migrations</p>
                                <p id="pending-count" class="text-xs text-gray-400 mt-0.5">Checking…</p>
                            </div>
                            <button type="button" id="btn-migrate-all" class="s-btn-tool">Run Migrations</button>
                        </div>

                        <div class="rounded-lg border border-gray-100 bg-gray-50 px-5 py-4 space-y-3">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Run One Specific Migration</p>
                            <div class="flex flex-col gap-3 sm:flex-row">
                                <input list="pending-migrations-list" id="migration-name" class="s-input flex-1"
                                       placeholder="e.g. 2026_09_04_000001_create_otp_verifications_table">
                                <datalist id="pending-migrations-list"></datalist>
                                <button type="button" id="btn-migrate-one" class="s-btn-tool whitespace-nowrap">Run This Migration</button>
                            </div>
                            <p class="text-xs text-gray-400">Type the exact migration filename (without .php) — pick one from the suggestions.</p>
                        </div>
                    </div>
                </div>

                {{-- Composer --}}
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-gray-900">Composer</h2>
                        <p class="text-sm text-gray-500">Install PHP dependencies listed in composer.json.</p>
                    </div>
                    <div class="px-6 py-6">
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 px-5 py-4">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">composer install</p>
                                <p class="text-xs text-gray-400 mt-0.5">Requires composer to be available on this server.</p>
                            </div>
                            <button type="button" id="btn-composer" class="s-btn-tool">Run Composer Install</button>
                        </div>
                    </div>
                </div>

                {{-- Cache --}}
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-gray-900">Clear Cache</h2>
                        <p class="text-sm text-gray-500">Clear cached config, routes, views, or application cache after changes.</p>
                    </div>
                    <div class="px-6 py-6">
                        <div class="flex flex-wrap gap-3">
                            <button type="button" class="s-btn-tool-outline" data-cache-type="config">Config Cache</button>
                            <button type="button" class="s-btn-tool-outline" data-cache-type="route">Route Cache</button>
                            <button type="button" class="s-btn-tool-outline" data-cache-type="view">View Cache</button>
                            <button type="button" class="s-btn-tool-outline" data-cache-type="cache">App Cache</button>
                            <button type="button" class="s-btn-tool" data-cache-type="all">Clear All Caches</button>
                        </div>
                    </div>
                </div>

                {{-- Output --}}
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-gray-900">Output</h2>
                    </div>
                    <div class="px-6 py-6">
                        <pre id="tool-output" class="max-h-96 overflow-auto rounded-lg bg-gray-900 px-4 py-3 text-xs leading-relaxed text-gray-100 whitespace-pre-wrap">Command output will appear here…</pre>
                    </div>
                </div>

            </div>
        </div>
        @endif

    </div>{{-- /right --}}
</div>

@endsection

@push('scripts')
<style>
    .s-label  { display:block; margin-bottom:.25rem; font-size:.8125rem; font-weight:500; color:#374151; }
    .s-input  { width:100%; border-radius:.5rem; border:1px solid #D1D5DB; padding:.5625rem .75rem; font-size:.875rem; color:#111827; outline:none; background:#fff; transition:border-color .15s,box-shadow .15s; }
    .s-input:focus { border-color:#2563EB; box-shadow:0 0 0 3px rgba(37,99,235,.12); }
    .s-input.err { border-color:#F87171; }
    select.s-input { appearance:auto; }

    .s-btn-save { display:inline-flex; align-items:center; gap:.5rem; border-radius:.5rem; background:#1D4ED8; padding:.5625rem 1.25rem; font-size:.875rem; font-weight:600; color:#fff; border:none; cursor:pointer; transition:background .15s; }
    .s-btn-save:hover { background:#1E40AF; }
    .s-btn-save:disabled { opacity:.6; cursor:not-allowed; }

    .s-btn-tool { display:inline-flex; align-items:center; gap:.5rem; border-radius:.5rem; background:#111827; padding:.5rem 1rem; font-size:.8125rem; font-weight:600; color:#fff; border:none; cursor:pointer; transition:background .15s; }
    .s-btn-tool:hover { background:#000; }
    .s-btn-tool:disabled { opacity:.6; cursor:not-allowed; }
    .s-btn-tool-outline { display:inline-flex; align-items:center; gap:.5rem; border-radius:.5rem; background:#fff; padding:.5rem 1rem; font-size:.8125rem; font-weight:600; color:#111827; border:1px solid #D1D5DB; cursor:pointer; transition:background .15s; }
    .s-btn-tool-outline:hover { background:#F9FAFB; }
    .s-btn-tool-outline:disabled { opacity:.6; cursor:not-allowed; }

    /* Toggle */
    .s-toggle { position:relative; display:inline-block; flex-shrink:0; cursor:pointer; }
    .s-toggle input { opacity:0; width:0; height:0; position:absolute; }
    .s-track { display:block; width:2.75rem; height:1.5rem; background:#D1D5DB; border-radius:9999px; transition:background .2s; position:relative; }
    .s-track::after { content:''; position:absolute; top:.2rem; left:.2rem; width:1.1rem; height:1.1rem; background:#fff; border-radius:9999px; transition:transform .2s; box-shadow:0 1px 3px rgba(0,0,0,.2); }
    .s-toggle input:checked + .s-track { background:#2563EB; }
    .s-toggle input:checked + .s-track::after { transform:translateX(1.25rem); }
</style>

<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

// ── Tab switching ────────────────────────────────────────────────────────────
function switchTab(id) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('bg-blue-600','text-white');
        b.classList.add('text-gray-600','hover:bg-gray-50','hover:text-gray-900');
    });
    document.getElementById('panel-' + id).classList.remove('hidden');
    const btn = document.querySelector('[data-tab="' + id + '"]');
    btn.classList.add('bg-blue-600','text-white');
    btn.classList.remove('text-gray-600','hover:bg-gray-50','hover:text-gray-900');
}

// ── AJAX form submission ─────────────────────────────────────────────────────
document.querySelectorAll('.settings-form').forEach(form => {
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Clear previous field errors
        form.querySelectorAll('.s-input').forEach(el => el.classList.remove('err'));

        const btn  = form.querySelector('[type=submit]');
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Saving…`;

        const fd = new FormData(form);

        // Explicitly include unchecked checkboxes as 0
        form.querySelectorAll('input[type=checkbox]').forEach(cb => {
            if (!cb.checked) fd.set(cb.name, '0');
        });

        try {
            const res  = await fetch(form.action, {
                method: 'POST',
                body: fd,
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json();

            if (res.ok && data.status) {
                showToast(data.message, 'success');
            } else if (res.status === 422 && data.errors) {
                const msgs = Object.values(data.errors).flat();
                showToast(msgs.join(' · '), 'error');
                Object.keys(data.errors).forEach(field => {
                    const el = form.querySelector('[name="' + field + '"]');
                    if (el) el.classList.add('err');
                });
            } else {
                showToast(data.message || 'Something went wrong. Please try again.', 'error');
            }
        } catch {
            showToast('Network error. Please check your connection.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = orig;
        }
    });
});

// ── Toast ────────────────────────────────────────────────────────────────────
let _toastTimer;
function showToast(msg, type) {
    const el    = document.getElementById('toast');
    const msgEl = document.getElementById('toast-msg');
    const okI   = document.getElementById('toast-icon-ok');
    const errI  = document.getElementById('toast-icon-err');
    const wrap  = document.getElementById('toast-icon');
    msgEl.textContent = msg;
    el.className = 'fixed top-5 right-5 z-50 max-w-sm rounded-xl border px-5 py-4 shadow-lg transition-all duration-300';
    if (type === 'success') {
        el.classList.add('border-green-200','bg-green-50','text-green-800');
        wrap.className = 'mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-green-200 text-green-700';
        okI.classList.remove('hidden'); errI.classList.add('hidden');
    } else {
        el.classList.add('border-red-200','bg-red-50','text-red-800');
        wrap.className = 'mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-200 text-red-700';
        errI.classList.remove('hidden'); okI.classList.add('hidden');
    }
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => el.classList.add('hidden'), 4500);
}

// ── Image preview ────────────────────────────────────────────────────────────
function previewImg(input, imgId, phId) {
    if (!input.files || !input.files[0]) return;
    const r = new FileReader();
    r.onload = e => {
        const img = document.getElementById(imgId);
        img.src = e.target.result;
        img.classList.remove('hidden');
        if (phId) { const ph = document.getElementById(phId); if (ph) ph.classList.add('hidden'); }
    };
    r.readAsDataURL(input.files[0]);
}

// ── APK filename ─────────────────────────────────────────────────────────────
function showApkName(input) {
    const el = document.getElementById('apk-filename');
    if (input.files && input.files[0]) {
        el.textContent = '✓ ' + input.files[0].name + ' (' + (input.files[0].size / 1048576).toFixed(1) + ' MB)';
        el.classList.remove('hidden');
    }
}

// ── Password toggle ──────────────────────────────────────────────────────────
function togglePwd(id) {
    const el = document.getElementById(id);
    el.type  = el.type === 'password' ? 'text' : 'password';
}

// ── System Tools ─────────────────────────────────────────────────────────────
(function () {
    const systemPanel = document.getElementById('panel-system');
    if (!systemPanel) return; // super_admin only

    const outputEl = document.getElementById('tool-output');

    function showOutput(text, ok) {
        outputEl.textContent = text && text.trim() ? text : (ok ? '(no output)' : 'Failed — no details returned.');
        outputEl.classList.toggle('text-red-300', !ok);
        outputEl.classList.toggle('text-gray-100', ok);
    }

    async function runTool(url, body, btn) {
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Running…';
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(body || {}),
            });
            const data = await res.json();
            showOutput((data.data && data.data.output) || data.message, !!data.status);
            showToast(data.message, data.status ? 'success' : 'error');
            if (data.status) refreshPendingMigrations();
        } catch {
            showOutput('Network error while running this command.', false);
            showToast('Network error. Please check your connection.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = orig;
        }
    }

    async function refreshPendingMigrations() {
        const countEl = document.getElementById('pending-count');
        const listEl  = document.getElementById('pending-migrations-list');
        try {
            const res  = await fetch("{{ route('admin.settings.system.migrations.pending') }}", {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json();
            const pending = (data.data && data.data.pending) || [];
            countEl.textContent = pending.length === 0
                ? 'Everything is up to date — no pending migrations.'
                : pending.length + ' pending migration' + (pending.length === 1 ? '' : 's') + '.';
            listEl.innerHTML = pending.map(name => `<option value="${name}">`).join('');
        } catch {
            countEl.textContent = 'Could not check migration status.';
        }
    }

    document.getElementById('btn-migrate-all').addEventListener('click', function () {
        if (!confirm('Run all pending database migrations now?')) return;
        runTool("{{ route('admin.settings.system.migrate') }}", {}, this);
    });

    document.getElementById('btn-migrate-one').addEventListener('click', function () {
        const name = document.getElementById('migration-name').value.trim();
        if (!name) { showToast('Enter a migration name first.', 'error'); return; }
        if (!confirm('Run migration "' + name + '"?')) return;
        runTool("{{ route('admin.settings.system.migrate-one') }}", { migration: name }, this);
    });

    document.getElementById('btn-composer').addEventListener('click', function () {
        if (!confirm('Run "composer install" now? This can take a while.')) return;
        runTool("{{ route('admin.settings.system.composer-install') }}", {}, this);
    });

    document.querySelectorAll('[data-cache-type]').forEach(btn => {
        btn.addEventListener('click', function () {
            runTool("{{ route('admin.settings.system.cache-clear') }}", { type: this.dataset.cacheType }, this);
        });
    });

    // Load pending-migration status once the System Tools tab is first shown.
    const origSwitchTab = switchTab;
    let systemLoaded = false;
    switchTab = function (id) {
        origSwitchTab(id);
        if (id === 'system' && !systemLoaded) {
            systemLoaded = true;
            refreshPendingMigrations();
        }
    };
})();
</script>
@endpush
