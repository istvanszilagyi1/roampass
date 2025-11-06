@extends('layouts.app')

@section('title', 'Partnereink - RoamPass')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<style>
/* Kártya pulzálás a térkép interakcióhoz */
@keyframes pulse-once {
  0% { transform: scale(1.0); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); border-color: rgba(59, 130, 246, 0.7); }
  50% { transform: scale(1.01); box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); border-color: rgba(59, 130, 246, 0); }
  100% { transform: scale(1.0); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); border-color: rgba(59, 130, 246, 0); }
}
.animate-pulse-once {
  animation: pulse-once 1.5s ease-out;
}
.bg-gray-850 { background-color: #1f1f1f; }
</style>

<section class="bg-gradient-to-br from-gray-950 via-gray-900 to-black text-white py-28">

    <div class="text-center mb-20 px-6 max-w-5xl mx-auto pb-5">
        <h1 class="text-5xl font-extrabold mb-12 text-center text-blue-400 drop-shadow-lg flex items-center justify-center gap-3">Partnertermek Katalógusa</h1>
        <p class="text-gray-300 max-w-4xl mx-auto text-xl animate-fade-in-delay">
            A RoamPass hálózat folyamatosan bővül! Keresd meg a hozzád legközelebbi edzőtermet, és kezdj el sportolni.
        </p>
    </div>

    <div class="max-w-7xl mx-auto px-6 mb-24">
        <div id="partners-map"
             class="w-full h-[550px] rounded-3xl shadow-2xl shadow-gray-950/80 border-4 border-gray-800 transition-all hover:shadow-blue-600/50 hover:border-blue-600/50">
            </div>
    </div>

    <div class="max-w-3xl mx-auto px-6 mb-24">
        <div class="bg-gray-850/80 backdrop-blur-sm p-8 md:p-10 rounded-3xl shadow-2xl shadow-black/50 border border-gray-700">
            <h2 class="text-3xl font-bold text-white mb-8 text-center">Keresés a hálózatban</h2>

            <div class="relative">
                <input type="text" id="searchTerm" placeholder="Terem neve, város vagy cím keresése..."
                       class="w-full bg-gray-900 border border-gray-700 rounded-xl pl-16 pr-6 py-4 text-gray-200
                       focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:bg-gray-700/50
                       hover:border-blue-500 transition duration-300 placeholder-gray-500 text-lg font-medium shadow-lg">

                <i data-lucide="search" class="w-6 h-6 text-blue-400 absolute left-5 top-1/2 transform -translate-y-1/2"></i>
            </div>

        </div>

        <div id="noResults" class="text-center text-xl text-gray-500 mt-12 hidden">
            <p>Sajnáljuk, nincs találat a megadott feltételekkel. Kérjük, próbálj másik kifejezést!</p>
        </div>
    </div>

    <div id="gymsContainer" class="max-w-6xl mx-auto px-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-10">
        @foreach($gyms as $gym)
        <a data-search="{{ strtolower($gym->name . ' ' . $gym->city . ' ' . $gym->address) }}"
           href="{{ route('partners.show', $gym->id) }}"
           class="gym-card bg-gray-800 rounded-3xl overflow-hidden shadow-2xl shadow-black/50 border border-gray-700
                  transition-all duration-500 hover:shadow-blue-600/40 hover:scale-[1.03] group relative">

            <div class="relative">
                <img src="{{ $gym->image_url }}" alt="{{ $gym->name }}"
                     class="w-full h-56 object-cover transition duration-500 group-hover:scale-105 opacity-90 group-hover:opacity-100">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
                    <span class="text-white text-lg font-bold py-2 px-4 bg-blue-600 rounded-full hover:bg-blue-500 transition-colors duration-300 transform -translate-y-2 group-hover:translate-y-0">
                        Megtekintés
                    </span>
                </div>
            </div>

            <div class="p-6 space-y-3">
                <h3 class="text-2xl font-extrabold text-blue-400 group-hover:text-white transition-colors duration-300">{{ $gym->name }}</h3>

                <div class="flex justify-between items-center text-sm">
                    <p class="text-gray-400 flex items-center gap-2 font-semibold">
                        <i data-lucide="map-pin" class="w-4 h-4 text-pink-400"></i> {{ $gym->city }}
                    </p>
                    @if(isset($gym->type))
                    <span class="text-xs px-3 py-1 bg-blue-900/50 rounded-full text-blue-300 border border-blue-800">{{ $gym->type }}</span>
                    @endif
                </div>

                <p class="text-gray-500 text-sm flex items-center gap-2">
                     <i data-lucide="locate-fixed" class="w-4 h-4"></i> {{ Str::limit($gym->address, 40) }}
                </p>
            </div>

        </a>
        @endforeach
    </div>
</section>

<script>
lucide.createIcons();

// Térkép inicializálása
document.addEventListener("DOMContentLoaded", () => {
    // Közép-Magyarország koordináták (fő fókusz)
    const initialCoords = [47.1625, 19.5033];
    const map = L.map('partners-map', {
        scrollWheelZoom: false,
        minZoom: 7
    }).setView(initialCoords, 7);

    // Sötét/Stílusos térkép csempe
    L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
        maxZoom: 18,
    }).addTo(map);

    // Marker adatok és megjelenítés
    const gymMarkers = [
        @php
            $markers = [];
            foreach($gyms as $gym) {
                if(isset($gym->coords['lat'])) {
                    $markers[] = [
                        'lat' => $gym->coords['lat'],
                        'lng' => $gym->coords['lng'],
                        'name' => $gym->name,
                        'city' => $gym->city
                    ];
                }
            }
        @endphp
        @foreach($markers as $marker)
            { lat: {{ $marker['lat'] }}, lng: {{ $marker['lng'] }}, name: "{{ $marker['name'] }}", city: "{{ $marker['city'] }}" },
        @endforeach
    ];

    gymMarkers.forEach(markerData => {
        L.marker([markerData.lat, markerData.lng])
            .addTo(map)
            .bindPopup(`<strong>${markerData.name}</strong><br>${markerData.city}`)
            .on('click', function() {
                 // Kártya kiemelése kattintásra
                 const searchableName = markerData.name.toLowerCase();
                 const card = Array.from(document.querySelectorAll('.gym-card')).find(c => c.dataset.search.includes(searchableName));

                 if (card) {
                     card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                     card.classList.add('animate-pulse-once', 'border-blue-500');
                     setTimeout(() => {
                        card.classList.remove('animate-pulse-once', 'border-blue-500');
                     }, 1500);
                 }
            });
    });
});

/* Csak Keresés logika */
const cards = document.querySelectorAll('.gym-card');
const search = document.getElementById('searchTerm');
const noResultsDiv = document.getElementById('noResults');

function updateFiltering() {
    const term = search.value.toLowerCase();
    let resultsFound = 0;

    cards.forEach(card => {
        const searchMatch = term === "" || card.dataset.search.includes(term);

        const isVisible = searchMatch;
        card.classList.toggle("hidden", !isVisible);

        if (isVisible) {
            resultsFound++;
        }
    });

    // Találat nélküli üzenet megjelenítése
    noResultsDiv.classList.toggle('hidden', resultsFound > 0);
}

search.addEventListener('input', updateFiltering);
</script>

@endsection
