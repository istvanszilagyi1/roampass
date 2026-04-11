@extends('layouts.app')

@section('title', 'Partnereink - RoamPass')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    @keyframes fade-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-up { animation: fade-up 0.8s ease-out forwards; }

    .text-neon-purple {
        color: #bf40ff;
        text-shadow: 0 0 12px rgba(191, 64, 255, 0.8), 0 0 25px rgba(191, 64, 255, 0.4);
    }

    .neon-border {
        border: 1px solid rgba(191, 64, 255, 0.5);
        box-shadow: 0 0 15px rgba(191, 64, 255, 0.2);
    }

    .glass-card {
        background: rgba(10, 12, 30, 0.7);
        backdrop-blur: md;
        border: 1px solid rgba(191, 64, 255, 0.3);
        border-radius: 2.5rem;
    }

    /* --- Gym Kártyák Stílusa --- */
    .gym-card-img-wrapper {
        width: 100%;
        height: 240px;
        overflow: hidden;
        position: relative;
    }
    .gym-card-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease-in-out;
    }
    .gym-card:hover img {
        transform: scale(1.1);
    }

    .gym-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: rgba(15, 18, 40, 0.9);
        transition: all 0.3s ease;
    }
    .gym-card:hover {
        border-color: #bf40ff;
        box-shadow: 0 0 30px rgba(191, 64, 255, 0.3);
    }

    .info-section {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .search-input-field {
        background-color: #0a0c1e !important;
        color: white !important;
        border: 1px solid rgba(191, 64, 255, 0.3);
        transition: all 0.3s ease;
    }
    .search-input-field:focus {
        border-color: #bf40ff;
        box-shadow: 0 0 15px rgba(191, 64, 255, 0.4);
    }
    .search-input-field::placeholder {
        color: #64748b;
    }

    .search-icon-right {
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .search-icon-right:hover {
        transform: translateY(-50%) scale(1.1);
    }

    #partners-map {
        z-index: 10;
    }
    .leaflet-bar a {
        background-color: #0a0c1e !important;
        color: #bf40ff !important;
        border-color: rgba(191, 64, 255, 0.3) !important;
    }
</style>

<section class="bg-[#020617] text-white min-h-screen pb-20">

    <div class="relative pt-32 pb-16 px-6 overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full bg-[radial-gradient(circle_at_center,_rgba(191,64,255,0.15),_transparent_70%)] -z-10"></div>

        <div class="max-w-5xl mx-auto text-center animate-fade-up">
            <span class="inline-block px-4 py-1.5 mb-6 text-xs font-black tracking-[0.2em] text-purple-400 bg-purple-500/10 border border-purple-500/20 rounded-full uppercase">
                Fedezd fel a hálózatot
            </span>
            <h1 class="text-5xl md:text-7xl font-black mb-8 leading-tight tracking-tighter">
                Partnertermek <span class="text-neon-purple italic">Katalógusa</span>
            </h1>
            <p class="text-gray-400 max-w-2xl mx-auto text-lg md:text-xl leading-relaxed">
                A RoamPass hálózat folyamatosan bővül. Keresd meg a hozzád legközelebbi edzőtermet, és eddz bárhol, bármikor.
            </p>
        </div>
    </div>

    {{-- TÉRKÉP SZEKCIÓ --}}
    <div class="max-w-7xl mx-auto px-6 mb-24 animate-fade-up" style="animation-delay: 0.2s">
        <div class="glass-card p-3 overflow-hidden shadow-[0_0_50px_rgba(191,64,255,0.1)]">
            <div id="partners-map" style="height: 450px;" class="w-full rounded-[2rem] bg-gray-900 shadow-inner"></div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-6 mb-24 mt-12 animate-fade-up" style="animation-delay: 0.3s">
        <div class="bg-gray-900/80 backdrop-blur-sm p-8 rounded-3xl shadow-2xl border border-purple-500/30 relative">
            <h2 class="text-2xl font-bold text-white mb-6 text-center uppercase tracking-widest">Keresés a hálózatban</h2>

            <div class="relative w-full">
                <input type="text" id="searchTerm"
                        placeholder="Terem neve, város vagy cím..."
                        class="search-input-field w-full rounded-xl pl-6 pr-14 py-4 text-lg font-medium shadow-lg outline-none transition-all">
            </div>
        </div>

        <div id="noResults" class="text-center text-xl text-gray-500 mt-12 hidden">
            <p>Sajnáljuk, nincs találat. Próbálj másik kifejezést!</p>
        </div>
    </div>

    {{-- KONDITERMEK GRID --}}
    <div id="gymsContainer" class="max-w-6xl mx-auto px-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-10">
        @foreach($gyms as $gym)
        <a data-search="{{ strtolower($gym->name . ' ' . $gym->city . ' ' . $gym->address) }}"
           href="{{ route('partners.show', $gym->id) }}"
           class="gym-card rounded-3xl overflow-hidden shadow-2xl border border-purple-500/20 transition-all duration-500 hover:scale-[1.03] group relative">

            <div class="gym-card-img-wrapper">
                <img src="{{ Str::startsWith($gym->image_url, 'http') ? $gym->image_url : asset('storage/' . $gym->image_url) }}"
                     alt="{{ $gym->name }}">

                <div class="absolute inset-0 bg-purple-900/40 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
                    <span class="text-white text-lg font-bold py-2 px-6 bg-purple-600 rounded-full shadow-[0_0_15px_rgba(191,64,255,0.5)]">
                        Megtekintés
                    </span>
                </div>

                @if(isset($gym->type))
                <div class="absolute top-4 left-4 z-20">
                    <span class="px-3 py-1 bg-purple-600/90 backdrop-blur-md text-[10px] font-black uppercase tracking-widest rounded-full border border-purple-400/50">
                        {{ $gym->type }}
                    </span>
                </div>
                @endif
            </div>

            <div class="info-section p-6 space-y-4">
                <h3 class="text-2xl font-extrabold text-purple-300 group-hover:text-neon-purple transition-colors">{{ $gym->name }}</h3>

                <div class="flex justify-between items-center text-sm">
                    <p class="text-gray-400 flex items-center gap-2 font-semibold">
                        <i data-lucide="map-pin" class="w-4 h-4 text-purple-500"></i> {{ $gym->city }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <div class="flex text-yellow-400">
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-400"></i>
                    </div>
                    <span class="text-white font-bold text-lg">{{ $gym->average_rating }}</span>
                    <span class="text-gray-500 text-xs">({{ $gym->review_count }} értékelés)</span>
                </div>

                {{-- ÉLŐ KIHASZNÁLTSÁG JELZŐ --}}
                @php 
                    $occupancy = $gym->getOccupancyStatus(); 
                @endphp
                
                @if($occupancy && is_array($occupancy))
                <div class="mt-2 flex items-center justify-between border-t border-purple-500/20 pt-4">
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Élő Forgalom:</span>
                    
                    <div class="flex items-center gap-2 {{ $occupancy['bg'] }} {{ $occupancy['color'] }} px-3 py-1.5 rounded-full border border-white/5 animate-pulse shadow-lg">
                        <i data-lucide="{{ $occupancy['icon'] }}" class="w-3.5 h-3.5"></i>
                        <span class="text-[10px] font-black uppercase tracking-wider">
                            {{ $occupancy['level'] }}
                        </span>
                    </div>
                </div>
                @endif

                <p class="text-gray-500 text-sm flex items-center gap-2 pt-4 border-t border-purple-500/20">
                    <i data-lucide="locate-fixed" class="w-4 h-4 text-purple-400/50"></i> {{ Str::limit($gym->address, 35) }}
                </p>
            </div>
        </a>
        @endforeach
    </div>
</section>

<script>
    lucide.createIcons();

    document.addEventListener("DOMContentLoaded", () => {
        const initialCoords = [47.1625, 19.5033];
        const map = L.map('partners-map', {
            scrollWheelZoom: false,
            minZoom: 7,
            zoomControl: true // Zoom gombok bekapcsolva
        }).setView(initialCoords, 7);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; CARTO'
        }).addTo(map);

        // Lila SVG Marker létrehozása
        const purpleIcon = L.divIcon({
            html: `<svg width="30" height="42" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" filter="drop-shadow(0 0 5px #bf40ff)">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" fill="#bf40ff" stroke="#fff" stroke-width="1"/>
                    <circle cx="12" cy="10" r="3" fill="#0a0c1e"/>
                   </svg>`,
            className: "",
            iconSize: [30, 42],
            iconAnchor: [15, 42],
            popupAnchor: [0, -40]
        });

        const gymMarkers = [
            @foreach($gyms as $gym)
                @if(isset($gym->coords['lat']))
                    @php
                        $occ = $gym->getOccupancyStatus();
                        $pinColor = '#bf40ff';
                        
                        if ($occ) {
                            if ($occ['level'] === 'Szellős') $pinColor = '#34d399';
                            elseif ($occ['level'] === 'Közepes') $pinColor = '#fbbf24';
                            elseif ($occ['level'] === 'Tömött') $pinColor = '#f87171';
                        }
                    @endphp
                    { 
                        lat: {{ $gym->coords['lat'] }}, 
                        lng: {{ $gym->coords['lng'] }}, 
                        name: "{{ $gym->name }}", 
                        city: "{{ $gym->city }}",
                        color: "{{ $pinColor }}"
                    },
                @endif
            @endforeach
        ];

        gymMarkers.forEach(markerData => {
            const dynamicIcon = L.divIcon({
                html: `<svg width="30" height="42" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" filter="drop-shadow(0 0 5px ${markerData.color})">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" fill="${markerData.color}" stroke="#fff" stroke-width="1"/>
                        <circle cx="12" cy="10" r="3" fill="#0a0c1e"/>
                       </svg>`,
                className: "",
                iconSize: [30, 42],
                iconAnchor: [15, 42],
                popupAnchor: [0, -40]
            });

            const marker = L.marker([markerData.lat, markerData.lng], { icon: dynamicIcon }).addTo(map);
            
            marker.bindPopup(`<b style="color:${markerData.color}; font-family: sans-serif;">${markerData.name}</b><br><span style="color:#64748b">${markerData.city}</span>`);
        });

        const searchInput = document.getElementById('searchTerm');
        const cards = document.querySelectorAll('.gym-card');
        const noResults = document.getElementById('noResults');

        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            let hasMatch = false;

            cards.forEach(card => {
                const isMatch = card.dataset.search.includes(term);
                card.style.display = isMatch ? "flex" : "none";
                if (isMatch) hasMatch = true;
            });

            noResults.classList.toggle('hidden', hasMatch);
        });
    });
</script>
@endsection