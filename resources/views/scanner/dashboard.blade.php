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
        <div id="user-info"
             class="hidden bg-gray-850 p-6 rounded-2xl shadow space-y-3 transition-all duration-700 border border-transparent">
            <h2 class="text-xl font-semibold text-indigo-400">Felhasználó adatai</h2>
            <p><strong>Név:</strong> <span id="user-name"></span></p>
            <p><strong>Diákigazolvány státusz:</strong> <span id="user-student"></span></p>
            <p><strong>Alkalmak száma:</strong> <span id="user-uses"></span></p>

            <div id="student-card-images" class="flex gap-4 mt-4 flex-wrap"></div>

            <button id="deduct-use"
                    class="bg-red-600 px-4 py-2 rounded hover:bg-red-500 transition-all">
                Alkalom levonása
            </button>
        </div>

    </div>
</section>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
const qrCodeSuccessCallback = (decodedText) => {
    updateUserUI(decodedText);
};

// Fő függvény: QR beolvasás és UI frissítés
function updateUserUI(qrText, deduct = false) {
    fetch("{{ route('scanner.scan') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ qr_code: qrText, deduct: deduct })
    })
    .then(res => {
        if (!res.ok) throw new Error("HTTP error " + res.status);
        return res.json();
    })
    .then(data => {
        console.log("Backend válasz:", data);

        const userInfo = document.getElementById('user-info');
        const userName = document.getElementById('user-name');
        const userStudent = document.getElementById('user-student');
        const userUses = document.getElementById('user-uses');
        const studentCardContainer = document.getElementById('student-card-images');
        const deductBtn = document.getElementById('deduct-use');

        // --- Alaphelyzet ---
        userInfo.classList.remove('border-green-500', 'border-red-500', 'bg-green-900/30', 'bg-red-900/30');
        userInfo.style.borderWidth = '0px';
        studentCardContainer.innerHTML = '';
        deductBtn.style.display = 'none';

        if (!data.user) {
            userInfo.classList.add('border-red-500', 'bg-red-900/30');
            userInfo.style.borderWidth = '2px';
            userInfo.classList.remove('hidden');
            userName.textContent = data.message || 'Felhasználó nem található.';
            userStudent.textContent = '❌ Hiba';
            userUses.textContent = 'N/A';
            alert(data.message || 'Ismeretlen hiba.');
            return;
        }

        // ✅ Felhasználói adatok frissítése
        const user = data.user;
        userInfo.classList.remove('hidden');
        userName.textContent = `${user.first_name} ${user.last_name}`;
        userStudent.textContent = user.student_id_verified ? '✅ Elfogadva' : '❌ Nincs elfogadva';
        userStudent.style.color = user.student_id_verified ? 'lightgreen' : 'red';
        userUses.textContent = user.remaining_uses;

        // Diákigazolvány képek
        if (user.student_card_front) {
            const frontImg = document.createElement('img');
            frontImg.src = user.student_card_front;
            frontImg.alt = "Diákigazolvány eleje";
            frontImg.className = "w-40 h-24 object-cover rounded-lg border border-gray-700";
            studentCardContainer.appendChild(frontImg);
        }
        if (user.student_card_back) {
            const backImg = document.createElement('img');
            backImg.src = user.student_card_back;
            backImg.alt = "Diákigazolvány hátulja";
            backImg.className = "w-40 h-24 object-cover rounded-lg border border-gray-700";
            studentCardContainer.appendChild(backImg);
        }

        // --- Állapot logika ---
        switch (data.status) {
            case 'deducted':
                // ✅ Sikeres levonás: zöld keret és eltűnik a gomb
                userInfo.classList.add('border-green-500', 'bg-green-900/30');
                userInfo.style.borderWidth = '2px';
                deductBtn.style.display = 'none';

                // 2 másodperc után eltűnik a zöld jelzés
                setTimeout(() => {
                    userInfo.classList.remove('border-green-500', 'bg-green-900/30');
                    userInfo.style.borderWidth = '0px';
                }, 2000);
                break;

            case 'no_uses':
                // ❌ Nincs több alkalom → piros jelzés
                userInfo.classList.add('border-red-500', 'bg-red-900/30');
                userInfo.style.borderWidth = '2px';
                deductBtn.style.display = 'none';
                break;

            case 'scanned':
            case 'deducted': // frissített adatoknál is gomb aktiválás
                // ℹ️ Csak beolvasás → gomb aktív
                deductBtn.style.display = 'inline-block';
                deductBtn.disabled = false;
                deductBtn.textContent = 'Alkalom levonása';

                // Esemény beállítása
                deductBtn.onclick = () => {
                    deductBtn.disabled = true;
                    deductBtn.textContent = 'Levonás...';
                    updateUserUI(qrText, true); // levonás
                };
                break;

            default:
                console.warn("Ismeretlen státusz:", data.status);
        }

    })
    .catch(err => {
        console.error("Hiba:", err);
        alert('A kapcsolat a szerverrel megszakadt.');
    });
}

// QR scanner inicializálása
let html5QrcodeScanner = new Html5QrcodeScanner(
    "qr-reader", { fps: 10, qrbox: 250 }
);
html5QrcodeScanner.render(qrCodeSuccessCallback);
</script>

@endsection
