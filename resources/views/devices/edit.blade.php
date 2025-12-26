@extends('layouts.app')

@section('title', 'Edit Device')
@section('page-title', 'Edit Device')
@section('page-subtitle', 'Update GPS tracking device information')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-md p-6">
        <form method="POST" action="{{ route('devices.update', $device->id) }}">
            @csrf
            @method('PUT')

            <!-- Device ID -->
            <div class="mb-6">
                <label for="device_id" class="block text-sm font-medium text-gray-700 mb-2">Device ID</label>
                <input type="text"
                       name="device_id"
                       id="device_id"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       value="{{ old('device_id', $device->device_id) }}">
                @error('device_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Animal Assignment -->
            <div class="mb-6">
                <label for="animal_id" class="block text-sm font-medium text-gray-700 mb-2">Assign to Animal</label>
                <select name="animal_id"
                        id="animal_id"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select an animal</option>
                    @foreach($animals as $animal)
                        <option value="{{ $animal->id }}" {{ (old('animal_id') ?? $device->animal_id) == $animal->id ? 'selected' : '' }}>
                            {{ $animal->name }} ({{ $animal->species }})
                        </option>
                    @endforeach
                </select>
                @error('animal_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status"
                        id="status"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="active" {{ (old('status') ?? $device->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ (old('status') ?? $device->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Battery Level -->
            <div class="mb-6">
                <label for="battery_level" class="block text-sm font-medium text-gray-700 mb-2">Battery Level (%)</label>
                <input type="number"
                       name="battery_level"
                       id="battery_level"
                       required
                       min="0"
                       max="100"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       value="{{ old('battery_level', $device->battery_level) }}">
                @error('battery_level')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Installation Date -->
            <div class="mb-6">
                <label for="installation_date" class="block text-sm font-medium text-gray-700 mb-2">Installation Date</label>
                <input type="date"
                       name="installation_date"
                       id="installation_date"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       value="{{ old('installation_date', $device->installation_date->format('Y-m-d')) }}">
                @error('installation_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex space-x-4">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Update Device
                </button>
                <a href="{{ route('devices.index') }}"
                   class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
