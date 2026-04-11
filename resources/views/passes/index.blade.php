@extends('layouts.app')

@section('title', 'Saját bérleted - RoamPass')

@section('content')
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    .ticket-card {
        background: rgba(10, 12, 30, 0.95);
        border: 1px solid rgba(191, 64, 255, 0.5);
        border-radius: 2.5rem;
        overflow: hidden;
        position: relative;
        max-width: 340px;
        margin: 0 auto;
        box-shadow: 0 0 50px rgba(191, 64, 255, 0.15), inset 0 1px 1px rgba(255,255,255,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    @media (min-width: 1024px) {
        .ticket-card {
            transform: scale(1.1);
            box-shadow: 0 0 80px rgba(191, 64, 255, 0.25);
        }
    }

    .ticket-cutout {
        @apply absolute top-[62%] -translate-y-1/2 w-10 h-10 bg-[#0a0c1e] rounded-full border border-purple-500/40 z-10;
        box-shadow: inset 0 0 15px rgba(191, 64, 255, 0.15);
    }

    .status-pulse {
        animation: pulse-purple 2s infinite;
    }

    @keyframes pulse-purple {
        0% { opacity: 1; text-shadow: 0 0 5px rgba(191, 64, 255, 0.8); }
        50% { opacity: 0.6; text-shadow: 0 0 15px rgba(191, 64, 255, 1); }
        100% { opacity: 1; text-shadow: 0 0 5px rgba(191, 64, 255, 0.8); }
    }

    .text-neon-purple {
        color: #bf40ff;
        text-shadow: 0 0 10px rgba(191, 64, 255, 0.5);
    }
    .alert-item {
        transition: opacity 0.4s ease, transform 0.4s ease;
    }
</style>

<div class="fixed inset-0 z-[-1] bg-cover bg-center"
     style="background-image: url('{{ asset('images/gym-bg2.png') }}'); filter: brightness(0.25);">
</div>

<section class="min-h-[calc(100vh-120px)] flex flex-col items-center justify-center text-white p-6">
    
    <div class="w-full max-w-lg animate-fade-up">
        <div class="max-w-4xl mx-auto">
            {{-- AJAX üzenet helye --}}
            <div id="ajax-message" class="hidden mb-6 p-4 rounded-xl border flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span class="text-sm font-bold" id="ajax-text"></span>
            </div>

            {{-- Sikeres művelet --}}
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl border border-green-500/50 bg-green-500/10 text-green-400 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Hibaüzenet --}}
            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl border border-red-500/50 bg-red-500/10 text-red-400 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
                    <i data-lucide="alert-triangle" class="w-5 h-5 flex-shrink-0"></i>
                    <div class="text-sm font-bold">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl border border-red-500/50 bg-red-500/10 text-red-400 flex items-start gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
                    <i data-lucide="alert-circle" class="w-5 h-5 mt-0.5 flex-shrink-0"></i>
                    <div class="text-sm font-bold w-full">
                        <p class="mb-2">Kérlek javítsd az alábbi hibákat:</p>
                        <ul class="list-disc list-inside space-y-1 text-red-300">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        @php
            $pass = $passes->first(function($p) {
                return $p->remaining_uses > 0 &&
                    \Carbon\Carbon::parse($p->expires_at)->isFuture();
            });
            $u = auth()->user();
        @endphp

        @if($pass)
            <div class="ticket-card">
                {{-- Oldalsó bevágások --}}
                <div class="ticket-cutout -left-5"></div>
                <div class="ticket-cutout -right-5"></div>

                <div class="p-8 pb-6 bg-purple-900/10">
                    <div class="flex flex-col items-center w-full">
                        <div class="flex items-center gap-2 mb-2 status-pulse text-center">
                            <span class="w-2 h-2 bg-purple-500 rounded-full shadow-[0_0_8px_#bf40ff]"></span>
                            <span class="text-purple-400 font-black uppercase tracking-[0.2em] text-[10px]">Aktív Bérlet</span>
                        </div>
                        <h2 class="text-2xl font-black tracking-tighter text-center uppercase leading-tight">
                            {{ $u->last_name }}<br>{{ $u->first_name }}
                        </h2>
                    </div>
                </div>

                <div class="border-t border-dashed border-purple-500/30 mx-8"></div>

                <div class="p-8 py-10 text-center relative">

                    <div id="qr-container" class="bg-white p-4 rounded-3xl shadow-[0_0_40px_rgba(191,64,255,0.4)] inline-block mb-4 relative overflow-hidden select-none cursor-pointer group">
                        
                        <img id="dynamic-qr" src="{{ route('passes.dynamic-qr', $pass->id) }}" alt="QR" class="w-40 h-40 transition-all duration-300 blur-md pointer-events-none">
                        
                        <div id="qr-overlay" class="absolute inset-0 flex flex-col items-center justify-center bg-black/50 backdrop-blur-md transition-all duration-300 z-10 opacity-100">
                            <i data-lucide="scan-line" class="w-10 h-10 text-purple-400 mb-2 animate-bounce drop-shadow-[0_0_8px_rgba(191,64,255,0.8)]"></i>
                            <span class="text-white text-[10px] font-black uppercase tracking-widest text-center px-4 drop-shadow-[0_3px_6px_rgba(0,0,0,1)]">
                                Koppints a<br>megjelenítéshez
                            </span>
                        </div>
                    </div>

                    <div class="max-w-[200px] mx-auto mb-8">
                        <div class="flex justify-between text-[9px] font-bold uppercase tracking-widest mb-1">
                            <span class="text-gray-400">Beléptető kód</span>
                            <span id="timer-text" style="color: #10b981; font-weight: 900;">30mp</span>
                        </div>
                        
                        <div class="w-full h-2 bg-gray-800 rounded-full overflow-hidden">
                            <div id="timer-bar" style="height: 100%; width: 100%; background-color: #10b981; transition: width 1s linear, background-color 0.3s ease;"></div>
                        </div>
                    </div>

                    {{-- Alkalom számláló --}}
                    <div class="flex flex-col items-center">
                        <div class="flex items-baseline gap-2">
                            <span class="text-7xl font-black text-white leading-none tracking-tighter">{{ $pass->remaining_uses }}</span>
                            <span class="text-purple-500/50 text-2xl font-bold">/ 12</span>
                        </div>
                        <p class="text-[10px] text-gray-400 uppercase font-black tracking-[0.3em] mt-4">Alkalom</p>
                    </div>
                </div>

                {{-- LÁB: Dátumok --}}
                <div class="bg-black/60 p-6 px-8 grid grid-cols-2 gap-4 border-t border-purple-500/20">
                    <div class="text-left">
                        <p class="text-[9px] text-gray-500 uppercase font-bold tracking-widest mb-1">Vásárolva</p>
                        <p class="text-xs font-black text-gray-300">{{ \Carbon\Carbon::parse($pass->purchase_date)->format('Y.m.d') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] text-gray-500 uppercase font-bold tracking-widest mb-1">Lejárat</p>
                        <p class="text-xs font-black text-neon-purple">
                            {{ $pass->expires_at ? \Carbon\Carbon::parse($pass->expires_at)->format('Y.m.d') : 'Végtelen' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-16 text-center lg:mt-24">
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-purple-400 text-xs font-black uppercase tracking-widest transition-all group">
                    <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i> 
                    Vissza a profilhoz
                </a>
            </div>
        @else
            <div class="ticket-card p-12 text-center border-dashed border-purple-500/30">
                <div class="w-20 h-20 bg-purple-500/10 rounded-full flex items-center justify-center mx-auto mb-6 border border-purple-500/20">
                    <i data-lucide="ticket-x" class="w-10 h-10 text-purple-500/50"></i>
                </div>
                <h2 class="text-xl font-black uppercase mb-2">Nincs aktív bérleted</h2>
                <p class="text-gray-500 text-sm mb-8">Válts bérletet és kezdj el edzeni bármhol!</p>
                <a href="{{ route('passes.create') }}" class="block bg-purple-600 hover:bg-purple-500 text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest transition-all shadow-[0_0_20px_rgba(191,64,255,0.4)]">
                    Bérlet vásárlása
                </a>
            </div>
        @endif
    </div>
</section>

<script>
    lucide.createIcons();

    document.addEventListener("DOMContentLoaded", () => {
        const timerText = document.getElementById('timer-text');
        const timerBar = document.getElementById('timer-bar');
        const qrImage = document.getElementById('dynamic-qr');
        
        const qrContainer = document.getElementById('qr-container');
        const qrOverlay = document.getElementById('qr-overlay');
        let revealTimeout; 

        if (!timerText || !timerBar || !qrImage) return;

        if (qrContainer && qrOverlay) {
            // Ha rákattint / rábök a dobozra
            qrContainer.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Ha már éles, ne csináljunk semmit
                if (qrOverlay.classList.contains('opacity-0')) return;

                // 1. Kód élesítése (Blur levétele, overlay eltüntetése)
                qrImage.classList.remove('blur-md');
                qrOverlay.classList.remove('opacity-100');
                qrOverlay.classList.add('opacity-0', 'pointer-events-none');

                // 2. Automatikus visszazárás 10 másodperc múlva
                clearTimeout(revealTimeout);
                revealTimeout = setTimeout(() => {
                    qrImage.classList.add('blur-md');
                    qrOverlay.classList.remove('opacity-0', 'pointer-events-none');
                    qrOverlay.classList.add('opacity-100');
                }, 10000);
            });

            // VÉDELEM: Ha átlép egy másik appba (pl. Messenger), vagy lehúzza az értesítéseket
            window.addEventListener('blur', () => {
                clearTimeout(revealTimeout);
                qrImage.classList.add('blur-md');
                qrOverlay.classList.remove('opacity-0', 'pointer-events-none');
                qrOverlay.classList.add('opacity-100');
            });
        }


        // Eredeti 30 másodperces frissítő logika
        let timeLeft = 30;
        
        function updateTimerDisplay() {
            timerText.innerText = timeLeft + 'mp';
            
            const percentage = (timeLeft / 30) * 100;
            timerBar.style.setProperty('width', percentage + '%', 'important');
            
            // Színek beállítása
            if (timeLeft <= 5) {
                timerBar.style.setProperty('background-color', '#ef4444', 'important');
                timerText.style.setProperty('color', '#ef4444', 'important');
            } else {
                timerBar.style.setProperty('background-color', '#10b981', 'important');
                timerText.style.setProperty('color', '#10b981', 'important');
            }
        }
        
        updateTimerDisplay();

        setInterval(() => {
            timeLeft--;
            
            if (timeLeft <= 0) {
                timeLeft = 30;
                
                let currentSrc = qrImage.src.split('?')[0];
                qrImage.src = currentSrc + '?t=' + new Date().getTime();
            }
            
            updateTimerDisplay();
        }, 1000);
    });
</script>
@endsection