<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-950 text-white px-6">
        <div class="bg-gray-900/60 backdrop-blur-md p-8 rounded-2xl shadow-xl w-full max-w-xl">
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="RoamPass" class="mx-auto h-16 mb-3">
                <h1 class="text-3xl font-bold text-blue-400">Bejelentkezés</h1>
                <p class="text-gray-400 text-sm mt-1">Üdv újra a RoamPass világában 🌍</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="email" :value="__('Email cím')" />
                    <x-text-input id="email"
                        class="block mt-1 w-full bg-gray-800 border-gray-700 text-white"
                        type="email" name="email"
                        :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Jelszó')" />
                    <x-text-input id="password"
                        class="block mt-1 w-full bg-gray-800 border-gray-700 text-white"
                        type="password" name="password"
                        required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember me -->
                <div class="flex items-center justify-between text-sm text-gray-400">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox"
                            class="rounded border-gray-600 text-blue-500 focus:ring-blue-500"
                            name="remember">
                        <span class="ml-2">Emlékezz rám</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="hover:text-blue-400">
                            Elfelejtett jelszó?
                        </a>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
                    <a href="{{ route('register') }}"
                        class="text-sm text-gray-400 hover:text-gray-200 underline order-2 sm:order-1">
                        Nincs még fiókod?
                    </a>

                    <x-primary-button class="w-full sm:w-auto order-1 sm:order-2">
                        {{ __('Bejelentkezem') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
