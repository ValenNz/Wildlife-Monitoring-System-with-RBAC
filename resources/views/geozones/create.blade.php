@extends('layouts.app')

@section('title', 'Add Risk Zone')
@section('page-title', 'Add New Risk Zone')
@section('page-subtitle', 'Define a new area of risk or protection')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-md p-6">
        <form method="POST" action="{{ route('geozones.store') }}">
            @csrf

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Zone Name</label>
                <input type="text"
                       name="name"
                       id="name"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter zone name"
                       value="{{ old('name') }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description"
                          id="description"
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Describe the zone">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type -->
            <div class="mb-6">
                <label for="zone_type" class="block text-sm font-medium text-gray-700 mb-2">Zone Type</label>
                <select name="zone_type"
                        id="zone_type"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select zone type</option>
                    <option value="road" {{ old('zone_type') == 'road' ? 'selected' : '' }}>Road</option>
                    <option value="poaching" {{ old('zone_type') == 'poaching' ? 'selected' : '' }}>Poaching</option>
                    <option value="urban" {{ old('zone_type') == 'urban' ? 'selected' : '' }}>Urban</option>
                    <option value="protected" {{ old('zone_type') == 'protected' ? 'selected' : '' }}>Protected Area</option>
                    <option value="other" {{ old('zone_type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('zone_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Polygon -->
            <div class="mb-6">
                <label for="polygon" class="block text-sm font-medium text-gray-700 mb-2">Polygon Coordinates</label>
                <textarea name="polygon"
                          id="polygon"
                          required
                          rows="6"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Enter polygon in WKT format, e.g., POLYGON((lat1 lon1, lat2 lon2, ...))">{{ old('polygon') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">
                    Format: <code>POLYGON((lat1 lon1, lat2 lon2, ...))</code>
                </p>
                @error('polygon')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex space-x-4">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Create Zone
                </button>
                <a href="{{ route('geozones.index') }}"
                   class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
