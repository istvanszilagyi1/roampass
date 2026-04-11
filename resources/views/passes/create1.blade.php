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

    /* Custom Input mezők a fizetőkapuhoz */
    .pay-input {
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(191, 64, 255, 0.3);
        color: white;
        padding: 0.6rem 1rem 0.6rem 2.2rem;
        border-radius: 0.75rem;
        width: 100%;
        outline: none;
        transition: all 0.3s ease;
        font-family: monospace;
        font-size: 13px;
        pointer-events: auto !important;
        position: relative;
        z-index: 10000;
    }
    .pay-input.no-icon {
        padding-left: 1rem;
    }
    .pay-input:focus {
        border-color: #bf40ff;
        box-shadow: 0 0 10px rgba(191, 64, 255, 0.3);
        background: rgba(0, 0, 0, 0.6);
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
    }
    
    #modal-content * {
        pointer-events: auto !important;
    }
    
    /* Modál háttér - ne blokkolja a kattintásokat a modálon belül */
    #payment-modal {
        pointer-events: none;
    }
    
    #payment-modal.flex {
        pointer-events: auto;
    }
    
    /* A modálon belüli konténer kapjon minden eseményt */
    #payment-modal .bg-\[\#0f1225\] {
        pointer-events: auto !important;
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
        font-size: 10px;
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

    {{-- VÁSÁRLÓI KÁRTYA (Kompakt méret) --}}
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

        {{-- JAVÍTOTT, GARANTÁLTAN KATTINTHATÓ GOMB --}}
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

{{-- FIZETÉSI MODÁL - Alapból HIDDEN osztállyal! --}}
<div id="payment-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/80 backdrop-blur-sm px-4 opacity-0 transition-opacity duration-300">
    
    <div class="bg-[#0f1225] border border-purple-500/40 rounded-3xl w-full max-w-sm shadow-[0_0_50px_rgba(191,64,255,0.2)] overflow-hidden transform scale-95 transition-transform duration-300" id="modal-content">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-purple-900/50 to-indigo-900/50 p-4 flex justify-between items-center border-b border-purple-500/20">
            <div class="flex items-center gap-3">
                <div class="bg-black/50 p-1.5 rounded-lg text-neon-purple border border-purple-500/30">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-white uppercase tracking-widest text-xs">RoamPass Pay</h3>
                    <p class="text-[9px] text-gray-400 font-mono">Biztonságos fizetés</p>
                </div>
            </div>
            <button type="button" onclick="closePaymentModal(event)" class="text-gray-400 hover:text-white transition-colors p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-5 space-y-5 relative">
            
            {{-- Összegző sáv --}}
            <div class="flex justify-between items-center bg-black/40 p-3 rounded-xl border border-white/5">
                <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Fizetendő:</span>
                <span class="text-lg font-black text-white">17 999 Ft</span>
            </div>

            {{-- Bankkártya Űrlap --}}
            <form id="simulation-form" method="POST" action="{{ route('passes.store') }}" onsubmit="handlePaymentSubmit(event)" class="space-y-3 m-0">
                @csrf
                
                <div class="space-y-1">
                    <label class="text-[9px] uppercase font-bold text-gray-400 tracking-widest ml-1">Kártyaszám</label>
                    <div class="relative">
                        <i data-lucide="credit-card" class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                        <input type="text" id="card-number" placeholder="0000 0000 0000 0000" class="pay-input" maxlength="19" oninput="formatCardNumber(this)" required>
                    </div>
                    <div class="error-message" id="card-number-error">A kártyaszám megadása kötelező</div>
                </div>

                <div class="space-y-1">
                    <label class="text-[9px] uppercase font-bold text-gray-400 tracking-widest ml-1">Név a kártyán</label>
                    <div class="relative">
                        <i data-lucide="user" class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                        <input type="text" id="card-name" placeholder="Gipsz Jakab" class="pay-input" required>
                    </div>
                    <div class="error-message" id="card-name-error">A kártyán szereplő név megadása kötelező</div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[9px] uppercase font-bold text-gray-400 tracking-widest ml-1">Lejárat (HH/ÉÉ)</label>
                        <input type="text" id="expiry" placeholder="12/25" class="pay-input no-icon text-center" maxlength="5" oninput="formatExpiry(this)" required>
                        <div class="error-message" id="expiry-error">A lejárati dátum megadása kötelező</div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[9px] uppercase font-bold text-gray-400 tracking-widest ml-1">CVC / CVV</label>
                        <div class="relative">
                            <input type="password" id="cvc" placeholder="123" class="pay-input no-icon text-center" maxlength="3" required>
                        </div>
                        <div class="error-message" id="cvc-error">A CVC kód megadása kötelező</div>
                    </div>
                </div>

                {{-- Általános hibaüzenet --}}
                <div class="error-message text-center" id="general-error">Kérlek tölts ki minden mezőt!</div>

                {{-- Betöltő overlay --}}
                <div id="processing-overlay" class="absolute inset-0 bg-[#0f1225]/95 backdrop-blur-md z-20 flex flex-col items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 rounded-b-3xl">
                    <i data-lucide="loader-2" class="w-8 h-8 text-neon-purple animate-spin mb-3"></i>
                    <p class="text-white font-bold tracking-widest uppercase text-xs animate-pulse">Feldolgozás...</p>
                    <p class="text-gray-500 text-[9px] mt-1 font-mono">Ne zárd be az ablakot!</p>
                </div>

                <button type="submit" id="pay-submit-btn"
                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-3.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all mt-2 flex justify-center items-center gap-2 shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                    <i data-lucide="lock" class="w-3 h-3"></i> Fizetés jóváhagyása
                </button>
            </form>

        </div>
        
        {{-- Footer logo --}}
        <div class="bg-black/30 p-2.5 text-center border-t border-white/5 flex justify-center gap-5 opacity-40">
            <i data-lucide="credit-card" class="w-4 h-4"></i>
            <i data-lucide="smartphone-nfc" class="w-4 h-4"></i>
            <i data-lucide="shield" class="w-4 h-4"></i>
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
    window.openPaymentModal = function(event) {
        if (event) {
            event.stopPropagation();
        }
        
        const modal = document.getElementById('payment-modal');
        const modalContent = document.getElementById('modal-content');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    };

    // Modál bezárása
    window.closePaymentModal = function(event) {
        if (event) {
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
        }, 300);
    };

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

    // Kívülre kattintás kezelése
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('payment-modal');
        const modalContent = document.getElementById('modal-content');
        
        if (modal && !modal.classList.contains('hidden') && modal.classList.contains('flex')) {
            if (!modalContent.contains(e.target)) {
                closePaymentModal();
            }
        }
    });
    
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