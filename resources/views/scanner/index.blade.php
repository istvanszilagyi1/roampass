@extends('layouts.app')

@section('title', 'Scanner - RoamPass')

@section('content')
<section class="py-12 bg-gray-950 text-white min-h-screen">
    <div class="max-w-2xl mx-auto px-6">
        <h1 class="text-3xl font-bold mb-6 text-blue-400">📷 Scanner</h1>

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

        <form method="POST" action="{{ route('scanner.scan') }}" class="space-y-4 bg-gray-850 p-6 rounded-2xl shadow">
            @csrf
            <label class="block mb-2">Felhasználó QR kód beolvasása (ID)</label>
            <input type="text" name="user_id" class="w-full p-3 rounded bg-gray-900 text-white" placeholder="User ID">

            <button type="submit" class="mt-2 bg-indigo-600 px-6 py-3 rounded-full hover:bg-indigo-500 transition-all">
                Beolvasás
            </button>
        </form>

        @if(isset($user))
            <div class="mt-6 bg-gray-800 p-4 rounded-lg">
                <p><strong>Név:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
                <p><strong>Diákigazolvány:</strong>
                    @if($user->hasValidStudentCard())
                        Érvényes
                    @else
                        Nem érvényes
                    @endif
                </p>
                <p><strong>Hány alkalom maradt:</strong> {{ $user->gymPasses->first()->remaining_uses ?? 'Nincs bérlet' }}</p>
            </div>
        @endif
    </div>
</section>
@endsection
