@extends('layouts.app')

@section('title', 'Saját bérleted - RoamPass')

@section('content')
<section class="py-16 bg-gray-950 text-white min-h-screen">
    <div class="max-w-3xl mx-auto px-6 text-center">

        <h1 class="text-4xl font-bold mb-8 text-blue-400">🎟️ Saját bérleted</h1>

        <!-- Session üzenetek -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-600 text-white rounded-lg shadow">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-600 text-white rounded-lg shadow">
                {{ session('error') }}
            </div>
        @endif

        @if($passes->count())
            @php
                $pass = $passes->first();
            @endphp
                <div class="bg-gray-850 rounded-3xl shadow-xl p-8 mb-12 inline-block">
                    @if($pass->remaining_uses > 0)
                        <p class="text-gray-300 mb-2 font-semibold"><strong>Hátralévő alkalmak:</strong> {{ $pass->remaining_uses }}/12</p>
                        <p class="text-gray-300 mb-6 font-semibold"><strong>Vásárlás dátuma:</strong> {{ $pass->purchase_date }}</p>

                        <div class="inline-block bg-gray-900 p-6 rounded-2xl shadow-lg">
                            <p class="mb-4 text-gray-300 font-semibold">Mutasd fel a QR kódot a bejelentkezéshez:</p>
                            <img src="{{ $pass->qr_code_url }}" alt="QR kód" class="mx-auto w-48 h-48 object-contain">
                        </div>
                    @else
                        <p class="text-red-500 font-semibold mt-2">Elfogytak az alkalmak, kérlek válts új bérletet!</p>
                    @endif
                </div>
        @else
            <p class="text-gray-300 text-lg">Még nincs bérleted. <a href="{{ route('passes.create') }}" class="text-blue-400 underline">Vásárolj most!</a></p>
        @endif

    </div>
</section>
@endsection
