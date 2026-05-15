<div class="relative" x-data="notificationBell()" x-init="init()">

    {{-- Bell Button --}}
    <button @click="toggle()"
            class="relative flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        {{-- Unread badge --}}
        <span x-show="unreadCount > 0"
              x-cloak
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -right-0.5 -top-0.5 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white">
        </span>
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="open"
         x-cloak
         @click.outside="open = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 top-full z-50 mt-2 w-80 origin-top-right overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-gray-900">Notifications</span>
                <span x-show="unreadCount > 0"
                      x-cloak
                      x-text="unreadCount"
                      class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-600">
                </span>
            </div>
            <button x-show="unreadCount > 0"
                    x-cloak
                    @click="markAllRead()"
                    class="text-xs font-medium text-blue-600 hover:text-blue-700 hover:underline">
                Mark all read
            </button>
        </div>

        {{-- Notification List --}}
        <div class="max-h-96 overflow-y-auto">

            {{-- Loading state --}}
            <div x-show="loading" class="flex items-center justify-center py-8">
                <svg class="h-5 w-5 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </div>

            {{-- Empty state --}}
            <div x-show="!loading && notifications.length === 0" class="py-10 text-center">
                <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="mt-2 text-sm font-medium text-gray-500">No notifications yet</p>
                <p class="text-xs text-gray-400">You're all caught up!</p>
            </div>

            {{-- Notification items --}}
            <template x-if="!loading && notifications.length > 0">
                <ul class="divide-y divide-gray-50">
                    <template x-for="n in notifications" :key="n.id">
                        <li @click="markRead(n)"
                            class="group flex cursor-pointer items-start gap-3 px-4 py-3 transition-colors hover:bg-gray-50"
                            :class="{ 'bg-blue-50/50': !n.is_read }">

                            {{-- Type icon --}}
                            <div class="mt-0.5 flex-shrink-0">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg"
                                     :class="n.type_color">
                                    {{-- loan_status icon --}}
                                    <template x-if="n.type_icon === 'document'">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </template>
                                    {{-- assignment icon --}}
                                    <template x-if="n.type_icon === 'user'">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </template>
                                    {{-- document icon --}}
                                    <template x-if="n.type_icon === 'paper-clip'">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                    </template>
                                    {{-- bell icon --}}
                                    <template x-if="n.type_icon === 'bell'">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                    </template>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-1">
                                    <p class="text-xs font-semibold text-gray-900 leading-tight"
                                       :class="{ 'font-bold': !n.is_read }"
                                       x-text="n.title">
                                    </p>
                                    <span x-show="!n.is_read"
                                          class="mt-0.5 h-2 w-2 flex-shrink-0 rounded-full bg-blue-500">
                                    </span>
                                </div>
                                <p class="mt-0.5 line-clamp-2 text-xs text-gray-500" x-text="n.message"></p>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-[10px] font-medium text-gray-400" x-text="n.time"></span>
                                    <span class="text-[10px] font-medium uppercase tracking-wide"
                                          :class="n.type_color"
                                          x-text="n.type_label">
                                    </span>
                                </div>
                            </div>
                        </li>
                    </template>
                </ul>
            </template>
        </div>

        {{-- Footer --}}
        <div class="border-t border-gray-100 px-4 py-2.5">
            <a href="{{ route('notifications.index') }}"
               class="block text-center text-xs font-medium text-blue-600 hover:text-blue-700">
                View all notifications
            </a>
        </div>
    </div>
</div>

<script>
function notificationBell() {
    return {
        open: false,
        loading: false,
        notifications: [],
        unreadCount: 0,
        pollInterval: null,

        init() {
            this.fetchCount();
            this.pollInterval = setInterval(() => this.fetchCount(), 60000);
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.fetchDropdown();
            }
        },

        fetchCount() {
            fetch('{{ route('notifications.unread-count') }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => { if (data.status) this.unreadCount = data.count; })
            .catch(() => {});
        },

        fetchDropdown() {
            this.loading = true;
            fetch('{{ route('notifications.dropdown') }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status) {
                    this.notifications = data.notifications;
                    this.unreadCount   = data.unread_count;
                }
            })
            .catch(() => {})
            .finally(() => { this.loading = false; });
        },

        markRead(notification) {
            if (notification.is_read) return;

            fetch(`/notifications/${notification.id}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status) {
                    notification.is_read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            })
            .catch(() => {});
        },

        markAllRead() {
            fetch('{{ route('notifications.mark-all-read') }}', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status) {
                    this.notifications.forEach(n => n.is_read = true);
                    this.unreadCount = 0;
                }
            })
            .catch(() => {});
        },
    };
}
</script>
