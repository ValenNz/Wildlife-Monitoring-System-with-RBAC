@extends('layouts.app')

@section('title', 'Device Management')
@section('page-title', 'Device Management')
@section('page-subtitle', 'Manage GPS tracking devices and sensors')

@section('content')

<div class="space-y-6">

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <p class="text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Devices</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalDevices }}</p>
                </div>
                <div class="bg-blue-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Active</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $activeDevices }}</p>
                </div>
                <div class="bg-green-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Inactive</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">{{ $inactiveDevices }}</p>
                </div>
                <div class="bg-red-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Low Battery</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $lowBatteryDevices }}</p>
                </div>
                <div class="bg-yellow-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">All Devices</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Showing {{ $deviceCount }} devices
                        @if(request('search'))
                            <span class="text-blue-600 font-medium">· Search: "{{ request('search') }}"</span>
                        @endif
                    </p>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('devices.create') }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Device
                    </a>

                    <a href="{{ route('devices.export') }}?{{ http_build_query(request()->all()) }}"
                    target="_blank"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export CSV
                    </a>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-3 sm:space-y-0 sm:space-x-3 mt-4">
                <!-- Form Pencarian & Filter -->
                <form method="GET" action="{{ route('devices.index') }}" class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 w-full">
                    <div class="relative flex-1">
                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search devices, animal name, or species..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>

                    <select name="per_page" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="10" {{ $perPage == '10' ? 'selected' : '' }}>10 per page</option>
                        <option value="100" {{ $perPage == '100' ? 'selected' : '' }}>100 per page</option>
                        <option value="1000" {{ $perPage == '1000' ? 'selected' : '' }}>1000 per page</option>
                        <option value="all" {{ $perPage == 'all' ? 'selected' : '' }}>All</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors mt-3 sm:mt-0">
                        Apply
                    </button>
                </form>

                @if(request('search') || request('status') || (request('per_page') && request('per_page') !== '10'))
                <a href="{{ route('devices.index') }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-center mt-3 sm:mt-0">
                    Clear
                </a>
                @endif
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Device ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Animal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Battery</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Installed</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Last Seen</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($devices as $device)
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="px-6 py-4">
                            <code class="bg-gray-100 px-2 py-1 rounded text-sm font-mono">{{ $device->device_id }}</code>
                        </td>
                        <td class="px-6 py-4">
                            @if($device->animal_name)
                                <div>
                                    <p class="font-medium text-gray-900">{{ $device->animal_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $device->species }}</p>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">Not assigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($device->status === 'active')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold flex items-center w-fit">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    Active
                                </span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold flex items-center w-fit">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-full bg-gray-200 rounded-full h-2 max-w-[100px]">
                                    <div class="h-2 rounded-full {{ $device->battery_level < 20 ? 'bg-red-500' : ($device->battery_level < 50 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                        style="width: {{ $device->battery_level }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700">{{ $device->battery_level }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($device->installation_date)->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($device->last_seen)
                                {{ \Carbon\Carbon::parse($device->last_seen)->diffForHumans() }}
                            @else
                                <span class="text-gray-400">Never</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('devices.show', $device->id) }}"
                                class="p-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors"
                                title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('devices.edit', $device->id) }}"
                                class="p-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg transition-colors"
                                title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <button type="button"
                                        onclick="testDevice({{ $device->id }})"
                                        class="p-2 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-lg transition-colors"
                                        title="Send Test Signal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('devices.reset', $device->id) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Reset this device to factory settings?');"
                                            class="p-2 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-lg transition-colors"
                                            title="Factory Reset">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('devices.destroy', $device->id) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this device?');"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors"
                                            title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                            </svg>
                            <p class="text-lg font-medium text-gray-600">
                                @if(request('search') || request('status'))
                                    No devices found matching your criteria
                                @else
                                    No devices available
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer: Pagination atau Info "All" -->
        @if($paginatedDevices)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ $paginatedDevices->firstItem() }}</span>
                    to <span class="font-medium">{{ $paginatedDevices->lastItem() }}</span>
                    of <span class="font-medium">{{ $paginatedDevices->total() }}</span> results
                </div>
                <div>
                    {{ $paginatedDevices->appends(request()->except('page'))->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
        @else
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-sm text-gray-600">
            Showing all {{ $deviceCount }} devices matching your criteria.
        </div>
        @endif
    </div>
</div>

<!-- Script untuk Fitur Test -->
<script>
    async function testDevice(deviceId) {
        const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenElement) {
            alert('CSRF token not found. Please refresh the page.');
            return;
        }

        try {
            const response = await fetch(`/devices/${deviceId}/test`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfTokenElement.getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            });

            const result = await response.json();
            if (response.ok && result.success) {
                alert('Test signal sent successfully!');
                location.reload();
            } else {
                throw new Error(result.message || 'Unknown error');
            }
        } catch (error) {
            alert('Failed to send test signal: ' + error.message);
        }
    }
</script>

@endsection
