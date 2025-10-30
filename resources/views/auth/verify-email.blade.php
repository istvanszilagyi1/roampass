<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-950 text-white px-6">
        <div class="bg-gray-900/60 backdrop-blur-md p-8 rounded-2xl shadow-xl w-full max-w-xl text-center">

            <img src="{{ asset('images/logo.png') }}" alt="RoamPass" class="mx-auto h-16 mb-4">
            <h1 class="text-3xl font-bold text-blue-400 mb-2">E-mail megerősítése</h1>

            <p class="text-gray-400 text-sm mb-6">
                Köszönjük a regisztrációt!
                Kérjük, erősítsd meg az e-mail címed a kiküldött levélben található linkre kattintva.
                Ha nem kaptál e-mailt, újra is küldhetjük.
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 font-medium text-sm text-green-500">
                    Új megerősítő e-mailt küldtünk a megadott címre.
                </div>
            @endif

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-6">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <x-primary-button class="w-full sm:w-auto">
                        {{ __('Megerősítő e-mail újraküldése') }}
                    </x-primary-button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="underline text-sm text-gray-400 hover:text-gray-200">
                        {{ __('Kijelentkezés') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
