@if(isset($isTruncated) && $isTruncated)
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
        <p>Hanya menampilkan 10.000 data pertama untuk performa.
           <a href="{{ route('historical-tracking.export', request()->all()) }}" class="underline">Ekspor semua data ke CSV</a>.
        </p>
    </div>
@endif

@extends('layouts.app')

@section('title', 'Tracking Data - Wildlife Database')
@section('page-title', 'GPS Tracking Data')
@section('page-subtitle', 'Real-time location monitoring of wildlife')

@push('styles')
<style>
    .compass-icon {
        display: inline-block;
        transition: transform 0.3s ease;
    }
    .speed-high {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    .speed-low {
        background-color: #dcfce7;
        color: #166534;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Statistik Ringkas -->
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center">
            <div class="bg-blue-100 p-3 rounded-lg mr-4">
                <i class="fas fa-map-location-dot text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Tracking Records</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($totalRecords) }}</p>
            </div>
        </div>
    </div>

    <!-- Kontrol & Tabel -->
    <div class="bg-white rounded-lg shadow overflow-hidden">

        <!-- Header dengan Fitur -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">GPS Tracking History</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Showing {{ $trackingData instanceof \Illuminate\Pagination\LengthAwarePaginator ? $trackingData->firstItem() . '-' . $trackingData->lastItem() : count($trackingData) }}
                        of {{ number_format($totalRecords) }} records
                        @if(request('search'))
                            <span class="text-blue-600"> · Filter: "{{ request('search') }}"</span>
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <!-- Search -->
                    <form method="GET" action="{{ route('historical-tracking.index') }}" class="relative">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search Device ID..."
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="hidden" name="sort_by" value="{{ request('sort_by', 'recorded_at_desc') }}">
                        <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                    </form>

                    <!-- Tombol Aksi -->
                    <div class="flex gap-2">
                        <a href="{{ route('map.index') }}"
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                            <i class="fas fa-map mr-2"></i>View Map
                        </a>
                        <a href="{{ route('historical-tracking.index', request()->all()) }}"
                           target="_blank"
                           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>

                    <!-- Pagination Selector -->
                    <select onchange="window.location.href=this.value"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="{{ route('historical-tracking.index', array_merge(request()->except('per_page'), ['per_page' => 10])) }}"
                                {{ request('per_page', 10) == 10 ? 'selected' : '' }}>
                            10 per page
                        </option>
                        <option value="{{ route('historical-tracking.index', array_merge(request()->except('per_page'), ['per_page' => 25])) }}"
                                {{ request('per_page') == 25 ? 'selected' : '' }}>
                            25 per page
                        </option>
                        <option value="{{ route('historical-tracking.index', array_merge(request()->except('per_page'), ['per_page' => 50])) }}"
                                {{ request('per_page') == 50 ? 'selected' : '' }}>
                            50 per page
                        </option>
                        <option value="{{ route('historical-tracking.index', array_merge(request()->except('per_page'), ['per_page' => 'all'])) }}"
                                {{ request('per_page') == 'all' ? 'selected' : '' }}>
                            Show All
                        </option>
                    </select>
                </div>
            </div>

            <!-- Sorting Controls -->
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="text-sm font-medium text-gray-700">Sort by:</span>
                @php
                    $sortOptions = [
                        'recorded_at_desc' => 'Time (Newest)',
                        'recorded_at_asc' => 'Time (Oldest)',
                        'speed_desc' => 'Speed (High)',
                        'speed_asc' => 'Speed (Low)',
                        'altitude_desc' => 'Altitude (High)',
                        'altitude_asc' => 'Altitude (Low)',
                        'id_desc' => 'ID (High)',
                        'id_asc' => 'ID (Low)',
                    ];
                @endphp
                @foreach($sortOptions as $key => $label)
                    <a href="{{ route('historical-tracking.index', array_merge(request()->except('sort_by'), ['sort_by' => $key])) }}"
                       class="px-3 py-1 rounded-lg text-sm font-medium transition-colors
                              {{ request('sort_by', 'recorded_at_desc') == $key ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Latitude</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Longitude</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Altitude (m)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Speed (km/h)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heading</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recorded At</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($trackingData as $data)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $data->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <code class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">{{ $data->device_id }}</code>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="flex items-center font-mono text-xs">
                                <i class="fas fa-location-dot mr-2 text-red-500"></i>
                                {{ number_format($data->latitude, 6) }}°
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="flex items-center font-mono text-xs">
                                <i class="fas fa-location-dot mr-2 text-blue-500"></i>
                                {{ number_format($data->longitude, 6) }}°
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="flex items-center">
                                <i class="fas fa-mountain mr-2 text-gray-500"></i>
                                {{ number_format($data->altitude ?? 0, 1) }} m
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                {{ ($data->speed ?? 0) > 10 ? 'speed-high' : 'speed-low' }}">
                                <i class="fas fa-gauge mr-1"></i>
                                {{ number_format($data->speed ?? 0, 1) }} km/h
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="flex items-center">
                                <i class="fas fa-compass mr-2 text-blue-500 compass-icon"
                                   style="transform: rotate({{ $data->heading ?? 0 }}deg);"></i>
                                {{ number_format($data->heading ?? 0, 1) }}°
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex flex-col">
                                <span>{{ \Carbon\Carbon::parse($data->recorded_at)->format('d M Y') }}</span>
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($data->recorded_at)->format('H:i:s') }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-map-location-dot text-4xl mb-2"></i>
                            <p class="mt-2">No tracking data available</p>
                            @if(request('search'))
                                <p class="text-sm mt-1">Try adjusting your search terms</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(request('per_page') != 'all' && $trackingData instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing <span class="font-medium">{{ $trackingData->firstItem() }}</span>
                to <span class="font-medium">{{ $trackingData->lastItem() }}</span>
                of <span class="font-medium">{{ $trackingData->total() }}</span> results
            </div>
            <div>
                {{ $trackingData->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        </div>
        @elseif(request('per_page') == 'all')
        <div class="px-6 py-4 bg-blue-50 border-t border-blue-200 text-center">
            <p class="text-sm text-blue-700">
                Showing all {{ count($trackingData) }} records (no pagination)
            </p>
        </div>
        @endif
    </div>
</div>
@endsection
