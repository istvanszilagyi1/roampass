@extends('layouts.app')

@section('title', 'RoamPass - Bérlet vásárlása')

@section('content')
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    /* 🌌 Neon stílusok */
    .neon-soft {
        background: rgba(10, 12, 30, 0.95);
        border: 1px solid rgba(191, 64, 255, 0.5);
        position: relative;
        backdrop-filter: blur(16px);
    }

    .neon-border {
        border: 1px solid rgba(191, 64, 255, 0.8);
        box-shadow: 0 0 25px rgba(191, 64, 255, 0.25);
    }

    .text-neon-purple {
        color: #bf40ff;
        text-shadow: 0 0 10px rgba(191, 64, 255, 0.5);
    }

    @keyframes floatUp {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-8px) rotate(2deg); }
    }
    .animate-float-subtle { animation: floatUp 6s ease-in-out infinite; }

    /* Custom Input mezők a fizetőkapuhoz - SZOLID HÁTTÉRREL */
    .pay-input {
        background: #0a0c1a;
        border: 1px solid rgba(191, 64, 255, 0.4);
        color: #ffffff !important;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border-radius: 0.75rem;
        width: 100%;
        outline: none;
        transition: all 0.3s ease;
        font-family: monospace;
        font-size: 15px;
        pointer-events: auto !important;
        position: relative;
        z-index: 10000;
    }

    .pay-input::placeholder {
        color: rgba(255, 255, 255, 0.5) !important;
    }

    .pay-input.no-icon {
        padding-left: 1rem;
    }
    .pay-input:focus {
        border-color: #bf40ff;
        box-shadow: 0 0 15px rgba(191, 64, 255, 0.4);
        background: #1a1e3a;; /* Fókuszkor egy picit világosabb, de szolid */
    }
    
    .pay-input.error {
        border-color: #ef4444;
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.3);
    }
    
    /* Modál tartalom biztosítása */
    #modal-content {
        pointer-events: auto !important;
        position: relative;
        z-index: 10001;
        margin: auto;
        max-height: 90vh;
        overflow-y: auto;
        width: 100%;
    }
    #modal-content label {
        color: #cbd5e1 !important;
        font-weight: bold;
    }
    
    #modal-content * {
        pointer-events: auto !important;
    }
    #modal-content::-webkit-scrollbar {
        width: 6px;
    }
    #modal-content::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }

    #modal-content::-webkit-scrollbar-thumb {
        background: #bf40ff;
        border-radius: 10px;
    }
    
    /* Modál háttér - ne blokkolja a kattintásokat a modálon belül */
    #payment-modal {
        pointer-events: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 9999;
        overflow-y: auto;
        overflow-x: hidden;
    }
    body.modal-open {
        overflow: hidden !important;
        position: fixed;
        width: 100%;
        height: 100%;
    }
    
    #payment-modal.flex {
        pointer-events: auto;
        display: flex !important;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    
    /* A modálon belüli konténer kapjon minden eseményt */
    #payment-modal .bg-\[\#0f1225\] {
        pointer-events: auto !important;
        position: relative;
        z-index: 10000;
        margin: auto;
    }
    
    /* Gombok és inputok biztosítása */
    button, input, .pay-input, #pay-submit-btn, [type="submit"] {
        pointer-events: auto !important;
        cursor: pointer;
    }
    
    input.pay-input {
        cursor: text;
    }
    
    /* Bezáró gomb */
    button[onclick="closePaymentModal()"] {
        pointer-events: auto !important;
        cursor: pointer;
        z-index: 10002;
    }
    
    /* Fizetés gomb speciális stílusok */
    #pay-submit-btn {
        pointer-events: auto !important;
        cursor: pointer !important;
        position: relative;
        z-index: 10003;
    }
    
    #pay-submit-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed !important;
    }
    
    /* Hibaüzenet */
    .error-message {
        color: #ef4444;
        font-size: 11px;
        margin-top: 4px;
        margin-left: 4px;
        display: none;
        font-family: monospace;
    }
    
    .error-message.show {
        display: block;
    }
</style>

{{-- Háttér fixálása --}}
<div class="fixed inset-0 z-[-1] bg-cover bg-center"
     style="background-image: url('{{ asset('images/gym-bg2.png') }}'); filter: brightness(0.25);">
</div>

{{-- FŐ TARTALOM (Eredeti kártya) --}}
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center py-12 px-4 relative z-10">

    {{-- VÁSÁRLÓI KÁRTYA --}}
    <div class="neon-soft neon-border rounded-[2rem] p-8 md:p-10 w-full max-w-md text-center relative z-20">

        {{-- Súlyzó ikonok --}}
        <div class="absolute -top-4 -left-4 text-purple-600/20 animate-float-subtle pointer-events-none">
            <i data-lucide="dumbbell" class="w-20 h-20"></i>
        </div>
        <div class="absolute -bottom-4 -right-4 text-purple-400/20 animate-float-subtle pointer-events-none" style="animation-delay: 1.5s;">
            <i data-lucide="dumbbell" class="w-20 h-20"></i>
        </div>

        <h1 class="text-3xl font-black mb-2 text-neon-purple uppercase tracking-tighter relative z-30">RoamPass</h1>
        <h2 class="text-base font-bold text-white mb-6 border-b border-purple-500/20 pb-4 uppercase tracking-widest relative z-30">
             12 Alkalmas Bérlet
        </h2>

        <div class="mb-8 text-left space-y-4 relative z-30">
            <div class="flex items-start gap-3 text-gray-300 text-sm">
                <i data-lucide="check-circle" class="w-5 h-5 text-purple-400 shrink-0"></i>
                <p class="leading-tight"><strong class="text-white">12 alkalom</strong> országos lefedettséggel.</p>
            </div>
            <div class="flex items-start gap-3 text-gray-300 text-sm">
                <i data-lucide="check-circle" class="w-5 h-5 text-purple-400 shrink-0"></i>
                <p class="leading-tight"><strong class="text-white">Bármelyik partnerünknél</strong> érvényes.</p>
            </div>
            <div class="flex items-start gap-3 text-gray-300 text-sm">
                <i data-lucide="check-circle" class="w-5 h-5 text-purple-400 shrink-0"></i>
                <p class="leading-tight">Érvényesség: <strong class="text-white">30 nap</strong>.</p>
            </div>

            <div class="pt-6 border-t border-purple-500/20 mt-6 text-center">
                <p class="text-[10px] uppercase tracking-[0.2em] text-purple-400 font-black mb-1">Fizetendő összeg</p>
                <p class="text-5xl font-black text-white tracking-tighter">
                    17.999 <span class="text-xl text-purple-500 font-bold">Ft</span>
                </p>
            </div>
        </div>

        {{-- GOMB --}}
        <div class="mt-8 relative" style="z-index: 100;">
            <button type="button" onclick="openPaymentModal(event)"
                    class="w-full bg-purple-600 hover:bg-purple-500 text-white font-black py-4 rounded-xl shadow-[0_0_20px_rgba(191,64,255,0.4)] transition-all uppercase tracking-widest flex items-center justify-center gap-2 cursor-pointer">
                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                Tovább a fizetéshez
            </button>
        </div>

        <p class="text-gray-500 mt-6 text-[10px] uppercase tracking-widest font-bold relative z-30">
            <i data-lucide="lock" class="w-3 h-3 inline-block mr-1 mb-0.5"></i> Biztonságos fizetés
        </p>
    </div>
</div>

{{-- FIZETÉSI MODÁL --}}
{{-- FIZETÉSI MODÁL --}}
<div id="payment-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/95 backdrop-blur-md opacity-0 transition-opacity duration-300" style="pointer-events: none; margin-top: 30px;">
    
    {{-- A modál szélessége meg lett növelve max-w-sm -> max-w-md --}}
    <div class="bg-[#0f1225] border-2 border-purple-500/50 rounded-3xl w-full max-w-md shadow-[0_0_50px_rgba(191,64,255,0.4)] overflow-hidden transform scale-95 transition-transform duration-300 relative" id="modal-content" style="margin: auto; pointer-events: auto;">
        
        {{-- Header szolidabb és sötétebb hátteret kapott --}}
        <div class="bg-gradient-to-r from-purple-900 to-indigo-900 p-5 flex justify-between items-center border-b border-purple-500/40">
            <div class="flex items-center gap-3">
                <div class="bg-black/40 p-2 rounded-lg text-neon-purple border border-purple-500/30">
                    <i data-lucide="shield-check" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h3 class="font-black text-white uppercase tracking-widest text-sm">RoamPass Pay</h3>
                    <p class="text-[10px] text-purple-200 font-mono">Biztonságos fizetés</p>
                </div>
            </div>
            <button type="button" onclick="closePaymentModal(event)" class="text-gray-300 hover:text-white transition-colors p-2 rounded-full hover:bg-white/10 cursor-pointer">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        {{-- Body megnövelt margókkal --}}
        <div class="p-6 md:p-8 space-y-6 relative" style="background: #0f1225;">
            
            {{-- Összegző sáv szolid fekete hátérrel --}}
            <div class="flex justify-between items-center bg-black/80 p-4 rounded-xl border border-white/10 shadow-inner">
                <span class="text-xs text-gray-300 uppercase font-bold tracking-wider">Fizetendő:</span>
                <span class="text-xl font-black text-white">17 999 Ft</span>
            </div>

            {{-- Bankkártya Űrlap --}}
            <form id="simulation-form" method="POST" action="{{ route('passes.store') }}" onsubmit="handlePaymentSubmit(event)" class="space-y-4 m-0">
                @csrf
                
                <div class="space-y-1.5">
                    <label class="text-[10px] uppercase font-bold text-gray-200 tracking-widest ml-1">Kártyaszám</label>
                    <div class="relative">
                        <i data-lucide="credit-card" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                        <input type="text" id="card-number" placeholder="0000 0000 0000 0000" class="pay-input" maxlength="19" oninput="formatCardNumber(this)" required>
                    </div>
                    <div class="error-message" id="card-number-error">A kártyaszám megadása kötelező</div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] uppercase font-bold text-gray-200 tracking-widest ml-1">Név a kártyán</label>
                    <div class="relative">
                        <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                        <input type="text" id="card-name" placeholder="Gipsz Jakab" class="pay-input" required>
                    </div>
                    <div class="error-message" id="card-name-error">A kártyán szereplő név megadása kötelező</div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] uppercase font-bold text-gray-200 tracking-widest ml-1">Lejárat (HH/ÉÉ)</label>
                        <input type="text" id="expiry" placeholder="12/25" class="pay-input no-icon text-center" maxlength="5" oninput="formatExpiry(this)" required>
                        <div class="error-message" id="expiry-error">Kötelező mező</div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] uppercase font-bold text-gray-200 tracking-widest ml-1">CVC / CVV</label>
                        <div class="relative">
                            <input type="password" id="cvc" placeholder="123" class="pay-input no-icon text-center" maxlength="3" required>
                        </div>
                        <div class="error-message" id="cvc-error">Kötelező mező</div>
                    </div>
                </div>

                {{-- Általános hibaüzenet --}}
                <div class="error-message text-center pt-2 text-red-400" id="general-error">Kérlek tölts ki minden mezőt megfelelően!</div>

                {{-- Betöltő overlay - Teljesen szolid háttérrel kitakarja az űrlapot --}}
                <div id="processing-overlay" class="absolute inset-0 bg-[#0f1225] z-20 flex flex-col items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 rounded-b-3xl">
                    <i data-lucide="loader-2" class="w-10 h-10 text-neon-purple animate-spin mb-4"></i>
                    <p class="text-white font-bold tracking-widest uppercase text-sm animate-pulse">Feldolgozás...</p>
                    <p class="text-gray-400 text-[10px] mt-2 font-mono">Kérjük, ne zárd be az ablakot!</p>
                </div>

                <button type="submit" id="pay-submit-btn"
                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-4 rounded-xl font-black text-sm uppercase tracking-widest transition-all mt-4 flex justify-center items-center gap-2 shadow-[0_0_15px_rgba(16,185,129,0.3)] cursor-pointer">
                    <i data-lucide="lock" class="w-4 h-4"></i> Fizetés jóváhagyása
                </button>
            </form>

        </div>
        
        {{-- Footer logo --}}
        <div class="bg-black p-3 text-center border-t border-white/5 flex justify-center gap-6 opacity-60">
            <i data-lucide="credit-card" class="w-5 h-5 text-gray-400"></i>
            <i data-lucide="smartphone-nfc" class="w-5 h-5 text-gray-400"></i>
            <i data-lucide="shield" class="w-5 h-5 text-gray-400"></i>
        </div>
    </div>
</div>

<script>
    // Ikonok betöltése
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
        // Fizetés gomb direkt event listener hozzáadása
        const payButton = document.getElementById('pay-submit-btn');
        if (payButton) {
            payButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                handlePaymentSubmit(e);
            });
        }
        
        // Űrlap event listener
        const form = document.getElementById('simulation-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                handlePaymentSubmit(e);
            });
        }
        
        // Input mezőkről eltüntetjük a hibaüzenetet gépeléskor
        document.querySelectorAll('.pay-input').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('error');
                const errorId = this.id + '-error';
                const errorElement = document.getElementById(errorId);
                if (errorElement) {
                    errorElement.classList.remove('show');
                }
                // Általános hiba elrejtése
                document.getElementById('general-error').classList.remove('show');
            });
        });
    });

    // Modál megnyitása
    // Modál megnyitása
    window.openPaymentModal = function(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        const modal = document.getElementById('payment-modal');
        const modalContent = document.getElementById('modal-content');
        
        // Test görgetés letiltása
        document.body.classList.add('modal-open');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Biztosítsuk, hogy a modál a viewport tetején kezdjen
        window.scrollTo(0, 0);
        
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    };

    // Modál bezárása
    window.closePaymentModal = function(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        const modal = document.getElementById('payment-modal');
        const modalContent = document.getElementById('modal-content');
        const form = document.getElementById('simulation-form');
        
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            
            // Test görgetés visszaengedélyezése
            document.body.classList.remove('modal-open');
            
            if(form) form.reset();
            
            // Fizetés gomb engedélyezése újranyitáskor
            const payButton = document.getElementById('pay-submit-btn');
            if (payButton) {
                payButton.disabled = false;
            }
            
            // Overlay elrejtése
            const overlay = document.getElementById('processing-overlay');
            if (overlay) {
                overlay.classList.add('opacity-0', 'pointer-events-none');
                overlay.classList.remove('opacity-100', 'pointer-events-auto');
            }
            
            // Hibaüzenetek elrejtése
            document.querySelectorAll('.error-message').forEach(el => {
                el.classList.remove('show');
            });
            document.querySelectorAll('.pay-input').forEach(input => {
                input.classList.remove('error');
            });
            
            // Eredeti scroll pozíció visszaállítása
            const scrollY = document.body.style.top;
            document.body.style.position = '';
            document.body.style.top = '';
            window.scrollTo(0, parseInt(scrollY || '0') * -1);
        }, 300);
    };

    // Kívülre kattintás kezelése javítva
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('payment-modal');
        const modalContent = document.getElementById('modal-content');
        
        if (modal && !modal.classList.contains('hidden') && modal.classList.contains('flex')) {
            // Ellenőrizzük, hogy a kattintás a modálon kívülre esett-e
            if (modalContent && !modalContent.contains(e.target)) {
                // Ne zárjuk be, ha a modál tartalmára kattintottak
                if (e.target === modal || modal.contains(e.target) && !modalContent.contains(e.target)) {
                    closePaymentModal();
                }
            }
        }
    });

    // ESC billentyűvel zárás
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('payment-modal');
            if (modal && !modal.classList.contains('hidden')) {
                closePaymentModal();
            }
        }
    });

    // Kártyaszám formázása
    window.formatCardNumber = function(input) {
        let value = input.value.replace(/\D/g, '');
        let formattedValue = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) formattedValue += ' ';
            formattedValue += value[i];
        }
        input.value = formattedValue;
    };

    // Lejárati dátum formázása
    window.formatExpiry = function(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length > 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        input.value = value;
    };

    // Validáció
    window.validateForm = function() {
        const cardNumber = document.getElementById('card-number')?.value.replace(/\s/g, '');
        const cardName = document.getElementById('card-name')?.value.trim();
        const expiry = document.getElementById('expiry')?.value.trim();
        const cvc = document.getElementById('cvc')?.value.trim();
        
        let isValid = true;
        
        // Összes hibaüzenet elrejtése
        document.querySelectorAll('.error-message').forEach(el => {
            el.classList.remove('show');
        });
        document.querySelectorAll('.pay-input').forEach(input => {
            input.classList.remove('error');
        });
        
        // Kártyaszám ellenőrzés
        if (!cardNumber || cardNumber.length < 16) {
            const errorEl = document.getElementById('card-number-error');
            const inputEl = document.getElementById('card-number');
            if (errorEl) errorEl.classList.add('show');
            if (inputEl) inputEl.classList.add('error');
            isValid = false;
        }
        
        // Név ellenőrzés
        if (!cardName || cardName.length < 3) {
            const errorEl = document.getElementById('card-name-error');
            const inputEl = document.getElementById('card-name');
            if (errorEl) errorEl.classList.add('show');
            if (inputEl) inputEl.classList.add('error');
            isValid = false;
        }
        
        // Lejárati dátum ellenőrzés
        if (!expiry || expiry.length < 5) {
            const errorEl = document.getElementById('expiry-error');
            const inputEl = document.getElementById('expiry');
            if (errorEl) errorEl.classList.add('show');
            if (inputEl) inputEl.classList.add('error');
            isValid = false;
        }
        
        // CVC ellenőrzés
        if (!cvc || cvc.length < 3) {
            const errorEl = document.getElementById('cvc-error');
            const inputEl = document.getElementById('cvc');
            if (errorEl) errorEl.classList.add('show');
            if (inputEl) inputEl.classList.add('error');
            isValid = false;
        }
        
        // Általános hibaüzenet
        if (!isValid) {
            document.getElementById('general-error').classList.add('show');
        }
        
        return isValid;
    };

    // Űrlap beküldése
    window.handlePaymentSubmit = function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Payment submitted - validating...');
        
        // Validáció
        if (!validateForm()) {
            console.log('Validation failed');
            return;
        }
        
        console.log('Validation passed, processing payment...');
        
        const form = document.getElementById('simulation-form');
        const overlay = document.getElementById('processing-overlay');
        const submitBtn = document.getElementById('pay-submit-btn');
        
        if (!form || !overlay || !submitBtn) {
            console.error('Required elements not found');
            return;
        }
        
        // Betöltőképernyő aktiválása
        overlay.classList.remove('opacity-0', 'pointer-events-none');
        overlay.classList.add('opacity-100', 'pointer-events-auto');
        
        submitBtn.disabled = true;
        
        // Űrlap adatok logolása
        const cardNumber = document.getElementById('card-number')?.value;
        const cardName = document.getElementById('card-name')?.value;
        const expiry = document.getElementById('expiry')?.value;
        const cvc = document.getElementById('cvc')?.value;
        
        console.log('Form data:', { cardNumber, cardName, expiry, cvc });
        
        // 2,5 másodperc várakozás, majd mehet a backendnek a request
        setTimeout(() => {
            console.log('Submitting form to backend');
            form.submit();
        }, 2500);
    };
    
    // Input mezők fókuszálásának biztosítása
    document.querySelectorAll('.pay-input').forEach(input => {
        input.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        input.addEventListener('focus', function(e) {
            e.stopPropagation();
        });
    });
</script>
@endsection