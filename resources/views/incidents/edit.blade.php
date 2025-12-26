@extends('layouts.app')

@section('title', 'Edit Incident')
@section('page-title', 'Edit Incident')
@section('page-subtitle', 'Update incident information')

@section('content')

<div class="max-w-4xl mx-auto">

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
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
        <div class="bg-gradient-to-r from-yellow-600 to-orange-600 p-6">
            <h2 class="text-2xl font-bold text-white">Edit Incident #{{ $incident->id }}</h2>
            <p class="text-yellow-100 mt-1">Update the information below</p>
        </div>

        <form action="{{ route('incidents.update', $incident->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title', $incident->title) }}" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="5" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $incident->description) }}</textarea>
            </div>

            <!-- Severity & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="severity" class="block text-sm font-semibold text-gray-700 mb-2">
                        Severity <span class="text-red-500">*</span>
                    </label>
                    <select id="severity" name="severity" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="low" {{ old('severity', $incident->severity) === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('severity', $incident->severity) === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('severity', $incident->severity) === 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('severity', $incident->severity) === 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="open" {{ old('status', $incident->status) === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="investigating" {{ old('status', $incident->status) === 'investigating' ? 'selected' : '' }}>Investigating</option>
                        <option value="resolved" {{ old('status', $incident->status) === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ old('status', $incident->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
            </div>

            <!-- Animal & Assigned User -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="animal_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Related Animal
                    </label>
                    <select id="animal_id" name="animal_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">None</option>
                        @foreach($animals as $animal)
                            <option value="{{ $animal->id }}" {{ old('animal_id', $incident->animal_id) == $animal->id ? 'selected' : '' }}>
                                {{ $animal->name }} ({{ $animal->species }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="assigned_to" class="block text-sm font-semibold text-gray-700 mb-2">
                        Assign To
                    </label>
                    <select id="assigned_to" name="assigned_to"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Unassigned</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to', $incident->assigned_to) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
                <input type="text" id="location" name="location" value="{{ old('location', $incident->location) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Coordinates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="latitude" class="block text-sm font-semibold text-gray-700 mb-2">Latitude</label>
                    <input type="number" step="0.000001" id="latitude" name="latitude" value="{{ old('latitude', $incident->latitude) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="longitude" class="block text-sm font-semibold text-gray-700 mb-2">Longitude</label>
                    <input type="number" step="0.000001" id="longitude" name="longitude" value="{{ old('longitude', $incident->longitude) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="flex space-x-3">
                    <a href="{{ route('incidents.show', $incident->id) }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                        Cancel
                    </a>

                    <form action="{{ route('incidents.destroy', $incident->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this incident?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                            Delete
                        </button>
                    </form>
                </div>

                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Update Incident
                </button>
            </div>
        </form>
    </div>

</div>

@endsection 
