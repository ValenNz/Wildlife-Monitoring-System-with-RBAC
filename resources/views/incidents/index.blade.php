@extends('layouts.app')

@section('title', 'Incidents')
@section('page-title', 'Incident Management')
@section('page-subtitle', 'Track and manage wildlife incidents')

@section('content')

<div class="space-y-6">

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Incidents</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalIncidents) }}</p>
                </div>
                <div class="bg-blue-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Open</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ number_format($openIncidents) }}</p>
                </div>
                <div class="bg-orange-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Critical</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">{{ number_format($criticalIncidents) }}</p>
                </div>
                <div class="bg-red-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Resolved Today</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($resolvedToday) }}</p>
                </div>
                <div class="bg-green-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg" role="alert">
        <p class="font-medium">Success!</p>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
        <p class="font-medium">Error!</p>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <!-- Incidents Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <!-- Header with Search & Filters -->
        <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">All Incidents</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Showing {{ $incidents->count() }} of {{ number_format($incidents->total()) }} incidents
                    </p>
                </div>

                <a href="{{ route('incidents.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Incident
                </a>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('incidents.index') }}" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                <!-- Search -->
                <input type="text" name="search" value="{{ $search }}" placeholder="Search incidents..."
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                <!-- Severity Filter -->
                <select name="severity" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Severities</option>
                    <option value="low" {{ $severity === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ $severity === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ $severity === 'high' ? 'selected' : '' }}>High</option>
                    <option value="critical" {{ $severity === 'critical' ? 'selected' : '' }}>Critical</option>
                </select>

                <!-- Status Filter -->
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    <option value="open" {{ $status === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="investigating" {{ $status === 'investigating' ? 'selected' : '' }}>Investigating</option>
                    <option value="resolved" {{ $status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>

                <!-- Submit -->
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded-lg transition-colors">
                        Filter
                    </button>
                    @if($search || $severity || $status)
                    <a href="{{ route('incidents.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                        Clear
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Incident</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Animal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Severity</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Assigned To</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($incidents as $incident)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $incident->title }}</div>
                                <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($incident->description, 60) }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($incident->animal_name)
                                <div class="font-medium text-gray-900">{{ $incident->animal_name }}</div>
                                <div class="text-sm text-gray-500">{{ $incident->species }}</div>
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($incident->severity === 'critical')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Critical</span>
                            @elseif($incident->severity === 'high')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">High</span>
                            @elseif($incident->severity === 'medium')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Medium</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Low</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($incident->status === 'open')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">Open</span>
                            @elseif($incident->status === 'investigating')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Investigating</span>
                            @elseif($incident->status === 'resolved')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Resolved</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">Closed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($incident->assigned_user)
                                <div class="text-sm font-medium text-gray-900">{{ $incident->assigned_user }}</div>
                            @else
                                <span class="text-gray-400">Unassigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ Carbon\Carbon::parse($incident->created_at)->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500">{{ Carbon\Carbon::parse($incident->created_at)->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('incidents.show', $incident->id) }}" class="p-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('incidents.edit', $incident->id) }}" class="p-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <p class="text-lg font-medium text-gray-600">No incidents found</p>
                            <p class="text-sm text-gray-400 mt-2">Create your first incident to get started</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($incidents->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $incidents->links('pagination::tailwind') }}
        </div>
        @endif
    </div>

</div>

@endsection
