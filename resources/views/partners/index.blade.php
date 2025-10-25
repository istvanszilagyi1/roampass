@extends('layouts.app')

@section('title', 'Partnereink - RoamPass')

@section('content')
<section class="text-center py-16 bg-gray-950 text-white">
    <h1 class="text-4xl font-bold mb-6">Partnereink</h1>
    <p class="text-gray-300 max-w-2xl mx-auto mb-12">
        Itt találod az összes RoamPass partner konditermet, ahol felhasználhatod a QR kódodat.
        Kattints a kártyákra a részletekért!
    </p>

    <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto px-6 mb-12">
        @foreach($gyms as $gym)
        <a href="{{ route('partners.show', $gym->id) }}" class="bg-gray-850 rounded-2xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
            <img src="{{ $gym->image_url }}" alt="{{ $gym->name }}" class="w-full h-48 object-cover">
            <div class="p-6">
                <h3 class="text-xl font-bold text-blue-400 mb-2">{{ $gym->name }}</h3>
                <p class="text-gray-400">{{ $gym->city }}</p>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endsection
