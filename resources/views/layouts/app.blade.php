<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#bf40ff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
    <title>@yield('title', 'RoamPass')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body.loading { overflow: hidden; }
        main { opacity: 0; transition: opacity 0.6s ease-in-out; }
        body.loaded main { opacity: 1; }

        body:not(.loaded) main { visibility: hidden; }
        body.loaded main { visibility: visible; }

        #loader {
            position: fixed;
            inset: 0;
            background: #05050a;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            transition: opacity 0.6s ease-in-out;
        }
        
        .loader-logo {
            width: 120px;
            filter: drop-shadow(0 0 20px rgba(191, 64, 255, 0.6));
            animation: loader-pulse 1.8s infinite ease-in-out;
        }

        @keyframes loader-pulse {
            0%, 100% { transform: scale(1); opacity: 0.9; }
            50% { transform: scale(1.08); opacity: 1; filter: drop-shadow(0 0 30px rgba(191, 64, 255, 0.9)); }
        }

        html {
            scroll-behavior: smooth;
        }

        header {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .top-spacer {
            height: 110px;
            transition: height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(10, 12, 30, 0.95);
        }

        ::-webkit-scrollbar-thumb {
            background: #bf40ff;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #9b2fe6;
        }


        #mobile-menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 90;
            background: rgba(10, 12, 30, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #mobile-menu.open {
            display: flex;
            opacity: 1;
        }

        #mobile-menu a,
        #mobile-menu button {
            font-size: 1.25rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #fff;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        #mobile-menu a:hover,
        #mobile-menu button:hover {
            color: #bf40ff;
            transform: scale(1.05);
        }

        #mobile-menu .close-btn {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            font-size: 2rem;
            color: #fff;
            background: none;
            border: none;
            cursor: pointer;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        #mobile-menu .close-btn:hover {
            color: #bf40ff;
            transform: rotate(90deg);
        }

        .top-spacer {
            height: 110px;
            transition: height 0.4s ease;
        }
        .top-spacer.hidden {
            height: 0;
        }

        #menu-btn {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 32px;
            height: 32px;
            cursor: pointer;
            z-index: 100;
        }

        #menu-btn span {
            display: block;
            width: 24px;
            height: 2px;
            background: #fff;
            margin: 3px 0;
            transition: all 0.3s ease;
        }

        @media (min-width: 1024px) {
            #menu-btn {
                display: none !important;
            }
        }
        body.menu-open {
            overflow: hidden;
        }

        @media (max-width: 1023px) {
            header {
                padding-top: 0.75rem !important;
                padding-bottom: 0.75rem !important;
            }
            
            header.py-5 {
                padding-top: 0.75rem !important;
                padding-bottom: 0.75rem !important;
            }
            
            .top-spacer {
                height: 70px !important;
            }
            
            .top-spacer.hidden {
                height: 0 !important;
            }
        }
    </style>

    <script>
        (function() {
            const loader = document.getElementById('loader');
            const isFirstVisit = !sessionStorage.getItem('roampass_loaded');
            
            if (!loader) return;
            
            if (isFirstVisit) {
                document.body.classList.add('loading');
                
                if (document.readyState === 'complete') {
                    setTimeout(finishLoading, 1000);
                } else {
                    window.addEventListener('load', () => {
                        setTimeout(finishLoading, 1000);
                    });
                }
            } else {
                hideLoaderImmediately();
            }
            
            function finishLoading() {
                if (!loader) return;
                
                loader.style.opacity = '0';
                document.body.classList.remove('loading');
                document.body.classList.add('loaded');
                sessionStorage.setItem('roampass_loaded', 'true');
                
                setTimeout(() => {
                    if (loader && loader.parentNode) {
                        loader.style.display = 'none';
                    }
                }, 600);
            }
            
            function hideLoaderImmediately() {
                if (!loader) return;
                loader.style.display = 'none';
                document.body.classList.remove('loading');
                document.body.classList.add('loaded');
            }
            
            setTimeout(() => {
                if (loader && loader.style.display !== 'none') {
                    console.warn('Loader timeout - forcing hide');
                    hideLoaderImmediately();
                }
            }, 3000);
        })();
        
        document.addEventListener("DOMContentLoaded", () => {
            const menuBtn = document.getElementById('menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            const closeBtn = document.getElementById('close-menu-btn');

            function openMenu() {
                if (!mobileMenu) return;
                mobileMenu.classList.add('open');
                document.body.classList.add('menu-open');
            }

            function closeMenu() {
                if (!mobileMenu) return;
                mobileMenu.classList.remove('open');
                document.body.classList.remove('menu-open');
            }

            if (menuBtn) {
                menuBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openMenu();
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    closeMenu();
                });
            }

            if (mobileMenu) {
                mobileMenu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', closeMenu);
                });
            }

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeMenu();
            });
        });

        (function setupHeader() {
            let header = null;
            let spacer = null;
            let sentinel = null;
            let isHeaderChanged = false;
            
            function createSentinel() {
                sentinel = document.createElement('div');
                sentinel.style.position = 'absolute';
                sentinel.style.top = '31px';
                sentinel.style.left = '0';
                sentinel.style.width = '1px';
                sentinel.style.height = '1px';
                sentinel.style.pointerEvents = 'none';
                sentinel.style.opacity = '0';
                document.body.insertBefore(sentinel, document.body.firstChild);
                
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (!header) return;
                        
                        const isAtTop = entry.isIntersecting;
                        
                        if (isAtTop) {
                            if (isHeaderChanged) {
                                header.classList.remove('bg-[#0a0c1e]/95', 'backdrop-blur-md', 'py-3', 'shadow-2xl');
                                header.classList.add('py-5');
                                if (spacer) spacer.classList.remove('hidden');
                                isHeaderChanged = false;
                            }
                        } else {
                            if (!isHeaderChanged) {
                                header.classList.add('bg-[#0a0c1e]/95', 'backdrop-blur-md', 'py-3', 'shadow-2xl');
                                header.classList.remove('py-5');
                                if (spacer) spacer.classList.add('hidden');
                                isHeaderChanged = true;
                            }
                        }
                    });
                }, {
                    threshold: 0,
                    rootMargin: '-31px 0px 0px 0px'
                });
                
                observer.observe(sentinel);
            }
            
            function initHeader() {
                header = document.querySelector('header');
                spacer = document.getElementById('top-spacer');
                
                if (!header) return;
                
                if (window.scrollY > 30) {
                    header.classList.add('bg-[#0a0c1e]/95', 'backdrop-blur-md', 'py-3', 'shadow-2xl');
                    header.classList.remove('py-5');
                    if (spacer) spacer.classList.add('hidden');
                    isHeaderChanged = true;
                } else {
                    header.classList.remove('bg-[#0a0c1e]/95', 'backdrop-blur-md', 'py-3', 'shadow-2xl');
                    header.classList.add('py-5');
                    if (spacer) spacer.classList.remove('hidden');
                    isHeaderChanged = false;
                }
                
                createSentinel();
            }
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initHeader);
            } else {
                initHeader();
            }
        })();
    </script>
</head>

<body class="bg-gray-950 text-gray-100 font-sans relative loading">

<div id="loader">
    <img src="{{ asset('images/logo.png') }}" alt="RoamPass" class="loader-logo">
    <div class="mt-6 h-1 w-24 bg-gray-900 rounded-full overflow-hidden">
        <div class="h-full bg-purple-600 animate-[loading_1.2s_infinite]"></div>
    </div>
</div>

<style> @keyframes loading { from { transform: translateX(-100%); } to { transform: translateX(100%); } } </style>

<div class="fixed inset-0 z-[-1] bg-cover bg-center"
     style="background-image: url('{{ asset('images/gym-bg2.png') }}'); filter: brightness(0.2);">
</div>

<header class="fixed top-0 left-0 w-full z-50 transition-all duration-500 bg-transparent py-5">
    <div class="container mx-auto flex justify-between items-center px-6">
        <a href="{{ route('home') }}" class="flex items-center space-x-2">
            <img src="{{ asset('images/logo.png') }}" alt="Roam Logo" class="h-9 w-auto">
            <span class="text-xl font-black text-white tracking-tighter uppercase">RoamPass</span>
        </a>

        <button id="menu-btn" class="lg:hidden focus:outline-none" aria-label="Menü megnyitása">
            <span></span><span></span><span></span>
        </button>

        <!-- DESKTOP NAV -->
        <nav id="nav-menu" class="hidden lg:flex items-center space-x-8 uppercase text-[10px] font-black tracking-[0.2em]">
            @auth
                @php
                    $user = Auth::user();
                    $isAdmin = $user->is_admin ?? false;
                    $isScanner = $user->scannerProfile ?? false;
                    $isPartner = \App\Models\Gym::where('owner_id', $user->id)->first();
                    $hasStudentId = !empty($user->student_id_number);
                    $currentRoute = request()->route()->getName();
                @endphp

                @if($isAdmin)
                    <a href="{{ route('home') }}" class="nav-link {{ $currentRoute == 'home' ? 'active' : '' }}">Főoldal</a>
                    <a href="{{ route('partners.index') }}" class="nav-link {{ $currentRoute == 'partners.index' ? 'active' : '' }}">Partnereink</a>
                    @if($hasStudentId)
                        <a href="{{ route('passes.index') }}" class="nav-link {{ $currentRoute == 'passes.index' ? 'active' : '' }}">Saját bérletem</a>
                    @endif
                    <a href="{{ route('passes.create') }}" class="nav-link {{ $currentRoute == 'passes.create' ? 'active' : '' }}">Bérlet vásárlás</a>
                    <a href="{{ route('profile.edit') }}" class="nav-link {{ $currentRoute == 'profile.edit' ? 'active' : '' }}">Profilom</a>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ $currentRoute == 'admin.dashboard' ? 'active' : '' }}">Admin Panel</a>

                @elseif($isScanner)
                    <a href="{{ route('scanner.dashboard') }}" class="nav-link {{ $currentRoute == 'scanner.dashboard' ? 'active' : '' }}">Scanner Dashboard</a>

                @elseif($isPartner)
                    <a href="{{ route('home') }}" class="nav-link {{ $currentRoute == 'home' ? 'active' : '' }}">Főoldal</a>
                    <a href="{{ route('partners.index') }}" class="nav-link {{ $currentRoute == 'partners.index' ? 'active' : '' }}">Partnereink</a>
                    <a href="{{ route('partner.dashboard') }}" class="nav-link {{ $currentRoute == 'partner.dashboard' ? 'active' : '' }}">Partner Dashboard</a>

                @else
                    <a href="{{ route('home') }}" class="nav-link {{ $currentRoute == 'home' ? 'active' : '' }}">Főoldal</a>
                    <a href="{{ route('partners.index') }}" class="nav-link {{ $currentRoute == 'partners.index' ? 'active' : '' }}">Partnereink</a>
                    @if($hasStudentId)
                        <a href="{{ route('passes.index') }}" class="nav-link {{ $currentRoute == 'passes.index' ? 'active' : '' }}">Saját bérletem</a>
                    @endif
                    <a href="{{ route('passes.create') }}" class="nav-link {{ $currentRoute == 'passes.create' ? 'active' : '' }}">Bérlet vásárlás</a>
                    <a href="{{ route('profile.edit') }}" class="nav-link {{ $currentRoute == 'profile.edit' ? 'active' : '' }}">Profilom</a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-red-500/70 hover:text-red-500 transition-colors">Kilépés</button>
                </form>
            @else
                @php
                    $currentRoute = request()->route()->getName();
                @endphp
                <a href="{{ route('home') }}" class="nav-link {{ $currentRoute == 'home' ? 'active' : '' }}">Főoldal</a>
                <a href="{{ route('partners.index') }}" class="nav-link {{ $currentRoute == 'partners.index' ? 'active' : '' }}">Partnereink</a>
                <a href="{{ route('login') }}" class="border border-purple-500/30 px-5 py-2 rounded-full hover:bg-purple-500/10 transition-all">Belépés</a>
                <a href="{{ route('register') }}" class="bg-purple-600 px-5 py-2 rounded-full shadow-lg shadow-purple-600/30 hover:bg-purple-500 transition-all text-white">Regisztráció</a>
            @endauth
        </nav>
    </div>
</header>

<div id="mobile-menu">
    <button id="close-menu-btn" class="close-btn" aria-label="Menü bezárása">✕</button>

    @auth
        @php
            $user = Auth::user();
            $isAdmin = $user->is_admin ?? false;
            $isScanner = $user->scannerProfile ?? false;
            $isPartner = \App\Models\Gym::where('owner_id', $user->id)->first();
            $hasStudentId = !empty($user->student_id_number);
        @endphp

        @if($isAdmin)
            <a href="{{ route('home') }}">Főoldal</a>
            <a href="{{ route('partners.index') }}">Partnereink</a>
            @if($hasStudentId)
                <a href="{{ route('passes.index') }}">Saját bérletem</a>
            @endif
            <a href="{{ route('passes.create') }}">Bérlet vásárlás</a>
            <a href="{{ route('profile.edit') }}">Profilom</a>
            <a href="{{ route('admin.dashboard') }}">Admin Panel</a>

        @elseif($isScanner)
            <a href="{{ route('scanner.dashboard') }}">Scanner Dashboard</a>

        @elseif($isPartner)
            <a href="{{ route('home') }}">Főoldal</a>
            <a href="{{ route('partners.index') }}">Partnereink</a>
            <a href="{{ route('partner.dashboard') }}">Partner Dashboard</a>

        @else
            <a href="{{ route('home') }}">Főoldal</a>
            <a href="{{ route('partners.index') }}">Partnereink</a>
            @if($hasStudentId)
                <a href="{{ route('passes.index') }}">Saját bérletem</a>
            @endif
            <a href="{{ route('passes.create') }}">Bérlet vásárlás</a>
            <a href="{{ route('profile.edit') }}">Profilom</a>
        @endif

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-red-500 hover:text-red-400">Kilépés</button>
        </form>
    @else
        <a href="{{ route('home') }}">Főoldal</a>
        <a href="{{ route('partners.index') }}">Partnereink</a>
        <a href="{{ route('login') }}">Belépés</a>
        <a href="{{ route('register') }}" class="text-purple-400">Regisztráció</a>
    @endauth
</div>

<div id="top-spacer" class="top-spacer"></div>

<main class="pb-12 container mx-auto px-4 relative z-10">
    @yield('content')
</main>

<footer class="relative z-[200] border-t border-purple-500/10 bg-[#0a0c1e]/95 backdrop-blur-md">
    <div class="max-w-6xl mx-auto px-6 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex flex-col items-center md:items-start">
                <h3 class="text-[8px] uppercase tracking-[0.3em] text-purple-400 font-black mb-1">Support</h3>
                <a href="mailto:info@roampass.hu" class="text-sm font-black text-white hover:text-purple-400">info@roampass.hu</a>
            </div>

            <div class="flex gap-6 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                <a href="#" class="hover:text-purple-400 transition-colors">ÁSZF</a>
                <a href="#" class="hover:text-purple-400 transition-colors">Adatkezelés</a>
            </div>

            <div class="flex gap-4">
                <a href="#" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:text-purple-400 border border-white/5 transition-all">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 448 512"><path d="M448 209.91a210.06 210.06 0 0 1-122.77-39.25V349.38A162.55 162.55 0 1 1 185 188.31V278.2a74.62 74.62 0 1 0 52.23 71.18V0l88 0a121.18 121.18 0 0 0 1.86 22.17h0A122.18 122.18 0 0 0 381 102.39a121.43 121.43 0 0 0 67 20.14Z"/></svg>
                </a>
                <a href="#" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:text-purple-400 border border-white/5 transition-all">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                    </svg>
                </a>
                <a href="#" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:text-purple-400 border border-white/5 transition-all">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                    </svg>
                </a>
            </div>
        </div>

        <div class="mt-6 text-center border-t border-white/5 pt-4">
            <p class="text-gray-600 text-[8px] font-medium tracking-[0.2em] uppercase">
                &copy; {{ date('Y') }} RoamPass · Minden jog fenntartva
            </p>
        </div>
    </div>
</footer>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    if (typeof lucide !== 'undefined') { 
        lucide.createIcons(); 
    }
</script>
<script>
    (function ultimateSafety() {
        setTimeout(() => {
            const loader = document.getElementById('loader');
            const body = document.body;
            
            if (loader && loader.style.display !== 'none') {
                console.warn('Ultimate safety: forcing loader hide');
                loader.style.display = 'none';
                body.classList.remove('loading');
                body.classList.add('loaded');
            }
            
            const main = document.querySelector('main');
            if (main && window.getComputedStyle(main).opacity === '0') {
                main.style.opacity = '1';
            }
        }, 200);
    })();
</script>

</body>
</html>
