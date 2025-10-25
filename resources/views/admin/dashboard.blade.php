@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<section class="py-12 bg-gray-950 text-white min-h-screen">
    <div class="max-w-6xl mx-auto px-6">

        <h1 class="text-4xl font-bold mb-8 text-blue-400">Admin Dashboard</h1>

        <!-- Statisztikák -->
        <div class="grid md:grid-cols-4 gap-6 mb-12">
            <div class="bg-gray-850 rounded-2xl p-6 shadow text-center">
                <p class="text-gray-400">Összes bérlet</p>
                <p class="text-3xl font-bold">{{ $totalPasses }}</p>
            </div>
            <div class="bg-gray-850 rounded-2xl p-6 shadow text-center">
                <p class="text-gray-400">Partnerek száma</p>
                <p class="text-3xl font-bold">{{ $totalGyms }}</p>
            </div>
            <div class="bg-gray-850 rounded-2xl p-6 shadow text-center">
                <p class="text-gray-400">Bevétel</p>
                <p class="text-3xl font-bold">{{ number_format($totalRevenue, 0, ',', ' ') }} Ft</p>
            </div>
            <div class="bg-gray-850 rounded-2xl p-6 shadow text-center">
                <p class="text-gray-400">Aktív felhasználók</p>
                <p class="text-3xl font-bold">{{ $activeUsers }}</p>
            </div>
        </div>

        <!-- Felhasználók kezelése -->
        <h2 class="text-2xl font-bold mb-4">Felhasználók</h2>
        <div class="overflow-x-auto mb-12">
            <table class="w-full text-left bg-gray-850 rounded-2xl shadow overflow-hidden">
                <thead class="bg-gray-800 text-gray-300">
                    <tr>
                        <th class="px-4 py-2">Név</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Bérlet alkalmak</th>
                        <th class="px-4 py-2">Diákigazolvány</th>
                        <th class="px-4 py-2">Műveletek</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="border-t border-gray-700">
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">
                            @if($user->gymPasses->first())
                                {{ $user->gymPasses->first()->remaining_uses }}/12
                            @else
                                Nincs
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $user->student_id_verified ? 'Ellenőrizve' : 'Nem ellenőrizve' }}</td>
                        <td class="px-4 py-2 space-x-2">
                            @if($user->gymPasses->first())
                            <form method="POST" action="{{ route('admin.updatePass', $user) }}" class="inline">
                                @csrf
                                <input type="number" name="remaining_uses" min="0" max="12" value="{{ $user->gymPasses->first()->remaining_uses }}" class="w-16 text-black">
                                <button type="submit" class="bg-blue-600 px-2 py-1 rounded hover:bg-blue-500">Frissít</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.deleteUser', $user) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 px-2 py-1 rounded hover:bg-red-500">Törlés</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Új partner hozzáadása -->
        <h2 class="text-2xl font-bold mb-4">Új partner hozzáadása</h2>
        <form method="POST" action="{{ route('admin.storeGym') }}" class="bg-gray-850 p-6 rounded-2xl shadow max-w-xl mx-auto mb-12 space-y-4">
            @csrf
            <input type="text" name="name" placeholder="Konditerem neve" class="w-full p-2 rounded bg-gray-900 text-white">
            <input type="text" name="city" placeholder="Város" class="w-full p-2 rounded bg-gray-900 text-white">
            <input type="text" name="address" placeholder="Cím" class="w-full p-2 rounded bg-gray-900 text-white">
            <input type="text" name="opening_hours" placeholder="Nyitvatartás" class="w-full p-2 rounded bg-gray-900 text-white">
            <input type="text" name="image_url" placeholder="Kép URL" class="w-full p-2 rounded bg-gray-900 text-white">
            <button type="submit" class="bg-green-600 px-4 py-2 rounded hover:bg-green-500">Hozzáadás</button>
        </form>

    </div>
</section>
@endsection
