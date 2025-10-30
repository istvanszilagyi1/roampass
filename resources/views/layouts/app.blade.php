<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RoamPass')</title>
    <script src="https://cdn.tailwindcss.com"></script>

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
                <img src="{{ asset('images/logo.png') }}" alt="Roam Logo" class="h-10 w-auto transition-transform group-hover:scale-105">
                <span class="text-xl font-bold text-white tracking-tight group-hover:text-indigo-400 transition-colors">RoamPass</span>
            </a>

            <!-- Hamburger (mobil) -->
            <button id="menu-btn" class="lg:hidden text-white text-3xl focus:outline-none">
                ☰
            </button>

            <!-- 🧭 NAVIGÁCIÓ -->
            <nav id="nav-menu" class="flex items-center space-x-4">
                @if(Auth::user() && Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Admin Panel</a>
                @endif
                @auth
                    @if(Auth::user()?->scannerProfile)
                        <a href="{{ route('scanner.dashboard') }}" class="nav-link">
                            Scanner Dashboard
                        </a>
                    @endif
                @endauth
                <a href="{{ route('home') }}" class="nav-link">Főoldal</a>
                <a href="{{ route('partners.index') }}" class="nav-link">Partnereink</a>

                @auth
                    <a href="{{ route('passes.index') }}" class="nav-link">Saját bérleteim</a>
                    <a href="{{ route('passes.create') }}" class="nav-link">Bérlet vásárlás</a>
                    <a href="{{ route('profile.edit') }}" class="nav-link">Profilom</a>

                    @php
                        // Ellenőrizzük, hogy van-e hozzárendelt gym
                        $ownedGym = \App\Models\Gym::where('owner_id', Auth::id())->first();
                    @endphp

                    @if($ownedGym)
                        <a href="{{ route('partner.dashboard') }}" class="nav-link">Partner Dashboard</a>
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
        <div id="mobile-menu" class="hidden flex-col items-center bg-gray-900/95 backdrop-blur-lg shadow-lg py-4 space-y-3 text-center lg:hidden">
            <a href="{{ route('home') }}" class="nav-link">Főoldal</a>
            <a href="{{ route('partners.index') }}" class="nav-link">Partnereink</a>
            @guest
                <a href="{{ route('login') }}" class="nav-btn-primary w-4/5">Bejelentkezés</a>
                <a href="{{ route('register') }}" class="nav-btn-secondary w-4/5">Regisztráció</a>
            @endguest
        </div>
    </header>

    <!-- 📄 CONTENT -->
    <main class="pt-24 container mx-auto px-4 relative z-10">
        @yield('content')
    </main>

    <!-- ⚙️ FOOTER -->
    <footer class="text-center py-8 mt-16 text-gray-400 text-sm z-10 relative">
        &copy; {{ date('Y') }} RoamPass. Minden jog fenntartva.
    </footer>

    <!-- ✨ TAILWIND STÍLUSOK -->
    <style>
        /* 🧭 Navbar linkek */
       .nav-link {
            @apply text-gray-200 font-semibold px-4 py-2 rounded-full bg-gray-800/70 backdrop-blur-sm shadow-sm
            hover:shadow-lg border border-gray-700
            hover:bg-indigo-600/80
            hover:text-white transition-all duration-300;
        }
        .nav-link::after {
            content: "";
            display: block;
            height: 2px;
            width: 0%;
            background: linear-gradient(to right, #6366f1, #9333ea);
            transition: width 0.3s ease-in-out;
            margin: 0 auto;
            border-radius: 2px;
        }
        .nav-link:hover::after {
            width: 60%;
        }

        /* 💜 Bejelentkezés gomb */
        .nav-btn-primary {
            @apply bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold px-5 py-2
            rounded-full shadow-md hover:shadow-xl hover:scale-105 hover:brightness-110
            transition-all duration-300;
        }

        /* 💠 Regisztráció gomb */
        .nav-btn-secondary {
            @apply border-2 border-indigo-500 text-indigo-300 font-semibold px-5 py-2 rounded-full
            bg-transparent hover:bg-indigo-600 hover:text-white hover:shadow-xl hover:scale-105
            transition-all duration-300;
        }

        /* 🚪 Kijelentkezés */
        .nav-btn-logout {
            @apply text-red-400 font-semibold px-4 py-2 rounded-full bg-gray-800/70 backdrop-blur-sm
            shadow-sm hover:bg-red-600/70 hover:text-white hover:shadow-md transition-all duration-300;
        }

        /* ✨ Navbar spacing */
        nav a, nav button {
            @apply flex items-center justify-center min-w-[130px] text-center;
        }
        nav {
            @apply flex items-center space-x-3;
        }

        /* 🌌 Smooth fade-in animáció */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        body.content-visible main {
            animation: fadeIn 0.7s ease-in-out;
        }
    </style>

</body>
</html>
