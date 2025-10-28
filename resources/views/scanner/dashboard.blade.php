@extends('layouts.app')

@section('title', 'Scanner Dashboard - ' . $gym->name)

@section('content')
<section class="py-12 bg-gray-950 text-white min-h-screen">
    <div class="max-w-4xl mx-auto px-6">

        <h1 class="text-3xl font-bold mb-6 text-indigo-400">🎯 {{ $gym->name }} Scanner</h1>

        <!-- QR olvasó -->
        <div class="mb-8">
            <div id="qr-reader" class="w-full"></div>
            <p class="text-gray-400 mt-2">QR kód beolvasása a felhasználóhoz.</p>
        </div>

        <!-- Felhasználó adatai -->
        <div id="user-info" class="hidden bg-gray-850 p-6 rounded-2xl shadow space-y-3">
            <h2 class="text-xl font-semibold text-indigo-400">Felhasználó adatai</h2>
            <p><strong>Név:</strong> <span id="user-name"></span></p>
            <p><strong>Diákigazolvány:</strong> <span id="user-student"></span></p>
            <p><strong>Alkalmak száma:</strong> <span id="user-uses"></span></p>

            <button id="deduct-use" class="bg-red-600 px-4 py-2 rounded hover:bg-red-500 transition-all">
                Alkalom levonása
            </button>
        </div>

        <!-- Statisztika grafikon -->
        <div class="mt-12">
            <h2 class="text-xl font-semibold text-indigo-400 mb-4">Utolsó 30 nap beolvasásai</h2>
            <canvas id="scansChart"></canvas>
        </div>
    </div>
</section>

<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const qrCodeSuccessCallback = (decodedText, decodedResult) => {
    // POST request a szervernek
    fetch("{{ route('scanner.scan') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ qr_code: decodedText })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            document.getElementById('user-info').classList.remove('hidden');
            document.getElementById('user-name').textContent = data.user.first_name + ' ' + data.user.last_name;
            document.getElementById('user-student').textContent = data.user.student_id_verified ? 'Elfogadva' : 'Nincs';
            document.getElementById('user-uses').textContent = data.user.remaining_uses;

            // Alkalom levonás
            document.getElementById('deduct-use').onclick = () => {
                fetch("{{ route('scanner.scan') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ qr_code: decodedText, deduct: true })
                }).then(r => r.json()).then(resp => {
                    if(resp.success){
                        document.getElementById('user-uses').textContent = resp.user.remaining_uses;
                        alert('Alkalom levonva!');
                    }
                });
            };
        } else {
            alert(data.message);
        }
    });
};

// QR olvasó inicializálása
let html5QrcodeScanner = new Html5QrcodeScanner(
    "qr-reader", { fps: 10, qrbox: 250 }
);
html5QrcodeScanner.render(qrCodeSuccessCallback);

// Grafikon adatok
const ctx = document.getElementById('scansChart').getContext('2d');
const scansChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! $dailyScans->pluck('date') !!},
        datasets: [{
            label: 'Beolvasások',
            data: {!! $dailyScans->pluck('total') !!},
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99,102,241,0.3)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endsection
