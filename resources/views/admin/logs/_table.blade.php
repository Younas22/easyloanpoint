@php
$badgeCss = [
    'green'  => 'bg-green-100  text-green-700  ring-green-200',
    'gray'   => 'bg-slate-100  text-slate-600  ring-slate-200',
    'blue'   => 'bg-blue-100   text-blue-700   ring-blue-200',
    'indigo' => 'bg-indigo-100 text-indigo-700 ring-indigo-200',
    'purple' => 'bg-purple-100 text-purple-700 ring-purple-200',
    'orange' => 'bg-orange-100 text-orange-700 ring-orange-200',
    'violet' => 'bg-violet-100 text-violet-700 ring-violet-200',
    'yellow' => 'bg-yellow-100 text-yellow-700 ring-yellow-200',
    'teal'   => 'bg-teal-100   text-teal-700   ring-teal-200',
    'red'    => 'bg-red-100    text-red-700    ring-red-200',
    'slate'  => 'bg-slate-100  text-slate-600  ring-slate-200',
];
@endphp

<div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-3.5">
        <p class="text-sm font-semibold text-slate-700">
            {{ number_format($logs->total()) }} {{ Str::plural('record', $logs->total()) }} found
        </p>
        <p class="text-xs text-slate-400">Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</p>
    </div>

    @if($logs->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 mb-3">
                <svg class="h-7 w-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-600">No activity logs found</p>
            <p class="mt-1 text-xs text-slate-400">Try adjusting your filters.</p>
        </div>
    @else

    {{-- Timeline / Table --}}
    <div class="divide-y divide-slate-100">
        @foreach($logs as $log)
        @php $color = $badgeCss[$log->badge_color] ?? $badgeCss['slate']; @endphp
        <div class="flex gap-4 px-5 py-4 hover:bg-slate-50 transition-colors">

            {{-- Timeline dot --}}
            <div class="mt-0.5 flex-shrink-0">
                <div class="h-2.5 w-2.5 rounded-full ring-2 ring-offset-2
                    @if($log->badge_color==='green')  bg-green-500  ring-green-300
                    @elseif($log->badge_color==='red')    bg-red-500    ring-red-300
                    @elseif($log->badge_color==='blue')   bg-blue-500   ring-blue-300
                    @elseif($log->badge_color==='indigo') bg-indigo-500 ring-indigo-300
                    @elseif($log->badge_color==='purple') bg-purple-500 ring-purple-300
                    @elseif($log->badge_color==='orange') bg-orange-500 ring-orange-300
                    @elseif($log->badge_color==='teal')   bg-teal-500   ring-teal-300
                    @elseif($log->badge_color==='yellow') bg-yellow-500 ring-yellow-300
                    @elseif($log->badge_color==='violet') bg-violet-500 ring-violet-300
                    @else bg-slate-400 ring-slate-200
                    @endif">
                </div>
            </div>

            {{-- Main content --}}
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-1">

                    {{-- Action badge --}}
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 {{ $color }}">
                        {{ $log->action_label }}
                    </span>

                    {{-- Role badge --}}
                    <span class="inline-flex items-center rounded-full bg-slate-800 px-2 py-0.5 text-xs font-medium text-white">
                        {{ $log->role_label }}
                    </span>

                    {{-- Timestamp --}}
                    <span class="text-xs text-slate-400 ml-auto whitespace-nowrap">
                        {{ $log->created_at->format('d M Y, h:i A') }}
                        <span class="ml-1 text-slate-300">({{ $log->created_at->diffForHumans() }})</span>
                    </span>
                </div>

                {{-- Description --}}
                <p class="text-sm text-slate-700 leading-relaxed">{{ $log->description }}</p>

                {{-- Meta row --}}
                <div class="mt-1.5 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-400">
                    @if($log->user)
                        <span class="flex items-center gap-1">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $log->user->name }}
                        </span>
                    @endif
                    @if($log->ip_address)
                        <span class="flex items-center gap-1">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                            {{ $log->ip_address }}
                        </span>
                    @endif
                    @if($log->user_agent)
                        <span class="flex items-center gap-1">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ $log->browser }}
                        </span>
                    @endif
                </div>
            </div>

        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
    <div class="border-t border-slate-100 px-5 py-3.5 flex items-center justify-between">
        <p class="text-xs text-slate-500">
            Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}
        </p>
        <div class="flex items-center gap-1">

            {{-- Previous --}}
            @if($logs->onFirstPage())
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-300 cursor-not-allowed">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </span>
            @else
                <a href="{{ $logs->previousPageUrl() }}" data-ajax
                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </a>
            @endif

            {{-- Page numbers --}}
            @foreach($logs->getUrlRange(max(1, $logs->currentPage()-2), min($logs->lastPage(), $logs->currentPage()+2)) as $page => $url)
                @if($page == $logs->currentPage())
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-xs font-semibold text-white">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}" data-ajax
                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            {{-- Next --}}
            @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" data-ajax
                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-300 cursor-not-allowed">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif

        </div>
    </div>
    @endif

    @endif
</div>
