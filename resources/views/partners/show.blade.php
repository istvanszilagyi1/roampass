@extends('layouts.app')

@section('title', $gym->name . ' - RoamPass')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<section class="py-24 bg-gradient-to-br from-gray-950 via-gray-900 to-black text-white max-w-6xl mx-auto px-6">

    <div class="bg-gray-850/90 backdrop-blur-sm rounded-3xl shadow-2xl shadow-black/80 border border-gray-700">

        <div class="relative overflow-hidden rounded-t-3xl">
            <img src="{{ $gym->image_url }}" alt="{{ $gym->name }}"
                 class="w-full h-[400px] object-cover transition duration-500 hover:scale-105 opacity-90">

            <div class="absolute inset-0 bg-black/40 flex items-end p-10">
                 <h1 class="text-5xl md:text-6xl font-extrabold text-white drop-shadow-lg">
                    {{ $gym->name }}
                 </h1>
            </div>
        </div>

        <div class="p-8 md:p-12 space-y-12">

            @if($gym->description)
                <div class="pb-6 border-b border-gray-700">
                    <h3 class="text-2xl font-bold mb-4 text-blue-400">Rólunk</h3>
                    <p class="text-gray-300 leading-relaxed text-lg">{{ $gym->description }}</p>
                </div>
            @endif

            <div class="grid md:grid-cols-3 gap-8 border-b border-gray-700 pb-6">

                <div class="flex flex-col gap-2">
                    <h4 class="text-sm font-semibold uppercase text-gray-400 flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-5 h-5 text-pink-400"></i> Elhelyezkedés
                    </h4>
                    <p class="text-lg font-bold text-white">{{ $gym->city }}</p>
                    <p class="text-md text-gray-300">{{ $gym->address }}</p>
                </div>

                <div class="flex flex-col gap-2">
                    <h4 class="text-sm font-semibold uppercase text-gray-400 flex items-center gap-2">
                        <i data-lucide="clock" class="w-5 h-5 text-green-400"></i> Nyitvatartás
                    </h4>
                    <p class="text-lg font-bold text-white">{{ $gym->opening_hours }}</p>
                    <p class="text-sm text-gray-500">Kérjük, ellenőrizze indulás előtt!</p>
                </div>

                @if(isset($gym->type))
                <div class="flex flex-col gap-2">
                    <h4 class="text-sm font-semibold uppercase text-gray-400 flex items-center gap-2">
                        <i data-lucide="dumbbell" class="w-5 h-5 text-blue-400"></i> Típus
                    </h4>
                    <p class="text-lg font-bold text-white">{{ $gym->type }}</p>
                    <p class="text-sm text-gray-500">RoamPass bérlettel látogatható.</p>
                </div>
                @endif
            </div>

            @if(isset($gym->website_url))
                <div class="text-center">
                    <a href="{{ $gym->website_url }}" target="_blank"
                       class="inline-flex items-center bg-blue-600 text-white px-10 py-4 rounded-full font-bold shadow-xl shadow-blue-600/30
                              hover:bg-blue-500 hover:scale-[1.05] transition-all duration-300 text-lg">
                        <i data-lucide="external-link" class="w-5 h-5 mr-3"></i>
                        Ugrás a konditerem weboldalára
                    </a>
                </div>
            @endif

        </div>
    </div>

    @if(isset($gym->coords['lat']))
    <div class="mt-20">
        <h3 class="text-3xl font-bold text-white mb-8 text-center border-b border-gray-800 pb-4">Megközelítés</h3>
        <div id="gym-map" class="w-full h-[550px] rounded-3xl shadow-2xl shadow-gray-950/80 border-4 border-blue-600/50"></div>
    </div>
    @endif

</section>

@if(isset($gym->coords['lat']))
<script>
    lucide.createIcons();

    document.addEventListener("DOMContentLoaded", () => {
        const coords = [{{ $gym->coords['lat'] }}, {{ $gym->coords['lng'] }}];

        // Térkép inicializálás
        const map = L.map('gym-map').setView(coords, 15);

        // Sötét stílusú térkép csempe (a katalógus oldalhoz hasonlóan)
        L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>',
            maxZoom: 18,
        }).addTo(map);

        // Marker hozzáadása
        L.marker(coords)
            .addTo(map)
            .bindPopup("<strong>{{ $gym->name }}</strong>")
            .openPopup();
    });
</script>
@endif

<style>
.bg-gray-850 { background-color: #1f1f1f; }
</style>

@endsection
