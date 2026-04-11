@extends('layouts.app')

@section('title', 'Scanner Dashboard - ' . $gym->name)

@section('content')
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    .glass-panel {
        background: rgba(10, 12, 30, 0.85);
        border: 1px solid rgba(191, 64, 255, 0.3);
        backdrop-filter: blur(12px);
        border-radius: 1.5rem;
        box-shadow: 0 0 30px rgba(191, 64, 255, 0.1);
    }

    .scanner-input {
        background-color: rgba(0, 0, 0, 0.7) !important;
        color: #ffffff !important;
        border: 2px solid rgba(191, 64, 255, 0.5) !important;
        font-family: monospace;
        letter-spacing: 2px;
        font-size: 1.1rem;
        text-align: center;
        @apply w-full p-4 rounded-xl outline-none transition-all duration-200;
    }
    @media (min-width: 640px) {
        .scanner-input { font-size: 1.2rem; }
    }
    .scanner-input:focus {
        border-color: #bf40ff !important;
        box-shadow: 0 0 20px rgba(191, 64, 255, 0.4);
    }

    .mode-btn.active {
        background-color: #7e22ce;
        color: white;
        border-color: #bf40ff;
        box-shadow: 0 0 15px rgba(191, 64, 255, 0.5);
    }
    .mode-btn {
        @apply bg-gray-900 text-gray-400 border border-gray-700 py-3 px-3 sm:px-6 rounded-xl font-bold uppercase tracking-wider sm:tracking-widest transition-all hover:text-white flex items-center justify-center gap-2 text-xs sm:text-sm;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .scan-card { animation: slideIn 0.4s ease-out forwards; }
</style>

<div class="fixed inset-0 z-[-1] bg-cover bg-center"
     style="background-image: url('{{ asset('images/gym-bg2.png') }}'); filter: brightness(0.2);">
</div>
<div id="error-toast" class="fixed top-5 left-1/2 -translate-x-1/2 z-[9999] hidden flex items-center gap-4 bg-red-600/95 backdrop-blur-md text-white px-6 py-4 rounded-2xl shadow-[0_10px_40px_rgba(220,38,38,0.5)] border border-red-400/50 transition-all duration-300 transform -translate-y-10 opacity-0 w-[90%] max-w-md pointer-events-none">
    <i data-lucide="alert-octagon" class="w-8 h-8 text-white animate-pulse shrink-0"></i>
    <div class="flex-1">
        <h4 class="font-black uppercase tracking-widest text-sm drop-shadow-md">Beolvasási Hiba</h4>
        <p id="error-toast-msg" class="text-xs font-bold text-red-100 mt-0.5"></p>
    </div>
</div>

<section class="py-6 sm:py-10 min-h-screen text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        <div class="glass-panel p-4 sm:p-6 mb-6 sm:mb-8 text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-purple-500 to-transparent"></div>
            <h1 class="text-xl sm:text-3xl font-black mb-2 text-white uppercase tracking-tight flex items-center justify-center gap-2 sm:gap-3">
                <i data-lucide="scan-line" class="text-purple-400 w-6 h-6 sm:w-8 sm:h-8"></i>
                <span class="truncate">{{ $gym->name }}</span> <span class="text-purple-500">Scanner</span>
            </h1>
            <p class="text-gray-400 text-[10px] sm:text-xs font-bold uppercase tracking-[0.2em] sm:tracking-[0.3em]">Beléptető Rendszer</p>
        </div>

        {{-- Módválasztó Gombok - Reszponzív grid --}}
        <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6">
            <button id="btn-mode-camera" class="mode-btn active" onclick="switchMode('camera')">
                <i data-lucide="camera" class="w-4 h-4 sm:w-5 sm:h-5"></i> Kamera
            </button>
            <button id="btn-mode-hardware" class="mode-btn" onclick="switchMode('hardware')">
                <i data-lucide="keyboard" class="w-4 h-4 sm:w-5 sm:h-5"></i> <span class="hidden xs:inline">Kézi</span> Scanner
            </button>
        </div>

        {{-- FŐ KONTRÉNER --}}
        <div class="glass-panel p-4 sm:p-6 mb-8 relative">
            
            {{-- 1. KAMERA NÉZET --}}
            <div id="camera-section">
                <div class="flex flex-col items-center">
                    <p class="text-purple-300 text-[10px] sm:text-xs font-black uppercase tracking-widest mb-4 animate-pulse">
                        <i data-lucide="focus" class="inline w-3 h-3 mr-1"></i> Kamera Aktív
                    </p>
                    
                    {{-- QR olvasó méretezése mobilhoz --}}
                    <div id="qr-reader" class="w-full max-w-[260px] xs:max-w-[300px] aspect-square overflow-hidden rounded-2xl border-2 border-purple-500/50 shadow-[0_0_30px_rgba(191,64,255,0.2)] bg-black"></div>
                    
                    <p id="camera-error-message" class="text-red-500 text-center hidden mt-4 font-bold text-xs sm:text-sm bg-red-500/10 p-3 rounded-lg border border-red-500/50 w-full">
                        ⚠️ Hiba: A kamera nem érhető el.
                    </p>
                </div>
            </div>

            {{-- 2. HARDWARE SCANNER NÉZET --}}
            <div id="hardware-section" class="hidden text-center py-4 sm:py-8">
                <i data-lucide="barcode" class="w-12 h-12 sm:w-16 sm:h-16 text-purple-500 mx-auto mb-4 sm:mb-6 opacity-80"></i>
                
                <h3 class="text-lg sm:text-xl font-bold text-white mb-2">Használd a kézi scannert!</h3>
                <p class="text-gray-400 text-xs sm:text-sm mb-6">Kattints a mezőbe, majd olvasd le a QR kódot.</p>

                <div class="relative max-w-md mx-auto">
                    <input type="text" id="hardware-input" class="scanner-input" placeholder="Kattints ide..." autocomplete="off">
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-purple-500 animate-pulse hidden sm:block">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </div>
                </div>
                
                <p class="text-gray-500 text-[9px] sm:text-[10px] mt-4 uppercase tracking-widest px-4">
                    Enter gomb automatikusan küldi az adatot
                </p>
            </div>

        </div>

        {{-- AKTÍV BEOLVASÁSOK LISTÁJA --}}
        <div id="active-scans" class="space-y-4"></div>

    </div>
</section>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    lucide.createIcons();
    
    let html5QrCode = null;
    let currentQrCode = null;
    let isProcessing = false;

    function switchMode(mode) {
        const camSec = document.getElementById('camera-section');
        const hardSec = document.getElementById('hardware-section');
        const btnCam = document.getElementById('btn-mode-camera');
        const btnHard = document.getElementById('btn-mode-hardware');
        const input = document.getElementById('hardware-input');

        if (mode === 'camera') {
            camSec.classList.remove('hidden');
            hardSec.classList.add('hidden');
            btnCam.classList.add('active');
            btnHard.classList.remove('active');
            startScanner();
        } else {
            camSec.classList.add('hidden');
            hardSec.classList.remove('hidden');
            btnCam.classList.remove('active');
            btnHard.classList.add('active');
            if (html5QrCode) html5QrCode.stop().catch(console.error);
            setTimeout(() => input.focus(), 100);
        }
    }

    function processScannedCode(decodedText) {
        if (isProcessing) return;
        if (currentQrCode === decodedText) {
            setTimeout(() => { currentQrCode = null; }, 3000); 
            return; 
        }

        isProcessing = true;
        currentQrCode = decodedText;
        document.getElementById('hardware-input').value = ''; 
        document.getElementById('hardware-input').placeholder = "Feldolgozás...";

        fetch("{{ route('scanner.scan') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ 
                qr_code: decodedText, 
                deduct: true 
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'deducted') {
                createScanCard(data.user, decodedText);
                playSuccessSound();
            } else {
                showErrorToast(data.message || 'Érvénytelen vagy lejárt bérlet!');
            }
        })
        .catch(err => {
            console.error(err);
            showErrorToast('Hálózati hiba történt a kommunikáció során.');
        })
        .finally(() => {
            isProcessing = false;
            document.getElementById('hardware-input').placeholder = "Kattints ide...";
            if (!document.getElementById('hardware-section').classList.contains('hidden')) {
                document.getElementById('hardware-input').focus();
            }
        });
    }

    function createScanCard(user, qrText) {
        const container = document.getElementById('active-scans');
        const card = document.createElement('div');
        
        card.className = 'scan-card bg-black/80 border-l-4 border-purple-500 rounded-r-xl p-4 sm:p-5 shadow-[0_5px_30px_rgba(191,64,255,0.15)] relative overflow-hidden flex flex-col backdrop-blur-md mb-4 w-full';
        
        // Tároljuk el a QR kódot a kártyán data attribútumban a későbbi visszavonáshoz
        card.setAttribute('data-qr-code', qrText);

        const headerDiv = document.createElement('div');
        headerDiv.className = 'flex justify-between items-start mb-3 sm:mb-4 border-b border-gray-800 pb-3';
        
        const title = document.createElement('h3');
        title.className = 'font-black text-white text-lg sm:text-2xl uppercase tracking-wide truncate pr-2';
        title.textContent = `${user.last_name} ${user.first_name}`;
        
        const badge = document.createElement('span');
        badge.className = 'bg-green-500/20 text-green-400 text-[8px] sm:text-[10px] font-bold px-2 sm:px-3 py-1 rounded-full border border-green-500/30 uppercase tracking-widest flex items-center gap-1 shrink-0';
        badge.innerHTML = '<i data-lucide="check" class="w-2 sm:w-3 h-2 sm:h-3"></i> OK';

        headerDiv.appendChild(title);
        headerDiv.appendChild(badge);
        card.appendChild(headerDiv);

        const gridDiv = document.createElement('div');
        gridDiv.className = 'grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-4';

        const studentIdBox = document.createElement('div');
        studentIdBox.className = 'bg-gray-900/60 border border-gray-700 p-2 sm:p-3 rounded-lg flex flex-col justify-center';
        studentIdBox.innerHTML = `
            <span class="text-[8px] sm:text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Diákigazolvány</span>
            <div class="flex items-center gap-2">
                <i data-lucide="id-card" class="w-4 h-4 sm:w-5 sm:h-5 text-purple-400"></i>
                <span class="text-sm sm:text-lg font-mono font-bold text-white tracking-wider">${user.student_id_number || '---'}</span>
            </div>
        `;
        gridDiv.appendChild(studentIdBox);

        const usesBox = document.createElement('div');
        usesBox.className = 'bg-purple-900/20 border border-purple-500/30 p-2 sm:p-3 rounded-lg flex flex-col justify-center text-center relative overflow-hidden';
        usesBox.innerHTML = `
            <div class="absolute inset-0 bg-purple-500/10 blur-xl"></div>
            <span class="text-[8px] sm:text-[10px] text-purple-300 uppercase font-bold tracking-widest mb-1 relative z-10">Maradék</span>
            <span class="text-2xl sm:text-3xl font-black text-white relative z-10 drop-shadow-[0_0_10px_rgba(191,64,255,0.5)]">${user.remaining_uses}</span>
        `;
        gridDiv.appendChild(usesBox);

        card.appendChild(gridDiv);

        const statusDiv = document.createElement('div');
        statusDiv.className = 'flex items-center gap-2 mb-4 text-[10px] sm:text-xs font-bold uppercase tracking-wider';
        const isVerified = user.student_id_verified;
        statusDiv.innerHTML = isVerified 
            ? `<span class="text-green-400 flex items-center gap-1"><i data-lucide="shield-check" class="w-3 sm:w-4 h-3 sm:h-4"></i> Ellenőrizve</span>`
            : `<span class="text-red-400 flex items-center gap-1"><i data-lucide="alert-triangle" class="w-3 sm:w-4 h-3 sm:h-4"></i> Nincs ellenőrizve</span>`;
        card.appendChild(statusDiv);

        const progressContainer = document.createElement('div');
        progressContainer.className = 'absolute bottom-0 left-0 w-full h-1 bg-gray-800';
        const progress = document.createElement('div');
        progress.className = 'h-full bg-gradient-to-r from-purple-500 to-indigo-500 shadow-[0_0_15px_#a855f7] transition-all linear';
        progress.style.width = '100%';
        progress.style.transitionDuration = '300s';
        progressContainer.appendChild(progress);
        card.appendChild(progressContainer);
        setTimeout(() => { progress.style.width = '0%'; }, 100);

        const footer = document.createElement('div');
        footer.className = 'flex justify-between items-center mt-auto pt-3 border-t border-white/5 gap-2';
        
        const countdown = document.createElement('span');
        countdown.className = 'text-[10px] font-mono text-gray-400 bg-gray-800 px-2 sm:px-3 py-1 sm:py-1.5 rounded-md border border-gray-700 shrink-0';
        let timeLeft = 300;
        
        function updateCountdown() {
            const m = Math.floor(timeLeft / 60);
            const s = timeLeft % 60;
            countdown.textContent = `${m}:${s.toString().padStart(2, '0')}`;
            if (timeLeft-- >= 0) setTimeout(updateCountdown, 1000);
            else { 
                if (countdown.parentElement) countdown.remove(); 
            }
        }
        updateCountdown();
        footer.appendChild(countdown);

        const cancelBtn = document.createElement('button');
        cancelBtn.className = 'group flex items-center gap-1 sm:gap-2 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/30 text-[8px] sm:text-[10px] font-black uppercase tracking-widest py-1.5 sm:py-2 px-3 sm:px-4 rounded-lg transition-all duration-300';
        cancelBtn.innerHTML = '<span>Visszavonás</span> <i data-lucide="x" class="w-2 sm:w-3 h-2 sm:h-3 group-hover:rotate-90 transition-transform"></i>';
        
        // Javított visszavonás hívás - a kártyáról olvassuk ki a QR kódot
        cancelBtn.onclick = function(event) {
            event.preventDefault();
            event.stopPropagation();
            const qrCode = card.getAttribute('data-qr-code');
            console.log('Cancelling scan for QR:', qrCode);
            cancelScan(qrCode, card, progress, title);
        };
        
        footer.appendChild(cancelBtn);

        card.appendChild(footer);
        
        container.prepend(card);
        lucide.createIcons();

        card.scrollIntoView({ behavior: 'smooth', block: 'center' });

        setTimeout(() => {
            if (document.body.contains(card)) {
                card.classList.add('opacity-0', '-translate-x-full');
                setTimeout(() => card.remove(), 500);
            }
        }, 300000);
    }

    function cancelScan(qrText, card, progress, title) {
        if (!qrText) {
            console.error('No QR code provided for cancellation');
            return;
        }
        
        console.log('Sending cancel request for QR:', qrText);
        
        fetch("{{ route('scanner.cancel') }}", {
            method: "POST",
            headers: { 
                "Content-Type": "application/json", 
                "X-CSRF-TOKEN": "{{ csrf_token() }}" 
            },
            body: JSON.stringify({ qr_code: qrText })
        })
        .then(res => {
            console.log('Response status:', res.status);
            console.log('Response headers:', res.headers);
            
            if (!res.ok) {
                // Ha nem OK a válasz, próbáljuk meg kiolvasni a hibaüzenetet
                return res.text().then(text => {
                    console.error('Error response text:', text);
                    try {
                        // Próbáljuk JSON-ként értelmezni
                        const errorData = JSON.parse(text);
                        throw new Error(errorData.message || `HTTP error ${res.status}`);
                    } catch (e) {
                        // Ha nem JSON, akkor a szöveges választ dobjuk
                        throw new Error(`HTTP error ${res.status}: ${text.substring(0, 100)}`);
                    }
                });
            }
            return res.json();
        })
        .then(data => {
            console.log('Cancel response:', data);
            
            // Sikeres visszavonás
            card.classList.remove('border-l-4', 'border-purple-500');
            card.classList.add('border-2', 'border-red-500', 'bg-red-950/90');
            
            const titleSpan = document.createElement('span');
            titleSpan.className = 'text-red-500 text-xs ml-2 bg-black px-1 rounded uppercase';
            titleSpan.textContent = 'VISSZAVONVA';
            title.appendChild(titleSpan);
            
            if (progress && progress.parentElement) {
                progress.parentElement.remove();
            }
            
            const footer = card.querySelector('div:last-child');
            if(footer) footer.remove();
            
            setTimeout(() => {
                card.classList.add('opacity-0', 'scale-90');
                setTimeout(() => {
                    if (card.parentElement) card.remove();
                }, 500);
            }, 2000);
        })
        .catch(err => {
            console.error('Cancel error:', err);
            alert('Hiba történt a visszavonás során: ' + err.message);
        });
    }


    // Hardware input eseménykezelő
    document.addEventListener('DOMContentLoaded', function() {
        const hardwareInput = document.getElementById('hardware-input');
        if (hardwareInput) {
            hardwareInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const code = this.value.trim();
                    if (code.length > 0) {
                        processScannedCode(code);
                    }
                }
            });
        }
    });

    // Kamera Init - Hátlapi kamera kényszerítése
    function startScanner() {
        // Megállítjuk a futó scannert, ha van ilyen
        if (html5QrCode) {
            html5QrCode.stop().catch(() => {});
        }

        html5QrCode = new Html5Qrcode("qr-reader");

        // A config részben kényszerítjük a hátlapi kamerát (environment)
        const qrConfig = { 
            fps: 10, 
            qrbox: { width: 220, height: 220 } 
        };

        html5QrCode.start(
            { facingMode: "environment" }, 
            qrConfig, 
            processScannedCode
        ).catch(err => {
            console.error("FacingMode error, falling back to device list...", err);
            
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length > 0) {
                    const rearCamera = devices.find(d => 
                        d.label.toLowerCase().includes('back') || 
                        d.label.toLowerCase().includes('rear')
                    ) || devices[0];
                    
                    html5QrCode.start(rearCamera.id, qrConfig, processScannedCode)
                        .catch(e => {
                            document.getElementById('camera-error-message').classList.remove('hidden');
                        });
                } else {
                    document.getElementById('camera-error-message').classList.remove('hidden');
                }
            }).catch(e => document.getElementById('camera-error-message').classList.remove('hidden'));
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        startScanner();
    });


    function showErrorToast(message) {
        playErrorSound();
        
        const toast = document.getElementById('error-toast');
        const msgEl = document.getElementById('error-toast-msg');
        msgEl.textContent = message;

        toast.classList.remove('hidden');
        lucide.createIcons();
        
        setTimeout(() => {
            toast.classList.remove('-translate-y-10', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        }, 10);

        // Automatikus eltűnés 3.5 másodperc múlva
        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('-translate-y-10', 'opacity-0');
            setTimeout(() => toast.classList.add('hidden'), 300);
        }, 3500);
    }

    function playSuccessSound() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine'; // Tiszta hang
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            gain.gain.setValueAtTime(0.1, ctx.currentTime);
            osc.start();
            gain.gain.exponentialRampToValueAtTime(0.00001, ctx.currentTime + 0.15);
            osc.stop(ctx.currentTime + 0.15);
        } catch(e) { console.log('Audio API nem támogatott'); }
    }

    function playErrorSound() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(150, ctx.currentTime);
            gain.gain.setValueAtTime(0.1, ctx.currentTime);
            osc.start();
            gain.gain.exponentialRampToValueAtTime(0.00001, ctx.currentTime + 0.3);
            osc.stop(ctx.currentTime + 0.3);
        } catch(e) { console.log('Audio API nem támogatott'); }
    }
</script>
@endsection