@extends('layouts.app')

@section('title', 'Species Management')
@section('page-title', 'Species Management')
@section('page-subtitle', 'Manage species in the system')

@section('content')

<div class="space-y-6">

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
        <div class="flex items-center">
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-center">
            <p class="text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">All Species</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Showing {{ $species->count() }} species
                    </p>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('species.create') }}"
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center">
                        Add Species
                    </a>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Scientific Name</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($species as $item)
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $item->name }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">{{ $item->scientific_name }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('species.show', $item->id) }}"
                                   class="p-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors"
                                   title="View Details">View</a>
                                <a href="{{ route('species.edit', $item->id) }}"
                                   class="p-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg transition-colors"
                                   title="Edit">Edit</a>
                                <form method="POST" action="{{ route('species.destroy', $item->id) }}"
                                      onsubmit="return confirm('Are you sure you want to delete this species?');"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors"
                                            title="Delete">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-16 text-center">
                            <p class="text-lg font-medium text-gray-600">No species available</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($species instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
