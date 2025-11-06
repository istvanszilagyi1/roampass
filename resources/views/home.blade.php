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
            ['title' => 'Teljes rugalmasság', 'desc' => 'Sportolj ott, ahol éppen vagy. Nem köt meg egyetlen terem sem.'],
            ['title' => 'Digitális bérlet', 'desc' => 'Online vásárlás, digitális belépés, automatikus hosszabbítás.'],
            ['title' => 'Országos hálózat', 'desc' => 'Több tucat városban elérhető RoamPass-partner konditermek.'],
        ] as $item)
            <div class="group relative bg-gradient-to-br from-gray-800 to-gray-900 rounded-3xl p-10 shadow-xl border border-gray-700 hover:border-blue-600 hover:shadow-blue-600/30 transform hover:-translate-y-3 transition-all duration-500">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-purple-500/10 to-pink-500/10 opacity-0 group-hover:opacity-100 transition duration-500 rounded-3xl blur-lg"></div>
                <h3 class="text-2xl font-bold text-blue-400 mb-3">{{ $item['title'] }}</h3>
                <p class="text-gray-300">{{ $item['desc'] }}</p>
            </div>
        @endforeach
    </section>

    <section class="max-w-5xl mx-auto px-6 mb-24">
        <h2 class="text-3xl font-bold text-center mb-12 text-blue-400">Hogyan működik?</h2>

        <div class="relative flex flex-col md:flex-row items-center justify-between">
            <div class="hidden md:block absolute top-1/2 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 opacity-30 z-0"></div>

            @foreach([
                ['icon' => '📝', 'title' => 'Regisztrálj', 'desc' => 'Hozz létre fiókot 1 perc alatt.'],
                ['icon' => '💳', 'title' => 'Válts bérletet', 'desc' => 'Vásárolj digitálisan, gyorsan és biztonságosan.'],
                ['icon' => '💪', 'title' => 'Edzés elkezdése', 'desc' => 'Mutasd fel a QR-kódot és sportolj.'],
            ] as $step)
            <div class="flex-1 relative z-10 flex flex-col items-center group hover:scale-105 transition-transform duration-500">

                <!-- Ikon konténer (fix magasság, középre igazítva) -->
                <div class="h-20 flex items-center justify-center mb-4">
                    <span class="text-6xl animate-bounce">{{ $step['icon'] }}</span>
                </div>

                <h3 class="text-xl font-bold mb-2 text-blue-400">{{ $step['title'] }}</h3>
                <p class="text-gray-300 text-center">{{ $step['desc'] }}</p>

                <!-- kis csatlakozó pont a vonalhoz -->
                <div class="absolute top-1/2 w-4 h-4 bg-blue-400 rounded-full md:-left-2 md:-translate-y-1/2"></div>
            </div>
            @endforeach
        </div>
    </section>



    <!-- CTA -->
<!-- CTA / Újdonságok -->
    <section class="max-w-7xl mx-auto px-6 mb-24 relative overflow-hidden">
            <div class="grid lg:grid-cols-2 gap-12 items-center bg-gray-900 p-8 md:p-16 rounded-3xl shadow-2xl border border-blue-600/50">

                <div class="text-center lg:text-left">
                    <span class="text-5xl mb-4 inline-block text-blue-400">🌐</span>
                    <h2 class="text-4xl font-extrabold mb-4 text-white">Bővül a RoamPass Hálózat!</h2>
                    <p class="text-lg text-gray-300 mb-6">
                        Minden héten új partnerekkel bővül a lista. Ellenőrizd, hogy a kedvenc termet megtalálod-e.
                    </p>
                    <a href="{{ route('partners.index') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-full font-bold shadow hover:bg-blue-500 hover:scale-105 transition-all duration-300">
                        Összes Partner Megtekintése
                    </a>
                </div>

                <div class="space-y-4 bg-gray-800 p-8 rounded-2xl border border-gray-700">
                    <div class="flex items-start group hover:bg-gray-700/50 p-3 rounded-lg transition duration-300">
                        <span class="text-2xl mr-4">📍</span>
                        <div>
                            <h3 class="font-bold text-lg text-white">Országos lefedettség</h3>
                            <p class="text-gray-400">Budapest, Debrecen, Szeged és vidéki nagyvárosok.</p>
                        </div>
                    </div>
                    <div class="flex items-start group hover:bg-gray-700/50 p-3 rounded-lg transition duration-300">
                        <span class="text-2xl mr-4">✅</span>
                        <div>
                            <h3 class="font-bold text-lg text-white">Együttműködés a legjobbakkal</h3>
                            <p class="text-gray-400">Kizárólag minőségi termeket válogatunk partnereink közé.</p>
                        </div>
                    </div>
                    <div class="flex items-start group hover:bg-gray-700/50 p-3 rounded-lg transition duration-300">
                        <span class="text-2xl mr-4">🔔</span>
                        <div>
                            <h3 class="font-bold text-lg text-white">Tájékoztatás</h3>
                            <p class="text-gray-400">Értesítést kapsz, ha új partner csatlakozik a közeledben.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-24 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 text-white relative overflow-hidden">
            <div class="max-w-6xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">

                <div class="group p-8">
                    <h2 class="text-4xl font-extrabold mb-4 text-blue-400">Légy RoamPass Partner! 📈</h2>
                    <p class="text-lg text-gray-300 mb-8">
                        Növeld a terem forgalmát és érj el egy új, rugalmas sportolói réteget. A csatlakozás egyszerű és digitális.
                    </p>
                    <ul class="text-gray-300 list-disc list-inside space-y-4 ml-4">
                        <li class="hover:text-white transition-colors duration-300"><strong class="text-purple-400">Új bevételi forrás:</strong> Garantált látogatószám a hálózatból.</li>
                        <li class="hover:text-white transition-colors duration-300"><strong class="text-pink-400">Zero Adminisztráció:</strong> Mi kezeljük a bérleteket, te csak a sportolót látod.</li>
                        <li class="hover:text-white transition-colors duration-300"><strong class="text-green-400">Célzott Marketing:</strong> Ingyenes promóció a RoamPass hálózatban.</li>
                    </ul>
                    <a href="mailto:info@roampass.hu" class="mt-8 inline-block text-blue-400 font-semibold hover:text-blue-300 transition-colors duration-300 border-b border-blue-400">
                        Kérj ingyenes tájékoztatást &rarr;
                    </a>
                </div>

                <div id="partner-form" class="bg-gray-850/70 backdrop-blur-sm p-10 rounded-3xl border border-gray-700 shadow-2xl transform hover:shadow-blue-600/30 transition duration-500 animate-subtle-float">
                    <h3 class="text-2xl font-bold mb-6 text-white text-center">Jelentkezés partnernek</h3>
                    <form action="mailto:info@roampass.hu" method="post" enctype="text/plain" class="grid gap-6 text-left">
                        <div>
                            <label for="gym-name-partner" class="block text-sm text-gray-300 mb-2">Terem neve <span class="text-red-500">*</span></label>
                            <input type="text" id="gym-name-partner" name="Terem_neve" required class="w-full bg-gray-900 text-white p-3 rounded-lg border border-gray-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all duration-300">
                        </div>
                        <div>
                            <label for="email-partner-form" class="block text-sm text-gray-300 mb-2">Kapcsolattartó Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email-partner-form" name="Email" required class="w-full bg-gray-900 text-white p-3 rounded-lg border border-gray-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all duration-300">
                        </div>
                        <div>
                            <label for="message-partner-form" class="block text-sm text-gray-300 mb-2">Üzenet/Kérdés</label>
                            <textarea id="message-partner-form" name="Üzenet" rows="3" class="w-full bg-gray-900 text-white p-3 rounded-lg border border-gray-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all duration-300"></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-3 rounded-full font-bold shadow hover:scale-105 transition-all duration-300">
                                Érdekel a Partner Program
                            </button>
                        </div>
                    </form>
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
