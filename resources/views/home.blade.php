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
    <section class="grid md:grid-cols-3 gap-10 text-center mb-24 max-w-6xl mx-auto px-6">
        @foreach([
            ['icon' => 'icon-flexibility.png', 'title' => 'Teljes rugalmasság', 'desc' => 'Sportolj ott, ahol éppen vagy. Nem köt meg egyetlen terem sem.'],
            ['icon' => 'icon-wallet.png', 'title' => 'Digitális bérlet', 'desc' => 'Online vásárlás, digitális belépés, automatikus hosszabbítás.'],
            ['icon' => 'icon-network.png', 'title' => 'Országos hálózat', 'desc' => 'Több tucat városban elérhető RoamPass-partner konditermek.'],
        ] as $item)
            <div class="group relative bg-gradient-to-br from-gray-800 to-gray-900 rounded-3xl p-10 shadow-xl border border-gray-700 hover:border-blue-600 hover:shadow-blue-600/30 transform hover:-translate-y-3 transition-all duration-500">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-purple-500/10 to-pink-500/10 opacity-0 group-hover:opacity-100 transition duration-500 rounded-3xl blur-lg"></div>
                <img src="{{ asset('images/' . $item['icon']) }}" alt="" class="mx-auto w-20 mb-5">
                <h3 class="text-2xl font-bold text-blue-400 mb-3">{{ $item['title'] }}</h3>
                <p class="text-gray-300">{{ $item['desc'] }}</p>
            </div>
        @endforeach
    </section>

    <section class="max-w-5xl mx-auto px-6 mb-24">
        <h2 class="text-3xl font-bold text-center mb-12 text-blue-400">Hogyan működik?</h2>
        <div class="flex flex-col md:flex-row md:space-x-8 space-y-8 md:space-y-0">
            @foreach([
                ['step' => '1️⃣', 'title' => 'Regisztrálj', 'desc' => 'Hozz létre egy fiókot mindössze 1 perc alatt.'],
                ['step' => '2️⃣', 'title' => 'Válassz bérletet', 'desc' => 'Vásárolj digitálisan, gyorsan és biztonságosan.'],
                ['step' => '3️⃣', 'title' => 'Lépj be a termekbe', 'desc' => 'Mutasd fel a QR-kódot és edzhetsz is!'],
            ] as $step)
            <div class="flex-1 bg-gray-850 p-8 rounded-2xl text-center border border-gray-700 hover:border-blue-500 hover:scale-105 transition-all duration-500">
                <div class="text-5xl mb-3">{{ $step['step'] }}</div>
                <h3 class="text-xl font-bold mb-2 text-blue-400">{{ $step['title'] }}</h3>
                <p class="text-gray-400">{{ $step['desc'] }}</p>
            </div>
            @endforeach
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

    <section class="max-w-4xl mx-auto text-center py-20 px-6 mb-24 bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl border border-gray-700 shadow-lg">
        <h2 class="text-3xl font-bold mb-6 text-blue-400">Lépj kapcsolatba velünk</h2>
        <p class="text-gray-400 mb-8 max-w-2xl mx-auto">Van kérdésed vagy partner lennél? Küldj üzenetet, és hamarosan válaszolunk!</p>

        <form action="mailto:partners@roampass.hu" method="post" enctype="text/plain" class="grid md:grid-cols-2 gap-6 text-left">
            <div>
                <label class="block text-sm text-gray-300 mb-2">Név</label>
                <input type="text" name="name" class="w-full bg-gray-800 text-white p-3 rounded-lg border border-gray-700 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm text-gray-300 mb-2">Email</label>
                <input type="email" name="email" class="w-full bg-gray-800 text-white p-3 rounded-lg border border-gray-700 focus:border-blue-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-300 mb-2">Üzenet</label>
                <textarea name="message" rows="4" class="w-full bg-gray-800 text-white p-3 rounded-lg border border-gray-700 focus:border-blue-500"></textarea>
            </div>
            <div class="md:col-span-2 text-center">
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-3 rounded-lg font-semibold shadow hover:scale-105 transition-all duration-300">
                    Üzenet küldése
                </button>
            </div>
        </form>
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
