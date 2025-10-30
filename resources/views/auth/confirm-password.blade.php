<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-950 text-white px-6">
        <div class="bg-gray-900/60 backdrop-blur-md p-8 rounded-2xl shadow-xl w-full max-w-md text-center">

            <img src="{{ asset('images/logo.png') }}" alt="RoamPass" class="mx-auto h-16 mb-4">
            <h1 class="text-3xl font-bold text-blue-400 mb-3">Jelszó megerősítése</h1>

            <p class="text-gray-400 text-sm mb-6">
                Ez az alkalmazás biztonságos területe.
                Kérjük, erősítsd meg a jelszavad, mielőtt folytatod.
            </p>

            <form method="POST" action="{{ route('password.confirm') }}" class="text-left">
                @csrf

                <!-- Password -->
                <div class="mb-6">
                    <x-input-label for="password" :value="__('Jelszó')" />
                    <x-text-input id="password" class="block mt-1 w-full"
                        type="password"
                        name="password"
                        required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex justify-center">
                    <x-primary-button class="w-full sm:w-auto">
                        {{ __('Megerősítés') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
