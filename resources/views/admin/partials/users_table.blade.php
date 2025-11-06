<table class="w-full text-left bg-gray-850 rounded-2xl shadow overflow-hidden">
    <thead class="bg-gray-800 text-gray-300">
        <tr>
            <th class="px-4 py-2">Azonosító</th>
            <th class="px-4 py-2">Vezetéknév</th>
            <th class="px-4 py-2">Keresztnév</th>
            <th class="px-4 py-2">Email</th>
            <th class="px-4 py-2">Iskola típusa</th>
            <th class="px-4 py-2">Bérlet alkalmak</th>
            <th class="px-4 py-2">Diákigazolvány</th>
            <th class="px-4 py-2">Ellenőrzés</th>
            <th class="px-4 py-2">Műveletek</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr class="border-t border-gray-700">
            <td class="px-4 py-2">{{ $user->id }}</td>
            <td class="px-4 py-2">{{ $user->last_name }}</td>
            <td class="px-4 py-2">{{ $user->first_name }}</td>
            <td class="px-4 py-2">{{ $user->email }}</td>
            <td class="px-4 py-2">{{ $user->student_type }}</td>
            <td class="px-4 py-2">
                @if($user->gymPasses->first())
                    {{ $user->gymPasses->first()->remaining_uses }}/12
                @else
                    Nincs
                @endif
            </td>
            <td class="px-4 py-2">
                @if(!$user->student_card_front || !$user->student_card_back)
                    <span class="text-gray-400">Nincs feltöltve</span>
                @elseif($user->student_id_verified)
                    <span class="text-green-400 font-semibold">
                        Elfogadva ({{ \Carbon\Carbon::parse($user->student_id_expiry)->format('Y.m.d') }})
                    </span>
                @else
                    <button onclick="openModal('{{ $user->id }}')"
                        class="bg-indigo-600 px-3 py-1 rounded hover:bg-indigo-500 text-white">
                        Ellenőrzés
                    </button>
                @endif
            </td>
            <td class="px-4 py-2">
                @if($user->ocr_status == 'high')
                    <span class="text-green-400 font-semibold">✅ Hologram ellenőrzés OK ({{ $user->ocr_confidence }}%)</span>
                @elseif($user->ocr_status == 'medium')
                    <span class="text-yellow-400 font-semibold">⚠️ Ellenőrzés közepes ({{ $user->ocr_confidence }}%)</span>
                @elseif($user->ocr_status == 'fail')
                    <span class="text-red-400 font-semibold">❌ Ellenőrzés sikertelen ({{ $user->ocr_confidence }}%)</span>
                @else
                    <span class="text-gray-400">Feldolgozás alatt...</span>
                @endif
            </td>

            <td class="px-4 py-2 space-x-2">
                @if($user->gymPasses->first())
                <form method="POST" action="{{ route('admin.updatePass', $user) }}" class="inline">
                    @csrf
                    <input type="number" name="remaining_uses" min="0" max="12"
                        value="{{ $user->gymPasses->first()->remaining_uses }}" class="w-16 text-black">
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
