@extends('layouts.app')

@section('title', 'RoamPass - Bérlet vásárlása')

@section('content')
<script src="https://unpkg.com/lucide@latest"></script>

<style>
.bg-gray-850 { background-color: #1f1f1f; }
@keyframes floatUp {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-8px) rotate(2deg); }
}
.animate-float-subtle { animation: floatUp 6s ease-in-out infinite; }

/* Kiemelés a diákkártya státuszának */
.student-status {
    @apply text-sm py-1.5 px-3 rounded-full font-semibold border inline-flex items-center gap-1;
}
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black text-white flex items-center justify-center py-24 px-4">

    <div class="bg-gray-850/90 backdrop-blur-sm rounded-3xl shadow-2xl shadow-black/70 p-10 md:p-12 max-w-lg w-full text-center relative overflow-hidden border border-blue-600/30">

        {{-- Súlyzó ikonok a háttérben --}}
        <div class="absolute -top-5 -left-5 text-blue-900/40 animate-float-subtle">
            <i data-lucide="dumbbell" class="w-20 h-20"></i>
        </div>
        <div class="absolute -bottom-5 -right-5 text-pink-900/40 animate-float-subtle" style="animation-delay: 1.5s;">
            <i data-lucide="dumbbell" class="w-20 h-20"></i>
        </div>

        <h1 class="text-4xl font-extrabold mb-3 text-blue-400 drop-shadow-lg">RoamPass Bérlet</h1>
        <h2 class="text-2xl font-semibold text-white mb-8 border-b border-gray-700 pb-4">
             12 Alkalmas Edzőterem Belépő
        </h2>

        <div class="mb-10 text-left space-y-4">
            <p class="text-gray-300">
                <i data-lucide="check-circle" class="w-5 h-5 inline text-green-400 mr-2"></i>
                Egy bérlettel <strong>12 alkalomra</strong> kapsz QR kódot.
            </p>
            <p class="text-gray-300">
                <i data-lucide="check-circle" class="w-5 h-5 inline text-green-400 mr-2"></i>
                Felhasználható <strong>bármely RoamPass partner</strong> konditeremben.
            </p>
            <p class="text-gray-300">
                <i data-lucide="check-circle" class="w-5 h-5 inline text-green-400 mr-2"></i>
                A lejárat a vásárlástól számított <strong>30 nap</strong>.
            </p>

            <div class="pt-6 border-t border-gray-700 mt-6">
                <p class="text-lg text-gray-400 font-semibold mb-2">Végleges ár:</p>

                @php
                    // Hardkódolt árak az utasításnak megfelelően
                    $regularPrice = '17.999 Ft';
                @endphp
                <p class="text-4xl font-extrabold text-white">
                    {{ $regularPrice }}
                </p>
            </div>

        </div>

        <form method="POST" action="{{ route('passes.store') }}">
            @csrf
            <button type="submit"
                    class="mt-6 bg-blue-600 text-white px-8 py-4 rounded-full font-bold shadow-2xl shadow-blue-600/50
                           hover:bg-blue-500 hover:scale-[1.03] transition-all duration-300 w-full text-xl uppercase tracking-wider">
                <i data-lucide="credit-card" class="w-5 h-5 inline mr-3"></i> Megvásárolom a bérletet
            </button>
        </form>

        <p class="text-gray-500 mt-6 text-xs">
            A gombra kattintva a fizetési felületre navigálsz.
        </p>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
