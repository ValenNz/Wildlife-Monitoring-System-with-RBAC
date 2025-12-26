@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')
@section('page-subtitle', 'Generate and view statistical reports')

@section('content')

<div class="space-y-6">

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Animals</p>
                    <!-- ✅ Diperbaiki: gunakan $totalAnimals -->
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalAnimals) }}</p>
                </div>
                <div class="bg-purple-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Active Devices</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($activeDevices) }}</p>
                </div>
                <div class="bg-green-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">GPS Readings</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($totalTracking) }}</p>
                </div>
                <div class="bg-blue-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Types -->
   <!-- Report Types -->
<div class="bg-white rounded-2xl shadow-lg p-6">
    <h3 class="text-xl font-bold text-gray-900 mb-6">Generate Report</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $reportTypes = [
                'activity' => ['title' => 'Animal Activity', 'desc' => 'Movement patterns and behavior analysis', 'color' => 'blue'],
                'device' => ['title' => 'Device Performance', 'desc' => 'Uptime, battery, and signal quality', 'color' => 'green'],
                'environmental' => ['title' => 'Environmental', 'desc' => 'Temperature, humidity, and conditions', 'color' => 'purple'],
                'incident' => ['title' => 'Incidents', 'desc' => 'Alerts, breaches, and anomalies', 'color' => 'red']
            ];
        @endphp

        @foreach($reportTypes as $type => $info)
        <a href="{{ route('reports.create', ['report_type' => $type]) }}"
           class="block border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
            <div class="bg-{{ $info['color'] }}-100 rounded-lg p-3 w-fit mb-4">
                <svg class="w-8 h-8 text-{{ $info['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($type === 'activity')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    @elseif($type === 'device')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    @elseif($type === 'environmental')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                    @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    @endif
                </svg>
            </div>
            <h4 class="font-bold text-gray-900 mb-2">{{ $info['title'] }}</h4>
            <p class="text-sm text-gray-600 mb-4">{{ $info['desc'] }}</p>
            <div class="w-full px-4 py-2 bg-{{ $info['color'] }}-600 hover:bg-{{ $info['color'] }}-700 text-white rounded-lg transition-colors text-sm font-medium text-center">
                Generate
            </div>
        </a>
        @endforeach
    </div>
</div>

    <!-- Recent Reports (Placeholder) -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-6">Recent Reports</h3>
        <div class="text-center py-12 text-gray-500">
            <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-lg font-medium mb-2">No reports generated yet</p>
            <p class="text-sm text-gray-400">Generate your first report using the cards above</p>
        </div>
    </div>
</div>

@endsection
