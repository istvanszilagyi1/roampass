@extends('layouts.app')

@section('title', 'Profilom - RoamPass')

@section('content')
<script src="https://unpkg.com/lucide@latest"></script>

<style>
.bg-gray-850 { background-color: #1f1f1f; }
.input-focus-style {
    @apply w-full bg-gray-900 border border-gray-700 text-white p-3 rounded-lg transition duration-300
    focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:bg-gray-800 outline-none;
}
.label-style {
    @apply block text-gray-300 font-medium mb-1;
}

/* Pulzáló árnyék a Függőben lévő státuszhoz */
@keyframes pulse-shadow {
  0% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.4); } /* Sárga/Borostyán */
  70% { box-shadow: 0 0 0 10px rgba(251, 191, 36, 0); }
  100% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0); }
}
.shadow-pulse-amber {
  animation: pulse-shadow 2s infinite;
}
</style>

<section class="py-24 bg-gradient-to-br from-gray-950 via-gray-900 to-black text-white min-h-screen">
    <div class="max-w-4xl mx-auto px-6">

        <h1 class="text-5xl font-extrabold mb-12 text-center text-blue-400 drop-shadow-lg flex items-center justify-center gap-3">
            <i data-lucide="user" class="w-8 h-8"></i> A Te RoamPass Profilod
        </h1>

        {{-- Üzenetek Szekció --}}
        <div class="mb-10 space-y-4">
            @if(session('success'))
                <div class="bg-green-700/40 text-green-200 py-4 px-5 rounded-xl text-center font-medium shadow-md">
                    <i data-lucide="check-circle" class="w-5 h-5 inline mr-2"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-700/40 text-red-200 py-4 px-5 rounded-xl text-center font-medium shadow-md">
                    <i data-lucide="alert-triangle" class="w-5 h-5 inline mr-2"></i> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-700/40 text-red-200 py-4 px-5 rounded-xl font-medium shadow-md text-left">
                    <i data-lucide="x-circle" class="w-5 h-5 inline mr-2"></i> <strong>Hiba történt:</strong>
                    <ul class="list-disc list-inside mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Diákigazolvány Státusz Jelző - Kiemelve, Színesen, Árnyékkal --}}
        @php
            $statusClasses = '';
            $statusText = '';
            $statusIcon = '';

            if($user->student_id_verified) {
                $statusClasses = 'border-green-500 bg-green-700/20 text-green-300 shadow-xl shadow-green-900/50';
                $statusIcon = '<i data-lucide="graduation-cap" class="w-7 h-7 inline mr-3"></i>';
                $statusText = 'Diákigazolvány elfogadva. (Lejárat: ' . \Carbon\Carbon::parse($user->student_id_expiry)->format('Y.m.d') . ')';
            } elseif(!$user->student_card_front || !$user->student_card_back) {
                $statusClasses = 'border-red-500 bg-red-700/20 text-red-300 shadow-xl shadow-red-900/50';
                $statusIcon = '<i data-lucide="alert-octagon" class="w-7 h-7 inline mr-3"></i>';
                $statusText = 'Nincs feltöltve: Kérjük, töltse fel a diákigazolvány mindkét oldalát!';
            } else {
                $statusClasses = 'border-yellow-500 bg-gray-700/30 text-yellow-300 shadow-pulse-amber shadow-xl';
                $statusIcon = '<i data-lucide="hourglass" class="w-7 h-7 inline mr-3 animate-pulse"></i>';
                $statusText = 'Ellenőrzés alatt: A diákigazolvány adatai adminisztrátori jóváhagyásra várnak.';
            }
        @endphp

        <div class="p-8 rounded-2xl mb-12 text-center font-semibold text-xl border-l-4 {{ $statusClasses }} transition duration-500">
            {!! $statusIcon !!} {!! $statusText !!}
        </div>


        {{-- Fő Profil Konténer --}}
        <div class="bg-gray-850 p-10 rounded-3xl shadow-2xl shadow-black/50 border border-gray-700 space-y-12">

            <div class="pb-6 border-b border-gray-700">
                <h2 class="text-3xl font-bold text-blue-400 mb-6 flex items-center gap-3">
                    <i data-lucide="info" class="w-6 h-6"></i> Személyes adatok
                </h2>

                <div class="grid md:grid-cols-2 gap-x-8 gap-y-6 text-left">
                    @php
                        $profileData = [
                            'Felhasználói azonosító' => $user->id,
                            'Email' => $user->email,
                            'Vezetéknév' => $user->last_name,
                            'Keresztnév' => $user->first_name,
                            'Regisztráció dátuma' => $user->created_at->format('Y.m.d'),
                        ];
                    @endphp

                    @foreach($profileData as $label => $value)
                    <div>
                        <span class="label-style text-gray-400">{{ $label }}:</span>
                        <p class="text-white font-semibold text-lg">{{ $value }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="pb-6 border-b border-gray-700">
                <h2 class="text-3xl font-bold text-blue-400 mb-6 flex items-center gap-3">
                    <i data-lucide="ticket" class="w-6 h-6"></i> Aktív Bérletek
                </h2>

                @if($user->gymPasses->count())
                    <div class="space-y-4">
                        @foreach($user->gymPasses as $pass)
                            <div class="bg-gray-900 p-4 rounded-xl border border-blue-700/50 flex justify-between items-center shadow-md">
                                <div>
                                    <p class="text-lg font-bold text-white">RoamPass Bérlet</p>
                                    <p class="text-sm text-gray-400">Vásárlás: {{ \Carbon\Carbon::parse($pass->purchase_date)->format('Y.m.d') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-extrabold text-green-400">{{ $pass->remaining_uses }}/12</p>
                                    <p class="text-sm text-gray-400">Hátralévő alkalmak</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-900/50 p-5 rounded-xl text-center text-gray-400 border border-gray-700">
                        <i data-lucide="x-circle" class="w-5 h-5 inline mr-2"></i> Jelenleg nincs aktív RoamPass bérleted.
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="pt-6 space-y-6">
                @csrf
                <h2 class="text-3xl font-bold text-blue-400 mb-6 flex items-center gap-3">
                    <i data-lucide="upload" class="w-6 h-6"></i> Diákigazolvány feltöltése
                </h2>
                <p class="text-gray-400 mb-4">Mindkét oldal feltöltése szükséges a diákigazolványos ár érvényesítéséhez.</p>

                <div class="grid md:grid-cols-2 gap-8 text-left">
                    <div>
                        <label for="student_card_front" class="label-style">Elülső oldal (max. 5MB)</label>
                        <input type="file" name="student_card_front" id="student_card_front" class="input-focus-style text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 transition cursor-pointer">
                        @if($user->student_card_front)
                            <p class="text-xs text-gray-500 mt-2">Aktuális fájl: Feltöltve.</p>
                            <a href="{{ asset('storage/'.$user->student_card_front) }}" target="_blank">
                                <img src="{{ asset('storage/'.$user->student_card_front) }}" alt="Elülső oldal" class="mt-4 rounded-xl shadow-lg w-full max-h-48 object-cover border border-gray-700 transition hover:opacity-80">
                            </a>
                        @endif
                    </div>

                    <div>
                        <label for="student_card_back" class="label-style">Hátoldal (max. 5 MB)</label>
                        <input type="file" name="student_card_back" id="student_card_back" class="input-focus-style text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 transition cursor-pointer">
                        @if($user->student_card_back)
                            <p class="text-xs text-gray-500 mt-2">Aktuális fájl: Feltöltve.</p>
                             <a href="{{ asset('storage/'.$user->student_card_back) }}" target="_blank">
                                <img src="{{ asset('storage/'.$user->student_card_back) }}" alt="Hátoldal" class="mt-4 rounded-xl shadow-lg w-full max-h-48 object-cover border border-gray-700 transition hover:opacity-80">
                            </a>
                        @endif
                    </div>
                </div>

                <button type="submit" class="mt-4 bg-indigo-600 text-white px-8 py-3 rounded-full font-semibold shadow-lg hover:bg-indigo-500 transition-all">
                    <i data-lucide="save" class="w-5 h-5 inline mr-2"></i> Feltöltés / Mentés
                </button>
            </form>

            <form method="POST" action="{{ route('profile.updatePassword') }}" class="pt-6 border-t border-gray-700 space-y-6">
            @csrf
            <h2 class="text-3xl font-bold text-blue-400 mb-6 flex items-center gap-3">
                <i data-lucide="lock" class="w-6 h-6"></i> Jelszó módosítása
            </h2>

            <div>
                <label for="current_password" class="label-style">Régi jelszó</label>
                <input type="password" name="current_password" id="current_password"
                    class="w-full bg-gray-900 border border-gray-700 text-white p-3 rounded-lg transition duration-300
                            focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:bg-gray-800 outline-none"
                    required>
            </div>
            <div>
                <label for="password" class="label-style">Új jelszó</label>
                <input type="password" name="password" id="password"
                    class="w-full bg-gray-900 border border-gray-700 text-white p-3 rounded-lg transition duration-300
                            focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:bg-gray-800 outline-none"
                    required>
            </div>
            <div>
                <label for="password_confirmation" class="label-style">Jelszó újra</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full bg-gray-900 border border-gray-700 text-white p-3 rounded-lg transition duration-300
                            focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:bg-gray-800 outline-none"
                    required>
            </div>

            <button type="submit" class="mt-4 bg-indigo-600 text-white px-8 py-3 rounded-full font-semibold shadow-lg hover:bg-indigo-500 transition-all">
                <i data-lucide="key" class="w-5 h-5 inline mr-2"></i> Jelszó módosítása
            </button>
        </form>
        </div>

    </div>
</section>
<script>
    lucide.createIcons();
</script>
@endsection
