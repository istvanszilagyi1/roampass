@extends('layouts.app')

@section('title', 'Admin Dashboard - RoamPass')

@section('content')
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    :root {
        --card-bg: #0f172a;
        --card-border: #1e293b;
        --input-bg: #020617;
    }

    .glass-panel {
        @apply bg-gray-900/60 backdrop-blur-md border border-gray-800/80 rounded-3xl shadow-2xl;
    }
    .stat-card {
        @apply relative overflow-hidden rounded-3xl p-6 transition-all duration-300 hover:-translate-y-1;
        background: linear-gradient(145deg, var(--card-bg), #090f1e);
        border: 1px solid var(--card-border);
        box-shadow: 0 4px 20px -5px rgba(0,0,0,0.5);
    }
    .stat-icon-bg {
        @apply absolute -right-6 -top-6 w-24 h-24 rounded-full opacity-20 blur-xl;
    }

    .input-style {
        background-color: var(--input-bg) !important;
        border: 1px solid var(--card-border) !important;
        @apply w-full p-3 rounded-2xl text-sm text-gray-200 placeholder-gray-600 outline-none transition-all duration-300;
    }
    .input-style:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        background-color: #0a0f1f !important;
    }

    .admin-table-row {
        @apply border-b border-gray-800/50 hover:bg-gray-800/10 transition-colors;
    }
    .table-header {
        @apply px-4 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-left;
    }

    .edit-row {
        display: none;
        background: rgba(15, 23, 42, 0.9);
    }

    .select2-container--default .select2-selection--single {
        background-color: var(--input-bg) !important;
        border: 1px solid var(--card-border) !important;
        border-radius: 1rem !important;
        height: 42px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #e2e8f0 !important;
        padding-left: 12px !important;
    }
    .select2-dropdown {
        background-color: var(--card-bg) !important;
        border: 1px solid var(--card-border) !important;
        border-radius: 1rem !important;
        overflow: hidden !important;
    }
    .select2-results__option--highlighted {
        background-color: #6366f1 !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        background-color: var(--input-bg) !important;
        color: #ffffff !important;
        border: 1px solid var(--card-border) !important;
        border-radius: 0.5rem !important;
        outline: none !important;
    }
    .alert-item {
        transition: opacity 0.4s ease, transform 0.4s ease;
    }
</style>

<section class="py-12 bg-[#020617] text-gray-100 min-h-screen bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-gray-900 via-[#020617] to-[#020617]">
    <div id="alert-container" class="max-w-4xl mx-auto w-full space-y-4 mb-8 mt-6 px-4 z-50 relative">
        <div id="ajax-message" class="hidden alert-item w-full relative overflow-hidden bg-gray-900/80 backdrop-blur-md border rounded-2xl p-4 flex items-center gap-4 transition-all duration-500 shadow-2xl">
            <i data-lucide="check-circle" id="ajax-icon" class="w-6 h-6 flex-shrink-0"></i>
            <div class="flex-1 text-sm font-bold tracking-wide" id="ajax-text"></div>
            <button type="button" class="ml-auto flex-shrink-0 opacity-70 hover:opacity-100 transition-opacity" onclick="closeAlert(this)">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        {{-- Sikeres művelet --}}
        @if(session('success'))
            <div class="alert-item w-full relative overflow-hidden bg-emerald-500/10 backdrop-blur-xl border border-emerald-500/30 text-emerald-400 rounded-2xl p-4 flex items-center gap-4 shadow-[0_0_30px_rgba(16,185,129,0.2)] animate-in fade-in slide-in-from-top-4 duration-500">
                <i data-lucide="check-circle" class="w-6 h-6 flex-shrink-0"></i>
                <div class="flex-1 text-sm font-bold tracking-wide">{{ session('success') }}</div>
                <button type="button" class="ml-auto flex-shrink-0 text-emerald-500/50 hover:text-emerald-400 transition-colors" onclick="closeAlert(this)">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        @endif

        {{-- Hibaüzenet --}}
        @if(session('error'))
            <div class="alert-item w-full relative overflow-hidden bg-red-500/10 backdrop-blur-xl border border-red-500/30 text-red-400 rounded-2xl p-4 flex items-center gap-4 shadow-[0_0_30px_rgba(239,68,68,0.2)] animate-in fade-in slide-in-from-top-4 duration-500">
                <i data-lucide="alert-triangle" class="w-6 h-6 flex-shrink-0"></i>
                <div class="flex-1 text-sm font-bold tracking-wide">{{ session('error') }}</div>
                <button type="button" class="ml-auto flex-shrink-0 text-red-500/50 hover:text-red-400 transition-colors" onclick="closeAlert(this)">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        @endif

        {{-- Form validációs hibák --}}
        @if($errors->any())
            <div class="alert-item w-full relative overflow-hidden bg-red-500/10 backdrop-blur-xl border border-red-500/30 text-red-400 rounded-2xl p-4 flex items-start gap-4 shadow-[0_0_30px_rgba(239,68,68,0.2)] animate-in fade-in slide-in-from-top-4 duration-500">
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">


        @php
            $pendingCount = 0;
            foreach($pendingUsers as $u) {
                if(!$u->student_id_verified && $u->student_card_front && $u->student_card_back) {
                    $pendingCount++;
                }
            }
        @endphp

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-gray-800 pb-6">
            <div>
                <h1 class="text-3xl font-black tracking-tight flex items-center gap-3 mb-1">
                    <div class="p-2 bg-indigo-600/20 rounded-xl border border-indigo-500/30">
                        <i data-lucide="layout-grid" class="text-indigo-500 w-6 h-6"></i>
                    </div>
                    Admin Vezérlőpult
                </h1>
                <p class="text-gray-500 text-sm">Rendszerszintű statisztikák és kezelés.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                @if($pendingCount > 0)
                    <a href="#pending-verifications" class="group flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-black uppercase text-xs tracking-widest shadow-[0_0_20px_rgba(37,99,235,0.3)] hover:shadow-[0_0_30px_rgba(37,99,235,0.5)] transition-all animate-pulse">
                        <i data-lucide="graduation-cap" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                        Ellenőrzendő ({{ $pendingCount }})
                    </a>
                @endif
                <a href="{{ route('admin.newsletter') }}" class="group flex items-center gap-2 bg-purple-600 hover:bg-purple-500 text-white px-6 py-3 rounded-xl font-black uppercase text-xs tracking-widest shadow-[0_0_20px_rgba(147,51,234,0.3)] hover:shadow-[0_0_30px_rgba(147,51,234,0.5)] transition-all">
                    <i data-lucide="mail-plus" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                    Hírlevél írása
                </a>
            </div>
        </div>
        
        {{-- RENDSZERBEÁLLÍTÁSOK SZEKCIÓ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
            {{-- Diákigazolvány Validálás --}}
            <div class="glass-panel p-6 border-indigo-500/20">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-500/20 rounded-lg">
                            <i data-lucide="shield-check" class="{{ ($settings['student_id_upload_required'] ?? '0') == '1' ? 'text-indigo-400' : 'text-gray-600' }} w-5 h-5"></i>
                        </div>
                        <h3 class="font-bold uppercase text-xs tracking-widest">Validálási mód</h3>
                    </div>
                    <span class="text-[10px] font-black uppercase px-2 py-1 rounded {{ ($settings['student_id_upload_required'] ?? '0') == '1' ? 'bg-indigo-500/20 text-indigo-400' : 'bg-gray-800 text-gray-500' }}">
                        {{ ($settings['student_id_upload_required'] ?? '0') == '1' ? 'Szigorú' : 'Normál' }}
                    </span>
                </div>

                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex items-center justify-between p-4 bg-black/30 rounded-2xl border border-white/5">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-gray-300">Fotó feltöltés</span>
                            <span class="text-[10px] {{ ($settings['student_id_upload_required'] ?? '0') == '1' ? 'text-indigo-400' : 'text-gray-500' }}">
                                {{ ($settings['student_id_upload_required'] ?? '0') == '1' ? 'Jelenleg kötelező' : 'Jelenleg nem kért' }}
                            </span>
                        </div>
                        <label class="switch">
                            <input type="hidden" name="student_id_upload_required" value="0">
                            <input type="checkbox" name="student_id_upload_required" value="1" {{ ($settings['student_id_upload_required'] ?? '0') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-black/30 rounded-2xl border border-white/5">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-gray-300">Érvényes Matrica Színe</span>
                            <span class="text-[10px] text-gray-500">Az OCR ezen a színen fog keresni</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="color" 
                                name="active_sticker_color" 
                                value="{{ $settings['active_sticker_color'] ?? '#B300B3' }}" 
                                onchange="this.form.submit()"
                                class="w-8 h-8 rounded cursor-pointer bg-transparent border-0 p-0">
                        </div>
                    </div>
                </form>
            </div>

            {{-- Rendszer Logok --}}
            <div class="glass-panel p-6 border-amber-500/20">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-500/20 rounded-lg">
                            <i data-lucide="activity" class="{{ ($settings['logging_enabled'] ?? '0') == '1' ? 'text-amber-400' : 'text-gray-600' }} w-5 h-5"></i>
                        </div>
                        <h3 class="font-bold uppercase text-xs tracking-widest">Action Logs</h3>
                    </div>
                    <span class="text-[10px] font-black uppercase px-2 py-1 rounded {{ ($settings['logging_enabled'] ?? '0') == '1' ? 'bg-amber-500/20 text-amber-400' : 'bg-gray-800 text-gray-500' }}">
                        {{ ($settings['logging_enabled'] ?? '0') == '1' ? 'Monitoring BE' : 'Monitoring KI' }}
                    </span>
                </div>
                <div class="space-y-4">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        <div class="flex items-center justify-between p-4 bg-black/30 rounded-2xl border border-white/5">
                            <span class="text-xs font-bold text-gray-300">Események rögzítése</span>
                            <label class="switch">
                                <input type="hidden" name="logging_enabled" value="0">
                                <input type="checkbox" name="logging_enabled" value="1" {{ ($settings['logging_enabled'] ?? '0') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </form>
                    <a href="{{ route('admin.logs') }}" class="block w-full py-3 bg-amber-500/10 text-amber-500 border border-amber-500/30 rounded-xl text-[10px] font-black uppercase text-center hover:bg-amber-500 hover:text-white transition-all shadow-[0_0_15px_rgba(245,158,11,0.05)]">
                        Naplófájlok megtekintése & szűrése
                    </a>
                </div>
            </div>
        </div>

        {{-- MANUÁLIS DIÁKIGAZOLVÁNY ELLENŐRZÉS --}}
        @if($pendingCount > 0)
            <div id="pending-verifications" class="space-y-6 scroll-mt-8">
                <h2 class="text-xl font-bold flex items-center gap-3 border-b border-gray-800 pb-4">
                    <div class="p-1.5 bg-blue-500/20 rounded-lg"><i data-lucide="graduation-cap" class="text-blue-400 w-5 h-5"></i></div>
                    Függőben lévő igazolvány ellenőrzések
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($pendingUsers as $user)
                        @if(!$user->student_id_verified && $user->student_card_front && $user->student_card_back)
                            <div class="glass-panel p-5 space-y-4 border-blue-500/20">
                                <div>
                                    <h3 class="text-sm font-bold text-blue-400">{{ $user->last_name ?? '' }} {{ $user->first_name ?? $user->name }}</h3>
                                    <p class="text-[10px] text-gray-500">{{ $user->email }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-500 uppercase mb-1.5">Előlap</p>
                                        <a href="{{ asset('storage/'.$user->student_card_front) }}" target="_blank" class="block group overflow-hidden rounded-xl border border-white/5">
                                            <img src="{{ asset('storage/'.$user->student_card_front) }}" alt="Előlap" class="w-full object-cover h-24 group-hover:scale-110 transition-transform duration-300">
                                        </a>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-500 uppercase mb-1.5">Hátlap</p>
                                        <a href="{{ asset('storage/'.$user->student_card_back) }}" target="_blank" class="block group overflow-hidden rounded-xl border border-white/5">
                                            <img src="{{ asset('storage/'.$user->student_card_back) }}" alt="Hátlap" class="w-full object-cover h-24 group-hover:scale-110 transition-transform duration-300">
                                        </a>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('admin.verifyStudent', $user) }}" class="space-y-3 pt-3 border-t border-white/5">
                                    @csrf
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-500 ml-1 uppercase">Lejárat dátuma</label>
                                        <input type="date" name="expiry_date" class="input-style !p-2 !text-xs" required>
                                    </div>

                                    <button type="submit" class="w-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500 hover:text-white py-2 rounded-xl font-black transition-all text-[10px] uppercase tracking-widest flex items-center justify-center gap-2 shadow-[0_0_15px_rgba(16,185,129,0.1)]">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i> Elfogadás
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Statisztikák --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="stat-card group"><div class="stat-icon-bg bg-blue-500"></div><div class="relative z-10"><h3 class="text-sm font-bold text-blue-400 uppercase tracking-wider mb-4">Összes bérlet</h3><p class="text-4xl font-extrabold">{{ $totalPasses }}</p></div></div>
            <div class="stat-card group"><div class="stat-icon-bg bg-indigo-500"></div><div class="relative z-10"><h3 class="text-sm font-bold text-indigo-400 uppercase tracking-wider mb-4">Partnerek</h3><p class="text-4xl font-extrabold">{{ $totalGyms }}</p></div></div>
            <div class="stat-card group"><div class="stat-icon-bg bg-emerald-500"></div><div class="relative z-10"><h3 class="text-sm font-bold text-emerald-400 uppercase tracking-wider mb-4">Összbevétel</h3><p class="text-4xl font-extrabold">{{ number_format($totalRevenue, 0, ',', ' ') }} Ft</p></div></div>
            <div class="stat-card group"><div class="stat-icon-bg bg-amber-500"></div><div class="relative z-10"><h3 class="text-sm font-bold text-amber-400 uppercase tracking-wider mb-4">Új tagok</h3><p class="text-4xl font-extrabold">{{ $newUsersLast30Days }}</p></div></div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <div class="xl:col-span-2 space-y-8">
                <div class="glass-panel p-6">
                    <h3 class="text-lg font-bold flex items-center gap-2 mb-6"><i data-lucide="bar-chart-big" class="text-indigo-400"></i> Trendek</h3>
                    <div class="h-[300px] w-full"><canvas id="usersChart"></canvas></div>
                </div>

                {{-- Felhasználók Táblázat Keresővel --}}
                <div>
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <h2 class="text-xl font-bold flex items-center gap-3">
                            <div class="p-1.5 bg-sky-500/20 rounded-lg"><i data-lucide="users" class="w-5 h-5 text-sky-400"></i></div>
                            Felhasználók
                        </h2>
                        <div class="relative w-full sm:w-72">
                            <input type="text" id="searchInput" placeholder="Keresés név vagy email..." class="input-style pl-12">
                        </div>
                    </div>
                    <div id="usersTable" class="glass-panel overflow-hidden">
                        @include('admin.partials.users_table', ['users'=>$users])
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div>
                    <h2 class="text-xl font-bold flex items-center gap-3 mb-6">
                        <div class="p-1.5 bg-emerald-500/20 rounded-lg"><i data-lucide="map-pin" class="text-emerald-400"></i></div>
                        Partnerek & Pénzügy
                    </h2>

                    {{-- IDŐSZAK VÁLASZTÓ --}}
                    <div class="flex items-center gap-2 mb-4 bg-black/20 p-3 rounded-2xl border border-white/5">
                        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center gap-2 w-full">
                            <select name="year" class="input-style !p-2 !text-xs !rounded-xl">
                                @foreach(range(now()->year, now()->year - 1) as $y)
                                    <option value="{{ $y }}" {{ ($selectedYear ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                            <select name="month" class="input-style !p-2 !text-xs !rounded-xl">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ ($selectedMonth ?? now()->month) == $m ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-indigo-600 p-2 rounded-xl hover:bg-indigo-500 transition-all shrink-0">
                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>

                    <div class="glass-panel overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-gray-900/50">
                                <tr>
                                    <th class="table-header">Partner</th>
                                    <th class="table-header">Forgalom</th>
                                    <th class="table-header text-right">Műveletek</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gyms as $gym)
                                <tr class="admin-table-row group">
                                    <td class="px-4 py-4">
                                        <p class="font-bold text-gray-200 text-sm">{{ $gym->name }}</p>
                                        <p class="text-[10px] text-gray-500 uppercase">{{ $gym->city }}</p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-xs font-bold text-indigo-400">{{ $gym->monthly_scans ?? 0 }} db</div>
                                        <div class="text-[8px] text-gray-500 uppercase tracking-tighter">
                                            {{ Carbon\Carbon::create()->month($selectedMonth ?? now()->month)->translatedFormat('F') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Számla generálás a választott hónapra --}}
                                            <form action="{{ route('admin.generateInvoice', $gym->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="month" value="{{ $selectedMonth ?? now()->month }}">
                                                <input type="hidden" name="year" value="{{ $selectedYear ?? now()->year }}">
                                                <button type="submit" class="p-2 bg-emerald-600/20 text-emerald-400 rounded-lg hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Számla generálása a választott időszakra">
                                                    <i data-lucide="file-plus" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                            <button onclick="toggleEdit('{{ $gym->id }}')" class="p-2 bg-gray-800 rounded-lg text-indigo-400 hover:text-white transition-all">
                                                <i data-lucide="settings-2" class="w-4 h-4"></i>
                                            </button>
                                            <form action="{{ route('admin.deleteGym', $gym->id) }}" method="POST" onsubmit="return confirm('Biztosan törlöd ezt a partnert? A művelet nem vonható vissza!');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 bg-red-900/30 text-red-400 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Partner törlése">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                {{-- Gördülő Szerkesztő Sáv --}}
                                <tr id="edit-gym-{{ $gym->id }}" class="edit-row">
                                    <td colspan="3" class="p-6 border-b border-indigo-500/20">
                                        <form method="POST" action="{{ route('admin.updateGym', $gym->id) }}" enctype="multipart/form-data" class="space-y-8">
                                            @csrf @method('PUT')
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                                {{-- Megjelenés szerkesztése --}}
                                                <div class="space-y-4">
                                                    <h4 class="text-xs font-black uppercase text-indigo-400 flex items-center gap-2 tracking-widest">
                                                        <i data-lucide="image" class="w-3 h-3"></i> Általános & Megjelenés
                                                    </h4>
                                                    <div class="space-y-1"><label class="text-[10px] font-bold text-gray-500 ml-1">Terem neve</label><input type="text" name="name" value="{{ $gym->name }}" class="input-style !p-2"></div>
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div class="space-y-1"><label class="text-[10px] font-bold text-gray-500 ml-1">Város</label><input type="text" name="city" value="{{ $gym->city }}" class="input-style !p-2"></div>
                                                        <div class="space-y-1"><label class="text-[10px] font-bold text-gray-500 ml-1">Nyitvatartás</label><input type="text" name="opening_hours" value="{{ $gym->opening_hours }}" class="input-style !p-2"></div>
                                                    </div>
                                                    <div class="space-y-1"><label class="text-[10px] font-bold text-gray-500 ml-1">Cím</label><input type="text" name="address" value="{{ $gym->address }}" class="input-style !p-2"></div>
                                                    <div class="space-y-1"><label class="text-[10px] font-bold text-gray-500 ml-1">Kép módosítása</label><input type="file" name="image" class="text-[10px] text-gray-400 block w-full mt-1"></div>
                                                </div>

                                                {{-- Pénzügyi adatok szerkesztése --}}
                                                <div class="space-y-4">
                                                    <h4 class="text-xs font-black uppercase text-emerald-400 flex items-center gap-2 tracking-widest">
                                                        <i data-lucide="landmark" class="w-3 h-3"></i> Pénzügy & Számlázás
                                                    </h4>
                                                    <div class="space-y-1"><label class="text-[10px] font-bold text-gray-500 ml-1">Számlázási név</label><input type="text" name="billing_name" value="{{ $gym->billing_name }}" class="input-style !p-2"></div>
                                                    <div class="space-y-1"><label class="text-[10px] font-bold text-gray-500 ml-1">Számlázási cím</label><input type="text" name="billing_address" value="{{ $gym->billing_address }}" class="input-style !p-2"></div>
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div class="space-y-1"><label class="text-[10px] font-bold text-gray-500 ml-1">Adószám</label><input type="text" name="tax_number" value="{{ $gym->tax_number }}" class="input-style !p-2" placeholder="Kötelező a számlához"></div>
                                                        <div class="space-y-1"><label class="text-[10px] font-bold text-gray-500 ml-1">Kifizetés / scan (Ft)</label><input type="number" name="payout_per_scan" value="{{ $gym->payout_per_scan }}" class="input-style !p-2"></div>
                                                    </div>
                                                    <div class="pt-4 border-t border-gray-800">
                                                        <label class="text-[10px] font-bold text-gray-500 ml-1 uppercase">Tulajdonos</label>
                                                        <select name="owner_id" class="select2">
                                                            <option></option>
                                                            @if($gym->owner) <option value="{{ $gym->owner->id }}" selected>{{ $gym->owner->last_name }} {{ $gym->owner->first_name }}</option> @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Korábbi számlák listája --}}
                                            <div class="bg-black/30 p-4 rounded-2xl border border-gray-800">
                                                <h5 class="text-[10px] font-black uppercase text-gray-500 mb-4 flex items-center gap-2 tracking-widest">
                                                    <i data-lucide="history" class="w-3 h-3"></i> 
                                                    Számlák ({{ $selectedYear ?? now()->year }}. {{ Carbon\Carbon::create()->month($selectedMonth ?? now()->month)->translatedFormat('F') }})
                                                </h5>
                                                
                                                {{-- SZŰRÉS: Csak a kiválasztott év és hónap számláit jelenítjük meg --}}
                                                @php
                                                    $filteredInvoices = $gym->invoices->filter(function($invoice) use ($selectedMonth, $selectedYear) {
                                                        $issueDate = \Carbon\Carbon::parse($invoice->issue_date);
                                                        return $issueDate->month == ($selectedMonth ?? now()->month) && 
                                                               $issueDate->year == ($selectedYear ?? now()->year);
                                                    });
                                                @endphp

                                                @forelse($filteredInvoices as $invoice)
                                                    <div class="flex justify-between items-center py-2 border-b border-white/5 last:border-0">
                                                        <div class="text-xs">
                                                            @if($invoice->pdf_url)
                                                                <a href="{{ $invoice->pdf_url }}" target="_blank" class="text-indigo-400 font-bold hover:underline hover:text-indigo-300 transition-colors">
                                                                    {{ $invoice->invoice_number }}
                                                                </a>
                                                            @else
                                                                <span class="text-gray-300 font-bold cursor-help" title="Emailben kiküldve (Nincs letöltési link)">{{ $invoice->invoice_number }}</span>
                                                            @endif
                                                            <span class="text-gray-500 ml-2 font-mono">{{ $invoice->issue_date }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-4">
                                                            <span class="text-xs font-black text-emerald-400">{{ number_format($invoice->amount, 0, ',', ' ') }} Ft</span>
                                                            
                                                            @if($invoice->pdf_url)
                                                                <a href="{{ $invoice->pdf_url }}" target="_blank" class="p-1.5 hover:bg-gray-700 rounded-lg text-gray-400 transition-all" title="Letöltés">
                                                                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                                                </a>
                                                            @else
                                                                <span class="p-1.5 rounded-lg text-gray-600 cursor-not-allowed" title="Emailben kiküldve">
                                                                    <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @empty
                                                    <p class="text-[10px] italic text-gray-600">Ebben a hónapban nem történt elszámolás.</p>
                                                @endforelse
                                            </div>

                                            <div class="flex justify-end gap-3">
                                                <button type="button" onclick="toggleEdit('{{ $gym->id }}')" class="px-6 py-2 text-xs font-bold uppercase text-gray-500 hover:text-white">Mégse</button>
                                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 px-8 py-2 rounded-xl font-bold text-xs uppercase transition-all shadow-lg">Mentés</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Új Partner Kártya --}}
                <div class="glass-panel p-6 border-indigo-500/20">
                    <h2 class="text-lg font-bold flex items-center gap-3 mb-6">
                        <div class="p-1.5 bg-indigo-500/20 rounded-lg">
                            <i data-lucide="plus" class="text-indigo-400"></i>
                        </div> 
                        Új Partner Létrehozása
                    </h2>
                    <form method="POST" action="{{ route('admin.storeGym') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        {{-- Név --}}
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-500 ml-1 uppercase">Terem neve</label>
                            <input type="text" name="name" placeholder="Pl: Gold Gym Budapest" class="input-style" required>
                        </div>

                        {{-- Város és Kifizetés egy sorban --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-500 ml-1 uppercase">Város</label>
                                <input type="text" name="city" placeholder="Pl: Budapest" class="input-style" required>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-500 ml-1 uppercase">Kifizetés / Scan</label>
                                <input type="number" name="payout_per_scan" value="1000" placeholder="Ft" class="input-style" required>
                            </div>
                        </div>

                        {{-- Cím --}}
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-500 ml-1 uppercase">Pontos Cím</label>
                            <input type="text" name="address" placeholder="Pl: 1052 Petőfi Sándor u. 12." class="input-style" required>
                        </div>

                        {{-- Nyitvatartás  --}}
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-500 ml-1 uppercase">Nyitvatartás</label>
                            <input type="text" name="opening_hours" placeholder="Pl: H-P: 06:00-22:00, Szo-V: 08:00-20:00" class="input-style">
                        </div>

                        {{-- Képfeltöltés --}}
                        <div class="space-y-1 pt-2">
                            <label class="text-[10px] font-bold text-gray-500 ml-1 uppercase">Borítókép</label>
                            <input type="file" name="image" class="text-xs text-gray-400 block w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-500/10 file:text-indigo-400 hover:file:bg-indigo-500/20 cursor-pointer">
                        </div>

                        <button class="w-full bg-indigo-600 hover:bg-indigo-500 py-3 rounded-xl font-bold transition-all uppercase tracking-widest text-xs shadow-lg mt-4">
                            Partner Mentése
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="glass-panel p-6 border-purple-500/20 mt-8 mb-8">
            <h2 class="text-lg font-bold flex items-center gap-3 mb-4">
                <div class="p-1.5 bg-purple-500/20 rounded-lg">
                    <i data-lucide="zap" class="text-purple-400 w-5 h-5"></i>
                </div>
                Manuális Karbantartás
            </h2>
            <p class="text-sm text-gray-400 mb-6">
                A cron job futtatásával minden lejárt diákigazolvány törlődik.
            </p>
            
            <form action="{{ route('admin.triggerExpirationCheck') }}" method="POST" onsubmit="return confirm('Biztosan lefuttatod a karbantartót most?');">
                @csrf
                <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white px-8 py-3 rounded-xl font-bold transition-all uppercase tracking-widest text-xs shadow-[0_0_20px_rgba(147,51,234,0.3)] hover:shadow-[0_0_30px_rgba(147,51,234,0.5)] flex items-center gap-2">
                    <i data-lucide="play-circle" class="w-4 h-4"></i>
                    Éjszakai folyamat azonnali futtatása
                </button>
            </form>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    lucide.createIcons();

    // --- ÉRTESÍTÉSEK KEZELÉSE ---
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

    function toggleEdit(id) {
        const row = document.getElementById('edit-gym-' + id);
        if (row.style.display === 'table-row') {
            row.style.display = 'none';
        } else {
            document.querySelectorAll('.edit-row').forEach(r => r.style.display = 'none');
            row.style.display = 'table-row';
            initSelect2();
        }
    }

    const rawData = @json(array_values($monthlyNewUsers));
    const currentMonthIndex = {{ now()->month }} - 1;
    
    const chartData = rawData.map((val, index) => index <= currentMonthIndex ? val : null);

    const ctx = document.getElementById('usersChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Már', 'Ápr', 'Máj', 'Jún', 'Júl', 'Aug', 'Szep', 'Okt', 'Nov', 'Dec'],
            datasets: [{
                label: 'Új regisztrációk',
                data: chartData,
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                spanGaps: false
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            plugins: { legend: { display: false } } 
        }
    });

    function initSelect2() {
        $('.select2').select2({
            width: '100%',
            dropdownParent: $('.edit-row:visible'),
            ajax: {
                url: "{{ route('admin.users.select2') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) { return { q: params.term }; },
                processResults: function(data) { return { results: data }; }
            }
        });
    }
    
    $(document).ready(function() {
        initSelect2();
    });

    let searchTimeout;
    $('#searchInput').on('keyup', function(){
        clearTimeout(searchTimeout);
        let query = $(this).val();
        searchTimeout = setTimeout(function(){
            $.ajax({
                url: "{{ route('admin.users.search') }}",
                type: "GET",
                data: { q: query },
                success: function(data){
                    $('#usersTable').html(data);
                    lucide.createIcons();
                }
            });
        }, 300);
    });
</script>
@endsection