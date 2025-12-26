@extends('layouts.app')

@section('title', 'Incident Detail')
@section('page-title', 'Incident #' . $incident->id)
@section('page-subtitle', $incident->title)

@section('content')

<div class="space-y-6">

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
        <p class="font-medium">Success!</p>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
        <p class="font-medium">Error!</p>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Incident Details -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Incident Info Card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-white">{{ $incident->title }}</h2>
                            <p class="text-blue-100 mt-1">Incident #{{ $incident->id }}</p>
                        </div>

                        @if($incident->severity === 'critical')
                            <span class="px-4 py-2 rounded-full text-sm font-bold bg-red-500 text-white">Critical</span>
                        @elseif($incident->severity === 'high')
                            <span class="px-4 py-2 rounded-full text-sm font-bold bg-orange-500 text-white">High</span>
                        @elseif($incident->severity === 'medium')
                            <span class="px-4 py-2 rounded-full text-sm font-bold bg-yellow-500 text-white">Medium</span>
                        @else
                            <span class="px-4 py-2 rounded-full text-sm font-bold bg-blue-500 text-white">Low</span>
                        @endif
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Description -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase mb-2">Description</h3>
                        <p class="text-gray-800 leading-relaxed">{{ $incident->description }}</p>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-2 gap-6 pt-6 border-t border-gray-200">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-1">Status</p>
                            @if($incident->status === 'open')
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">Open</span>
                            @elseif($incident->status === 'investigating')
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">Investigating</span>
                            @elseif($incident->status === 'resolved')
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">Resolved</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">Closed</span>
                            @endif
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-1">Reported</p>
                            <p class="text-gray-800">{{ Carbon\Carbon::parse($incident->reported_at)->format('d M Y, H:i') }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-1">Animal</p>
                            @if($incident->animal_name)
                                <p class="text-gray-800 font-medium">{{ $incident->animal_name }}</p>
                                <p class="text-sm text-gray-500">{{ $incident->species }} - {{ $incident->tag_id }}</p>
                            @else
                                <p class="text-gray-400">Not related to any animal</p>
                            @endif
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-1">Location</p>
                            @if($incident->location)
                                <p class="text-gray-800">{{ $incident->location }}</p>
                                @if($incident->latitude && $incident->longitude)
                                    <p class="text-sm text-gray-500">{{ $incident->latitude }}, {{ $incident->longitude }}</p>
                                @endif
                            @else
                                <p class="text-gray-400">No location specified</p>
                            @endif
                        </div>
                    </div>

                    <!-- Resolution (if resolved) -->
                    @if($incident->status === 'resolved' && $incident->resolution_notes)
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-600 uppercase mb-2">Resolution</h3>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-gray-800 leading-relaxed">{{ $incident->resolution_notes }}</p>
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-green-100">
                                <div>
                                    <p class="text-sm text-gray-600">Resolved by</p>
                                    <p class="font-medium text-gray-800">{{ $incident->resolved_by_name ?? 'Unknown' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Resolved on</p>
                                    <p class="font-medium text-gray-800">{{ Carbon\Carbon::parse($incident->resolved_at)->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Right Column: Actions & Info -->
        <div class="space-y-6">

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Quick Actions</h3>
                </div>

                <div class="p-6 space-y-3">
                    <!-- Edit Button -->
                    <a href="{{ route('incidents.edit', $incident->id) }}"
                       class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Incident
                    </a>

                    <!-- Escalate Button -->
                    @if($incident->severity !== 'critical')
                    <form action="{{ route('incidents.escalate', $incident->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors flex items-center justify-center"
                                onclick="return confirm('Escalate this incident to higher severity?')">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            Escalate
                        </button>
                    </form>
                    @endif

                    <!-- Resolve Button -->
                    @if($incident->status !== 'resolved' && $incident->status !== 'closed')
                    <button onclick="showResolveModal()"
                            class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Mark as Resolved
                    </button>
                    @endif

                    <!-- Back Button -->
                    <a href="{{ route('incidents.index') }}"
                       class="w-full px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Assignment Card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Assignment</h3>
                </div>

                <div class="p-6">
                    @if($incident->assigned_user)
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="bg-blue-100 rounded-full p-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $incident->assigned_user }}</p>
                                <p class="text-sm text-gray-500">{{ $incident->assigned_email }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 mb-4">Not assigned to anyone</p>
                    @endif

                    <!-- Assign Form -->
                    <form action="{{ route('incidents.assign', $incident->id) }}" method="POST">
                        @csrf
                        <select name="assigned_to" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Assign to...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $incident->assigned_to == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            Update Assignment
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Resolve Modal -->
<div id="resolveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full mx-4">
        <div class="bg-green-600 p-6 rounded-t-2xl">
            <h3 class="text-2xl font-bold text-white">Resolve Incident</h3>
        </div>
        <form action="{{ route('incidents.resolve', $incident->id) }}" method="POST" class="p-6">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Resolution Notes *</label>
                <textarea name="resolution_notes" rows="5" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                          placeholder="Describe how the incident was resolved..."></textarea>
            </div>
            <div class="flex space-x-3">
                <button type="button" onclick="hideResolveModal()"
                        class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    Resolve
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showResolveModal() {
    document.getElementById('resolveModal').classList.remove('hidden');
}

function hideResolveModal() {
    document.getElementById('resolveModal').classList.add('hidden');
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideResolveModal();
    }
});
</script>
@endpush

@endsection
