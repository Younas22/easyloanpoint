@extends('layouts.app')

@section('title', 'HR Management')
@section('page-title', 'HR Management')

@section('page-header')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">HR Management</h1>
            <p class="mt-0.5 text-sm text-gray-500">Manage HR members, their access and status.</p>
        </div>
        <a href="{{ route('admin.hr.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add HR Member
        </a>
    </div>
@endsection

@section('content')

    {{-- Filters --}}
    <div class="mb-5 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.hr.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1 block text-xs font-medium text-gray-600">Search</label>
                <div class="relative">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Name, email or phone…"
                           class="w-full rounded-lg border border-gray-300 py-2.5 pl-9 pr-4 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            <div class="sm:w-44">
                <label class="mb-1 block text-xs font-medium text-gray-600">Status</label>
                <select name="status"
                        class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.hr.index') }}"
                       class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">#</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">HR Member</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Contact</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Joined</th>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($hrs as $hr)
                        <tr class="transition-colors hover:bg-gray-50">
                            <td class="whitespace-nowrap px-5 py-4 text-gray-500">
                                {{ ($hrs->currentPage() - 1) * $hrs->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if($hr->profile_image)
                                        <img src="{{ asset($hr->profile_image) }}"
                                             alt="{{ $hr->name }}"
                                             class="h-9 w-9 rounded-full object-cover ring-2 ring-gray-100">
                                    @else
                                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">
                                            {{ strtoupper(substr($hr->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $hr->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $hr->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-gray-600">
                                {{ $hr->phone ?? '—' }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <button type="button"
                                        onclick="toggleStatus({{ $hr->id }}, this)"
                                        data-active="{{ $hr->status ? '1' : '0' }}"
                                        class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold transition-colors
                                               {{ $hr->status
                                                   ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                   : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $hr->status ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    {{ $hr->status ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-gray-500">
                                {{ $hr->created_at->format('d M Y') }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.hr.edit', $hr) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:border-gray-300">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <button type="button"
                                            onclick="confirmDelete({{ $hr->id }}, '{{ addslashes($hr->name) }}')"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 transition-colors hover:bg-red-50">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="mt-3 text-sm font-medium text-gray-500">No HR members found</p>
                                <p class="mt-1 text-xs text-gray-400">Try adjusting your search or filter criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($hrs->hasPages())
            <div class="border-t border-gray-100 bg-gray-50 px-5 py-3">
                {{ $hrs->links() }}
            </div>
        @endif
    </div>

    {{-- Hidden delete form --}}
    <form id="delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

@endsection

@push('scripts')
<script>
    function confirmDelete(id, name) {
        if (typeof Swal === 'undefined') {
            if (!confirm(`Delete "${name}"? This cannot be undone.`)) return;
            submitDelete(id);
            return;
        }
        Swal.fire({
            title: 'Delete HR Member?',
            html: `<p class="text-gray-600">You are about to delete <strong>${name}</strong>. This action cannot be undone.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
        }).then(result => {
            if (result.isConfirmed) submitDelete(id);
        });
    }

    function submitDelete(id) {
        const form = document.getElementById('delete-form');
        form.action = `/admin/hr/${id}`;
        form.submit();
    }

    function toggleStatus(id, btn) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/admin/hr/${id}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (!data.status) return;
            const active = data.active;
            btn.dataset.active = active ? '1' : '0';
            btn.className = `inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold transition-colors ${
                active
                    ? 'bg-green-100 text-green-700 hover:bg-green-200'
                    : 'bg-red-100 text-red-700 hover:bg-red-200'
            }`;
            btn.innerHTML = `
                <span class="h-1.5 w-1.5 rounded-full ${active ? 'bg-green-500' : 'bg-red-500'}"></span>
                ${active ? 'Active' : 'Inactive'}
            `;
        });
    }
</script>
@endpush
