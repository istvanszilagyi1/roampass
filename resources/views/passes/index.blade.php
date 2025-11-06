@extends('layouts.app')

@section('title', 'Saját bérleted - RoamPass')

@section('content')
<script src="https://unpkg.com/lucide@latest"></script>

<style>
.bg-gray-850 { background-color: #1f1f1f; }
</style>

<section class="py-24 bg-gradient-to-br from-gray-950 via-gray-900 to-black text-white min-h-screen">
    <div class="max-w-xl mx-auto px-6 text-center">

        <h1 class="text-5xl font-extrabold mb-12 text-blue-400 drop-shadow-lg flex items-center justify-center gap-3">
            <i data-lucide="ticket" class="w-8 h-8"></i> Saját Bérleted
        </h1>

        <div class="mb-10 space-y-4">
            @if(session('success'))
                <div class="p-4 bg-green-700/40 text-green-200 rounded-xl shadow font-medium">
                    <i data-lucide="check-circle" class="w-5 h-5 inline mr-2"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-700/40 text-red-200 rounded-xl shadow font-medium">
                    <i data-lucide="alert-triangle" class="w-5 h-5 inline mr-2"></i> {{ session('error') }}
                </div>
            @endif
        </div>

        @if($passes->count())
            @php
                // Feltételezve, hogy csak egy aktív bérletet mutatunk be (elsőt)
                $pass = $passes->first();
                $isActive = $pass->remaining_uses > 0;
            @endphp

            <div class="bg-gray-850 rounded-3xl shadow-2xl shadow-black/50 border border-gray-700 p-8 md:p-10 mb-10">

                <div class="mb-8 border-b border-gray-700 pb-6">
                    <p class="text-gray-400 text-lg font-medium mb-3">Vásárlás dátuma: <strong class="text-white">{{ \Carbon\Carbon::parse($pass->purchase_date)->format('Y.m.d') }}</strong></p>

                    @if($isActive)
                        <div class="bg-gray-900 p-5 rounded-2xl border border-green-600/50 shadow-inner">
                            <p class="text-2xl font-bold text-gray-300">Hátralévő alkalmak:</p>
                            <p class="text-7xl font-extrabold text-green-400 mt-2">
                                {{ $pass->remaining_uses }}<span class="text-5xl text-gray-500">/12</span>
                            </p>
                        </div>
                    @else
                        <div class="p-5 rounded-2xl border border-red-500 bg-red-700/20 shadow-xl">
                             <p class="text-2xl font-bold text-red-400">
                                <i data-lucide="x-octagon" class="w-6 h-6 inline mr-2"></i> Lejárt
                            </p>
                            <p class="text-lg font-semibold text-red-300 mt-2">
                                Elfogytak az alkalmak, kérlek válts új bérletet!
                            </p>
                        </div>
                    @endif
                </div>

                @if($isActive)
                    <div class="inline-block bg-gray-900 p-8 rounded-2xl shadow-xl border border-blue-600/50">
                        <p class="mb-5 text-lg text-white font-semibold flex items-center justify-center gap-2">
                            <i data-lucide="qr-code" class="w-6 h-6 text-blue-400"></i> Mutasd fel a QR kódot a bejelentkezéshez
                        </p>
                        <img src="{{ $pass->qr_code_url }}" alt="QR kód"
                            class="mx-auto w-56 h-56 object-contain rounded-lg border-4 border-white shadow-2xl transition duration-300 hover:scale-105">
                    </div>
                @endif

            </div>

        @else
            <div class="bg-gray-850/80 p-10 rounded-3xl shadow-xl border border-gray-700">
                <p class="text-gray-300 text-2xl font-semibold mb-6">Még nincs aktív RoamPass bérleted.</p>
                <a href="{{ route('passes.create') }}"
                   class="inline-flex items-center bg-indigo-600 text-white px-8 py-4 rounded-full font-bold shadow-lg
                          hover:bg-indigo-500 transition-all duration-300 text-lg">
                    <i data-lucide="chevron-right" class="w-5 h-5 mr-2"></i> Első bérleted vásárlása most!
                </a>
            </div>
        @endif

    </div>
</section>

<script>
    lucide.createIcons();
</script>
@endsection
