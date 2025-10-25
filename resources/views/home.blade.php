@extends('layouts.app')

@section('title', 'RoamPass - A bérlet, ami Veled utazik')

@section('content')

<!-- Teljes háttér -->
<div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black text-white relative overflow-hidden">

    <!-- Háttér animált súlyzók -->
    <div class="absolute inset-0 overflow-hidden -z-10 opacity-10">
        <div class="animate-float absolute top-10 left-1/5 text-6xl">🏋️‍♂️</div>
        <div class="animate-float-delay absolute top-1/3 right-1/5 text-5xl">💪</div>
        <div class="animate-float absolute bottom-10 left-1/3 text-7xl">🏋️</div>
        <div class="animate-float absolute top-1/2 left-1/2 text-6xl">🏋️‍♀️</div>
        <div class="animate-float-delay absolute bottom-1/4 right-1/3 text-5xl">💪</div>
    </div>

    <!-- HERO -->
    <section class="text-center py-24">
        <h1 class="text-5xl font-extrabold mb-6 drop-shadow-lg">Mozogj bárhol, egyetlen bérlettel!</h1>
        <p class="text-lg mb-8 max-w-2xl mx-auto text-gray-300 drop-shadow-sm">
            A RoamPass lehetővé teszi, hogy egyetlen bérlettel több városban, különböző edzőtermekben sportolj.
            Egyszerű. Digitális. Szabad.
        </p>

        @guest
            <div class="space-x-4">
                <a href="{{ route('login') }}" class="bg-white text-gray-900 px-6 py-3 rounded-lg font-semibold shadow hover:bg-gray-200 hover:scale-105 transition-all duration-300">Bejelentkezés</a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold shadow hover:bg-blue-500 hover:scale-105 transition-all duration-300">Regisztráció</a>
            </div>
        @else
            <a href="{{ route('passes.index') }}" class="bg-white text-gray-900 px-6 py-3 rounded-lg font-semibold shadow hover:bg-gray-200 hover:scale-105 transition-all duration-300">Saját bérleteim</a>
        @endguest
    </section>

    <!-- Miért a RoamPass? -->
    <section class="grid md:grid-cols-3 gap-8 text-center mb-24 max-w-6xl mx-auto px-6">
        <div class="p-8 bg-gray-850 text-white rounded-2xl shadow-lg hover:shadow-blue-900/40 transition-all duration-500 hover:-translate-y-2 hover:scale-105 animate-subtle-float">
            <img src="{{ asset('images/icon-flexibility.png') }}" alt="" class="mx-auto w-16 mb-4">
            <h3 class="text-xl font-bold mb-2 text-blue-400">Teljes rugalmasság</h3>
            <p class="text-gray-400">Sportolj ott, ahol éppen vagy. Nem köt meg egyetlen terem sem.</p>
        </div>
        <div class="p-8 bg-gray-850 text-white rounded-2xl shadow-lg hover:shadow-blue-900/40 transition-all duration-500 hover:-translate-y-2 hover:scale-105 animate-subtle-float-delay">
            <img src="{{ asset('images/icon-wallet.png') }}" alt="" class="mx-auto w-16 mb-4">
            <h3 class="text-xl font-bold mb-2 text-blue-400">Digitális bérlet</h3>
            <p class="text-gray-400">Online vásárlás, digitális belépés, automatikus hosszabbítás.</p>
        </div>
        <div class="p-8 bg-gray-850 text-white rounded-2xl shadow-lg hover:shadow-blue-900/40 transition-all duration-500 hover:-translate-y-2 hover:scale-105 animate-subtle-float">
            <img src="{{ asset('images/icon-network.png') }}" alt="" class="mx-auto w-16 mb-4">
            <h3 class="text-xl font-bold mb-2 text-blue-400">Országos hálózat</h3>
            <p class="text-gray-400">Több tucat városban elérhető RoamPass-partner konditermek.</p>
        </div>
    </section>

    <!-- CTA -->
<!-- CTA / Információs szekció -->
    <section class="relative text-center py-24 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-950 text-white rounded-2xl shadow-2xl max-w-6xl mx-auto mb-24 overflow-hidden">
        <div class="absolute inset-0 opacity-10 overflow-hidden -z-10">
            <div class="animate-float absolute top-10 left-1/3 text-6xl">💪</div>
            <div class="animate-float-delay absolute top-1/2 right-1/4 text-7xl">🏋️‍♂️</div>
            <div class="animate-float absolute bottom-10 left-1/4 text-5xl">🏋️‍♀️</div>
            <div class="animate-float-delay absolute bottom-20 right-1/3 text-6xl">💪</div>
        </div>

        <div class="bg-gray-850/80 backdrop-blur-sm inline-block px-12 py-10 rounded-3xl shadow-xl border border-gray-700 max-w-3xl">
            <h2 class="text-3xl font-bold mb-4 drop-shadow-lg">Újdonságok és információk</h2>
            <p class="text-lg mb-6 text-gray-300 drop-shadow-sm">
                Folyamatosan bővítjük partnertermeink listáját, új funkciók érkeznek a RoamPass alkalmazásba,
                és mostantól egyszerűen követheted a legnépszerűbb edzéseket a városodban.
            </p>
            <ul class="text-gray-400 list-disc list-inside space-y-2 text-left">
                <li>Új partnertermek több városban</li>
                <li>Digitális belépés és automatikus bérletkezelés</li>
                <li>Heti kihívások és exkluzív edzésajánlók</li>
                <li>Tippek és útmutatók kezdőknek és haladóknak</li>
            </ul>
            <a href="{{ route('home') }}#features" class="mt-6 inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold shadow hover:bg-blue-500 hover:scale-105 transition-all duration-300">Tudj meg többet</a>
        </div>
    </section>

    <section class="relative text-center py-24 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-950 text-white rounded-2xl shadow-2xl max-w-6xl mx-auto mb-24 overflow-hidden">
        <div class="absolute inset-0 opacity-10 overflow-hidden -z-10">
            <div class="animate-float absolute top-10 left-1/4 text-6xl">🏋️‍♂️</div>
            <div class="animate-float-delay absolute top-1/2 right-1/3 text-7xl">💪</div>
            <div class="animate-float absolute bottom-10 left-1/3 text-5xl">🏋️‍♀️</div>
            <div class="animate-float-delay absolute bottom-20 right-1/4 text-6xl">💪</div>
        </div>

        <div class="bg-gray-850/80 backdrop-blur-sm inline-block px-12 py-10 rounded-3xl shadow-xl border border-gray-700 max-w-4xl">
            <h2 class="text-3xl font-bold mb-4 drop-shadow-lg">Csatlakozz partnerként!</h2>
            <p class="text-lg mb-6 text-gray-300 drop-shadow-sm">
                Légy része a RoamPass hálózatnak, és növeld teremforgalmad! Egy egyszerű, digitális bérletkezeléssel könnyedén vonzhatsz új sportolókat.
            </p>
            <ul class="text-gray-400 list-disc list-inside space-y-2 text-left">
                <li>Új látogatók a partnerteremből</li>
                <li>Egyszerű bérletkezelés és beléptetés</li>
                <li>Marketing támogatás a platformon</li>
                <li>Hozzáférés statisztikákhoz és riportokhoz</li>
            </ul>
            <div class="mt-6 flex flex-col md:flex-row justify-center gap-4">
                <a href="mailto:partners@roampass.hu" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold shadow hover:bg-blue-500 hover:scale-105 transition-all duration-300">
                    Kapcsolatfelvétel
                </a>
                <a href="{{ route('home') }}" class="bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold shadow hover:bg-gray-600 hover:scale-105 transition-all duration-300">
                    Tudj meg többet
                </a>
            </div>
        </div>
    </section>

</div>

<style>
@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-15px) rotate(5deg); }
}
@keyframes floatDelay {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(15px) rotate(-5deg); }
}
@keyframes subtleFloat {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-5px); }
}
.animate-float { animation: float 6s ease-in-out infinite; }
.animate-float-delay { animation: floatDelay 8s ease-in-out infinite; }
.animate-subtle-float { animation: subtleFloat 6s ease-in-out infinite; }
.animate-subtle-float-delay { animation: subtleFloat 8s ease-in-out infinite; }
.bg-gray-850 { background-color: #1f1f1f; }
</style>

@endsection
