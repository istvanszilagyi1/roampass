@extends('layouts.app')

@section('title', 'RoamPass - Edzz ott, ahol éppen vagy!')

@section('content')
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes scroll { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
    @keyframes neonPulse {
        0%, 100% { text-shadow: 0 0 12px rgba(191, 64, 255, 0.8), 0 0 25px rgba(191, 64, 255, 0.4); }
        50% { text-shadow: 0 0 20px rgba(191, 64, 255, 1), 0 0 35px rgba(191, 64, 255, 0.6); }
    }
    
    .animate-fade-up { animation: fadeInUp 0.8s ease-out forwards; }
    .animate-neon-pulse { animation: neonPulse 3s infinite; }
    
    .scroll-container { display: flex; width: max-content; animation: scroll 40s linear infinite; }

    .reviews-container {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        gap: 2rem;
        padding-bottom: 1rem;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
        cursor: grab;
        scroll-behavior: smooth;
    }
    .reviews-container::-webkit-scrollbar {
        display: none;
    }
    .reviews-container:active {
        cursor: grabbing;
    }
    .review-card {
        scroll-snap-align: center;
    }

    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus {
        -webkit-text-fill-color: #ffffff !important;
        -webkit-box-shadow: 0 0 0px 1000px #0a0c1e inset !important;
        transition: background-color 5000s ease-in-out 0s;
    }
    
    input, textarea, input:focus, textarea:focus { 
        color: white !important; 
        background-color: rgba(0, 0, 0, 0.6) !important;
    }

    .neon-border { border: 1px solid rgba(191, 64, 255, 0.8); box-shadow: 0 0 15px rgba(191, 64, 255, 0.3); }
    .neon-soft { background: rgba(10, 12, 30, 0.9); border: 1px solid rgba(191, 64, 255, 0.5); position: relative; }
    .text-neon-purple { color: #bf40ff; text-shadow: 0 0 12px rgba(191, 64, 255, 0.8), 0 0 25px rgba(191, 64, 255, 0.4); }

    .arrow-neon {
        color: #bf40ff;
        filter: drop-shadow(0 0 8px rgba(191, 64, 255, 0.9));
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-out, opacity 0.3s ease;
        opacity: 0;
    }
    .faq-item.active .faq-answer {
        max-height: 300px;
        opacity: 1;
        padding-top: 1rem;
    }
    .faq-item.active .faq-icon { transform: rotate(180deg); color: #bf40ff; }

    #review-prev, #review-next {
        box-shadow: 0 0 15px rgba(191, 64, 255, 0.5);
    }
    
    #review-prev:hover, #review-next:hover {
        box-shadow: 0 0 25px rgba(191, 64, 255, 0.8);
    }
    
    #review-dots button {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    #review-dots button:hover {
        transform: scale(1.2);
    }
    
    .review-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .review-card:hover {
        transform: translateY(-8px);
        border-color: rgba(191, 64, 255, 0.8);
        box-shadow: 0 0 30px rgba(191, 64, 255, 0.3);
    }
    
    @media (max-width: 768px) {
        #review-prev {
            margin-left: -10px !important;
        }
        
        #review-next {
            margin-right: -10px !important;
        }
        
        #review-prev, #review-next {
            padding: 0.5rem;
        }
        
        #review-prev i, #review-next i {
            width: 1.25rem;
            height: 1.25rem;
        }
        
        .review-card {
            width: 280px;
        }
    }
    
    @media (max-width: 640px) {
        .reviews-container {
            gap: 1rem;
        }
        
        .review-card {
            width: 260px;
            padding: 1rem;
        }
    }
</style>

<div class="min-h-screen text-white font-sans selection:bg-purple-500/30 overflow-x-hidden">
    <section class="relative min-h-[85vh] flex items-center justify-end px-6 py-24">
        <div class="relative z-10 max-w-5xl w-full md:mr-20 text-right animate-fade-up">
            <h1 class="text-6xl md:text-8xl font-black mb-6 uppercase animate-neon-pulse">
                <div class="grid grid-cols-1">
                    <span class="text-neon-purple leading-none tracking-tight block">EDZZ OTT,</span>
                    <span class="leading-none tracking-tight">
                        AHOL <span class="text-neon-purple">ÉPPEN</span> VAGY!
                    </span>
                </div>
            </h1>
            <p class="text-xl md:text-2xl mb-12 text-gray-300 leading-relaxed font-light text-right ml-auto w-full">
                A <strong class="text-purple-400 font-bold">ROAMPASS</strong>-szal egyetlen bérlet megnyitja neked az ország legjobb termeit.
            </p>
            <div class="flex flex-wrap items-center justify-end gap-4 mt-10">
                <a href="{{ route('passes.index') }}" 
                class="flex items-center gap-2 bg-purple-600 hover:bg-purple-500 text-white px-8 py-4 rounded-full font-bold uppercase text-sm tracking-widest transition-all duration-300 shadow-[0_0_20px_rgba(191,64,255,0.5)] hover:scale-110 active:scale-95">
                    <span>Bérletet veszek</span>
                    <i data-lucide="arrow-right-circle" class="w-5 h-5"></i>
                </a>
                <a href="{{ route('partners.index') }}" 
                class="flex items-center gap-2 border-2 border-purple-500 text-purple-200 hover:bg-purple-500/10 px-8 py-4 rounded-full font-bold uppercase text-sm tracking-widest transition-all duration-300 shadow-[0_0_15px_rgba(191,64,255,0.3)] hover:scale-105 active:scale-95">
                    <i data-lucide="map-pin" class="w-4 h-4 text-purple-400"></i>
                    <span>Termek keresése</span>
                </a>
            </div>
        </div>
    </section>

    <section class="py-12 border-y border-white/5 bg-black/20 overflow-hidden">
        <h2 class="text-center text-xs uppercase tracking-[0.5em] text-purple-400 font-bold mb-8">Kiemelt Partnereink</h2>
        <div class="scroll-container gap-8 items-center">
            @foreach(range(1, 2) as $i)
                @foreach(['Gold Fitness', 'Titan Gym', 'Aura Wellness', 'PowerHouse', 'Peak Gym', 'Urban Fitness', 'Victory Gym', 'Iron Temple'] as $name)
                    <div class="flex items-center gap-3 shrink-0 neon-soft neon-border px-6 py-3 rounded-xl">
                        <i data-lucide="shield-check" class="text-purple-500 w-5 h-5"></i>
                        <span class="uppercase font-black text-sm tracking-widest text-gray-200">{{ $name }}</span>
                    </div>
                @endforeach
            @endforeach
        </div>
    </section>

    <section class="py-24 max-w-6xl mx-auto px-6 text-center">
        <h2 class="text-4xl font-black pb-12 mb-16 uppercase tracking-widest text-neon-purple">Hogyan működik?</h2>
        <div class="flex flex-col md:flex-row items-center justify-center gap-6 md:gap-4">
            @foreach([
                ['icon' => 'smartphone', 't' => 'Regisztrálj', 'd' => 'Fiók 1 perc alatt.'],
                ['icon' => 'credit-card', 't' => 'Válts bérletet', 'd' => 'Biztonságos fizetés.'],
                ['icon' => 'dumbbell', 't' => 'Edzz bárhol', 'd' => 'Irány a konditerem!']
            ] as $index => $step)
                <div class="flex-1 neon-soft neon-border rounded-2xl p-8 transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_0_40px_rgba(191,64,255,0.4)]">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center rounded-xl bg-purple-900/30 border border-purple-500/30">
                        <i data-lucide="{{ $step['icon'] }}" class="w-8 h-8 text-purple-300"></i>
                    </div>
                    <h3 class="font-bold uppercase text-purple-200 mb-2">{{ $step['t'] }}</h3>
                    <p class="text-gray-400 text-sm">{{ $step['d'] }}</p>
                </div>
                @if($index < 2)
                    <i data-lucide="move-right" class="hidden md:block w-10 h-10 arrow-neon animate-pulse mx-2"></i>
                @endif
            @endforeach
        </div>
    </section>

    <section class="py-24 max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-4xl font-black pb-12 mb-16 uppercase tracking-widest text-white">Bérlet</h2>
        <h3 class="text-3xl md:text-5xl font-black mb-6 text-neon-purple uppercase">Rugalmas árazás, maximális szabadság</h3>
        <p class="text-xl text-gray-300 leading-relaxed max-w-2xl mx-auto mb-10">
            Dinamikus árképzésünk lehetővé teszi, hogy bérletedet már 
            <span class="text-white font-bold border-b-2 border-purple-500 pb-1 tracking-wider">17.990 Ft-os</span> áron elindítsd.
        </p>
        <div class="flex justify-center">
            <a href="{{ route('passes.index') }}" class="inline-flex items-center gap-4 bg-purple-600 hover:bg-purple-500 text-white px-16 py-6 rounded-full font-black uppercase text-base tracking-widest transition-all duration-300 shadow-[0_0_30px_rgba(191,64,255,0.4)] hover:scale-110 active:scale-95">
                <span>Bérletet választok</span>
                <i data-lucide="chevron-right" class="w-6 h-6"></i>
            </a>
        </div>
    </section>

    <section class="py-24 overflow-hidden border-t border-white/5">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-4xl font-black text-center mb-16 pb-4 uppercase tracking-widest text-neon-purple mb-8">Akik már minket választottak</h2>
            <div class="relative">
                <button id="review-prev" 
                        class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-purple-600/80 hover:bg-purple-500 text-white rounded-full p-3 backdrop-blur-md transition-all duration-300 hover:scale-110 disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:scale-100"
                        style="transform: translateY(-50%); margin-left: -20px;">
                    <i data-lucide="chevron-left" class="w-6 h-6"></i>
                </button>
                
                <div id="reviews-slider" class="reviews-container px-6 md:px-12 scroll-smooth">
                    @foreach([
                        ['name' => 'Tóth Anna', 'text' => 'Szuper kényelmes! Hétvégén vidéken is tudok edzeni.', 'img' => '47'],
                        ['name' => 'Kovács Bence', 'text' => 'Nagyon tetszik a rugalmasság, nem kell több bérlet.', 'img' => '33'],
                        ['name' => 'Nagy Gábor', 'text' => 'Digitális, gyors, modern. Pont, amit kerestem.', 'img' => '12'],
                        ['name' => 'Kiss Dóra', 'text' => 'A legjobb dolog, ami a kondibérletekkel történt.', 'img' => '45'],
                        ['name' => 'Szabó Péter', 'text' => 'Végre nem vagyok egy teremhez kötve. Zseniális!', 'img' => '55'],
                        ['name' => 'Varga Lilla', 'text' => 'Csak ajánlani tudom a RoamPass-t. Szabadságot ad.', 'img' => '22'],
                        ['name' => 'Molnár Dávid', 'text' => 'Egyszerű, gyors és megbízható. Mindenkinek ajánlom!', 'img' => '68'],
                        ['name' => 'Balogh Tamás', 'text' => 'A legjobb befektetés volt az évben. 5 csillag!', 'img' => '41']
                    ] as $review)
                        <div class="review-card neon-soft neon-border rounded-2xl p-6 w-80 shrink-0 hover:border-purple-400 transition-all duration-300 hover:-translate-y-2 select-none">
                            <div class="flex items-center gap-4 mb-4 pointer-events-none">
                                <img src="https://i.pravatar.cc/100?img={{ $review['img'] }}" class="w-12 h-12 rounded-full border-2 border-purple-500">
                                <div>
                                    <h4 class="font-bold text-white">{{ $review['name'] }}</h4>
                                    <div class="flex gap-1 mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i data-lucide="star" class="w-3 h-3 fill-purple-500 text-purple-500"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-400 text-sm italic pointer-events-none">"{{ $review['text'] }}"</p>
                        </div>
                    @endforeach
                </div>
                
                {{-- Jobbra mutató nyíl --}}
                <button id="review-next" 
                        class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-purple-600/80 hover:bg-purple-500 text-white rounded-full p-3 backdrop-blur-md transition-all duration-300 hover:scale-110 disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:scale-100"
                        style="transform: translateY(-50%); margin-right: -20px;">
                    <i data-lucide="chevron-right" class="w-6 h-6"></i>
                </button>
            </div>
            
            {{-- Dots navigáció --}}
            <div class="flex justify-center gap-3 mt-8" id="review-dots">
            </div>
        </div>
    </section>

    <section class="py-24 max-w-3xl mx-auto px-6">
        <h2 class="text-4xl font-black text-center pb-12 mb-16 uppercase tracking-widest text-neon-purple">Gyakori Kérdések</h2>
        <div class="space-y-4">
            @foreach([
                ['q' => 'Bármelyik városban használhatom?', 'a' => 'Igen! Minden partnertermünk vár téged országszerte.'],
                ['q' => 'Hogy működik a belépés?', 'a' => 'Mutasd fel a digitális kódodat a pultnál és már mehetsz is edzeni.'],
                ['q' => 'Van hűségidő?', 'a' => 'Nincs! Bármikor lemondhatod, nálunk a szabadság az első.']
            ] as $index => $faq)
                <div class="faq-item neon-soft neon-border rounded-2xl p-6 cursor-pointer group" onclick="toggleFaq({{ $index }})">
                    <div class="flex justify-between items-center">
                        <h4 class="text-lg font-bold text-white group-hover:text-purple-300 transition-colors">{{ $faq['q'] }}</h4>
                        <i data-lucide="chevron-down" class="faq-icon text-purple-500 transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer text-gray-400 text-sm leading-relaxed">
                        {{ $faq['a'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="py-24 max-w-6xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
        <div>
            <h2 class="text-4xl font-black mb-10 text-neon-purple uppercase">Légy RoamPass Partner!</h2>
            <p class="text-lg text-gray-400 mb-8">Növeld a termed forgalmát és érj el egy új, rugalmas sportolói réteget.</p>
            <ul class="space-y-6">
                <li class="flex items-center gap-3 text-gray-300 font-medium hover:text-white transition-colors"><i data-lucide="trending-up" class="text-purple-400"></i> Új bevételi forrás</li>
                <li class="flex items-center gap-3 text-gray-300 font-medium hover:text-white transition-colors"><i data-lucide="zap" class="text-purple-400"></i> Automatizált folyamat</li>
                <li class="flex items-center gap-3 text-gray-300 font-medium hover:text-white transition-colors"><i data-lucide="users" class="text-purple-400"></i> Országos megjelenés</li>
            </ul>
        </div>

        <div id="partner-form" class="bg-gray-850 neon-border rounded-3xl p-8 md:p-12 transition-all duration-500 hover:shadow-[0_0_50px_rgba(191,64,255,0.25)]">
            @if(session('success'))
                <div id="form-success-message" class="text-center py-12 animate-in fade-in zoom-in duration-700">
                    <i data-lucide="check-circle" class="w-16 h-16 text-emerald-500 mx-auto mb-6"></i>
                    <h3 class="text-3xl font-black text-white mb-3">Sikeres jelentkezés!</h3>
                    <p class="text-gray-400">Köszönjük érdeklődését, hamarosan felvesszük Önnel a kapcsolatot!</p>
                </div>
            @else
                <div id="form-alerts">
                    @if(session('error'))
                        <div id="error-alert" class="bg-red-500/10 border border-red-500/50 text-red-400 p-4 rounded-xl mb-6 text-sm flex items-center gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                </div>

                <form action="{{ route('partner.apply') }}" method="POST" class="space-y-8">
                    @csrf
                    <div class="space-y-6">
                        <div class="group">
                            <label class="block text-xs uppercase tracking-[0.2em] text-purple-400 font-bold mb-3">Edzőterem neve</label>
                            <input type="text" name="gym_name" required placeholder="Pl.: RoamGym Budapest"
                                value="{{ old('gym_name') }}"
                                class="w-full !text-white bg-black/60 border border-white/10 p-5 rounded-2xl outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all">
                            @error('gym_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-xs uppercase tracking-[0.2em] text-purple-400 font-bold mb-3">Kapcsolattartó Email</label>
                            <input type="email" name="email" required placeholder="pelda@email.hu"
                                value="{{ old('email') }}"
                                class="w-full !text-white bg-black/60 border border-white/10 p-5 rounded-2xl outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all">
                            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-xs uppercase tracking-[0.2em] text-purple-400 font-bold mb-3">Rövid üzenet</label>
                            <textarea name="message" rows="3" placeholder="Írjon nekünk pár szót..."
                                class="w-full !text-white bg-black/60 border border-white/10 p-5 rounded-2xl outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all">{{ old('message') }}</textarea>
                            @error('message') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full py-6 bg-gradient-to-r from-purple-700 to-purple-500 text-white font-black rounded-2xl uppercase text-sm tracking-[0.2em] shadow-lg hover:shadow-purple-500/50 hover:-translate-y-1 active:scale-95 transition-all">
                        Jelentkezem partnernek
                    </button>
                </form>
            @endif
        </div>
    </section>
</div>

<script>
    // Értesítés eltüntetése
    document.addEventListener('DOMContentLoaded', function() {
        const errorAlert = document.getElementById('error-alert');
        if (errorAlert) {
            setTimeout(() => {
                errorAlert.style.transition = 'all 0.5s ease';
                errorAlert.style.opacity = '0';
                errorAlert.style.transform = 'translateY(-20px)';
                setTimeout(() => errorAlert.remove(), 500);
            }, 4000);
        }
        
        // Slider inicializálása
        initReviewSlider();
    });

    // FAQ lenyitás
    function toggleFaq(index) {
        const items = document.querySelectorAll('.faq-item');
        items.forEach((item, i) => {
            if (i === index) item.classList.toggle('active');
            else item.classList.remove('active');
        });
    }

    // Slider funkciók
    function initReviewSlider() {
        const slider = document.getElementById('reviews-slider');
        const prevBtn = document.getElementById('review-prev');
        const nextBtn = document.getElementById('review-next');
        const dotsContainer = document.getElementById('review-dots');
        
        if (!slider) return;
        
        let currentIndex = 0;
        let cardWidth = 0;
        let visibleCards = 0;
        
        // Frissíti a látható kártyák számát és a kártya szélességét
        function updateDimensions() {
            const cards = document.querySelectorAll('.review-card');
            if (cards.length === 0) return;
            
            const containerWidth = slider.clientWidth;
            const card = cards[0];
            const cardRect = card.getBoundingClientRect();
            const gap = 32; // gap between cards (2rem = 32px)
            
            cardWidth = cardRect.width + gap;
            visibleCards = Math.floor(containerWidth / cardWidth);
            
            return { cards, containerWidth, cardRect, gap };
        }
        
        // Görgetés adott indexre
        function scrollToIndex(index) {
            const cards = document.querySelectorAll('.review-card');
            if (cards.length === 0) return;
            
            const maxIndex = Math.max(0, cards.length - Math.max(1, visibleCards));
            currentIndex = Math.min(Math.max(0, index), maxIndex);
            
            slider.scrollTo({
                left: currentIndex * cardWidth,
                behavior: 'smooth'
            });
            
            updateDots();
            updateButtons();
        }
        
        // Dots frissítése
        function updateDots() {
            if (!dotsContainer) return;
            
            const cards = document.querySelectorAll('.review-card');
            if (cards.length === 0) return;
            
            const totalDots = Math.ceil(cards.length / Math.max(1, visibleCards));
            
            // Dots generálása
            dotsContainer.innerHTML = '';
            for (let i = 0; i < totalDots; i++) {
                const dot = document.createElement('button');
                dot.className = `w-2 h-2 rounded-full transition-all duration-300 ${
                    i === Math.floor(currentIndex / Math.max(1, visibleCards)) 
                        ? 'bg-purple-500 w-6' 
                        : 'bg-gray-600 hover:bg-gray-500'
                }`;
                dot.addEventListener('click', () => {
                    scrollToIndex(i * Math.max(1, visibleCards));
                });
                dotsContainer.appendChild(dot);
            }
        }
        
        // Gombok állapotának frissítése
        function updateButtons() {
            if (!prevBtn || !nextBtn) return;
            
            const cards = document.querySelectorAll('.review-card');
            const maxIndex = Math.max(0, cards.length - Math.max(1, visibleCards));
            
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex >= maxIndex;
        }
        
        // Következő slide
        function nextSlide() {
            const cards = document.querySelectorAll('.review-card');
            const maxIndex = Math.max(0, cards.length - Math.max(1, visibleCards));
            if (currentIndex < maxIndex) {
                scrollToIndex(currentIndex + Math.max(1, Math.floor(visibleCards)));
            }
        }
        
        // Előző slide
        function prevSlide() {
            if (currentIndex > 0) {
                scrollToIndex(currentIndex - Math.max(1, Math.floor(visibleCards)));
            }
        }
        
        // Scroll esemény kezelése
        let scrollTimeout;
        slider.addEventListener('scroll', () => {
            if (scrollTimeout) clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                const newIndex = Math.round(slider.scrollLeft / cardWidth);
                if (newIndex !== currentIndex) {
                    currentIndex = newIndex;
                    updateDots();
                    updateButtons();
                }
            }, 100);
        });
        
        // Ablak átméretezés kezelése
        let resizeTimeout;
        window.addEventListener('resize', () => {
            if (resizeTimeout) clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                updateDimensions();
                scrollToIndex(currentIndex);
                updateDots();
                updateButtons();
            }, 250);
        });
        
        // Gomb események
        if (prevBtn) prevBtn.addEventListener('click', prevSlide);
        if (nextBtn) nextBtn.addEventListener('click', nextSlide);
        
        // 🖱️ EGÉRREL VALÓ HÚZÁS (Drag to scroll) a véleményekhez
        let isDown = false;
        let startX;
        let scrollLeft;
        
        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            slider.style.scrollSnapType = 'none';
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
            slider.style.cursor = 'grabbing';
        });
        
        slider.addEventListener('mouseleave', () => {
            isDown = false;
            slider.style.scrollSnapType = 'x mandatory';
            slider.style.cursor = 'grab';
        });
        
        slider.addEventListener('mouseup', () => {
            isDown = false;
            slider.style.scrollSnapType = 'x mandatory';
            slider.style.cursor = 'grab';
        });
        
        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 1.5;
            slider.scrollLeft = scrollLeft - walk;
        });
        
        // Touch események mobilra
        let touchStartX = 0;
        let touchScrollLeft = 0;
        
        slider.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].pageX - slider.offsetLeft;
            touchScrollLeft = slider.scrollLeft;
            slider.style.scrollSnapType = 'none';
        });
        
        slider.addEventListener('touchmove', (e) => {
            if (touchStartX === 0) return;
            const x = e.touches[0].pageX - slider.offsetLeft;
            const walk = (x - touchStartX) * 1.5;
            slider.scrollLeft = touchScrollLeft - walk;
        });
        
        slider.addEventListener('touchend', () => {
            touchStartX = 0;
            slider.style.scrollSnapType = 'x mandatory';
        });
        
        // Inicializálás
        setTimeout(() => {
            updateDimensions();
            scrollToIndex(0);
            slider.style.cursor = 'grab';
        }, 100);
    }
</script>
@endsection