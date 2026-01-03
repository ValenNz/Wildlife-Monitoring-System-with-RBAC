@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Wildlife Monitoring Overview')

@section('content')

<div class="space-y-6">

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Active Animals -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90 uppercase tracking-wide">Active Animals</p>
                        <p class="text-4xl font-bold mt-2">{{ number_format($activeAnimals) }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-2xl p-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- GPS Readings -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90 uppercase tracking-wide">GPS Readings</p>
                        <p class="text-4xl font-bold mt-2">{{ number_format($gpsReadings) }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-2xl p-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Protected Zones -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
            <div class="bg-gradient-to-r from-purple-500 to-fuchsia-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90 uppercase tracking-wide">Protected Zones</p>
                        <p class="text-4xl font-bold mt-2">{{ number_format($protectedZones) }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-2xl p-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Animals -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90 uppercase tracking-wide">Total Animals</p>
                        <p class="text-4xl font-bold mt-2">{{ number_format($totalAnimals) }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-2xl p-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Signal Status -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Devices -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Devices</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalDevices) }}</p>
                </div>
            </div>
        </div>

        <!-- Offline Devices -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-xl">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Offline Devices</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($offlineDevices) }}</p>
                </div>
            </div>
        </div>

        <!-- Low Battery -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-xl">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Low Battery</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($lowBatteryDevices) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Notifications -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900">Recent Notifications</h3>
            <a href="{{ route('notifications.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                View All
            </a>
        </div>
        @if($recentNotifications->count() > 0)
            <div class="space-y-4">
                @foreach($recentNotifications as $notification)
                <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                    <div class="mt-1 mr-3">
                        @if($notification->type === 'error')
                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                        @elseif($notification->type === 'warning')
                            <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                        @else
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        @endif
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ $notification->title }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                        <p class="text-xs text-gray-500 mt-2">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-8">No recent notifications</p>
        @endif
    </div>

    <!-- Monitored Animals Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Monitored Animals</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Showing {{ $monitoredAnimals->count() }} animals
                        @if($search)
                            <span class="text-blue-600 font-medium">· Search: "{{ $search }}"</span>
                        @endif
                    </p>
                </div>
                <div class="flex space-x-3">
                    <form method="GET" action="{{ route('dashboard.index') }}" class="flex items-center">
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Search animals..."
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <input type="hidden" name="sort" value="{{ $sortBy }}">
                        <input type="hidden" name="order" value="{{ $sortOrder }}">
                        <select name="per_page" class="ml-2 px-2 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="10" {{ $perPage == '10' ? 'selected' : '' }}>10</option>
                            <option value="100" {{ $perPage == '100' ? 'selected' : '' }}>100</option>
                            <option value="1000" {{ $perPage == '1000' ? 'selected' : '' }}>1000</option>
                            <option value="all" {{ $perPage == 'all' ? 'selected' : '' }}>All</option>
                        </select>
                        <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Apply</button>
                    </form>
                    @if($search)
                    <a href="{{ route('dashboard.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg text-sm">Clear</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            <a href="{{ route('dashboard.index', array_merge(request()->query(), ['sort' => 'name', 'order' => $sortOrder === 'asc' && $sortBy === 'name' ? 'desc' : 'asc'])) }}">
                                Name {{ $sortBy === 'name' ? ($sortOrder === 'asc' ? '↑' : '↓') : '' }}
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            <a href="{{ route('dashboard.index', array_merge(request()->query(), ['sort' => 'species', 'order' => $sortOrder === 'asc' && $sortBy === 'species' ? 'desc' : 'asc'])) }}">
                                Species {{ $sortBy === 'species' ? ($sortOrder === 'asc' ? '↑' : '↓') : '' }}
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tag ID</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            <a href="{{ route('dashboard.index', array_merge(request()->query(), ['sort' => 'status', 'order' => $sortOrder === 'asc' && $sortBy === 'status' ? 'desc' : 'asc'])) }}">
                                Status {{ $sortBy === 'status' ? ($sortOrder === 'asc' ? '↑' : '↓') : '' }}
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Last Seen</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($monitoredAnimals as $animal)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $animal->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $animal->species }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $animal->tag_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($animal->device_status === 'active')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            @if($animal->last_seen)
                                {{ \Carbon\Carbon::parse($animal->last_seen)->diffForHumans() }}
                            @else
                                <span class="text-gray-400">Never</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('animals.show', $animal->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p>No animals found</p>
                            @if($search)
                                <p class="text-sm mt-2">Try adjusting your search</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($paginatedAnimals)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $paginatedAnimals->appends(['search' => $search, 'sort' => $sortBy, 'order' => $sortOrder, 'per_page' => $perPage])->links('pagination::tailwind') }}
        </div>
        @endif
    </div>
</div>

@endsection
