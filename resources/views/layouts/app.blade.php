<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>@yield('title', 'RoamPass')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // 🌫 Görgetésre a navbar háttér és árnyék aktiválása
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.classList.add('bg-gray-900/90', 'shadow-lg', 'backdrop-blur-md');
                header.classList.remove('bg-transparent');
            } else {
                header.classList.remove('bg-gray-900/90', 'shadow-lg');
                header.classList.add('bg-transparent');
            }
        });
    </script>
</head>
<body class="bg-gray-950 text-gray-100 font-sans relative overflow-x-hidden duration-700 ease-in-out">

    <!-- 🔄 LOADER -->
    <div id="loader" class="fixed inset-0 bg-[rgba(10,10,15,0.95)] flex items-center justify-center z-[9999] transition-opacity duration-[1000ms] opacity-100">
        <img src="{{ asset('images/logo.png') }}" alt="RoamPass Logo" class="w-32 h-32 animate-pulse">
    </div>

    <script>
    window.addEventListener('load', () => {
        const loader = document.getElementById('loader');

        // Mutatjuk legalább 1 másodpercig
        setTimeout(() => {
            loader.classList.add('opacity-0');
        }, 1000); // 1s után kezd el halványodni

        // Teljes eltűnés 2,5s-nál (1s várakozás + 1,5s fade)
        setTimeout(() => {
            loader.style.display = 'none';
        }, 1500);
    });
    </script>

    <style>
        /* 🌌 Tartalom fade-in, miután a loader eltűnt */
        body.content-visible {
            opacity: 1;
        }
    </style>

    <!-- 🖼️ Háttérkép sötét overlay-jel -->
    <div class="fixed inset-0 z-[-1] bg-cover bg-center"
        style="background-image: url('{{ asset('images/gym-bg.jpg') }}'); filter: brightness(0.35);">
    </div>

    <!-- 🌈 NAVBAR -->
    <header class="fixed top-0 left-0 w-full z-50 transition-all duration-500 bg-transparent backdrop-blur-xl">
    <div class="container mx-auto flex justify-between items-center h-16 px-6">
        <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
            <img src="{{ asset('images/logo.png') }}" alt="Roam Logo"
                class="h-10 w-auto transition-transform group-hover:scale-105">
            <span class="text-xl font-bold text-white tracking-tight group-hover:text-indigo-400 transition-colors">RoamPass</span>
        </a>

        <!-- Hamburger (mobil) -->
        <button id="menu-btn" class="lg:hidden text-white text-3xl focus:outline-none hover:text-indigo-400 transition-colors">
            ☰
        </button>

        <!-- 🧭 NAVIGÁCIÓ -->
        <nav id="nav-menu" class="hidden lg:flex items-center space-x-3">
            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Főoldal</a>
            <a href="{{ route('partners.index') }}" class="nav-link {{ request()->routeIs('partners.index') ? 'active' : '' }}">Partnereink</a>

            @auth
                @php
                    // Megnézzük, hogy a felhasználó admin vagy scanner-e.
                    // Ezek a szerepek elrejtik a standard felhasználói funkciókat.
                    $isSpecialRole = Auth::user()->is_admin ?? false;
                    // JAVÍTVA: A scanner jogosultság ellenőrzése a scannerProfile relációval történik.
                    $isSpecialRole = $isSpecialRole || (Auth::user()?->scannerProfile);
                    $isPartner = \App\Models\Gym::where('owner_id', Auth::id())->first();
                @endphp

                {{-- ADMIN PANEL (csak adminoknak látható) --}}
                @if(Auth::user() && Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Admin Panel</a>
                @endif

                {{-- SCANNER DASHBOARD (csak scannereknek látható) --}}
                @if(Auth::user()?->scannerProfile)
                    <a href="{{ route('scanner.dashboard') }}" class="nav-link {{ request()->routeIs('scanner.dashboard') ? 'active' : '' }}">Scanner Dashboard</a>
                @endif

                {{-- STANDARD USER LINKS (elrejtve adminok és scannerek elől) --}}
                @unless($isSpecialRole)
                    <a href="{{ route('passes.index') }}" class="nav-link {{ request()->routeIs('passes.index') ? 'active' : '' }}">Saját bérleteim</a>
                    <a href="{{ route('passes.create') }}" class="nav-link {{ request()->routeIs('passes.create') ? 'active' : '' }}">Bérlet vásárlás</a>
                @endunless

                {{-- PROFILE LINK (minden bejelentkezett felhasználónak látható) --}}
                <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">Profilom</a>

                {{-- PARTNER DASHBOARD (csak a konditerem tulajdonosoknak látható) --}}
                @if($isPartner)
                    <a href="{{ route('partner.dashboard') }}" class="nav-link {{ request()->routeIs('partner.dashboard') ? 'active' : '' }}">Partner Dashboard</a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="nav-btn-logout">Kijelentkezés</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-btn-primary">Bejelentkezés</a>
                <a href="{{ route('register') }}" class="nav-btn-secondary">Regisztráció</a>
            @endauth
        </nav>
    </div>

    <!-- 📱 Mobilmenü -->
    <div id="mobile-menu" class="hidden flex-col items-center bg-gray-900/95 backdrop-blur-xl shadow-xl py-6 space-y-4 text-center lg:hidden transition-all duration-500">
        <a href="{{ route('home') }}" class="mobile-link">Főoldal</a>
        <a href="{{ route('partners.index') }}" class="mobile-link">Partnereink</a>

        @guest
            <a href="{{ route('login') }}" class="nav-btn-primary w-4/5">Bejelentkezés</a>
            <a href="{{ route('register') }}" class="nav-btn-secondary w-4/5">Regisztráció</a>
        @else
            {{-- Mivel a mobilmenü is az @auth blokkon belül van, újra el kell végezni a jogosultság ellenőrzést --}}
            @php
                $isSpecialRole = Auth::user()->is_admin ?? false;
                // JAVÍTVA: A scanner jogosultság ellenőrzése a scannerProfile relációval történik.
                $isSpecialRole = $isSpecialRole || (Auth::user()?->scannerProfile);
                $isPartner = \App\Models\Gym::where('owner_id', Auth::id())->first();
            @endphp

            @if(Auth::user() && Auth::user()->is_admin)
                 <a href="{{ route('admin.dashboard') }}" class="mobile-link">Admin Panel</a>
            @endif

            @if(Auth::user()?->scannerProfile)
                 <a href="{{ route('scanner.dashboard') }}" class="mobile-link">Scanner Dashboard</a>
            @endif

            @unless($isSpecialRole)
                <a href="{{ route('passes.index') }}" class="mobile-link">Saját bérleteim</a>
                <a href="{{ route('passes.create') }}" class="mobile-link">Bérlet vásárlás</a>
            @endunless

            <a href="{{ route('profile.edit') }}" class="mobile-link">Profilom</a>

            @if($isPartner)
                <a href="{{ route('partner.dashboard') }}" class="mobile-link">Partner Dashboard</a>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="nav-btn-logout w-4/5">Kijelentkezés</button>
            </form>
        @endauth
    </div>
        <script>
            // Mobilmenü megnyitása / zárása
            document.getElementById('menu-btn').addEventListener('click', () => {
                const menu = document.getElementById('mobile-menu');
                menu.classList.toggle('hidden');
                menu.classList.toggle('flex');
            });
        </script>
    </header>


    <!-- 📄 CONTENT -->
    <main class="pt-24 container mx-auto px-4 relative z-10">
        @yield('content')
    </main>

    <!-- ⚙️ FOOTER -->
    <footer class="text-center py-8 mt-16 text-gray-400 text-sm z-10 relative">
        &copy; {{ date('Y') }} RoamPass. Minden jog fenntartva.
    </footer>


</body>
</html>
