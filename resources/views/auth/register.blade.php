<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-950 text-white px-6 relative z-0">
        <div class="bg-gray-900/60 backdrop-fix p-8 rounded-2xl shadow-xl w-full max-w-2xl relative z-50 pointer-events-auto">
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="RoamPass" class="mx-auto h-16 mb-3">
                <h1 class="text-3xl font-bold text-blue-400">Regisztráció</h1>
                <p class="text-gray-400 text-sm mt-1">Csatlakozz a RoamPass közösséghez 🚀</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="last_name" :value="__('Vezetéknév')" />
                        <x-text-input id="last_name" class="block mt-1 w-full bg-gray-800 border-gray-700"
                            type="text" name="last_name" :value="old('last_name')" required autofocus autocomplete="family-name" />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="first_name" :value="__('Keresztnév')" />
                        <x-text-input id="first_name" class="block mt-1 w-full bg-gray-800 border-gray-700"
                            type="text" name="first_name" :value="old('first_name')" required autocomplete="given-name" />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full bg-gray-800 border-gray-700"
                        type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="password" :value="__('Jelszó')" />
                        <x-text-input id="password" class="block mt-1 w-full bg-gray-800 border-gray-700"
                            type="password" name="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Jelszó megerősítése')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full bg-gray-800 border-gray-700"
                            type="password" name="password_confirmation" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6">
                    <a href="{{ route('login') }}"
                       class="text-sm text-gray-400 hover:text-gray-200 underline order-2 sm:order-1">
                        Már van fiókod?
                    </a>

                    <x-primary-button class="w-full sm:w-auto order-1 sm:order-2">
                        {{ __('Regisztrálok') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
