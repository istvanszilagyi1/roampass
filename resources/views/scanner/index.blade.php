@extends('layouts.app')

@section('title', 'Scanner - RoamPass')

@section('content')
<style>
    .alert-item {
        transition: opacity 0.4s ease, transform 0.4s ease;
    }
</style>

<section class="py-12 bg-gray-950 text-white min-h-screen">
    <div class="max-w-2xl mx-auto px-6">
        <h1 class="text-3xl font-bold mb-6 text-blue-400">📷 Scanner</h1>

        <div id="alert-container" class="max-w-4xl mx-auto w-full space-y-4 mb-8 mt-6 px-4 z-50 relative">
            
            {{-- Sikeres beolvasás --}}
            @if(session('success'))
                <div class="alert-item w-full relative overflow-hidden bg-green-500/10 backdrop-blur-md border border-green-500/30 text-green-400 rounded-2xl p-4 flex items-center gap-4 shadow-[0_0_15px_rgba(34,197,94,0.15)] animate-in fade-in slide-in-from-top-4 duration-500">
                    <i data-lucide="check-circle" class="w-6 h-6 flex-shrink-0"></i>
                    <div class="flex-1 text-sm font-bold tracking-wide">{{ session('success') }}</div>
                    <button type="button" class="ml-auto flex-shrink-0 text-green-500/50 hover:text-green-400 transition-colors" onclick="closeAlert(this)">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            @endif

            {{-- Hiba a beolvasáskor --}}
            @if(session('error'))
                <div class="alert-item w-full relative overflow-hidden bg-red-500/10 backdrop-blur-md border border-red-500/30 text-red-400 rounded-2xl p-4 flex items-center gap-4 shadow-[0_0_15px_rgba(239,68,68,0.15)] animate-in fade-in slide-in-from-top-4 duration-500">
                    <i data-lucide="alert-triangle" class="w-6 h-6 flex-shrink-0"></i>
                    <div class="flex-1 text-sm font-bold tracking-wide">{{ session('error') }}</div>
                    <button type="button" class="ml-auto flex-shrink-0 text-red-500/50 hover:text-red-400 transition-colors" onclick="closeAlert(this)">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            @endif

            {{-- Form validációs hibák (ha pl. üresen küldi be) --}}
            @if($errors->any())
                <div class="alert-item w-full relative overflow-hidden bg-red-500/10 backdrop-blur-md border border-red-500/30 text-red-400 rounded-2xl p-4 flex items-start gap-4 shadow-[0_0_15px_rgba(239,68,68,0.15)] animate-in fade-in slide-in-from-top-4 duration-500">
                    <i data-lucide="alert-circle" class="w-6 h-6 mt-0.5 flex-shrink-0"></i>
                    <div class="flex-1 text-sm font-bold tracking-wide">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="ml-auto flex-shrink-0 text-red-500/50 hover:text-red-400 transition-colors mt-0.5" onclick="closeAlert(this)">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            @endif
        </div>

        {{-- BEOLVASÓ FORM --}}
        <form method="POST" action="{{ route('scanner.scan') }}" class="space-y-4 bg-gray-850 p-6 rounded-2xl shadow border border-gray-800">
            @csrf
            <label class="block mb-2 font-bold text-gray-300">Felhasználó QR kód beolvasása (ID)</label>
            <div class="relative">
                <input type="text" name="user_id" class="w-full p-4 pl-12 rounded-xl bg-gray-900 border border-gray-700 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all" placeholder="User ID">
                <i data-lucide="qr-code" class="w-5 h-5 text-gray-500 absolute left-4 top-1/2 -translate-y-1/2"></i>
            </div>

            <button type="submit" class="mt-4 w-full bg-blue-600 px-6 py-4 rounded-xl font-bold uppercase tracking-widest hover:bg-blue-500 transition-all flex items-center justify-center gap-2">
                <i data-lucide="scan" class="w-5 h-5"></i> Beolvasás
            </button>
        </form>

        {{-- EREDMÉNY KÁRTYA --}}
        @if(isset($user))
            <div class="mt-8 bg-gray-800/50 border border-gray-700 p-6 rounded-2xl">
                <h3 class="text-xl font-black mb-4 border-b border-gray-700 pb-3">Felhasználó Adatai</h3>
                
                <div class="space-y-3">
                    <p class="flex items-center justify-between">
                        <span class="text-gray-400">Név:</span>
                        <strong class="text-lg">{{ $user->first_name }} {{ $user->last_name }}</strong>
                    </p>
                    
                    <p class="flex items-center justify-between">
                        <span class="text-gray-400">Diákigazolvány:</span>
                        @if($user->hasValidStudentCard())
                            <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-lg font-bold text-sm">Érvényes</span>
                        @else
                            <span class="bg-red-500/20 text-red-400 px-3 py-1 rounded-lg font-bold text-sm">Nem érvényes</span>
                        @endif
                    </p>
                    
                    <p class="flex items-center justify-between">
                        <span class="text-gray-400">Hátralévő alkalmak:</span>
                        <strong class="text-2xl text-blue-400">{{ $user->gymPasses->first()->remaining_uses ?? '0' }}</strong>
                    </p>
                </div>
            </div>
        @endif
    </div>
</section>

<script>
    // Értesítés bezárása gombbal
    function closeAlert(button) {
        const alertBox = button.closest('.alert-item');
        alertBox.style.opacity = '0';
        alertBox.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            alertBox.style.display = 'none';
        }, 400);
    }

    // Automatikus eltüntetés 5 másodperc után
    document.addEventListener('DOMContentLoaded', () => {
        const alerts = document.querySelectorAll('.alert-item');
        alerts.forEach(alertBox => {
            setTimeout(() => {
                if (alertBox && alertBox.style.display !== 'none') {
                    alertBox.style.opacity = '0';
                    alertBox.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        alertBox.style.display = 'none';
                    }, 400);
                }
            }, 5000);
        });
    });
</script>
@endsection