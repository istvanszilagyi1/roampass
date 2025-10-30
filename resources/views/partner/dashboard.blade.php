@extends('layouts.app')

@section('title', 'Partner Dashboard - RoamPass')

@section('content')
<section class="py-16 bg-gray-950 text-white min-h-screen">
    @if(session('success'))
            <div class="bg-green-700/40 text-green-200 py-3 px-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-700/40 text-red-200 py-3 px-4 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-700/40 text-red-200 py-3 px-4 rounded-lg mb-6">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
    @endif
    <div class="max-w-6xl mx-auto px-6">
        <h1 class="text-4xl font-bold mb-8 text-blue-400">
            🏋️‍♂️ {{ $gym->name }} - Partner Dashboard
        </h1>

        <div class="grid md:grid-cols-3 gap-6 mb-10">
            <div class="bg-gray-900 p-6 rounded-2xl shadow">
                <h2 class="text-gray-400 text-sm">Összes beolvasás</h2>
                <p class="text-3xl font-bold text-indigo-400 mt-2">{{ $stats['total_scans'] }}</p>
            </div>

            <div class="bg-gray-900 p-6 rounded-2xl shadow">
                <h2 class="text-gray-400 text-sm">Havi bevétel</h2>
                <p class="text-3xl font-bold text-green-400 mt-2">{{ number_format($stats['monthly_revenue'], 0, ',', ' ') }} Ft</p>
            </div>

            <div class="bg-gray-900 p-6 rounded-2xl shadow">
                <h2 class="text-gray-400 text-sm">Utolsó beolvasás</h2>
                <p class="text-3xl font-bold text-yellow-400 mt-2">
                    {{ $lastScan ? \Carbon\Carbon::parse($lastScan->scanned_at)->format('Y.m.d H:i') : 'Nincs adat' }}
                </p>
            </div>
        </div>

        <div class="bg-gray-900 p-6 rounded-2xl shadow">
            <h2 class="text-xl font-semibold text-indigo-400 mb-4">📊 Napi beolvasások az elmúlt 30 napban</h2>
            <canvas id="dailyScansChart"></canvas>
        </div>
        <!-- Scanner profilok kezelése -->
        <h2 class="text-2xl font-bold mb-4 mt-12">📋 Jelenlegi scanner profilok</h2>

        @if($scanners->isEmpty())
            <p class="text-gray-400 mb-6">Még nincs létrehozott scanner profil.</p>
        @else
            <div class="space-y-3 mb-10">
                @foreach($scanners as $scanner)
                    <div class="bg-gray-850 p-4 rounded-2xl flex justify-between items-center shadow">
                        <div>
                            <p class="text-lg font-semibold text-indigo-400">{{ $scanner->name }}</p>

                            {{-- 1. Eredeti $scanner->user megjelenítése --}}
                            @if($scanner->user)
                                <p class="text-gray-300 text-sm">
                                    👤 {{ $scanner->user->first_name }} {{ $scanner->user->last_name }}
                                    — 📧 {{ $scanner->user->email }}
                                </p>
                            @endif

                            <p class="text-gray-500 text-xs mt-1">
                                Létrehozva: {{ $scanner->created_at->format('Y.m.d. H:i') }}
                            </p>

                            {{-- 2. Keresd meg a felhasználót a $scanner->id alapján --}}
                            @php
                                // Ez a sor keresi meg az adatbázisban azt a User-t, akinek az ID-je megegyezik a $scanner->id-val
                                // Feltételezi, hogy a \App\Models\User vagy \App\User modell használatos
                                $targetUser = \App\Models\User::find($scanner->user_id);
                            @endphp

                            @if ($targetUser)
                                {{-- Ha megtalálta, jelenítsd meg az e-mail címét --}}
                                <p class="text-gray-500 text-xs mt-1">
                                    Email cím: {{ $targetUser->email }}
                                </p>
                                <p class="text-gray-500 text-xs mt-1">
                                    Vezetéknév: {{ $targetUser->last_name }}
                                </p>
                                <p class="text-gray-500 text-xs mt-1">
                                    Keresztnév: {{ $targetUser->first_name }}
                                </p>
                            @else
                                {{-- Ha nem talált felhasználót az ID alapján --}}
                                <p class="text-gray-500 text-xs mt-1">
                                    Email: {{ $scanner->email }} (Nincs User ID: {{ $scanner->id }} )
                                </p>
                            @endif
                        </div>
                    </div>

                        <form method="POST" action="{{ route('partner.scanner.destroy', $scanner) }}">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-600 px-3 py-1 rounded hover:bg-red-500 transition-all">
                                Törlés
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('partner.scanner.store') }}" class="bg-gray-850 p-6 rounded-2xl shadow max-w-lg mx-auto mb-12 space-y-4">
            @csrf
            <h2 class="text-xl font-semibold text-white">Új Scanner profil létrehozása</h2>

            <!-- Scanner profil neve (pl. "Bejárat 1") -->
            <input type="text" name="scanner_name" placeholder="Scanner profil neve" class="w-full p-2 rounded bg-gray-900 text-white" required>

            <!-- Scanner felhasználó adatai -->
            <input type="text" name="first_name" placeholder="Scanner keresztneve" class="w-full p-2 rounded bg-gray-900 text-white" required>
            <input type="text" name="last_name" placeholder="Scanner vezetékneve" class="w-full p-2 rounded bg-gray-900 text-white" required>
            <input type="email" name="email" placeholder="Scanner email címe" class="w-full p-2 rounded bg-gray-900 text-white" required>
            <input type="password" name="password" placeholder="Jelszó" class="w-full p-2 rounded bg-gray-900 text-white" required>
            <input type="password" name="password_confirmation" placeholder="Jelszó újra" class="w-full p-2 rounded bg-gray-900 text-white" required>

            <button type="submit" class="bg-green-600 px-4 py-2 rounded hover:bg-green-500">
                Új scanner profil létrehozása
            </button>
        </form>


    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('dailyScansChart').getContext('2d');

const labels = {!! json_encode($dailyScans->pluck('date')) !!};
const data = {!! json_encode($dailyScans->pluck('total')) !!};

const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Beolvasások száma',
            data: data,
            borderColor: '#4F46E5',
            backgroundColor: 'rgba(79,70,229,0.2)',
            tension: 0.3,
            fill: true,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1, // egész számok
                    color: '#ccc'
                }
            },
            x: { ticks: { color: '#ccc' } }
        },
        plugins: {
            legend: { labels: { color: '#fff' } },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.parsed.y + ' beolvasás';
                    }
                }
            }
        }
    }
});


</script>
@endsection
