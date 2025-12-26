@extends('layouts.app')

@section('title', 'Device Details')
@section('page-title', 'Device Details')
@section('page-subtitle', 'View GPS tracking device information')

@section('content')

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Device Information -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">Device Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Device ID</span>
                        <p class="mt-1 text-gray-900 font-mono">{{ $device->device_id }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Status</span>
                        <p class="mt-1">
                            @if($device->status === 'active')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Active</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Inactive</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Battery Level</span>
                        <p class="mt-1">{{ $device->battery_level }}%</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Installation Date</span>
                        <p class="mt-1">{{ \Carbon\Carbon::parse($device->installation_date)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Last Seen</span>
                        <p class="mt-1">
                            @if($device->last_seen)
                                {{ \Carbon\Carbon::parse($device->last_seen)->diffForHumans() }}
                            @else
                                <span class="text-gray-400">Never</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Animal Information -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">Assigned Animal</h3>
                @if($device->animal_name)
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Name</span>
                        <p class="mt-1 text-gray-900">{{ $device->animal_name }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Species</span>
                        <p class="mt-1 text-gray-900">{{ $device->species_name }}</p>
                    </div>
                </div>
                @else
                <p class="text-gray-500">This device is not assigned to any animal.</p>
                @endif
            </div>
        </div>

        <div class="mt-8 flex space-x-4">
            <a href="{{ route('devices.edit', $device->id) }}"
               class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                Edit Device
            </a>
            <a href="{{ route('devices.index') }}"
               class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                Back to List
            </a>
        </div>
    </div>
</div>

@endsection
