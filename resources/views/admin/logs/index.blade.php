@extends('layouts.app')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')

@section('content')
<div class="flex flex-col items-center justify-center py-24 text-center">
    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 mb-4">
        <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
        </svg>
    </div>
    <h2 class="text-xl font-semibold text-gray-800">Activity Logs</h2>
    <p class="mt-1 text-sm text-gray-500">Activity logs module coming soon.</p>
</div>
@endsection
