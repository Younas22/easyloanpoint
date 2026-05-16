@if($customers->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100">
            <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <p class="text-sm font-semibold text-gray-700">No customers found</p>
        <p class="mt-1 text-xs text-gray-400">Try adjusting your search or filter criteria.</p>
    </div>
@else
    {{-- Desktop Table --}}
    <div class="hidden overflow-x-auto md:block">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/70">
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Mobile</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Loan Amount</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Loan Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Assigned Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Loans</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($customers as $customer)
                    @php
                        $latestLoan = $customer->loans->first();
                    @endphp
                    <tr class="group transition-colors hover:bg-blue-50/30">
                        {{-- Customer Name --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-blue-900 text-xs font-bold text-white">
                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $customer->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $customer->email ?: '—' }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Mobile --}}
                        <td class="px-4 py-3">
                            <span class="font-mono text-sm text-gray-700">{{ $customer->phone ?: '—' }}</span>
                        </td>

                        {{-- Loan Amount --}}
                        <td class="px-4 py-3">
                            @if($latestLoan)
                                <span class="font-semibold text-gray-900">
                                    ₹{{ number_format($latestLoan->amount_requested, 0) }}
                                </span>
                            @else
                                <span class="text-gray-400">No loan</span>
                            @endif
                        </td>

                        {{-- Loan Status --}}
                        <td class="px-4 py-3">
                            @if($latestLoan)
                                @php
                                    $cls = match($latestLoan->status) {
                                        'pending'      => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'under_review' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'approved'     => 'bg-green-100 text-green-800 border-green-200',
                                        'rejected'     => 'bg-red-100 text-red-800 border-red-200',
                                        'disbursed'    => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                        default        => 'bg-gray-100 text-gray-700 border-gray-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $cls }}">
                                    {{ $latestLoan->status_label }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>

                        {{-- Assigned Date --}}
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $customer->created_at->format('d M Y') }}
                        </td>

                        {{-- Loan Count --}}
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center justify-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-700">
                                {{ $customer->loans_count }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('hr.customers.show', $customer) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-blue-900 bg-blue-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-blue-800">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="divide-y divide-gray-100 md:hidden">
        @foreach($customers as $customer)
            @php $latestLoan = $customer->loans->first(); @endphp
            <div class="p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-900 text-sm font-bold text-white">
                            {{ strtoupper(substr($customer->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $customer->name }}</p>
                            <p class="text-xs text-gray-500">{{ $customer->phone ?: '—' }}</p>
                        </div>
                    </div>
                    @if($latestLoan)
                        @php
                            $cls = match($latestLoan->status) {
                                'pending'      => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'under_review' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'approved'     => 'bg-green-100 text-green-800 border-green-200',
                                'rejected'     => 'bg-red-100 text-red-800 border-red-200',
                                'disbursed'    => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                default        => 'bg-gray-100 text-gray-700 border-gray-200',
                            };
                        @endphp
                        <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-semibold {{ $cls }}">
                            {{ $latestLoan->status_label }}
                        </span>
                    @endif
                </div>
                <div class="mt-3 flex items-center justify-between">
                    <div class="text-sm">
                        @if($latestLoan)
                            <span class="font-semibold text-gray-900">₹{{ number_format($latestLoan->amount_requested, 0) }}</span>
                            <span class="ml-1 text-gray-400 text-xs">loan amount</span>
                        @else
                            <span class="text-gray-400 text-xs">No loan yet</span>
                        @endif
                    </div>
                    <a href="{{ route('hr.customers.show', $customer) }}"
                       class="inline-flex items-center gap-1 rounded-lg border border-blue-900 bg-blue-900 px-3 py-1.5 text-xs font-semibold text-white">
                        View Profile
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif
