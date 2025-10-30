<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-950 text-white px-6">
        <div class="bg-gray-900/60 backdrop-blur-md p-8 rounded-2xl shadow-xl w-full max-w-xl">
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="RoamPass" class="mx-auto h-16 mb-3">
                <h1 class="text-3xl font-bold text-blue-400">Elfelejtett jelszó</h1>
                <p class="text-gray-400 text-sm mt-1">
                    Add meg az e-mail címed, és küldünk egy linket, amivel új jelszót állíthatsz be.
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email cím')" />
                    <x-text-input id="email"
                        class="block mt-1 w-full bg-gray-800 border-gray-700 text-white"
                        type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
                    <a href="{{ route('login') }}"
                        class="text-sm text-gray-400 hover:text-gray-200 underline order-2 sm:order-1">
                        Vissza a bejelentkezéshez
                    </a>

                    <x-primary-button class="w-full sm:w-auto order-1 sm:order-2">
                        {{ __('Jelszó visszaállító link küldése') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
