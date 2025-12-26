@extends('layouts.app')

@section('title', 'Zone Details')
@section('page-title', 'Zone Details')
@section('page-subtitle', 'View risk zone information')

@section('content')

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Zone Information -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">Zone Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Name</span>
                        <p class="mt-1 text-gray-900">{{ $zone->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Type</span>
                        <p class="mt-1">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($zone->zone_type === 'protected') bg-green-100 text-green-800
                                @elseif($zone->zone_type === 'urban') bg-red-100 text-red-800
                                @elseif($zone->zone_type === 'poaching') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($zone->zone_type) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Created At</span>
                        <p class="mt-1">{{ \Carbon\Carbon::parse($zone->created_at)->format('d M Y H:i:s') }}</p>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">Description</h3>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-900">{{ $zone->description ?? 'No description provided.' }}</p>
                </div>
            </div>
        </div>

        <!-- Polygon -->
        <div class="mt-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Polygon Coordinates</h3>
            <div class="p-4 bg-gray-50 rounded-lg">
                <pre class="text-sm text-gray-900 whitespace-pre-wrap">{{ $zone->polygon }}</pre>
            </div>
        </div>

        <div class="mt-8 flex space-x-4">
            <a href="{{ route('geozones.edit', $zone->id) }}"
               class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                Edit Zone
            </a>
            <a href="{{ route('geozones.index') }}"
               class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                Back to List
            </a>
        </div>
    </div>
</div>

@endsection
