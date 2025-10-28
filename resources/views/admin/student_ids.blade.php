@extends('layouts.app')

@section('title', 'Diákigazolványok ellenőrzése - Admin')

@section('content')
<section class="py-16 bg-gray-950 text-white min-h-screen">
    <div class="max-w-6xl mx-auto px-6">
        <h1 class="text-4xl font-bold mb-8 text-blue-400">🎓 Diákigazolványok ellenőrzése</h1>

        @if(session('success'))
            <div class="bg-green-700/40 text-green-200 py-3 px-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid md:grid-cols-2 gap-8">
            @foreach($users as $user)
                @if(!$user->student_id_verified && $user->student_card_front && $user->student_card_back)
                    <div class="bg-gray-850 rounded-3xl shadow-lg p-6 space-y-4">
                        <h2 class="text-xl font-semibold text-indigo-400">{{ $user->name }} ({{ $user->email }})</h2>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-300 mb-2 font-semibold">Elülső oldal</p>
                                <img src="{{ asset('storage/'.$user->student_card_front) }}" alt="Elülső oldal" class="rounded-lg shadow-lg w-full object-cover max-h-64">
                            </div>
                            <div>
                                <p class="text-gray-300 mb-2 font-semibold">Hátoldal</p>
                                <img src="{{ asset('storage/'.$user->student_card_back) }}" alt="Hátoldal" class="rounded-lg shadow-lg w-full object-cover max-h-64">
                            </div>
                        </div>

                        <!-- Elfogadás / lejárat beállítás -->
                        <form method="POST" action="{{ route('admin.verifyStudent', $user) }}" class="mt-4 space-y-2">
                            @csrf
                            <label class="block text-gray-300 mb-1">Lejárat dátuma</label>
                            <input type="date" name="expiry_date" class="w-full p-2 rounded-lg bg-gray-900 text-white" required>

                            <button type="submit" class="bg-green-600 px-4 py-2 rounded-full hover:bg-green-500 transition-all w-full">
                                ✅ Elfogadás
                            </button>
                        </form>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endsection
