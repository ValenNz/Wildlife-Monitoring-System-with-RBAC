@extends('layouts.app')

@section('title', 'Environmental Data - Wildlife Database')
@section('page-title', 'Environmental Data')
@section('page-subtitle', 'Monitor environmental conditions from tracking devices')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    * {
        font-family: 'Outfit', sans-serif;
    }

    code, .mono {
        font-family: 'JetBrains Mono', monospace;
    }

    /* Custom Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    .pulse {
        animation: pulse 2s ease-in-out infinite;
    }

    .slide-in {
        animation: slideInRight 0.5s ease-out forwards;
    }

    /* Stagger animation delays */
    .delay-1 { animation-delay: 0.1s; opacity: 0; }
    .delay-2 { animation-delay: 0.2s; opacity: 0; }
    .delay-3 { animation-delay: 0.3s; opacity: 0; }
    .delay-4 { animation-delay: 0.4s; opacity: 0; }

    /* Custom gradient backgrounds */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    /* Custom hover effects */
    .hover-lift {
        transition: all 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* Status indicators */
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }

    .status-online { background-color: #10b981; }
    .status-offline { background-color: #ef4444; }
    .status-warning { background-color: #f59e0b; }
</style>
@endpush

@section('content')

<div class="space-y-6">

    <!-- 1️⃣ STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        <!-- Total Records Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover-lift fade-in-up delay-1">
            <div class="bg-gradient-primary p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90 uppercase tracking-wide">Total Records</p>
                        <p class="text-4xl font-bold mt-2">{{ number_format($totalRecords) }}</p>
                        <p class="text-xs opacity-75 mt-1">Environmental data</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-2xl p-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gradient-to-r from-purple-50 to-transparent">
                <div class="flex items-center text-sm text-purple-700">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">All Time Data</span>
                </div>
            </div>
        </div>

        <!-- Average Temperature Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover-lift fade-in-up delay-2">
            <div class="bg-gradient-warning p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90 uppercase tracking-wide">Avg Temperature</p>
                        <p class="text-4xl font-bold mt-2">{{ number_format($avgTemperature, 1) }}°C</p>
                        <p class="text-xs opacity-75 mt-1">Current average</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-2xl p-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gradient-to-r from-pink-50 to-transparent">
                <div class="flex items-center text-sm text-pink-700">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Climate Monitoring</span>
                </div>
            </div>
        </div>

        <!-- Average Humidity Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover-lift fade-in-up delay-3">
            <div class="bg-gradient-info p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90 uppercase tracking-wide">Avg Humidity</p>
                        <p class="text-4xl font-bold mt-2">{{ number_format($avgHumidity, 1) }}%</p>
                        <p class="text-xs opacity-75 mt-1">Moisture level</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-2xl p-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gradient-to-r from-blue-50 to-transparent">
                <div class="flex items-center text-sm text-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Air Quality</span>
                </div>
            </div>
        </div>

        <!-- Active Devices Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover-lift fade-in-up delay-4">
            <div class="bg-gradient-success p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90 uppercase tracking-wide">Active Devices</p>
                        <p class="text-4xl font-bold mt-2">{{ number_format($activeDevices) }}</p>
                        <p class="text-xs opacity-75 mt-1">Operational sensors</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-2xl p-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gradient-to-r from-green-50 to-transparent">
                <div class="flex items-center text-sm text-green-700">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Online & Recording</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2️⃣ ENVIRONMENTAL DATA TABLE -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden slide-in">

        <!-- Header with Search & Filters -->
        <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                        </svg>
                        Environmental Monitoring Data
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Showing {{ is_countable($environmentalData) ? count($environmentalData) : $environmentalData->count() }}
                        @if(method_exists($environmentalData, 'total'))
                            of {{ number_format($environmentalData->total()) }}
                        @endif
                        records
                        @if(request('search'))
                            <span class="text-blue-600 font-medium">· Search: "{{ request('search') }}"</span>
                        @endif
                    </p>
                </div>

                <!-- Search & Controls -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">

                    <!-- Search Bar -->
                    <form method="GET" action="{{ route('weather.index') }}" class="relative flex-1 sm:w-64">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search Device ID..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>

                        <!-- Hidden inputs to preserve sort & pagination -->
                        <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                        <input type="hidden" name="pagination" value="{{ request('pagination', '10') }}">
                    </form>

                    <!-- Clear Search Button -->
                    @if(request('search'))
                    <a href="{{ route('weather.index') }}"
                       class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-center flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear
                    </a>
                    @endif

                    <!-- Pagination Selector -->
                    <select onchange="window.location.href=this.value"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="{{ route('weather.index', array_merge(request()->except('pagination'), ['pagination' => '10'])) }}"
                                {{ request('pagination', '10') == '10' ? 'selected' : '' }}>10 per page</option>
                        <option value="{{ route('weather.index', array_merge(request()->except('pagination'), ['pagination' => '25'])) }}"
                                {{ request('pagination') == '25' ? 'selected' : '' }}>25 per page</option>
                        <option value="{{ route('weather.index', array_merge(request()->except('pagination'), ['pagination' => '50'])) }}"
                                {{ request('pagination') == '50' ? 'selected' : '' }}>50 per page</option>
                        <option value="{{ route('weather.index', array_merge(request()->except('pagination'), ['pagination' => 'all'])) }}"
                                {{ request('pagination') == 'all' ? 'selected' : '' }}>Show All</option>
                    </select>

                    <!-- Export Button -->
                    {{-- <a href="{{ route('weather.export', request()->all()) }}"
                       target="_blank"
                       class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export CSV
                    </a>
                </div> --}}
            </div>

            <!-- Sort Options -->
            <div class="flex items-center space-x-2 mt-4">
                <span class="text-sm font-medium text-gray-700">Sort by:</span>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('weather.index', array_merge(request()->except('sort_by'), ['sort_by' => request('sort_by') === 'recorded_at_asc' ? 'recorded_at_desc' : 'recorded_at_asc'])) }}"
                       class="px-3 py-1 rounded-lg text-sm font-medium transition-colors {{ in_array(request('sort_by'), ['recorded_at_asc', 'recorded_at_desc']) ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Time
                        @if(in_array(request('sort_by'), ['recorded_at_asc', 'recorded_at_desc']))
                            <span class="ml-1">{{ request('sort_by') === 'recorded_at_asc' ? '↑' : '↓' }}</span>
                        @endif
                    </a>
                    <a href="{{ route('weather.index', array_merge(request()->except('sort_by'), ['sort_by' => request('sort_by') === 'temperature_asc' ? 'temperature_desc' : 'temperature_asc'])) }}"
                       class="px-3 py-1 rounded-lg text-sm font-medium transition-colors {{ in_array(request('sort_by'), ['temperature_asc', 'temperature_desc']) ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Temperature
                        @if(in_array(request('sort_by'), ['temperature_asc', 'temperature_desc']))
                            <span class="ml-1">{{ request('sort_by') === 'temperature_asc' ? '↑' : '↓' }}</span>
                        @endif
                    </a>
                    <a href="{{ route('weather.index', array_merge(request()->except('sort_by'), ['sort_by' => request('sort_by') === 'humidity_asc' ? 'humidity_desc' : 'humidity_asc'])) }}"
                       class="px-3 py-1 rounded-lg text-sm font-medium transition-colors {{ in_array(request('sort_by'), ['humidity_asc', 'humidity_desc']) ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Humidity
                        @if(in_array(request('sort_by'), ['humidity_asc', 'humidity_desc']))
                            <span class="ml-1">{{ request('sort_by') === 'humidity_asc' ? '↑' : '↓' }}</span>
                        @endif
                    </a>
                    <a href="{{ route('weather.index', array_merge(request()->except('sort_by'), ['sort_by' => request('sort_by') === 'pressure_asc' ? 'pressure_desc' : 'pressure_asc'])) }}"
                       class="px-3 py-1 rounded-lg text-sm font-medium transition-colors {{ in_array(request('sort_by'), ['pressure_asc', 'pressure_desc']) ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Pressure
                        @if(in_array(request('sort_by'), ['pressure_asc', 'pressure_desc']))
                            <span class="ml-1">{{ request('sort_by') === 'pressure_asc' ? '↑' : '↓' }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Device ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Temperature</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Humidity</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Pressure</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Light Level</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Recorded At</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($environmentalData as $data)
                    <tr class="hover:bg-blue-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">#{{ $data->id }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <code class="bg-blue-100 text-blue-800 px-3 py-1 rounded-md text-xs font-mono">{{ $data->device_id }}</code>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($data->temperature > 30)
                                    <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-red-700">{{ number_format($data->temperature, 1) }}°C</span>
                                @elseif($data->temperature < 10)
                                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-blue-700">{{ number_format($data->temperature, 1) }}°C</span>
                                @else
                                    <svg class="w-5 h-5 text-orange-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-900">{{ number_format($data->temperature, 1) }}°C</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.5 2a3.5 3.5 0 101.665 6.58L8.585 10l-1.42 1.42a3.5 3.5 0 101.414 1.414l8.128-8.127a1 1 0 00-1.414-1.414L7.165 11.42A3.5 3.5 0 105.5 2z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($data->humidity, 1) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ number_format($data->pressure, 2) }} hPa</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-20 bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-gradient-to-r from-yellow-300 to-yellow-500 h-2.5 rounded-full transition-all"
                                         style="width: {{ min($data->light_level, 100) }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-gray-700">{{ number_format($data->light_level, 1) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium">{{ \Carbon\Carbon::parse($data->recorded_at)->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($data->recorded_at)->format('H:i:s') }}</div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                            </svg>
                            <p class="text-lg font-medium text-gray-600">
                                @if(request('search'))
                                    No data found matching "{{ request('search') }}"
                                @else
                                    No environmental data available
                                @endif
                            </p>
                            <p class="text-sm text-gray-400 mt-2">
                                @if(request('search'))
                                    Try adjusting your search terms
                                @else
                                    Environmental readings will appear here once devices start recording
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        @if(request('pagination') != 'all' && method_exists($environmentalData, 'links'))
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ $environmentalData->firstItem() ?? 0 }}</span>
                    to <span class="font-medium">{{ $environmentalData->lastItem() ?? 0 }}</span>
                    of <span class="font-medium">{{ $environmentalData->total() }}</span> results
                </div>
                <div>
                    {{ $environmentalData->appends(request()->query())->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
        @elseif(request('pagination') === 'all')
        <div class="px-6 py-4 bg-blue-50 border-t border-blue-200 text-center">
            <p class="text-sm text-blue-700 font-medium">
                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                Showing all {{ is_countable($environmentalData) ? count($environmentalData) : $environmentalData->count() }} records (no pagination)
            </p>
        </div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
<script>
    // Auto-submit form on pagination change
    document.querySelectorAll('select[onchange]').forEach(function(select) {
        // Already has onchange handler
    });

    // Add smooth scroll to top when pagination changes
    if (window.location.search.includes('page=')) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>
@endpush
