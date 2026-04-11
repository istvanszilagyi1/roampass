@extends('layouts.app')

@section('title', $gym->name . ' - RoamPass')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

@php
    $userReview = null;
    if(auth()->check()) {
        $userReview = $gym->reviews->where('user_id', auth()->id())->first();
    }
@endphp

<style>
    .bg-gray-850 { background-color: #1f1f1f; }

    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 8px;
        align-items: center;
    }

    .star-rating input { display: none; }

    .star-rating label {
        cursor: pointer;
        color: #6b7280;
        transition: all 0.18s ease-in-out;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
        color: #fbbf24;
        transform: scale(1.12);
    }

    .section-gap { padding-top: 2.5rem; padding-bottom: 2.5rem; }
    .section-divider {
        height: 1px;
        background: linear-gradient(90deg, rgba(255,255,255,0.03), rgba(255,255,255,0.06), rgba(255,255,255,0.03));
        margin: 1.25rem 0;
        border-radius: 2px;
    }

    .card-soft {
        background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
        border: 1px solid rgba(255,255,255,0.04);
        padding: 1.25rem;
        border-radius: 1rem;
        box-shadow: 0 6px 18px rgba(2,6,23,0.55);
    }

    .review-card {
        padding: 1rem 1.25rem;
        background: rgba(17,24,39,0.45);
        border: 1px solid rgba(148,163,184,0.04);
        border-radius: 1rem;
        margin-bottom: 0.6rem;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .review-card:hover { transform: translateY(-4px); box-shadow: 0 10px 30px rgba(2,6,23,0.6); }

    .form-card { padding: 1rem; border-radius: .85rem; }
    .summary-box { padding: .75rem; border-radius: .75rem; }

    .reviews-grid { gap: 1rem; }
    @media (min-width: 768px) {
        .reviews-grid { gap: 1.5rem; }
        .section-gap { padding-top: 3.5rem; padding-bottom: 3.5rem; }
    }

    @media (max-width: 768px) {
        .gym-header-img {
            height: 300px !important;
        }
        
        .header-gradient {
            background: linear-gradient(to top, #111827 0%, #111827cc 40%, transparent 100%) !important;
        }
        
        .header-content {
            padding: 1.5rem !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            background: linear-gradient(to top, #111827 0%, transparent 100%) !important;
        }
        
        .gym-title {
            font-size: 2rem !important;
            line-height: 1.2 !important;
            margin-bottom: 0.75rem !important;
        }
        
        .rating-badge {
            padding: 0.5rem 1rem !important;
            font-size: 0.875rem !important;
        }
        
        .rating-badge .stars {
            width: 1.25rem !important;
            height: 1.25rem !important;
        }
        
        .rating-badge .rating-number {
            font-size: 1.125rem !important;
        }
        
        #gym-map {
            height: 400px !important;
        }
        
        .card-soft {
            padding: 1rem !important;
        }
        
        .section-gap {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }
        
        .py-28 {
            padding-top: 5rem !important;
            padding-bottom: 5rem !important;
        }
        
        .pb-32 {
            padding-bottom: 6rem !important;
        }
        
        .p-8 {
            padding: 1.5rem !important;
        }
        
        .md\\:p-16 {
            padding: 1.5rem !important;
        }
        
        .star-rating label i {
            width: 1.75rem !important;
            height: 1.75rem !important;
        }
        
        .review-card {
            padding: 0.875rem 1rem !important;
        }
        
        .direction-button {
            width: 100% !important;
            justify-content: center !important;
            padding: 0.875rem 1rem !important;
        }
    }
    
    @media (max-width: 480px) {
        .gym-header-img {
            height: 250px !important;
        }
        
        .gym-title {
            font-size: 1.5rem !important;
        }
        
        .rating-badge {
            padding: 0.375rem 0.75rem !important;
        }
        
        #gym-map {
            height: 350px !important;
        }
        
        .mt-40 {
            margin-top: 2rem !important;
        }
    }
</style>

<section class="py-28 pb-32 bg-gradient-to-br from-gray-950 via-gray-900 to-black text-white max-w-7xl mx-auto px-6">
    <div class="bg-gray-850/90 backdrop-blur-sm rounded-[2.5rem] shadow-2xl shadow-black/80 border border-gray-700 overflow-hidden">
        <div class="relative">
            <img src="{{ Str::startsWith($gym->image_url, 'http') ? $gym->image_url : asset('storage/' . $gym->image_url) }}"
                 alt="{{ $gym->name }}"
                 class="gym-header-img w-full h-[450px] object-cover transition duration-700 hover:scale-105 opacity-80">

            <div class="header-gradient absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent"></div>

            <div class="header-content absolute inset-x-0 bottom-0 flex flex-col justify-end p-8 md:p-16">
                 <h1 class="gym-title text-5xl md:text-7xl font-extrabold text-white drop-shadow-2xl mb-4 tracking-tight">
                    {{ $gym->name }}
                 </h1>

                 <div class="rating-badge inline-flex items-center gap-3 bg-black/70 backdrop-blur-md px-5 py-2 rounded-full border border-white/20 w-max">
                    <div class="stars flex text-yellow-400">
                        <i data-lucide="star" class="w-6 h-6 fill-yellow-400"></i>
                    </div>
                    <span class="rating-number font-bold text-xl text-white">{{ $gym->average_rating }}</span>
                    <span class="text-gray-300 text-sm font-medium">({{ $gym->review_count }} vélemény alapján)</span>
                 </div>
            </div>
        </div>

        <div class="p-8 md:p-16 space-y-16">
            @if($gym->description)
                <div class="pb-8 border-b border-gray-800">
                    <h3 class="text-3xl font-bold mb-6 text-blue-400 flex items-center gap-3">
                        <i data-lucide="info" class="w-8 h-8"></i> Rólunk
                    </h3>
                    <p class="text-gray-300 leading-relaxed text-xl font-light">{{ $gym->description }}</p>
                </div>
            @endif

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-12 pb-12 border-b border-gray-800">

                <div class="space-y-3">
                    <h4 class="text-sm font-bold uppercase text-gray-500 tracking-widest flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-5 h-5 text-pink-500"></i> Elhelyezkedés
                    </h4>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $gym->city }}</p>
                        <p class="text-lg text-gray-400">{{ $gym->address }}</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <h4 class="text-sm font-bold uppercase text-gray-500 tracking-widest flex items-center gap-2">
                        <i data-lucide="clock" class="w-5 h-5 text-green-500"></i> Nyitvatartás
                    </h4>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $gym->opening_hours }}</p>
                        <p class="text-sm text-gray-500 italic">Ünnepnapokon változhat</p>
                    </div>
                </div>

                @if(isset($gym->type))
                <div class="space-y-3">
                    <h4 class="text-sm font-bold uppercase text-gray-500 tracking-widest flex items-center gap-2">
                        <i data-lucide="dumbbell" class="w-5 h-5 text-blue-500"></i> Típus
                    </h4>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $gym->type }}</p>
                        <span class="inline-block mt-2 px-3 py-1 bg-blue-900/30 text-blue-300 text-xs rounded-full border border-blue-800">RoamPass Elfogadóhely</span>
                    </div>
                </div>
                @endif

                @php 
                    $occupancy = $gym->getOccupancyStatus(); 
                @endphp
                
                @if($occupancy && is_array($occupancy))
                <div class="space-y-3">
                    <h4 class="text-sm font-bold uppercase text-gray-500 tracking-widest flex items-center gap-2">
                        <i data-lucide="activity" class="w-5 h-5 {{ $occupancy['color'] }}"></i> Jelenlegi Forgalom
                    </h4>
                    <div>
                        <div class="inline-flex items-center gap-3 {{ $occupancy['bg'] }} {{ $occupancy['color'] }} px-5 py-2.5 rounded-2xl border border-white/5 animate-pulse shadow-lg mt-1">
                            <i data-lucide="{{ $occupancy['icon'] }}" class="w-6 h-6"></i>
                            <span class="text-lg font-black uppercase tracking-wider">
                                {{ $occupancy['level'] }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-3 italic">Az elmúlt 2 óra belépései alapján kalkulálva.</p>
                    </div>
                </div>
                @endif

            </div>
            <div class="space-y-16 section-gap">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-3xl font-bold text-white flex items-center gap-3">
                        <i data-lucide="message-square" class="w-8 h-8 text-blue-400"></i> Vélemények
                    </h3>
                </div>

                {{-- Értékelés beküldése / Szerkesztése --}}
                <div class="mb-16">
                    <div class="card-soft p-4 md:p-6 rounded-2xl">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                             {{-- Form (left / main) --}}
                            <div class="md:col-span-2 form-card">
                                 @auth
                                    <form action="{{ route('gyms.review.store', $gym->id) }}" method="POST" class="space-y-4">
                                        @csrf
                                        @if($userReview)
                                            <h4 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
                                                <i data-lucide="edit-3" class="w-5 h-5 text-yellow-400"></i> Korábbi véleményed módosítása
                                            </h4>
                                        @else
                                            <h4 class="text-lg font-bold text-white mb-2">Milyen volt az edzés? Értékeld a termet!</h4>
                                        @endif

                                        <label class="block text-sm font-medium text-gray-400 mb-2 uppercase">Értékelésed</label>
                                        <div class="star-rating flex items-center gap-4 py-2">
                                            @for($i=5; $i>=1; $i--)
                                                <input type="radio" id="star{{$i}}" name="rating" value="{{$i}}"
                                                    {{ ($userReview && $userReview->rating == $i) ? 'checked' : '' }} required />
                                                <label for="star{{$i}}" class="text-gray-400 hover:text-yellow-400 transition transform" title="{{$i}} csillag">
                                                    <i data-lucide="star" class="w-9 h-9"></i>
                                                </label>
                                            @endfor
                                        </div>

                                        <label class="block text-sm font-medium text-gray-400 mt-4 mb-2 uppercase">Véleményed (opcionális)</label>
                                        <textarea name="comment" rows="4"
                                            class="w-full bg-gray-950 border border-gray-800 rounded-xl p-4 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition resize-none shadow-inner"
                                            placeholder="Írd le a tapasztalataidat... (pl. tiszta öltözők, jó gépek)">{{ $userReview ? $userReview->comment : '' }}</textarea>

                                         <div class="flex items-center gap-3 mt-3">
                                             <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-br from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white px-5 py-2.5 rounded-lg font-semibold shadow-md transform hover:-translate-y-0.5 transition">
                                                 <i data-lucide="{{ $userReview ? 'refresh-cw' : 'send' }}" class="w-4 h-4"></i>
                                                 {{ $userReview ? 'Vélemény frissítése' : 'Vélemény beküldése' }}
                                             </button>
                                             <span class="text-sm text-gray-400 hidden md:inline">Köszönjük a visszajelzésed — segít másoknak is!</span>
                                         </div>
                                    </form>
                                 @else
                                    <div class="rounded-lg border border-dashed border-gray-800 p-6 flex flex-col items-center justify-center gap-4">
                                         <p class="text-gray-300 text-base">Szeretnéd megosztani a tapasztalataidat?</p>
                                         <a href="{{ route('login') }}" class="inline-flex items-center border border-blue-500 text-blue-400 px-5 py-2 rounded-lg font-semibold hover:bg-blue-500 hover:text-white transition">
                                             Jelentkezz be az értékeléshez
                                         </a>
                                     </div>
                                 @endauth
                            </div>

                            <div class="md:col-span-1 summary-box bg-black/30 border border-gray-800">
                                <div class="text-center">
                                    <div class="inline-flex items-center justify-center gap-3 bg-black/60 p-3 rounded-full mx-auto mb-3">
                                        <i data-lucide="star" class="w-5 h-5 text-yellow-400"></i>
                                        <div>
                                            <div class="text-2xl font-bold text-white leading-none">{{ $gym->average_rating }}</div>
                                            <div class="text-xs text-gray-400">({{ $gym->review_count }} vélemény)</div>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-300 mb-3">Átlagértékelés és legfrissebb vélemények</p>
                                    @if($gym->review_count > 0)
                                        <a href="#reviews-list" class="inline-block text-sm text-blue-400 hover:underline">Lapozz a véleményekhez</a>
                                    @endif
                                </div>
                            </div>
                         </div>
                    </div>
                 </div>

                {{-- Lista --}}
                <div id="reviews-list" class="grid reviews-grid grid-cols-1 md:grid-cols-2">
                    @forelse($gym->reviews->sortByDesc('created_at') as $review)
                        <article class="review-card">
                             <header class="flex items-start gap-4 mb-3">
                                 <div class="w-12 h-12 flex-shrink-0 rounded-full bg-gradient-to-br from-gray-700 to-gray-600 flex items-center justify-center font-semibold text-white text-lg border border-gray-600">
                                     {{ substr($review->user->last_name, 0, 1) }}{{ substr($review->user->first_name, 0, 1) }}
                                 </div>
                                 <div class="flex-1">
                                     <div class="flex items-center justify-between gap-4">
                                         <div>
                                             <p class="text-white font-semibold">{{ $review->user->last_name }} {{ $review->user->first_name }}</p>
                                             <p class="text-xs text-gray-500">{{ $review->created_at->format('Y.m.d H:i') }}</p>
                                         </div>
                                         <div class="flex items-center gap-1">
                                             <div class="flex text-yellow-400">
                                                 @for($i=0; $i < $review->rating; $i++)
                                                     <i data-lucide="star" class="w-4 h-4 fill-yellow-400"></i>
                                                 @endfor
                                                 @for($i=$review->rating; $i < 5; $i++)
                                                     <i data-lucide="star" class="w-4 h-4 text-gray-700"></i>
                                                 @endfor
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </header>

                             @if($review->comment)
                                <p class="text-gray-300 text-sm leading-relaxed mb-3 break-words">{{ $review->comment }}</p>
                             @endif

                             <footer class="flex items-center justify-between">
                                 <div class="text-xs text-gray-500">Felhasználói értékelés</div>
                                 @auth
                                     @if(auth()->user()->is_admin || auth()->id() === $review->user_id)
                                         <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Biztosan törlöd ezt a véleményt?');">
                                             @csrf
                                             @method('DELETE')
                                             <button type="submit" class="text-sm text-red-400 hover:text-red-500 transition">Törlés</button>
                                         </form>
                                     @endif
                                 @endauth
                             </footer>
                         </article>
                    @empty
                        <div class="col-span-1 md:col-span-2 text-center py-12">
                            <div class="inline-block bg-black/30 border border-dashed border-gray-800 rounded-2xl p-8">
                                <i data-lucide="message-square-off" class="w-12 h-12 mx-auto mb-4 opacity-40"></i>
                                <p class="text-lg text-gray-300 mb-2">Még nem érkezett értékelés ehhez a teremhez.</p>
                                <p class="text-sm text-gray-400">Légy te az első, aki véleményt ír!</p>
                            </div>
                        </div>
                    @endforelse
                </div>
             </div>

            @if(isset($gym->website_url))
                <div class="text-center border-t border-gray-800 pt-10">
                    <a href="{{ $gym->website_url }}" target="_blank"
                       class="inline-flex items-center bg-gray-800 text-white px-8 py-4 rounded-full font-bold border border-gray-600 hover:bg-gray-700 hover:border-gray-500 transition-all duration-300">
                        <i data-lucide="globe" class="w-5 h-5 mr-3 text-blue-400"></i>
                        Hivatalos weboldal megnyitása
                    </a>
                </div>
            @endif

        </div>
    </div>

    @if(isset($gym->coords['lat']))
        <div class="mt-12 md:mt-40 mb-12 space-y-8 md:space-y-16">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 md:mb-8 gap-4 md:gap-6">
                <div>
                    <h3 class="text-2xl md:text-4xl font-bold text-white mb-2">Megközelítés</h3>
                    <p class="text-gray-400 text-sm md:text-base">Találd meg a leggyorsabb utat a konditeremhez.</p>
                </div>

                {{-- Útvonaltervező link --}}
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $gym->coords['lat'] }},{{ $gym->coords['lng'] }}"
                target="_blank"
                class="direction-button inline-flex items-center bg-blue-600 text-white px-5 md:px-8 py-3 md:py-4 rounded-xl md:rounded-2xl font-bold shadow-lg shadow-blue-900/40 hover:bg-blue-500 hover:-translate-y-1 transition-all duration-300 text-sm md:text-base">
                    <i data-lucide="navigation" class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3"></i> Útvonaltervezés (Google Maps)
                </a>
            </div>
            <div id="gym-map" 
                class="w-full rounded-2xl md:rounded-[2rem] shadow-2xl shadow-black border-2 md:border-4 border-gray-800 z-10 relative overflow-hidden" 
                style="height: 250px; min-height: 250px;">
                <style>
                    @media (min-width: 768px) {
                        #gym-map {
                            height: 400px !important;
                            min-height: 400px;
                        }
                    }
                </style>
                <div id="map-container" style="width: 100%; height: 100%;"></div>
            </div>
        </div>
    @else
        <div class="mt-12 md:mt-40 mb-12">
            <div class="bg-gray-800/50 rounded-2xl p-8 text-center border border-gray-700">
                <i data-lucide="map-off" class="w-12 h-12 mx-auto mb-4 text-gray-500"></i>
                <p class="text-gray-400">A térkép megjelenítéséhez szükséges koordináták nem állnak rendelkezésre.</p>
            </div>
        </div>
    @endif

</section>

@if(isset($gym->coords['lat']))
<script>
    lucide.createIcons();

    document.addEventListener("DOMContentLoaded", () => {
        const mapContainer = document.getElementById('gym-map');
        if (!mapContainer) {
            console.error('Térkép konténer nem található!');
            return;
        }

        const coords = [{{ $gym->coords['lat'] }}, {{ $gym->coords['lng'] }}];
        
        if (!coords[0] || !coords[1]) {
            console.error('Érvénytelen koordináták:', coords);
            mapContainer.innerHTML = '<div class="flex items-center justify-center h-full bg-gray-900 text-gray-400">Érvénytelen koordináták</div>';
            return;
        }

        try {
            // Térkép inicializálás
            const map = L.map('gym-map').setView(coords, 15);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '© <a href="https://carto.com/attributions">CARTO</a>, © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                subdomains: 'abcd',
                maxZoom: 19,
                minZoom: 3
            }).addTo(map);

            // Egyedi marker létrehozása
            const customIcon = L.divIcon({
                className: 'custom-marker',
                html: '<div style="background-color: #3b82f6; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
                iconSize: [24, 24],
                popupAnchor: [0, -12]
            });

            // Marker hozzáadása
            const marker = L.marker(coords, { icon: customIcon })
                .addTo(map)
                .bindPopup(`
                    <div style="font-family: sans-serif; padding: 4px;">
                        <strong style="color: #1f2937;">{{ $gym->name }}</strong><br>
                        <span style="color: #4b5563; font-size: 12px;">{{ $gym->address }}</span>
                    </div>
                `)
                .openPopup();

            // Ablak átméretezéskor frissítjük a térképet
            window.addEventListener('resize', () => {
                setTimeout(() => map.invalidateSize(), 100);
            });

            console.log('Térkép sikeresen betöltve:', coords);
        } catch (error) {
            console.error('Hiba a térkép betöltésekor:', error);
            mapContainer.innerHTML = '<div class="flex items-center justify-center h-full bg-gray-900 text-red-400">Hiba történt a térkép betöltésekor</div>';
        }
    });
</script>
@endif

@endsection