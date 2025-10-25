@extends('layouts.app')

@section('title', $gym->name . ' - RoamPass')

@section('content')
<section class="py-16 bg-gray-950 text-white max-w-4xl mx-auto px-6">
    <div class="rounded-2xl overflow-hidden shadow-lg mb-8">
        <img src="{{ $gym->image_url }}" alt="{{ $gym->name }}" class="w-full h-64 object-cover">
    </div>

    <h1 class="text-4xl font-bold mb-4 text-blue-400">{{ $gym->name }}</h1>

    <p class="text-gray-300 mb-2"><strong>Cím:</strong> {{ $gym->address }}</p>
    <p class="text-gray-300 mb-2"><strong>Város:</strong> {{ $gym->city }}</p>
    <p class="text-gray-300 mb-2"><strong>Nyitvatartás:</strong> {{ $gym->opening_hours }}</p>

    @if($gym->description)
        <p class="text-gray-300 mb-4">{{ $gym->description }}</p>
    @endif

    @if($gym->latitude && $gym->longitude)
    <div id="gym-map" class="w-full h-96 rounded-2xl shadow-lg mb-8"></div>
    @endif

    <a href="{{ route('passes.index') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold shadow hover:bg-indigo-500 hover:scale-105 transition-all duration-300">
        🎟️ Bérlet használata
    </a>
</section>

@if($gym->latitude && $gym->longitude)
<script>
function initGymMap() {
    const map = new google.maps.Map(document.getElementById('gym-map'), {
        center: { lat: parseFloat({{ $gym->latitude }}), lng: parseFloat({{ $gym->longitude }}) },
        zoom: 15
    });
    new google.maps.Marker({
        position: { lat: parseFloat({{ $gym->latitude }}), lng: parseFloat({{ $gym->longitude }}) },
        map: map,
        title: '{{ $gym->name }}'
    });
}
</script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initGymMap">
</script>
@endif
@endsection
