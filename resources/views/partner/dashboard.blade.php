@extends('layouts.app')

@section('title', 'Partner Dashboard - RoamPass')

@section('content')
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    .glass-panel {
        background: rgba(10, 12, 30, 0.7);
        border: 1px solid rgba(191, 64, 255, 0.2);
        backdrop-filter: blur(12px);
        border-radius: 1rem;
        transition: all 0.3s ease;
    }

    .stat-card {
        @apply glass-panel p-6 relative overflow-hidden;
    }
    .stat-card:hover {
        border-color: rgba(191, 64, 255, 0.6);
        box-shadow: 0 0 20px rgba(191, 64, 255, 0.15);
        transform: translateY(-2px);
    }

    .input-style {
        background-color: rgba(0, 0, 0, 0.5) !important;
        color: #ffffff !important;
        border: 1px solid rgba(191, 64, 255, 0.3) !important;
        @apply w-full p-3 rounded-xl text-sm placeholder-gray-500 outline-none transition-all duration-200;
    }

    .input-style:focus {
        border-color: #bf40ff !important;
        box-shadow: 0 0 15px rgba(191, 64, 255, 0.3);
    }

    .text-neon {
        color: #bf40ff;
        text-shadow: 0 0 10px rgba(191, 64, 255, 0.5);
    }
</style>

<div class="fixed inset-0 z-[-1] bg-cover bg-center" style="background-image: url('{{ asset('images/gym-bg2.png') }}'); filter: brightness(0.2);"></div>

<section class="py-8 min-h-screen text-white">
    <div class="max-w-7xl mx-auto px-4 animate-fade-up">

        {{-- Üzenetek (Automatikus eltűnéssel) --}}
        <div id="alert-container" class="fixed top-6 left-1/2 -translate-x-1/2 z-[100] w-full max-w-lg px-4">
            @if(session('success'))
                <div id="success-alert" class="glass-panel p-4 border-emerald-500/30 bg-emerald-500/10 text-emerald-400 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500 mb-3">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div id="error-alert" class="glass-panel p-4 border-red-500/30 bg-red-500/10 text-red-400 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    <span class="text-sm font-bold">{{ session('error') }}</span>
                </div>
            @endif
        </div>

        {{-- Fejléc & IDŐSZAK VÁLASZTÓ --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-10 gap-6 glass-panel p-6 border-purple-500/30">
            <div>
                <h1 class="text-3xl font-black text-white flex items-center gap-4 uppercase tracking-tight">
                    <span class="p-3 bg-purple-600/20 border border-purple-500 rounded-xl text-white shadow-[0_0_15px_rgba(191,64,255,0.3)]">
                        <i data-lucide="layout-dashboard" class="w-6 h-6"></i>
                    </span>
                    <span>{{ $gym->name }} <span class="text-neon">Partner</span></span>
                </h1>
            </div>

            {{-- Választó form --}}
            <form action="{{ route('partner.dashboard') }}" method="GET" class="flex items-center gap-2 bg-black/40 p-2 rounded-2xl border border-white/5">
                <select name="year" class="input-style !p-2 !text-xs !w-24">
                    @foreach(range(now()->year, now()->year - 1) as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <select name="month" class="input-style !p-2 !text-xs !w-32">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-purple-600 p-2.5 rounded-xl hover:bg-purple-500 transition-all shrink-0">
                    <i data-lucide="refresh-cw" class="w-4 h-4 text-white"></i>
                </button>
            </form>
        </div>

        {{-- Statisztikai Kártyák --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="stat-card group">
                <div class="flex justify-between items-start relative z-10">
                    <h2 class="text-gray-400 text-[10px] uppercase font-black tracking-[0.2em]">Havi beolvasás</h2>
                    <i data-lucide="scan" class="w-5 h-5 text-neon"></i>
                </div>
                <p class="text-4xl font-black text-white mt-4 tracking-tight">{{ $stats['monthly_scans_count'] }}</p>
                <div class="text-[9px] text-gray-500 uppercase mt-2 font-bold tracking-widest">{{ Carbon\Carbon::create()->month($selectedMonth)->translatedFormat('F') }}</div>
            </div>

            <div class="stat-card group">
                <div class="flex justify-between items-start relative z-10">
                    <h2 class="text-gray-400 text-[10px] uppercase font-black tracking-[0.2em]">Havi bevétel</h2>
                    <i data-lucide="trending-up" class="w-5 h-5 text-green-400"></i>
                </div>
                <p class="text-4xl font-black text-white mt-4 tracking-tight">
                    {{ number_format($stats['monthly_revenue'], 0, ',', ' ') }} <span class="text-sm text-gray-500">Ft</span>
                </p>
                <div class="text-[9px] text-emerald-500 uppercase mt-2 font-bold tracking-widest">Választott időszak</div>
            </div>

            <div class="stat-card group">
                <div class="flex justify-between items-start relative z-10">
                    <h2 class="text-gray-400 text-[10px] uppercase font-black tracking-[0.2em]">Összes beolvasás</h2>
                    <i data-lucide="database" class="w-5 h-5 text-blue-400"></i>
                </div>
                <p class="text-4xl font-black text-white mt-4 tracking-tight">{{ $stats['total_scans'] }}</p>
            </div>

            <div class="stat-card group">
                <div class="flex justify-between items-start relative z-10">
                    <h2 class="text-gray-400 text-[10px] uppercase font-black tracking-[0.2em]">Utolsó aktivitás</h2>
                    <i data-lucide="clock" class="w-5 h-5 text-orange-400"></i>
                </div>
                <div class="mt-4">
                    <p class="text-2xl font-black text-white tracking-tight">{{ $lastScan ? \Carbon\Carbon::parse($lastScan->scanned_at)->format('H:i') : '--:--' }}</p>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ $lastScan ? \Carbon\Carbon::parse($lastScan->scanned_at)->format('Y.m.d') : '' }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-8">
            {{-- Grafikon (8 oszlop) --}}
            <div class="col-span-12 lg:col-span-8">
                <div class="glass-panel p-8 h-full">
                    <h3 class="text-xs font-black mb-8 flex items-center gap-3 uppercase tracking-[0.2em] text-purple-300">
                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Napi forgalom (Elmúlt 30 nap)
                    </h3>
                    <div class="h-[300px] w-full"><canvas id="dailyScansChart"></canvas></div>
                </div>
            </div>

            {{-- SZÁMLÁK / INVOICES (4 oszlop) --}}
            <div class="col-span-12 lg:col-span-4">
                <div class="glass-panel p-8 h-full flex flex-col">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xs font-black flex items-center gap-3 uppercase tracking-[0.2em] text-neon">
                            <i data-lucide="file-text" class="w-4 h-4"></i> Számlák
                        </h3>
                        <span class="text-[9px] font-bold text-gray-500 uppercase tracking-widest bg-black/30 px-2 py-1 rounded">
                            {{ $selectedYear }}. {{ Carbon\Carbon::create()->month($selectedMonth)->translatedFormat('F') }}
                        </span>
                    </div>
                    
                    <div class="space-y-3 overflow-y-auto max-h-[350px] pr-2 custom-scrollbar">
                        {{-- BLADE SZŰRÉS: Itt szűrjük ki csak azokat, amik a választott évre/hónapra esnek --}}
                        @php
                            $filteredInvoices = collect($invoices)->filter(function($invoice) use ($selectedYear, $selectedMonth) {
                                $date = \Carbon\Carbon::parse($invoice->issue_date ?? $invoice->created_at);
                                return $date->year == $selectedYear && $date->month == $selectedMonth;
                            });
                        @endphp

                        @forelse($filteredInvoices as $invoice)
                            <div class="bg-black/40 border border-white/5 p-4 rounded-xl flex justify-between items-center group hover:border-purple-500/30 transition-all">
                                <div>
                                    <p class="text-xs font-black text-white tracking-wide">{{ $invoice->invoice_number }}</p>
                                    <p class="text-[10px] text-gray-500 font-bold uppercase">
                                        {{ \Carbon\Carbon::parse($invoice->issue_date ?? $invoice->created_at)->format('Y.m.d') }}
                                    </p>
                                </div>
                                <div class="text-right flex items-center gap-4">
                                    <p class="text-sm font-black text-emerald-400">{{ number_format($invoice->amount, 0, ',', ' ') }} Ft</p>
                                    
                                    @if($invoice->pdf_url)
                                        <a href="{{ $invoice->pdf_url }}" target="_blank" class="p-2 bg-purple-600/10 text-purple-400 rounded-lg hover:bg-purple-600 hover:text-white transition-all" title="Számla letöltése">
                                            <i data-lucide="download" class="w-4 h-4"></i>
                                        </a>
                                    @else
                                        <span class="p-2 bg-gray-800/30 text-gray-600 rounded-lg cursor-not-allowed" title="Emailben kiküldve">
                                            <i data-lucide="mail" class="w-4 h-4"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 opacity-30">
                                <i data-lucide="folder-off" class="w-8 h-8 mx-auto mb-2"></i>
                                <p class="text-xs uppercase font-bold tracking-widest">Nincs számla ebben a hónapban</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Scanner Létrehozás (4 oszlop) --}}
            <div class="col-span-12 lg:col-span-4">
                <div class="glass-panel p-8">
                    <h3 class="text-xs font-black mb-6 flex items-center gap-3 uppercase tracking-[0.2em] text-neon">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i> Új Scanner Profil
                    </h3>
                    <form method="POST" action="{{ route('partner.scanner.store') }}" class="space-y-4">
                        @csrf
                        <input type="text" name="scanner_name" placeholder="Helyszín neve (pl. Kapu 1)" class="input-style" required>
                        <div class="grid grid-cols-2 gap-3">
                            <input type="text" name="last_name" placeholder="Vezetéknév" class="input-style" required>
                            <input type="text" name="first_name" placeholder="Keresztnév" class="input-style" required>
                        </div>
                        <input type="email" name="email" placeholder="Email cím" class="input-style" required>
                        <div class="grid grid-cols-2 gap-3">
                            <input type="password" name="password" placeholder="Jelszó" class="input-style" required>
                            <input type="password" name="password_confirmation" placeholder="Megerősítés" class="input-style" required>
                        </div>
                        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-500 text-white py-4 rounded-xl text-xs font-black transition-all uppercase tracking-widest mt-2">
                            Létrehozás
                        </button>
                    </form>
                </div>
            </div>

            {{-- Scanner Lista (8 oszlop) --}}
            <div class="col-span-12 lg:col-span-8">
                <div class="glass-panel p-8">
                    <h3 class="text-xs font-black mb-6 flex items-center gap-3 uppercase tracking-[0.2em] text-gray-300">
                        <i data-lucide="users" class="w-4 h-4"></i> Aktív Scannerek
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($scanners as $scanner)
                            <div class="bg-black/40 border border-white/5 p-4 rounded-2xl flex justify-between items-center group hover:border-purple-500/40 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-purple-900/20 rounded-lg flex items-center justify-center text-neon border border-purple-500/20"><i data-lucide="user" class="w-5 h-5"></i></div>
                                    @php $scannerUser = \App\Models\User::find($scanner->user_id); @endphp
                                    <div><p class="text-sm font-black text-white uppercase">{{ $scanner->name }}</p><p class="text-[10px] font-bold text-gray-500">{{ $scannerUser->email ?? '' }}</p></div>
                                </div>
                                <form method="POST" action="{{ route('partner.scanner.destroy', $scanner) }}" onsubmit="return confirm('Biztosan törlöd?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-gray-600 hover:text-red-500 transition-all"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    lucide.createIcons();

    document.addEventListener('DOMContentLoaded', function() {
        const alerts = ['success-alert', 'error-alert'];
        alerts.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                setTimeout(() => {
                    el.style.transition = 'all 0.5s ease';
                    el.style.opacity = '0';
                    el.style.transform = 'translate(-50%, -20px)';
                    setTimeout(() => el.remove(), 500);
                }, 4000);
            }
        });
    });

    window.onload = function() {
        const ctx = document.getElementById('dailyScansChart').getContext('2d');
        const labels = {!! json_encode($dailyScans->pluck('date')) !!};
        const data = {!! json_encode($dailyScans->pluck('total')) !!};
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(191, 64, 255, 0.4)');
        gradient.addColorStop(1, 'rgba(191, 64, 255, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Beolvasások',
                    data: data,
                    borderColor: '#bf40ff',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#666' } },
                    x: { grid: { display: false }, ticks: { color: '#666' } }
                }
            }
        });
    };
</script>
@endsection