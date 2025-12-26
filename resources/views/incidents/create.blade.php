@extends('layouts.app')

@section('title', 'Create Incident')
@section('page-title', 'Create New Incident')
@section('page-subtitle', 'Report a new wildlife incident')

@section('content')

<div class="max-w-4xl mx-auto">

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" role="alert">
        <p class="font-medium">Error!</p>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
        <p class="font-medium">Please fix the following errors:</p>
        <ul class="list-disc list-inside mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6">
            <h2 class="text-2xl font-bold text-white">Incident Information</h2>
            <p class="text-blue-100 mt-1">Fill in the details below</p>
        </div>

        <form action="{{ route('incidents.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Brief description of the incident">
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="5" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Detailed description of what happened">{{ old('description') }}</textarea>
            </div>

            <!-- Severity & Status Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Severity -->
                <div>
                    <label for="severity" class="block text-sm font-semibold text-gray-700 mb-2">
                        Severity <span class="text-red-500">*</span>
                    </label>
                    <select id="severity" name="severity" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Severity</option>
                        <option value="low" {{ old('severity') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('severity') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('severity') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Status</option>
                        <option value="open" {{ old('status') === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="investigating" {{ old('status') === 'investigating' ? 'selected' : '' }}>Investigating</option>
                        <option value="resolved" {{ old('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
            </div>

            <!-- Animal & Assigned User Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Animal -->
                <div>
                    <label for="animal_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Related Animal (Optional)
                    </label>
                    <select id="animal_id" name="animal_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Animal</option>
                        @foreach($animals as $animal)
                            <option value="{{ $animal->id }}" {{ old('animal_id') == $animal->id ? 'selected' : '' }}>
                                {{ $animal->name }} ({{ $animal->species }}) - {{ $animal->tag_id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Assigned User -->
                <div>
                    <label for="assigned_to" class="block text-sm font-semibold text-gray-700 mb-2">
                        Assign To (Optional)
                    </label>
                    <select id="assigned_to" name="assigned_to"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Unassigned</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">
                    Location (Optional)
                </label>
                <input type="text" id="location" name="location" value="{{ old('location') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g., Zone A, Near River">
            </div>

            <!-- Coordinates Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Latitude -->
                <div>
                    <label for="latitude" class="block text-sm font-semibold text-gray-700 mb-2">
                        Latitude (Optional)
                    </label>
                    <input type="number" step="0.000001" id="latitude" name="latitude" value="{{ old('latitude') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="-6.200000">
                </div>

                <!-- Longitude -->
                <div>
                    <label for="longitude" class="block text-sm font-semibold text-gray-700 mb-2">
                        Longitude (Optional)
                    </label>
                    <input type="number" step="0.000001" id="longitude" name="longitude" value="{{ old('longitude') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="106.816666">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('incidents.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Create Incident
                </button>
            </div>
        </form>
    </div>

</div>

@endsection
