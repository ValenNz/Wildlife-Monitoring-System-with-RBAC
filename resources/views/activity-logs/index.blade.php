@extends('layouts.app')

@section('title', 'Activity Logs')
@section('page-title', 'System Activity Logs')
@section('page-subtitle', 'Audit trail of all system activities')

@section('content')

<div class="space-y-6">

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6">
            <p class="text-sm text-gray-600">Total Logs</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalLogs) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6">
            <p class="text-sm text-gray-600">Today's Logs</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($todayLogs) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md p-4">
        <form method="GET" action="{{ route('activity-logs.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search logs..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Level</label>
                <select name="level" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="">All Levels</option>
                    <option value="info" {{ request('level') == 'info' ? 'selected' : '' }}>Info</option>
                    <option value="warning" {{ request('level') == 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="error" {{ request('level') == 'error' ? 'selected' : '' }}>Error</option>
                    <option value="debug" {{ request('level') == 'debug' ? 'selected' : '' }}>Debug</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">
                    Filter
                </button>
                @if(request()->query())
                    <a href="{{ route('activity-logs.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md text-sm">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        @if($logs->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Context</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    @if($log->level === 'info') bg-blue-100 text-blue-800
                                    @elseif($log->level === 'warning') bg-yellow-100 text-yellow-800
                                    @elseif($log->level === 'error') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($log->level) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ Str::limit($log->message, 100) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ Str::limit($log->context ?? '-', 50) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $logs->appends(request()->query())->links('pagination::tailwind', ['paginator' => 'activity-logs.index']) }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No activity logs</h3>
                <p class="text-gray-500">
                @if(request()->query())
                        No logs match your search.
                    @else
                        System activity logs will appear here.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

@endsection
