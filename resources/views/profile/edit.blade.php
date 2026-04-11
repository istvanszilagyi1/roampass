@extends('layouts.app')

@section('title', 'Profilom - RoamPass')

@section('content')
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    .neon-soft {
        background: rgba(10, 12, 30, 0.85);
        border: 1px solid rgba(191, 64, 255, 0.4);
        position: relative;
        backdrop-filter: blur(12px);
    }

    .neon-border {
        border: 1px solid rgba(191, 64, 255, 0.8);
        box-shadow: 0 0 15px rgba(191, 64, 255, 0.2);
    }

    .input-field {
        background-color: rgba(0, 0, 0, 0.6) !important;
        border: 1px solid rgba(191, 64, 255, 0.3) !important;
        color: #ffffff !important;
        padding: 14px 16px !important;
        border-radius: 12px !important;
        width: 100%;
        outline: none !important;
        transition: all 0.3s ease;
    }

    .input-field:focus {
        border-color: #bf40ff !important;
        box-shadow: 0 0 15px rgba(191, 64, 255, 0.4) !important;
    }

    .static-info-box {
        background-color: rgba(191, 64, 255, 0.05) !important;
        border: 1px solid rgba(191, 64, 255, 0.2) !important;
        color: #ffffff !important;
        padding: 14px 16px !important;
        border-radius: 12px !important;
        font-weight: 600;
    }

    .text-neon-purple {
        color: #bf40ff;
        text-shadow: 0 0 10px rgba(191, 64, 255, 0.5);
    }

    .pass-card {
        background: linear-gradient(135deg, rgba(15, 18, 40, 0.9) 0%, rgba(10, 12, 30, 1) 100%);
        border: 1px solid rgba(191, 64, 255, 0.3);
    }

    .id-preview-img {
        @apply w-full h-48 object-cover rounded-xl border border-purple-500/40 mb-4 bg-black/40;
    }

    #main-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    
    footer {
        position: relative !important;
        z-index: 50 !important;
    }

    .custom-checkbox {
        accent-color: #bf40ff;
        width: 1.5rem;
        height: 1.5rem;
        cursor: pointer;
    }

    .alert-item {
        transition: opacity 0.4s ease, transform 0.4s ease;
    }
</style>

<div class="fixed inset-0 z-[-1] bg-cover bg-center" style="background-image: url('{{ asset('images/gym-bg2.png') }}'); filter: brightness(0.25);"></div>

<div id="main-wrapper">
    <section class="py-12 text-white flex-grow relative z-10 pb-20">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-6">
                <div class="animate-fade-up">
                    <h1 class="text-3xl font-black tracking-tight mb-1">Szia, <span class="text-neon-purple">{{ $user->first_name }}!</span></h1>
                    <p class="text-gray-400 text-sm italic">Személyes fiókod és bérleteid kezelése.</p>
                </div>

                <div class="flex items-center gap-4">
                    @php
                        $isActive = $user->hasActiveStudentId() || (!$isUploadRequired && !empty($user->student_id_number));
                    @endphp

                    @if($isActive)
                        <div class="bg-green-500/10 text-green-400 border border-green-500/30 px-6 py-2 rounded-full text-xs font-black uppercase flex items-center gap-2 shadow-[0_0_20px_rgba(34,197,94,0.2)]">
                            <i data-lucide="check-circle" class="w-4 h-4"></i> Aktív Hallgató
                        </div>
                    @else
                        <div class="bg-red-500/10 text-red-400 border border-red-500/30 px-6 py-2 rounded-full text-xs font-black uppercase flex items-center gap-2 shadow-[0_0_20px_rgba(239,68,68,0.2)] animate-pulse">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i> Hitelesítés szükséges
                        </div>
                    @endif
                </div>
            </div>

            @if(!$isActive)
                <div class="max-w-7xl mx-auto mb-8 p-5 bg-gradient-to-r from-purple-900/40 to-black/60 backdrop-blur-md border border-purple-500/50 rounded-2xl shadow-[0_0_30px_rgba(191,64,255,0.15)] flex items-start gap-5 animate-fade-up">
                    <div class="bg-purple-500/20 p-3 rounded-xl border border-purple-500/30 text-neon-purple flex-shrink-0 mt-1">
                        <i data-lucide="info" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-white uppercase tracking-widest mb-1">Következő lépés</h3>
                        <p class="text-gray-300 text-sm leading-relaxed mb-3">Ahhoz, hogy bérletet tudj vásárolni a RoamPass rendszerében, először hitelesítened kell a diákstátuszodat. Kérjük, add meg az adataidat az alábbi űrlapon!</p>
                        <a href="#profile-form" class="inline-flex items-center gap-2 text-xs font-bold uppercase text-purple-400 hover:text-white transition-colors">
                            <span>Ugrás az adatokhoz</span>
                            <i data-lucide="arrow-down" class="w-4 h-4 animate-bounce"></i>
                        </a>
                    </div>
                </div>
            @endif

            <div id="alert-container" class="max-w-4xl mx-auto w-full space-y-4 mb-8 mt-6 px-4 z-50 relative">
                
                {{-- AJAX üzenet helye --}}
                <div id="ajax-message" class="hidden alert-item w-full relative overflow-hidden backdrop-blur-md border rounded-2xl p-4 flex items-center gap-4 transition-all duration-500">
                    <i data-lucide="check-circle" id="ajax-icon" class="w-6 h-6 flex-shrink-0"></i>
                    <div class="flex-1 text-sm font-bold tracking-wide" id="ajax-text"></div>
                    <button type="button" class="flex-shrink-0 opacity-70 hover:opacity-100 transition-opacity" onclick="closeAlert(this)">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                {{-- Sikeres művelet --}}
                @if(session('success'))
                    <div class="alert-item w-full relative overflow-hidden bg-green-500/10 backdrop-blur-md border border-green-500/30 text-green-400 rounded-2xl p-4 flex items-center gap-4 shadow-[0_0_15px_rgba(34,197,94,0.15)] animate-in fade-in slide-in-from-top-4 duration-500">
                        <i data-lucide="check-circle" class="w-6 h-6 flex-shrink-0"></i>
                        {{-- FONTOS: flex-1 --}}
                        <div class="flex-1 text-sm font-bold tracking-wide">{{ session('success') }}</div>
                        <button type="button" class="flex-shrink-0 text-green-500/50 hover:text-green-400 transition-colors" onclick="closeAlert(this)">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                @endif

                {{-- Hibaüzenet --}}
                @if(session('error'))
                    <div class="alert-item w-full relative overflow-hidden bg-red-500/10 backdrop-blur-md border border-red-500/30 text-red-400 rounded-2xl p-4 flex items-center gap-4 shadow-[0_0_15px_rgba(239,68,68,0.15)] animate-in fade-in slide-in-from-top-4 duration-500">
                        <i data-lucide="alert-triangle" class="w-6 h-6 flex-shrink-0"></i>
                        {{-- FONTOS: flex-1 --}}
                        <div class="flex-1 text-sm font-bold tracking-wide">{{ session('error') }}</div>
                        <button type="button" class="flex-shrink-0 text-red-500/50 hover:text-red-400 transition-colors" onclick="closeAlert(this)">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                @endif

                {{-- Form validációs hibák --}}
                @if($errors->any())
                    <div class="alert-item w-full relative overflow-hidden bg-red-500/10 backdrop-blur-md border border-red-500/30 text-red-400 rounded-2xl p-4 flex items-start gap-4 shadow-[0_0_15px_rgba(239,68,68,0.15)] animate-in fade-in slide-in-from-top-4 duration-500">
                        <i data-lucide="alert-circle" class="w-6 h-6 mt-0.5 flex-shrink-0"></i>
                        <div class="flex-1 text-sm font-bold tracking-wide">
                            <p class="mb-2 uppercase text-[10px] tracking-widest text-red-500/70">Kérlek javítsd az alábbi hibákat:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="flex-shrink-0 text-red-500/50 hover:text-red-400 transition-colors mt-0.5" onclick="closeAlert(this)">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                @endif
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-900/50 p-6 rounded-3xl border border-purple-500/20 text-center">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Összes edzés</p>
                    <h3 class="text-4xl font-black text-white">{{ $totalScans }}</h3>
                </div>
                
                <div class="bg-gray-900/50 p-6 rounded-3xl border border-purple-500/20 text-center">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Kedvenc termed</p>
                    <h3 class="text-xl font-black text-purple-400 uppercase">{{ $favoriteGym }}</h3>
                </div>

                <div class="bg-gray-900/50 p-6 rounded-3xl border border-purple-500/20 text-center">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Becsült megtakarítás</p>
                    <h3 class="text-xl font-black text-emerald-400">{{ number_format($totalScans * 1000, 0, ',', ' ') }} Ft</h3>
                </div>
            </div>

            <div class="bg-gray-900/50 p-8 rounded-[2.5rem] border border-purple-500/20 mb-12">
                <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                    <i data-lucide="trending-up" class="w-5 h-5 text-purple-500"></i> Havi Aktivitás
                </h3>
                <div class="h-64">
                    <canvas id="workoutChart"></canvas>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('workoutChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chartLabels) !!},
                        datasets: [{
                            label: 'Edzések száma',
                            data: {!! json_encode($chartValues) !!},
                            borderColor: '#bf40ff',
                            backgroundColor: 'rgba(191, 64, 255, 0.1)',
                            borderWidth: 4,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#64748b' } },
                            x: { grid: { display: false }, ticks: { color: '#64748b' } }
                        }
                    }
                });
            </script>

            <div class="grid grid-cols-12 gap-8">
                <div class="col-span-12 lg:col-span-4 space-y-6">
                    <div class="neon-soft neon-border rounded-3xl p-8 flex flex-col items-center text-center">
                        <div class="w-24 h-24 bg-gradient-to-tr from-purple-700 to-indigo-500 rounded-full flex items-center justify-center mb-4 shadow-[0_0_30px_rgba(191,64,255,0.3)]">
                            <span class="text-3xl font-black text-white uppercase">{{ substr($user->last_name, 0, 1) }}{{ substr($user->first_name, 0, 1) }}</span>
                        </div>
                        <h3 class="font-black text-xl text-white tracking-tight">{{ $user->last_name }} {{ $user->first_name }}</h3>
                        <p class="text-purple-400/60 text-sm mb-6">{{ $user->email }}</p>
                    </div>

                    <div class="neon-soft neon-border rounded-3xl p-6">
                        <h4 class="text-sm font-black mb-5 flex items-center gap-2 uppercase text-neon-purple tracking-widest">
                            <i data-lucide="lock" class="w-4 h-4"></i> Jelszó módosítása
                        </h4>
                        <form action="{{ route('profile.updatePassword') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="password" name="current_password" placeholder="Jelenlegi jelszó" class="input-field" required>
                            <input type="password" name="password" placeholder="Új jelszó" class="input-field" required>
                            <input type="password" name="password_confirmation" placeholder="Megerősítés" class="input-field" required>
                            <button class="w-full bg-purple-600 hover:bg-purple-500 text-white py-4 rounded-xl font-black text-xs uppercase tracking-widest transition-all">
                                Frissítés
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-8 space-y-8">
                    <form id="profile-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="neon-soft neon-border rounded-3xl p-8 mb-8">
                            <h2 class="text-xl font-black mb-8 flex items-center gap-3 text-white uppercase tracking-widest">
                                <i data-lucide="shield-check" class="text-neon-purple w-6 h-6"></i> Regisztrált adatok
                            </h2>

                            <div class="space-y-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                    <div class="space-y-2">
                                        <span class="text-[10px] font-black text-gray-500 uppercase ml-1 tracking-[0.2em]">Vezetéknév</span>
                                        <div class="static-info-box uppercase tracking-tighter">{{ $user->last_name }}</div>
                                    </div>
                                    <div class="space-y-2">
                                        <span class="text-[10px] font-black text-gray-500 uppercase ml-1 tracking-[0.2em]">Keresztnév</span>
                                        <div class="static-info-box uppercase tracking-tighter">{{ $user->first_name }}</div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-neon-purple uppercase ml-1 tracking-[0.2em]">Diákigazolvány száma</label>
                                        <div class="relative group">
                                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-neon-purple transition-colors">
                                                <i data-lucide="hash" class="w-5 h-5"></i>
                                            </div>
                                            <input type="text" name="student_id_number" maxlength="10" 
                                                   value="{{ old('student_id_number', $user->student_id_number) }}" 
                                                   class="input-field pl-12 text-lg font-black tracking-widest" required>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <span class="text-[10px] font-black text-gray-500 uppercase ml-1 tracking-[0.2em]">Érvényesség (Lejárat)</span>
                                        @if($user->hasActiveStudentId() && $user->student_id_expiry)
                                            <div class="static-info-box border-green-500/30 text-green-400 bg-green-500/10 flex items-center gap-2">
                                                <i data-lucide="calendar-check" class="w-4 h-4"></i>
                                                <span>{{ \Carbon\Carbon::parse($user->student_id_expiry)->format('Y. m. d.') }}</span>
                                            </div>
                                        @else
                                            <div class="static-info-box border-red-500/30 text-red-400 bg-red-500/10 flex items-center gap-2">
                                                <i data-lucide="calendar-x" class="w-4 h-4"></i>
                                                <span>Nincs aktív érvényesség</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($isUploadRequired)
    
                                    @if($user->hasActiveStudentId())
                                        <div class="mt-8 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-xl text-yellow-400 text-xs font-bold flex items-start gap-3">
                                            <i data-lucide="info" class="w-5 h-5 flex-shrink-0"></i>
                                            <p>A profilod jelenleg hitelesítve van. Ha új igazolványképeket töltesz fel (pl. új féléves matrica miatt), a hitelesítésed átmenetileg felfüggesztésre kerül az újbóli ellenőrzésig!</p>
                                        </div>
                                    @endif

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12 border-t border-white/5 pt-8">
                                        <div class="space-y-3">
                                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1 tracking-widest">Igazolvány Előlap</label>
                                            
                                            <div class="relative w-full h-48 rounded-xl border border-purple-500/40 mb-4 bg-black/40 overflow-hidden flex items-center justify-center">
                                                <img id="preview-front" 
                                                     src="{{ $user->student_card_front ? Storage::url($user->student_card_front) . '?t='.time() : '#' }}" 
                                                     class="w-full h-full object-cover {{ $user->student_card_front ? '' : 'hidden' }}" 
                                                     alt="Előlap">
                                                
                                                <div id="placeholder-front" class="flex flex-col items-center justify-center text-gray-600 {{ $user->student_card_front ? 'hidden' : '' }}">
                                                    <i data-lucide="image" class="w-12 h-12 mb-2 opacity-50"></i>
                                                    <span class="text-xs font-bold uppercase tracking-widest opacity-50">Nincs kép feltöltve</span>
                                                </div>
                                            </div>

                                            <div class="relative border-2 border-dashed border-purple-500/20 rounded-2xl p-4 hover:border-purple-500/50 transition-all bg-black/20 group text-center">
                                                <input type="file" name="student_card_front" onchange="handlePreview(event, 'preview-front', 'placeholder-front')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                                <i data-lucide="camera" class="w-6 h-6 mx-auto text-gray-600 group-hover:text-neon-purple mb-2"></i>
                                                <p class="text-[9px] font-black text-gray-500 uppercase">Kiválasztás</p>
                                            </div>
                                        </div>

                                        <div class="space-y-3">
                                            <label class="text-[10px] font-black text-gray-400 uppercase ml-1 tracking-widest">Igazolvány Hátlap</label>
                                            
                                            <div class="relative w-full h-48 rounded-xl border border-purple-500/40 mb-4 bg-black/40 overflow-hidden flex items-center justify-center">
                                                <img id="preview-back" 
                                                     src="{{ $user->student_card_back ? Storage::url($user->student_card_back) . '?t='.time() : '#' }}" 
                                                     class="w-full h-full object-cover {{ $user->student_card_back ? '' : 'hidden' }}" 
                                                     alt="Hátlap">
                                                
                                                <div id="placeholder-back" class="flex flex-col items-center justify-center text-gray-600 {{ $user->student_card_back ? 'hidden' : '' }}">
                                                    <i data-lucide="scan-eye" class="w-12 h-12 mb-2 opacity-50"></i>
                                                    <span class="text-xs font-bold uppercase tracking-widest opacity-50">Nincs kép feltöltve</span>
                                                </div>
                                            </div>

                                            <div class="relative border-2 border-dashed border-purple-500/20 rounded-2xl p-4 hover:border-purple-500/50 transition-all bg-black/20 group text-center">
                                                <input type="file" name="student_card_back" onchange="handlePreview(event, 'preview-back', 'placeholder-back')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                                <i data-lucide="scan-eye" class="w-6 h-6 mx-auto text-gray-600 group-hover:text-neon-purple mb-2"></i>
                                                <p class="text-[9px] font-black text-gray-500 uppercase">Kiválasztás</p>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-[9px] text-gray-600 italic mt-4">* Éles képet tölts fel a matrica láthatóságához!</p>
                                @endif
                            </div>
                        </div>

                        <div class="neon-soft neon-border rounded-3xl p-8 mb-8">
                            <h2 class="text-xl font-black mb-8 flex items-center gap-3 text-white uppercase tracking-widest">
                                <i data-lucide="database" class="text-neon-purple w-6 h-6"></i> Adatkezelési beállítások
                            </h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex items-center gap-4 p-5 bg-black/40 rounded-2xl border border-white/5 hover:border-purple-500/30 transition-all">
                                    <input type="checkbox" id="newsletter-toggle" class="custom-checkbox" {{ $user->wants_newsletter ? 'checked' : '' }}>
                                    <label for="newsletter-toggle" class="cursor-pointer select-none">
                                        <span class="block text-xs font-bold text-gray-200">Hírlevél feliratkozás</span>
                                        <span class="block text-[10px] text-gray-500 uppercase tracking-tighter">Pipáld be az értesítésekhez!</span>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between p-5 bg-black/40 rounded-2xl border border-white/5 hover:border-purple-500/30 transition-all">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-gray-200">Adatkezelési nyilatkozat</span>
                                        <p class="text-[10px] text-gray-500 uppercase tracking-tighter">
                                            Elfogadva: {{ $user->privacy_policy_accepted_at ? \Carbon\Carbon::parse($user->privacy_policy_accepted_at)->format('Y.m.d.') : 'Nincs adat' }}
                                        </p>
                                    </div>
                                    <a href="/adatkezeles" target="_blank" class="p-2 bg-purple-500/10 hover:bg-purple-500/20 rounded-lg transition-all text-neon-purple group">
                                        <i data-lucide="external-link" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- MENTÉS GOMB --}}
                        <div class="flex justify-end">
                            <button type="submit" id="save-button" formnovalidate class="bg-white text-black hover:bg-purple-600 hover:text-white px-12 py-4 rounded-xl font-black uppercase tracking-widest transition-all flex items-center gap-3 shadow-xl active:scale-95">
                                <i data-lucide="save" id="save-icon" class="w-5 h-5"></i> 
                                <span id="button-text">{{ $isUploadRequired ? 'Mentés és Hitelesítés' : 'Adatok mentése' }}</span>
                            </button>
                        </div>
                    </form>

                    <div class="mt-12 p-6 bg-red-500/5 rounded-3xl border border-red-500/20 flex justify-between items-center">
                        <div>
                            <h4 class="text-white font-bold">Adatvédelem és GDPR</h4>
                            <p class="text-gray-500 text-xs">Bármikor lekérheted nálunk tárolt személyes adataidat.</p>
                        </div>
                        <a href="{{ route('profile.export') }}" class="px-4 py-2 bg-gray-800 text-red-400 text-xs font-black uppercase rounded-xl hover:bg-red-500 hover:text-white transition-all">
                            Adatok letöltése (.JSON)
                        </a>
                    </div>

                    {{-- BÉRLETEK --}}
                    <div class="neon-soft neon-border rounded-3xl p-8 mt-12 mb-16">
                        <h2 class="text-lg font-black mb-8 flex items-center gap-2 uppercase text-white tracking-widest">
                            <i data-lucide="ticket" class="w-5 h-5 text-neon-purple"></i> Aktuális bérlet
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @forelse($user->gymPasses->where('remaining_uses', '>', 0) as $pass)
                                <div class="pass-card rounded-2xl p-6 relative group hover:border-purple-500 transition-all">
                                    <div class="flex justify-between items-start mb-6">
                                        <div>
                                            <p class="text-[10px] font-black text-neon-purple uppercase mb-1">Státusz: Aktív</p>
                                            <p class="text-4xl font-black text-white">{{ $pass->remaining_uses }} <span class="text-xs text-gray-500 uppercase font-normal tracking-normal">alkalom</span></p>
                                        </div>
                                        <div class="bg-purple-500/10 p-3 rounded-xl border border-purple-500/20 group-hover:bg-purple-600 transition-all text-neon-purple group-hover:text-white">
                                            <i data-lucide="qr-code" class="w-6 h-6"></i>
                                        </div>
                                    </div>
                                    <a href="{{ route('passes.index') }}" class="block text-center bg-purple-600/20 hover:bg-purple-600 text-white py-3 rounded-xl font-black text-xs uppercase tracking-widest transition-all">
                                        QR kód megnyitása
                                    </a>
                                </div>
                            @empty
                                <div class="col-span-2 py-10 text-center border-2 border-dashed border-white/5 rounded-3xl bg-black/20 text-gray-600 italic text-sm">
                                    Jelenleg nincs aktív bérleted.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    if (typeof lucide !== 'undefined') { 
        lucide.createIcons(); 
    }

    // Kép előnézet kezelése
    function handlePreview(event, previewId, placeholderId) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById(previewId);
                const placeholderBox = document.getElementById(placeholderId);
                
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
                previewImg.classList.add('animate-in', 'fade-in', 'zoom-in', 'duration-500');
                
                if(placeholderBox) {
                    placeholderBox.classList.add('hidden');
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Fő űrlap mentés gomb animáció
    document.getElementById('profile-form').addEventListener('submit', function(e) {
        const btn = document.getElementById('save-button');
        const text = document.getElementById('button-text');
        const icon = document.getElementById('save-icon');
        
        const studentId = document.querySelector('input[name="student_id_number"]').value;
        if (!studentId) {
            e.preventDefault();
            alert('A diákigazolvány szám megadása kötelező!');
            return false;
        }
        
        btn.disabled = true;
        btn.classList.add('opacity-70', 'cursor-wait');
        text.innerText = 'Feldolgozás...';
        
        icon.setAttribute('data-lucide', 'loader-2');
        icon.classList.add('animate-spin');
        lucide.createIcons();
        
        return true;
    });

    // File inputok ellenőrzése
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            if (this.files[0]) {
                if (this.files[0].size > 5 * 1024 * 1024) {
                    alert('A kép mérete túl nagy! Maximum 5MB lehet.');
                    this.value = ''; 
                    
                    const previewId = this.closest('.space-y-3').querySelector('img').id;
                    if (previewId) {
                        const originalSrc = document.getElementById(previewId).getAttribute('data-original-src');
                        if (originalSrc) {
                            document.getElementById(previewId).src = originalSrc;
                        }
                    }
                }
            }
        });
    });

    document.querySelectorAll('.id-preview-img').forEach(img => {
        img.setAttribute('data-original-src', img.src);
    });

    function closeAlert(button) {
        const alertBox = button.closest('.alert-item');
        alertBox.style.opacity = '0';
        alertBox.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            alertBox.style.display = 'none';
        }, 400);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const alerts = document.querySelectorAll('.alert-item:not(#ajax-message)');
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

    // --- CHECKBOX AJAX LOGIKA FRISSÍTVE AZ ÚJ ALERTHEZ ---
    document.getElementById('newsletter-toggle').addEventListener('change', function() {
        const isChecked = this.checked;
        const messageBox = document.getElementById('ajax-message');
        const messageText = document.getElementById('ajax-text');
        const messageIcon = document.getElementById('ajax-icon');

        fetch('{{ route("profile.newsletter") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                wants_newsletter: isChecked
            })
        })
        .then(response => response.json())
        .then(data => {
            // Visszaállítjuk a láthatóságot, ha korábban be lett zárva
            messageBox.style.display = 'flex';
            messageBox.style.opacity = '1';
            messageBox.style.transform = 'translateY(0)';

            messageBox.classList.remove('hidden', 'bg-red-500/10', 'border-red-500/30', 'text-red-400');
            messageBox.classList.add('bg-green-500/10', 'border-green-500/30', 'text-green-400', 'shadow-[0_0_15px_rgba(34,197,94,0.15)]');
            
            messageIcon.setAttribute('data-lucide', 'check-circle');
            messageText.innerText = isChecked ? 'Feliratkozva a hírlevélre!' : 'Leiratkozva a hírlevélről!';
            lucide.createIcons();
            
            setTimeout(() => {
                closeAlert(messageBox.querySelector('button'));
            }, 3000);
        })
        .catch(error => {
            console.error('Hiba:', error);
            messageBox.style.display = 'flex';
            messageBox.style.opacity = '1';
            messageBox.style.transform = 'translateY(0)';

            messageBox.classList.remove('hidden', 'bg-green-500/10', 'border-green-500/30', 'text-green-400');
            messageBox.classList.add('bg-red-500/10', 'border-red-500/30', 'text-red-400', 'shadow-[0_0_15px_rgba(239,68,68,0.15)]');
            
            messageIcon.setAttribute('data-lucide', 'alert-triangle');
            messageText.innerText = 'Hiba történt a mentés során!';
            lucide.createIcons();
        });
    });
</script>
@endsection