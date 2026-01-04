@extends('layouts.app')

@section('title', 'Species Management')
@section('page-title', 'Species Management')
@section('page-subtitle', 'Manage species in the system')

@section('content')

<div class="space-y-6">

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <p class="text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">All Species</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Showing {{ $species->total() ?? $species->count() }} species
                    </p>
                </div>

                @can('create', App\Models\Species::class)
                    <div>
                        <a href="{{ route('species.create') }}"
                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Species
                        </a>
                    </div>
                @endcan
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Scientific Name</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Conservation Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($species as $item)
                    <tr class="hover:bg-blue-50 transition-colors"> 
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 font-mono">{{ $item->scientific_name }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @switch($item->conservation_status)
                                    @case('Critically Endangered')
                                        bg-red-100 text-red-800
                                        @break
                                    @case('Endangered')
                                        bg-yellow-100 text-yellow-800
                                        @break
                                    @case('Vulnerable')
                                        bg-orange-100 text-orange-800
                                        @break
                                    @default
                                        bg-gray-100 text-gray-800
                                @endswitch">
                                {{ $item->conservation_status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('species.show', $item->id) }}"
                                   class="p-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors"
                                   aria-label="View details of {{ $item->name }}">
                                    View
                                </a>

                                @can('update', $item)
                                    <a href="{{ route('species.edit', $item->id) }}"
                                       class="p-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg transition-colors"
                                       aria-label="Edit {{ $item->name }}">
                                        Edit
                                    </a>
                                @endcan

                                @can('delete', $item)
                                    <form method="POST" action="{{ route('species.destroy', $item->id) }}"
                                          onsubmit="return confirm('Are you sure you want to delete this species? This action cannot be undone.');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors"
                                                aria-label="Delete {{ $item->name }}">
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <p class="text-lg font-medium text-gray-600">No species available</p>
                            @can('create', App\Models\Species::class)
                                <p class="mt-2 text-sm text-gray-500">Click "Add Species" to get started.</p>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($species instanceof \Illuminate\Pagination\LengthAwarePaginator && $species->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-2 sm:space-y-0">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ $species->firstItem() }}</span> to
                    <span class="font-medium">{{ $species->lastItem() }}</span> of
                    <span class="font-medium">{{ $species->total() }}</span> results
                </div>
                <div>
                    {{ $species->links() }}
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

@endsection
