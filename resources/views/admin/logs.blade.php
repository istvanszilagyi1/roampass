@extends('layouts.app')

@section('title', 'Rendszernaplók - RoamPass')

@section('content')
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    :root { --card-bg: #0f172a; --card-border: #1e293b; --input-bg: #020617; }
    .glass-panel { @apply bg-gray-900/60 backdrop-blur-md border border-gray-800/80 rounded-3xl shadow-2xl; }
    .input-style { background-color: var(--input-bg) !important; border: 1px solid var(--card-border) !important; @apply w-full p-3 rounded-xl text-sm text-gray-200 outline-none transition-all duration-300; }
    .log-row { @apply border-b border-gray-800/40 hover:bg-white/5 transition-colors; }
    .badge { @apply px-2 py-1 rounded text-[10px] font-black uppercase; }
</style>

<section class="py-12 bg-[#020617] text-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-black tracking-tight flex items-center gap-3">
                    <div class="p-2 bg-amber-500/20 rounded-xl border border-amber-500/30">
                        <i data-lucide="activity" class="text-amber-500 w-6 h-6"></i>
                    </div>
                    Rendszernaplók
                </h1>
                <p class="text-gray-500 text-sm">Minden felhasználói és adminisztrátori művelet visszakövetése.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-gray-400 hover:text-white flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Vissza a Dashboardra
            </a>
        </div>

        {{-- Szűrő Panel --}}
        <div class="glass-panel p-6">
            <form action="{{ route('admin.logs') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-500 uppercase ml-2">Keresés</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Email vagy üzenet..." class="input-style">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-500 uppercase ml-2">Esemény típus</label>
                    <select name="action_type" class="input-style">
                        <option value="">Összes típus</option>
                        @foreach($actionTypes as $type)
                            <option value="{{ $type }}" {{ request('action_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-500 uppercase ml-2">Dátum</label>
                    <input type="date" name="date" value="{{ request('date') }}" class="input-style">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-500 py-3 rounded-xl font-bold text-xs uppercase transition-all">Szűrés</button>
                    <a href="{{ route('admin.logs') }}" class="p-3 bg-gray-800 rounded-xl hover:bg-gray-700 transition-all">
                        <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- Log Táblázat --}}
        <div class="glass-panel overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-900/50 border-b border-gray-800">
                            <th class="px-6 py-4 text-[10px] font-black text-gray-500 uppercase">Időpont</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-500 uppercase">Felhasználó</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-500 uppercase">Esemény</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-500 uppercase">Leírás</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-500 uppercase">IP Cím</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/30">
                        @forelse($logs as $log)
                            <tr class="log-row">
                                <td class="px-6 py-4 text-xs font-medium text-gray-400">
                                    {{ $log->created_at->format('Y.m.d H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold text-gray-200">{{ $log->user->email ?? 'Rendszer/Vendég' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="badge {{ str_contains($log->action, 'FAIL') ? 'bg-red-500/20 text-red-400' : 'bg-indigo-500/20 text-indigo-400' }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-300">
                                    {{ $log->description }}
                                </td>
                                <td class="px-6 py-4 text-[10px] font-mono text-gray-600">
                                    {{ $log->ip_address }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">Nem található rögzített esemény a megadott feltételekkel.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-gray-900/20">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</section>

<script>
    lucide.createIcons();
</script>
@endsection