@extends('layouts.app')

@section('title', 'Loan Types')

@section('page-header')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Loan Types</h1>
            <p class="mt-0.5 text-sm text-gray-500">Manage loan products visible to customers in the app.</p>
        </div>
        <a href="{{ route('admin.loan-types.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add Loan Type
        </a>
    </div>
@endsection

@section('content')

    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">#</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Loan Type</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Amount</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Repayment</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Applications</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($loanTypes as $lt)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4 text-gray-400 text-xs">{{ $lt->id }}</td>
                        <td class="px-5 py-4">
                            <div class="font-semibold text-gray-900">{{ $lt->name }}</div>
                            @if($lt->description)
                                <div class="text-xs text-gray-400 mt-0.5">{{ Str::limit($lt->description, 60) }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-4 font-semibold text-gray-900">₹{{ number_format($lt->amount, 2) }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $lt->repayment_days }} days</td>
                        <td class="px-5 py-4">
                            <form method="POST" action="{{ route('admin.loan-types.toggle-status', $lt) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold transition-colors
                                        {{ $lt->status === 'active'
                                            ? 'bg-green-50 text-green-700 hover:bg-green-100 border border-green-200'
                                            : 'bg-gray-100 text-gray-500 hover:bg-gray-200 border border-gray-200' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $lt->status === 'active' ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    {{ $lt->status === 'active' ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-4 text-gray-700">{{ $lt->loans()->count() }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.loan-types.edit', $lt) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.loan-types.destroy', $lt) }}"
                                      onsubmit="return confirm('Delete \'{{ $lt->name }}\'? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100 transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">
                            No loan types yet. <a href="{{ route('admin.loan-types.create') }}" class="text-blue-600 hover:underline">Add one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
