@extends('layouts.app')

@section('title', 'Panel Permissions')

@section('page-header')
    <div>
        <h1 class="text-xl font-bold text-gray-900">Panel Permissions</h1>
        <p class="mt-0.5 text-sm text-gray-500">Show or hide sidebar items for Admin and HR panels.</p>
    </div>
@endsection

@section('content')

{{-- Toast --}}
<div id="toast"
     class="fixed top-5 right-5 z-50 hidden max-w-xs rounded-xl border px-5 py-3 shadow-lg transition-all duration-300">
    <p id="toast-msg" class="text-sm font-medium"></p>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

    {{-- ── Admin Panel ─────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                <svg class="h-4 w-4 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Admin Panel</h2>
                <p class="text-xs text-gray-400">Sidebar items visible to Admin</p>
            </div>
        </div>
        <ul class="divide-y divide-gray-100">
            @foreach($permissions->get('admin', collect()) as $perm)
                <li class="flex items-center justify-between px-6 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="h-2 w-2 rounded-full {{ $perm->is_hidden ? 'bg-gray-300' : 'bg-green-500' }}"></div>
                        <span class="text-sm font-medium {{ $perm->is_hidden ? 'text-gray-400 line-through' : 'text-gray-800' }}">
                            {{ $perm->label }}
                        </span>
                    </div>
                    <button type="button"
                            onclick="togglePerm({{ $perm->id }}, this)"
                            data-hidden="{{ $perm->is_hidden ? '1' : '0' }}"
                            class="perm-toggle relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none
                                   {{ $perm->is_hidden ? 'bg-gray-200' : 'bg-blue-600' }}">
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200
                                     {{ $perm->is_hidden ? 'translate-x-0' : 'translate-x-5' }}">
                        </span>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- ── HR Panel ─────────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100">
                <svg class="h-4 w-4 text-purple-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900">HR Panel</h2>
                <p class="text-xs text-gray-400">Sidebar items visible to HR</p>
            </div>
        </div>
        <ul class="divide-y divide-gray-100">
            @foreach($permissions->get('hr', collect()) as $perm)
                <li class="flex items-center justify-between px-6 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="h-2 w-2 rounded-full {{ $perm->is_hidden ? 'bg-gray-300' : 'bg-green-500' }}"></div>
                        <span class="text-sm font-medium {{ $perm->is_hidden ? 'text-gray-400 line-through' : 'text-gray-800' }}">
                            {{ $perm->label }}
                        </span>
                    </div>
                    <button type="button"
                            onclick="togglePerm({{ $perm->id }}, this)"
                            data-hidden="{{ $perm->is_hidden ? '1' : '0' }}"
                            class="perm-toggle relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none
                                   {{ $perm->is_hidden ? 'bg-gray-200' : 'bg-blue-600' }}">
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200
                                     {{ $perm->is_hidden ? 'translate-x-0' : 'translate-x-5' }}">
                        </span>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function togglePerm(id, btn) {
    btn.disabled = true;
    try {
        const res  = await fetch(`{{ url('admin/permissions') }}/${id}/toggle`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (!data.status) throw new Error();

        const hidden = data.is_hidden;
        btn.dataset.hidden = hidden ? '1' : '0';

        // Toggle color
        btn.classList.toggle('bg-blue-600', !hidden);
        btn.classList.toggle('bg-gray-200',  hidden);

        // Move knob
        const knob = btn.querySelector('span');
        knob.classList.toggle('translate-x-5', !hidden);
        knob.classList.toggle('translate-x-0',  hidden);

        // Update dot + label
        const li   = btn.closest('li');
        const dot  = li.querySelector('.h-2');
        const label = li.querySelector('span.text-sm');

        dot.classList.toggle('bg-green-500', !hidden);
        dot.classList.toggle('bg-gray-300',   hidden);
        label.classList.toggle('text-gray-800',    !hidden);
        label.classList.toggle('text-gray-400',     hidden);
        label.classList.toggle('line-through',      hidden);

        showToast(hidden ? 'Item hidden from panel.' : 'Item visible in panel.', !hidden);
    } catch {
        showToast('Something went wrong.', false);
    } finally {
        btn.disabled = false;
    }
}

let _tt;
function showToast(msg, success) {
    const el = document.getElementById('toast');
    const mp = document.getElementById('toast-msg');
    mp.textContent = msg;
    el.className = `fixed top-5 right-5 z-50 max-w-xs rounded-xl border px-5 py-3 shadow-lg transition-all duration-300
        ${success ? 'border-green-200 bg-green-50 text-green-800' : 'border-red-200 bg-red-50 text-red-800'}`;
    clearTimeout(_tt);
    _tt = setTimeout(() => el.classList.add('hidden'), 2800);
}
</script>
@endpush
