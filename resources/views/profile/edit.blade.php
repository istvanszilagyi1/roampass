@extends('layouts.app')

@section('title', 'Profilom - RoamPass')

@section('content')
<section class="py-16 bg-gray-950 text-white min-h-screen">
    <div class="max-w-3xl mx-auto px-6 text-center">

        <h1 class="text-4xl font-bold mb-8 text-blue-400">👤 Profilom</h1>

        {{-- Üzenetek --}}
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

        @if($user->student_id_verified)
            <div class="bg-green-700/40 text-green-200 py-3 px-4 rounded-lg mb-6">
                ✅ Diákigazolvány elfogadva (Lejárat: {{ \Carbon\Carbon::parse($user->student_id_expiry)->format('Y.m.d') }})
            </div>
        @elseif($user->student_card_front || $user->student_card_back)
            <div class="bg-yellow-600/40 text-yellow-200 py-3 px-4 rounded-lg mb-6">
                ⚠️ Diákigazolvány ellenőrzés alatt
            </div>
        @else
            <div class="bg-red-700/40 text-red-200 py-3 px-4 rounded-lg mb-6">
                ❌ Nincs feltöltve diákigazolvány
            </div>
        @endif

        {{-- Profil adatok (csak olvasható) --}}
        <div class="bg-gray-850 p-8 rounded-3xl shadow-lg space-y-6 text-left">
            <div>
                <span class="block text-gray-400 mb-1">Felhasználói azonosító:</span>
                <p class="text-white font-semibold text-lg">{{ $user->id }}</p>
            </div>
            <div>
                <span class="block text-gray-400 mb-1">Vezetéknév:</span>
                <p class="text-white font-semibold text-lg">{{ $user->last_name }}</p>
            </div>

            <div>
                <span class="block text-gray-400 mb-1">Keresztnév:</span>
                <p class="text-white font-semibold text-lg">{{ $user->first_name }}</p>
            </div>

            <div>
                <span class="block text-gray-400 mb-1">Email:</span>
                <p class="text-white font-semibold text-lg">{{ $user->email }}</p>
            </div>

            <div>
                <span class="block text-gray-400 mb-1">Iskola típusa:</span>
                <p class="text-white font-semibold text-lg">{{ $user->student_type }}</p>
            </div>

            <div>
                <span class="block text-gray-400 mb-1">Regisztráció dátuma:</span>
                <p class="text-white font-semibold text-lg">{{ $user->created_at->format('Y.m.d') }}</p>
            </div>

            <div>
                <span class="block text-gray-400 mb-1">Bérletek:</span>
                @if($user->gymPasses->count())
                    @foreach($user->gymPasses as $pass)
                        <p class="text-white font-semibold">
                            Hátralévő alkalmak: {{ $pass->remaining_uses }}/12 (Vásárlás: {{ $pass->purchase_date }})
                        </p>
                    @endforeach
                @else
                    <p class="text-white font-semibold">Nincs vásárolt bérlet</p>
                @endif
            </div>

            {{-- Diákigazolvány feltöltés / megtekintés --}}
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="border-t border-gray-700 pt-6 space-y-4">
                @csrf
                <h2 class="text-xl font-semibold text-indigo-400 mb-4">🎓 Diákigazolvány</h2>
                <p class="text-gray-400 text-sm mb-4">Mindkét oldal feltöltése kötelező (elöl és hátul).</p>

                <div class="grid md:grid-cols-2 gap-6 text-left">
                    <div>
                        <label class="block text-gray-300 mb-2">Elülső oldal</label>
                        <input type="file" name="student_card_front" class="w-full bg-gray-900 text-white p-2 rounded-lg">
                        @if($user->student_card_front)
                            <img src="{{ asset('storage/'.$user->student_card_front) }}" alt="Elülső oldal" class="mt-3 rounded-lg shadow-lg w-full max-h-48 object-cover">
                        @endif
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2">Hátoldal</label>
                        <input type="file" name="student_card_back" class="w-full bg-gray-900 text-white p-2 rounded-lg">
                        @if($user->student_card_back)
                            <img src="{{ asset('storage/'.$user->student_card_back) }}" alt="Hátoldal" class="mt-3 rounded-lg shadow-lg w-full max-h-48 object-cover">
                        @endif
                    </div>
                </div>

                <button type="submit" class="mt-2 bg-indigo-600 px-6 py-3 rounded-full hover:bg-indigo-500 transition-all">
                    Feltöltés / Mentés
                </button>
            </form>

            {{-- Jelszó változtatás --}}
            <form method="POST" action="{{ route('profile.updatePassword') }}" class="border-t border-gray-700 pt-6 space-y-4">
                @csrf
                <h2 class="text-xl font-semibold text-indigo-400 mb-4">🔒 Jelszó módosítása</h2>

                <div>
                    <label class="block text-gray-300 mb-1">Régi jelszó</label>
                    <input type="password" name="current_password" class="w-full bg-gray-900 text-white p-3 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-gray-300 mb-1">Új jelszó</label>
                    <input type="password" name="password" class="w-full bg-gray-900 text-white p-3 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-gray-300 mb-1">Jelszó újra</label>
                    <input type="password" name="password_confirmation" class="w-full bg-gray-900 text-white p-3 rounded-lg" required>
                </div>

                <button type="submit" class="mt-2 bg-indigo-600 px-6 py-3 rounded-full hover:bg-indigo-500 transition-all">
                    Jelszó módosítása
                </button>
            </form>
        </div>

    </div>
</section>
@endsection
