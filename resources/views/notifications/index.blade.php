@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('page-header')
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Notifications</h2>
            <p class="text-sm text-gray-500">
                @if($unreadCount > 0)
                    You have <span class="font-semibold text-blue-600">{{ $unreadCount }}</span> unread notification{{ $unreadCount !== 1 ? 's' : '' }}.
                @else
                    You're all caught up!
                @endif
            </p>
        </div>
        @if($unreadCount > 0)
            <button onclick="markAllReadPage()"
                    id="btn-mark-all"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50">
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Mark all as read
            </button>
        @endif
    </div>
@endsection

@section('content')

    {{-- Filters --}}
    <form method="GET" action="{{ route('notifications.index') }}"
          class="mb-5 flex flex-wrap gap-3">
        <select name="type"
                onchange="this.form.submit()"
                class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            <option value="">All Types</option>
            <option value="loan_status" @selected(request('type') === 'loan_status')>Loan Status</option>
            <option value="assignment"  @selected(request('type') === 'assignment')>Assignment</option>
            <option value="document"    @selected(request('type') === 'document')>Document</option>
            <option value="system"      @selected(request('type') === 'system')>System</option>
        </select>

        <select name="status"
                onchange="this.form.submit()"
                class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            <option value="">All Status</option>
            <option value="unread" @selected(request('status') === 'unread')>Unread</option>
            <option value="read"   @selected(request('status') === 'read')>Read</option>
        </select>

        @if(request()->hasAny(['type', 'status']))
            <a href="{{ route('notifications.index') }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-500 shadow-sm hover:bg-gray-50">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Clear
            </a>
        @endif
    </form>

    {{-- Notifications list --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        @forelse($notifications as $notification)
            <div id="notif-{{ $notification->id }}"
                 class="group flex items-start gap-4 border-b border-gray-100 px-5 py-4 last:border-b-0 transition-colors hover:bg-gray-50
                         {{ !$notification->is_read ? 'bg-blue-50/40' : '' }}">

                {{-- Icon --}}
                <div class="mt-0.5 flex-shrink-0">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $notification->type_color }}">
                        @if($notification->type_icon === 'document')
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        @elseif($notification->type_icon === 'user')
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        @elseif($notification->type_icon === 'paper-clip')
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                        @else
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        @endif
                    </div>
                </div>

                {{-- Body --}}
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-gray-900 {{ !$notification->is_read ? 'font-bold' : '' }}">
                                    {{ $notification->title }}
                                </p>
                                @if(!$notification->is_read)
                                    <span class="h-2 w-2 flex-shrink-0 rounded-full bg-blue-500"></span>
                                @endif
                            </div>
                            <p class="mt-0.5 text-sm text-gray-600">{{ $notification->message }}</p>
                            <div class="mt-1.5 flex items-center gap-3">
                                <span class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide {{ $notification->type_color }}">
                                    {{ $notification->type_label }}
                                </span>
                                @if($notification->read_at)
                                    <span class="text-xs text-gray-400">
                                        Read {{ $notification->read_at->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-shrink-0 items-center gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                            @if(!$notification->is_read)
                                <button onclick="markReadSingle({{ $notification->id }})"
                                        title="Mark as read"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-blue-50 hover:text-blue-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                            @endif
                            <button onclick="deleteNotification({{ $notification->id }})"
                                    title="Delete"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-16 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="mt-3 text-sm font-medium text-gray-500">No notifications found</p>
                <p class="mt-1 text-xs text-gray-400">
                    @if(request()->hasAny(['type', 'status']))
                        Try adjusting your filters.
                    @else
                        You're all caught up!
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="mt-5">
            {{ $notifications->links() }}
        </div>
    @endif

@endsection

@push('scripts')
<script>
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    function markReadSingle(id) {
        fetch(`/notifications/${id}/read`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status) {
                const el = document.getElementById(`notif-${id}`);
                if (el) {
                    el.classList.remove('bg-blue-50/40');
                    el.querySelectorAll('.h-2.w-2.rounded-full.bg-blue-500').forEach(dot => dot.remove());
                    el.querySelectorAll('button[onclick*="markReadSingle"]').forEach(btn => btn.parentElement.removeChild(btn));
                }
            }
        })
        .catch(() => {});
    }

    function markAllReadPage() {
        fetch('{{ route('notifications.mark-all-read') }}', {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status) { location.reload(); }
        })
        .catch(() => {});
    }

    function deleteNotification(id) {
        if (!confirm('Delete this notification?')) return;
        fetch(`/notifications/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status) {
                const el = document.getElementById(`notif-${id}`);
                if (el) {
                    el.style.transition = 'opacity 0.2s';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 200);
                }
            }
        })
        .catch(() => {});
    }
</script>
@endpush
