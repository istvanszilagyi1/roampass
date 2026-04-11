<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-900/50">
                <th class="table-header">ID</th>
                <th class="table-header">Felhasználó</th>
                <th class="table-header">Bérlet</th>
                <th class="table-header">Diákigazolvány</th>
                <th class="table-header">Beállítások</th>
                <th class="table-header text-right">Műveletek</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            @php
                $activePass = $user->gymPasses->first(function($p) {
                    if ($p->remaining_uses <= 0) return false;
                    if (is_null($p->expires_at)) return true;
                    return !\Carbon\Carbon::parse($p->expires_at)->endOfDay()->isPast();
                });
            @endphp
            <tr class="admin-table-row group">
                <td class="px-4 py-4 text-xs font-mono text-gray-500">#{{ $user->id }}</td>

                <td class="px-4 py-4">
                    <div class="flex flex-col">
                        <span class="font-bold text-gray-200">{{ $user->last_name }} {{ $user->first_name }}</span>
                        <span class="text-xs text-gray-500">{{ $user->email }}</span>
                    </div>
                </td>

                <td class="px-4 py-4">
                    @if($activePass)
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-bold">
                            <i data-lucide="ticket" class="w-3 h-3"></i>
                            {{ $activePass->remaining_uses }} / 12 alkalom
                        </div>
                    @else
                        <span class="text-gray-600 text-xs italic">Nincs bérlet</span>
                    @endif
                </td>

                <td class="px-4 py-4">
                    @if($user->student_id_number)
                        <div class="space-y-1">
                            <div class="text-xs font-bold text-emerald-400 flex items-center gap-1">
                                <i data-lucide="id-card" class="w-3 h-3"></i>
                                {{ $user->student_id_number }}
                            </div>
                        </div>
                    @else
                        <span class="text-gray-600 text-xs italic">Nincs megadva</span>
                    @endif
                </td>

                {{-- Beállítások (GDPR & Hírlevél) --}}
                <td class="px-4 py-4">
                    <div class="flex flex-col gap-2 items-start">
                        
                        {{-- Hírlevél státusz --}}
                        <div class="flex items-center gap-2 text-xs font-bold {{ $user->wants_newsletter ? 'text-purple-400' : 'text-gray-600 opacity-60' }}">
                            @if($user->wants_newsletter)
                                <i data-lucide="mail-check" class="w-3.5 h-3.5"></i>
                                <span>Hírlevél: <span class="uppercase">Igen</span></span>
                            @else
                                <i data-lucide="mail-minus" class="w-3.5 h-3.5"></i>
                                <span>Hírlevél: <span class="uppercase">Nem</span></span>
                            @endif
                        </div>

                        {{-- Adatkezelés státusz --}}
                        <div class="flex items-center gap-2 text-xs font-bold {{ $user->privacy_policy_accepted_at ? 'text-green-400' : 'text-red-400' }}"
                             title="{{ $user->privacy_policy_accepted_at ? 'Elfogadva: ' . $user->privacy_policy_accepted_at : 'Nincs elfogadva' }}">
                            @if($user->privacy_policy_accepted_at)
                                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                                <span>GDPR: <span class="uppercase">OK</span></span>
                            @else
                                <i data-lucide="shield-alert" class="w-3.5 h-3.5"></i>
                                <span>GDPR: <span class="uppercase">Hiányzik</span></span>
                            @endif
                        </div>

                    </div>
                </td>

                {{-- Műveletek --}}
                <td class="px-4 py-4 text-right">
                    <div class="flex justify-end items-center gap-2">
                        @if($activePass)
                            <form method="POST" action="{{ route('admin.updatePass', $user) }}" class="flex items-center gap-1 bg-gray-900/50 p-1 rounded-lg border border-gray-800">
                                @csrf
                                <input type="number" name="remaining_uses" min="0" max="12"
                                    value="{{ $activePass->remaining_uses }}"
                                    class="w-10 bg-transparent text-center text-xs font-bold text-white outline-none">
                                <button type="submit" class="p-1 hover:text-indigo-400 transition-colors" title="Mentés">
                                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                        @endif

                        {{-- Törlés --}}
                        <form method="POST" action="{{ route('admin.deleteUser', $user) }}" onsubmit="return confirm('Biztosan törlöd a felhasználót?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 bg-red-500/10 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="p-4 border-t border-gray-800/50">
    {{ $users->links('vendor.pagination.tailwind') }}
</div>