@extends('layouts.app')

@section('content')
<div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Monitored Animals</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Showing {{ $monitoredAnimals->count() }} animals
                        @if($search)
                            <span class="text-blue-600 font-medium">· Search: "{{ $search }}"</span>
                        @endif
                    </p>
                </div>
                <div class="flex space-x-3">
<!-- Ganti ini -->
<form method="GET" action="{{ route('animals.index') }}" class="flex items-center">
                            <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Search animals..."
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <input type="hidden" name="sort" value="{{ $sortBy }}">
                        <input type="hidden" name="order" value="{{ $sortOrder }}">
                        <select name="per_page" class="ml-2 px-2 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="10" {{ $perPage == '10' ? 'selected' : '' }}>10</option>
                            <option value="100" {{ $perPage == '100' ? 'selected' : '' }}>100</option>
                            <option value="1000" {{ $perPage == '1000' ? 'selected' : '' }}>1000</option>
                            <option value="all" {{ $perPage == 'all' ? 'selected' : '' }}>All</option>
                        </select>
                        <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Apply</button>
                    </form>

                    @if($search)
                    <a href="{{ route('dashboard.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg text-sm">Clear</a>
                    @endif
                    @if(auth()->user()->can('manage_animals'))
                        <button onclick="openModal('create-animal-modal')" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">+ Add Animal</button>
                    @endif
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            <a href="{{ route('dashboard.index', array_merge(request()->query(), ['sort' => 'name', 'order' => $sortOrder === 'asc' && $sortBy === 'name' ? 'desc' : 'asc'])) }}">
                                Name {{ $sortBy === 'name' ? ($sortOrder === 'asc' ? '↑' : '↓') : '' }}
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            <a href="{{ route('dashboard.index', array_merge(request()->query(), ['sort' => 'species', 'order' => $sortOrder === 'asc' && $sortBy === 'species' ? 'desc' : 'asc'])) }}">
                                Species {{ $sortBy === 'species' ? ($sortOrder === 'asc' ? '↑' : '↓') : '' }}
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tag ID</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            <a href="{{ route('dashboard.index', array_merge(request()->query(), ['sort' => 'status', 'order' => $sortOrder === 'asc' && $sortBy === 'status' ? 'desc' : 'asc'])) }}">
                                Status {{ $sortBy === 'status' ? ($sortOrder === 'asc' ? '↑' : '↓') : '' }}
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Last Seen</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($monitoredAnimals as $animal)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $animal->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $animal->species }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $animal->tag_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($animal->device_status === 'active')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            @if($animal->last_seen)
                                {{ \Carbon\Carbon::parse($animal->last_seen)->diffForHumans() }}
                            @else
                                <span class="text-gray-400">Never</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('animals.show', $animal->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>

                            @if(auth()->user()->can('manage_animals'))
                                <button onclick="openModal('edit-{{ $animal->id }}')" class="text-yellow-600 hover:text-yellow-900 mr-3">Edit</button>
                                <button onclick="confirmDelete({{ $animal->id }})" class="text-red-600 hover:text-red-900">Delete</button>

                                <!-- Modal Edit untuk setiap baris -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('animals.show', $animal->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>

                                    @if(auth()->user()->can('manage_animals'))
                                        <button onclick="openEditModal({{ $animal->id }}, '{{ $animal->name }}', '{{ $animal->species }}', '{{ $animal->tag_id }}')" class="text-yellow-600 hover:text-yellow-900 mr-3">Edit</button>
                                        <button onclick="confirmDelete({{ $animal->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                                    @endif
                                </td>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p>No animals found</p>
                            @if($search)
                                <p class="text-sm mt-2">Try adjusting your search</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($paginatedAnimals)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $paginatedAnimals->appends(['search' => $search, 'sort' => $sortBy, 'order' => $sortOrder, 'per_page' => $perPage])->links('pagination::tailwind') }}
        </div>
        @endif
    </div>
@endsection

<!-- Global JS for Modals -->
<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}



function confirmDelete(id) {
    const url = `/animals/${id}`;
    if (confirm('Are you sure you want to delete this animal?')) {
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Failed to delete.');
            }
        });
    }
}
</script>
