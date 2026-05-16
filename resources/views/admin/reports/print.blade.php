<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report — EasyLoanPoint</title>
    <link rel="stylesheet" href="{{ asset('public/build/assets/app.css') }}">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
            .page-break { page-break-before: always; }
        }
        body { background: #fff; font-family: sans-serif; }
    </style>
</head>
<body class="bg-white p-8 text-gray-900">

    {{-- Print toolbar --}}
    <div class="no-print mb-6 flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-5 py-3">
        <p class="text-sm text-gray-600">Print preview — <strong>{{ $filters['label'] }}</strong></p>
        <div class="flex gap-2">
            <button onclick="window.print()"
                    class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                Print / Save PDF
            </button>
            <button onclick="window.close()"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100">
                Close
            </button>
        </div>
    </div>

    {{-- Report header --}}
    <div class="mb-6 flex items-start justify-between border-b border-gray-200 pb-5">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">EasyLoanPoint</h1>
            <p class="mt-0.5 text-sm text-gray-500">Loan Management System — Report</p>
        </div>
        <div class="text-right text-sm text-gray-500">
            <p class="font-semibold text-gray-900">{{ $filters['label'] }}</p>
            <p>Generated: {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    {{-- Loan Summary --}}
    <h2 class="mb-3 text-xs font-bold uppercase tracking-widest text-gray-400">Loan Summary</h2>
    <div class="mb-6 grid grid-cols-3 gap-3 sm:grid-cols-6">
        @foreach([
            ['Total',        $summary['loan_total'],        'bg-slate-900 text-white'],
            ['Pending',      $summary['loan_pending'],      'bg-amber-50 border border-amber-200 text-amber-800'],
            ['Under Review', $summary['loan_under_review'], 'bg-blue-50 border border-blue-200 text-blue-800'],
            ['Approved',     $summary['loan_approved'],     'bg-green-50 border border-green-200 text-green-800'],
            ['Rejected',     $summary['loan_rejected'],     'bg-red-50 border border-red-200 text-red-800'],
            ['Disbursed',    $summary['loan_disbursed'],    'bg-purple-50 border border-purple-200 text-purple-800'],
        ] as [$label, $val, $cls])
        <div class="rounded-xl p-3 {{ $cls }}">
            <p class="text-xs font-medium opacity-70">{{ $label }}</p>
            <p class="mt-0.5 text-xl font-bold">{{ number_format($val) }}</p>
        </div>
        @endforeach
    </div>

    {{-- Customer + Financial --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">

        <div class="rounded-xl border border-gray-200 p-4">
            <h2 class="mb-3 text-xs font-bold uppercase tracking-widest text-gray-400">Customer Summary</h2>
            <div class="space-y-2">
                @foreach([
                    ['New (Period)',   $summary['cust_new']],
                    ['Active Total',  $summary['cust_active']],
                    ['Inactive Total',$summary['cust_inactive']],
                    ['All Customers', $summary['cust_total']],
                    ['Active HR',     $summary['hr_total']],
                ] as [$lbl, $val])
                <div class="flex items-center justify-between border-b border-gray-100 pb-1">
                    <span class="text-sm text-gray-600">{{ $lbl }}</span>
                    <span class="font-semibold text-gray-900">{{ number_format($val) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 p-4">
            <h2 class="mb-3 text-xs font-bold uppercase tracking-widest text-gray-400">Financial Summary</h2>
            <div class="space-y-2">
                @foreach([
                    ['Total Requested', $summary['fin_total_req']],
                    ['Approved Amount', $summary['fin_approved']],
                    ['Pending Amount',  $summary['fin_pending']],
                    ['Disbursed Amount',$summary['fin_disbursed']],
                ] as [$lbl, $val])
                <div class="flex items-center justify-between border-b border-gray-100 pb-1">
                    <span class="text-sm text-gray-600">{{ $lbl }}</span>
                    <span class="font-semibold text-gray-900">₹{{ number_format($val, 2) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- HR Performance --}}
    @if(count($hrStats) > 0)
    <h2 class="mb-3 text-xs font-bold uppercase tracking-widest text-gray-400">HR Performance</h2>
    <div class="mb-6 overflow-hidden rounded-xl border border-gray-200">
        <table class="w-full text-sm">
            <thead class="bg-slate-900 text-xs font-semibold uppercase tracking-wide text-slate-300">
                <tr>
                    <th class="px-4 py-2.5 text-left">HR Manager</th>
                    <th class="px-3 py-2.5 text-center">Customers</th>
                    <th class="px-3 py-2.5 text-center">Total</th>
                    <th class="px-3 py-2.5 text-center">Approved</th>
                    <th class="px-3 py-2.5 text-center">Rejected</th>
                    <th class="px-3 py-2.5 text-center">Pending</th>
                    <th class="px-3 py-2.5 text-center">Disbursed</th>
                    <th class="px-3 py-2.5 text-right">Appr. Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($hrStats as $hr)
                <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                    <td class="px-4 py-2 font-medium text-gray-900">{{ $hr['name'] }}</td>
                    <td class="px-3 py-2 text-center text-gray-700">{{ $hr['assigned_customers'] }}</td>
                    <td class="px-3 py-2 text-center font-semibold text-gray-900">{{ $hr['total_loans'] }}</td>
                    <td class="px-3 py-2 text-center font-medium text-green-700">{{ $hr['approved'] }}</td>
                    <td class="px-3 py-2 text-center font-medium text-red-600">{{ $hr['rejected'] }}</td>
                    <td class="px-3 py-2 text-center font-medium text-amber-600">{{ $hr['pending'] }}</td>
                    <td class="px-3 py-2 text-center font-medium text-purple-600">{{ $hr['disbursed'] }}</td>
                    <td class="px-3 py-2 text-right font-semibold text-gray-900">₹{{ number_format($hr['approved_amount'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Loan Details --}}
    <div class="page-break">
        <h2 class="mb-3 text-xs font-bold uppercase tracking-widest text-gray-400">
            Loan Details ({{ $loans->count() }} records)
        </h2>
        <div class="overflow-hidden rounded-xl border border-gray-200">
            <table class="w-full text-xs">
                <thead class="bg-slate-900 text-xs font-semibold uppercase tracking-wide text-slate-300">
                    <tr>
                        <th class="px-3 py-2.5 text-left">Loan #</th>
                        <th class="px-3 py-2.5 text-left">Customer</th>
                        <th class="px-3 py-2.5 text-left">Type</th>
                        <th class="px-3 py-2.5 text-right">Requested</th>
                        <th class="px-3 py-2.5 text-right">Approved</th>
                        <th class="px-3 py-2.5 text-center">Status</th>
                        <th class="px-3 py-2.5 text-left">HR</th>
                        <th class="px-3 py-2.5 text-left">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($loans as $loan)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="px-3 py-1.5 font-mono font-semibold text-blue-700">{{ $loan->loan_number }}</td>
                        <td class="px-3 py-1.5">
                            <div class="font-medium text-gray-900">{{ $loan->customer?->name ?? '—' }}</div>
                            <div class="text-gray-400">{{ $loan->customer?->phone ?? '' }}</div>
                        </td>
                        <td class="px-3 py-1.5 text-gray-600">{{ $loan->loan_type_label }}</td>
                        <td class="px-3 py-1.5 text-right font-medium text-gray-900">₹{{ number_format($loan->amount_requested, 0) }}</td>
                        <td class="px-3 py-1.5 text-right text-gray-600">
                            {{ $loan->amount_approved ? '₹'.number_format($loan->amount_approved, 0) : '—' }}
                        </td>
                        <td class="px-3 py-1.5 text-center">
                            <span class="{{ $loan->status_badge_class }} inline-flex items-center rounded-full px-1.5 py-0.5 text-xs font-medium">
                                {{ $loan->status_label }}
                            </span>
                        </td>
                        <td class="px-3 py-1.5 text-gray-600">{{ $loan->assignedHR?->name ?? '—' }}</td>
                        <td class="px-3 py-1.5 text-gray-500">{{ $loan->applied_at?->format('d M Y') ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-gray-400">No records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8 border-t border-gray-200 pt-4 text-center text-xs text-gray-400">
        EasyLoanPoint &copy; {{ date('Y') }} — Report generated {{ now()->format('d M Y H:i') }}
    </div>

</body>
</html>
