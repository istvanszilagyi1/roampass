@extends('layouts.app')

@section('title', 'RoamPass - Bérlet vásárlása')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black text-white flex items-center justify-center py-24 px-4">

    <div class="bg-gray-850/90 backdrop-blur-sm rounded-3xl shadow-xl p-12 max-w-lg w-full text-center relative overflow-hidden">
        <!-- Animált súlyzó a háttérben -->
        <div class="absolute top-4 left-1/4 text-6xl opacity-10 animate-float">🏋️‍♂️</div>
        <div class="absolute bottom-4 right-1/3 text-5xl opacity-10 animate-float-delay">💪</div>

        <h1 class="text-3xl font-bold mb-6 drop-shadow-lg">🎟️ Vásárold meg bérleted!</h1>
        <p class="text-gray-300 mb-8 drop-shadow-sm">
            Egy bérlettel 12 alkalomra kapsz QR kódot, amit bármely RoamPass partner konditeremben felhasználhatsz.
        </p>

        <form method="POST" action="{{ route('passes.store') }}">
            @csrf
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold shadow hover:bg-blue-500 hover:scale-105 transition-all duration-300 w-full">
                Megvásárolom a bérletet
            </button>
        </form>

        <p class="text-gray-400 mt-6 text-sm">
            A bérlet aktiválása után QR kódot kapsz, amelyet minden alkalommal a terem bejáratánál tudsz használni.
        </p>
    </div>
</div>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(5deg); }
}
@keyframes floatDelay {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(10px) rotate(-5deg); }
}
.animate-float { animation: float 6s ease-in-out infinite; }
.animate-float-delay { animation: floatDelay 8s ease-in-out infinite; }
</style>
@endsection
