@extends('layouts.app')

@section('title', 'Live Map')
@section('page-title', 'Live Animal Tracking Map')
@section('page-subtitle', 'Real-time GPS monitoring and visualization')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map-container {
        height: 600px;
        width: 100%;
        border-radius: 0.5rem;
    }
    .legend {
        background: white;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .animal-popup {
        max-width: 250px;
    }
    .control-panel {
        background: white;
        padding: 1rem;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-active { background-color: #dcfce7; color: #166534; }
    .status-inactive { background-color: #fee2e2; color: #b91c1c; }
    .animal-initial {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        border-radius: 50%;
    }
    .animal-card:hover {
        cursor: pointer;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 2px #3b82f6;
    }
    .loading {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')

<div class="space-y-6">
    <!-- Control Panel -->
    <div class="control-panel">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Animal Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Species</label>
                <select id="species-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="all">All Species</option>
                    @php
                        $speciesList = $animalsWithPositions->pluck('species')->unique()->sort();
                    @endphp
                    @foreach($speciesList as $species)
                        <option value="{{ $species }}">{{ $species }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Time Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Time Range</label>
                <select id="time-range" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="1">Last 1 Hour</option>
                    <option value="24" selected>Last 24 Hours</option>
                    <option value="168">Last 7 Days</option>
                    <option value="720">Last 30 Days</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end space-x-2">
                <button id="refresh-map" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <span id="refresh-icon">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </span>
                    <span id="refresh-text">Refresh</span>
                </button>
                <button id="show-heatmap" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                    Heatmap
                </button>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Live Tracking Map</h3>
            <div class="flex items-center justify-between mt-2">
                <div class="text-sm text-gray-600">
                    <span class="font-medium">{{ $activeAnimals }}</span> of <span class="font-medium">{{ $totalAnimals }}</span> animals active
                </div>
                <div class="flex space-x-2">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-1"></div>
                        <span class="text-sm">Active</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-1"></div>
                        <span class="text-sm">Inactive</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="map-container"></div>
    </div>

    <!-- Animals List -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-bold text-gray-900">Tracked Animals</h4>
            <span class="text-sm text-gray-500">{{ $animalsWithPositions->count() }} animals</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($animalsWithPositions as $animal)
            <div class="animal-card p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow"
                 data-id="{{ $animal->id }}"
                 onclick="centerMapOnAnimal({{ $animal->id }})">
                <div class="flex items-center space-x-3">
                    <div class="animal-initial"
                         style="background: linear-gradient(135deg,
                         @if(str_contains($animal->species, 'Orangutan')) #667eea, #764ba2
                         @elseif(str_contains($animal->species, 'Harimau')) #f093fb, #f5576c
                         @else #4facfe, #00f2fe @endif);">
                        {{ strtoupper(substr($animal->name, 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <h5 class="font-semibold text-gray-900">{{ $animal->name }}</h5>
                        <p class="text-sm text-gray-500">{{ $animal->species }}</p>
                    </div>
                    <span class="status-badge {{ $animal->status === 'active' ? 'status-active' : 'status-inactive' }}">
                        {{ ucfirst($animal->status ?? 'Unknown') }}
                    </span>
                </div>

                @if($animal->latitude && $animal->longitude)
                <div class="mt-2 text-xs text-gray-500">
                    <div>📍 {{ number_format($animal->latitude, 4) }}°, {{ number_format($animal->longitude, 4) }}°</div>
                    <div>🕒 {{ $animal->recorded_at ? \Carbon\Carbon::parse($animal->recorded_at)->diffForHumans() : 'No data' }}</div>
                </div>
                @endif
            </div>
            @empty
            <div class="col-span-3 text-center py-8 text-gray-500">
                <p>No animals currently tracked</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet.heat@0.2.0/dist/leaflet-heat.min.js"></script>
<script>
// State global
let currentHeatmap = null;
let animalMarkers = {};

// Inisialisasi peta
const map = L.map('map-container').setView([-2.5, 102], 8);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Data awal
const initialAnimalData = @json($animalsWithPositions ?? []);
const initialGeozoneData = @json($geozones ?? []);

// Fungsi utility
function createAnimalIcon(species) {
    let color;
    if (species && species.includes('Orangutan')) color = '#667eea';
    else if (species && species.includes('Harimau')) color = '#f093fb';
    else if (species && species.includes('Badak')) color = '#10b981';
    else color = '#3b82f6';

    return L.divIcon({
        className: 'animal-marker',
        html: `<div style="background:${color}; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:bold; font-size:12px;">🐾</div>`,
        iconSize: [30, 30],
        iconAnchor: [15, 15]
    });
}

function addAnimalMarkers(animals) {
    // Hapus marker lama
    Object.values(animalMarkers).forEach(marker => marker.remove());
    animalMarkers = {};

    // Tambahkan marker baru
    animals.forEach(animal => {
        if (animal.latitude && animal.longitude) {
            const marker = L.marker([animal.latitude, animal.longitude], {
                icon: createAnimalIcon(animal.species),
                title: animal.name
            }).addTo(map);

            marker.bindPopup(`
                <div class="animal-popup">
                    <h4 class="font-bold">${animal.name}</h4>
                    <p class="text-sm">${animal.species}</p>
                    <p class="text-xs mt-1">
                        <span class="inline-flex items-center">
                            <span class="w-2 h-2 rounded-full mr-1 ${animal.status === 'active' ? 'bg-green-500' : 'bg-red-500'}"></span>
                            ${animal.status === 'active' ? 'Active' : 'Inactive'}
                        </span>
                    </p>
                    <p class="text-xs mt-1">Last seen: ${animal.recorded_at ? new Date(animal.recorded_at).toLocaleString() : 'No data'}</p>
                    <div class="mt-2">
                        <a href="/animals/${animal.id}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">View Details</a>
                    </div>
                </div>
            `);

            marker.on('click', () => centerMapOnAnimal(animal.id));
            animalMarkers[animal.id] = marker;
        }
    });
}

function addGeoZones(geozones) {
    geozones.forEach(zone => {
        if (zone.polygon) {
            try {
                const polygon = L.polygon(zone.polygon, {
                    color: '#6b7280',
                    fillColor: '#6b7280',
                    fillOpacity: 0.2,
                    weight: 2
                }).addTo(map);

                polygon.bindPopup(`
                    <strong>${zone.name}</strong><br>
                    Type: ${zone.zone_type}<br>
                    ${zone.description || ''}
                `);
            } catch (e) {
                console.warn('Invalid polygon:', zone.id);
            }
        }
    });
}

function centerMapOnAnimal(animalId) {
    const animal = initialAnimalData.find(a => a.id == animalId);
    if (animal && animal.latitude && animal.longitude) {
        map.setView([animal.latitude, animal.longitude], 12);
        animalMarkers[animalId]?.openPopup();
    }
}

// Inisialisasi awal
addAnimalMarkers(initialAnimalData);
addGeoZones(initialGeozoneData);

// Event: Filter Spesies
document.getElementById('species-filter').addEventListener('change', function() {
    const selectedSpecies = this.value;
    const filteredAnimals = selectedSpecies === 'all'
        ? initialAnimalData
        : initialAnimalData.filter(a => a.species === selectedSpecies);

    addAnimalMarkers(filteredAnimals);

    // Filter daftar satwa
    document.querySelectorAll('.animal-card').forEach(card => {
        const cardSpecies = card.querySelector('.text-gray-500').textContent;
        card.style.display = (selectedSpecies === 'all' || cardSpecies === selectedSpecies) ? 'block' : 'none';
    });
});

// Event: Refresh Data
document.getElementById('refresh-map').addEventListener('click', async function() {
    const btn = this;
    const icon = document.getElementById('refresh-icon');
    const text = document.getElementById('refresh-text');

    // Tampilkan loading
    icon.innerHTML = '<div class="loading"></div>';
    text.textContent = 'Refreshing...';
    btn.disabled = true;

    try {
        const response = await fetch("{{ route('map.api.positions') }}");
        const data = await response.json();

        if (data.success) {
            // Simpan data baru sebagai data utama
            window.initialAnimalData = data.data;
            addAnimalMarkers(data.data);

            // Update daftar satwa
            const animalList = document.querySelector('.grid.grid-cols-1');
            if (animalList) {
                animalList.innerHTML = '';
                data.data.forEach(animal => {
                    const card = document.createElement('div');
                    card.className = 'animal-card p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow';
                    card.setAttribute('data-id', animal.id);
                    card.onclick = () => centerMapOnAnimal(animal.id);
                    card.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <div class="animal-initial" style="background: linear-gradient(135deg, ${
                                animal.species.includes('Orangutan') ? '#667eea, #764ba2' :
                                animal.species.includes('Harimau') ? '#f093fb, #f5576c' : '#4facfe, #00f2fe'
                            });">
                                ${animal.name.substring(0,2).toUpperCase()}
                            </div>
                            <div class="flex-1">
                                <h5 class="font-semibold text-gray-900">${animal.name}</h5>
                                <p class="text-sm text-gray-500">${animal.species}</p>
                            </div>
                            <span class="status-badge ${animal.status === 'active' ? 'status-active' : 'status-inactive'}">
                                ${animal.status === 'active' ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                        ${animal.latitude && animal.longitude ? `
                        <div class="mt-2 text-xs text-gray-500">
                            <div>📍 ${parseFloat(animal.latitude).toFixed(4)}°, ${parseFloat(animal.longitude).toFixed(4)}°</div>
                            <div>🕒 ${animal.recorded_at ? new Date(animal.recorded_at).toLocaleString() : 'No data'}</div>
                        </div>
                        ` : ''}
                    `;
                    animalList.appendChild(card);
                });
            }
        }
    } catch (error) {
        console.error('Refresh failed:', error);
        alert('Failed to refresh data. Please try again.');
    } finally {
        // Kembalikan tombol
        icon.innerHTML = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
        text.textContent = 'Refresh';
        btn.disabled = false;
    }
});

// Event: Heatmap
document.getElementById('show-heatmap').addEventListener('click', async function() {
    const btn = this;

    // Hapus heatmap lama
    if (currentHeatmap) {
        map.removeLayer(currentHeatmap);
        currentHeatmap = null;
        btn.textContent = 'Heatmap';
        btn.classList.remove('bg-red-600', 'hover:bg-red-700');
        btn.classList.add('bg-purple-600', 'hover:bg-purple-700');
        return;
    }

    // Tampilkan loading
    btn.textContent = 'Loading...';
    btn.disabled = true;

    try {
        const timeRange = document.getElementById('time-range').value;
        const days = Math.max(1, parseInt(timeRange) / 24);
        const response = await fetch("{{ route('map.api.heatmap') }}?days=" + days);
        const data = await response.json();

        if (data.success && data.data.length > 0) {
            // Format data untuk heatmap: [[lat, lng, intensity], ...]
            const heatmapData = data.data.map(point => [
                parseFloat(point.latitude),
                parseFloat(point.longitude),
                parseInt(point.intensity) || 1
            ]);

            // Buat heatmap
            currentHeatmap = L.heatLayer(heatmapData, {
                radius: 25,
                blur: 15,
                maxZoom: 18,
                gradient: {
                    0.4: '#00ff00',
                    0.6: '#ffff00',
                    0.8: '#ff7700',
                    1.0: '#ff0000'
                }
            }).addTo(map);

            btn.textContent = 'Hide Heatmap';
            btn.classList.remove('bg-purple-600', 'hover:bg-purple-700');
            btn.classList.add('bg-red-600', 'hover:bg-red-700');
        } else {
            alert('No heatmap data available for selected time range.');
        }
    } catch (error) {
        console.error('Heatmap failed:', error);
        alert('Failed to load heatmap. Please try again.');
    } finally {
        if (!currentHeatmap) {
            btn.textContent = 'Heatmap';
            btn.classList.remove('bg-red-600', 'hover:bg-red-700');
            btn.classList.add('bg-purple-600', 'hover:bg-purple-700');
        }
        btn.disabled = false;
    }
});

// Event: Filter Waktu (untuk refresh data berdasarkan waktu)
document.getElementById('time-range').addEventListener('change', function() {
    // Opsional: Anda bisa tambahkan logika untuk memuat data historis
    // Untuk sekarang, hanya update heatmap jika aktif
    if (currentHeatmap) {
        document.getElementById('show-heatmap').click(); // Toggle off
        document.getElementById('show-heatmap').click(); // Toggle on dengan waktu baru
    }
});
</script>
@endpush
