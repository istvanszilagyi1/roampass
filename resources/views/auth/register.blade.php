<x-guest-layout>
    <style>
        .neon-soft {
            background: rgba(10, 12, 30, 0.95);
            border: 1px solid rgba(191, 64, 255, 0.5);
            backdrop-filter: blur(20px);
            box-shadow: 0 0 30px rgba(191, 64, 255, 0.15);
        }

        .input-neon {
            background-color: rgba(0, 0, 0, 0.6) !important;
            border: 1px solid rgba(191, 64, 255, 0.3) !important;
            color: #ffffff !important;
            transition: all 0.3s ease;
        }
        
        .input-neon:focus {
            border-color: #bf40ff !important;
            box-shadow: 0 0 15px rgba(191, 64, 255, 0.4) !important;
            outline: none !important;
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: #ffffff !important;
            -webkit-box-shadow: 0 0 0px 1000px #05050a inset !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        .checkbox-neon {
            appearance: none;
            background-color: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(191, 64, 255, 0.5);
            width: 1rem;
            height: 1rem;
            border-radius: 0.25rem;
            display: inline-block;
            position: relative;
            cursor: pointer;
        }
        .checkbox-neon:checked {
            background-color: #bf40ff;
            border-color: #bf40ff;
        }
        .checkbox-neon:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 0.7rem;
            font-weight: bold;
        }

        .text-neon {
            color: #bf40ff;
            text-shadow: 0 0 10px rgba(191, 64, 255, 0.6);
        }
    </style>

    <div class="fixed inset-0 z-[-1] bg-cover bg-center" style="background-image: url('{{ asset('images/gym-bg2.png') }}'); filter: brightness(0.2);"></div>

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="neon-soft p-6 sm:p-10 rounded-[2rem] w-full max-w-lg relative overflow-hidden animate-fade-up">

            <div class="absolute -top-20 -right-20 w-40 h-40 bg-purple-600/20 rounded-full blur-[50px]"></div>
            <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-purple-600/20 rounded-full blur-[50px]"></div>

            <div class="text-center mb-6 relative z-10">
                <img src="{{ asset('images/logo.png') }}" alt="RoamPass" class="mx-auto h-14 mb-3 drop-shadow-[0_0_15px_rgba(191,64,255,0.4)]">
                
                <h1 class="text-2xl font-black text-white uppercase tracking-tight">
                    Regisztráció
                </h1>
                <p class="text-gray-400 text-xs mt-1 font-medium tracking-wide">Csatlakozz a <span class="text-neon font-bold">RoamPass</span> közösséghez 🚀</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4 relative z-10">
                @csrf

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="last_name" :value="__('Vezetéknév')" class="text-purple-300 text-[10px] font-bold uppercase tracking-widest mb-1 block" />
                        <x-text-input id="last_name" class="block w-full rounded-xl px-3 py-2.5 input-neon text-sm"
                            type="text" name="last_name" :value="old('last_name')" required autofocus autocomplete="family-name" />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-1 text-red-400 text-[10px] font-bold" />
                    </div>

                    <div>
                        <x-input-label for="first_name" :value="__('Keresztnév')" class="text-purple-300 text-[10px] font-bold uppercase tracking-widest mb-1 block" />
                        <x-text-input id="first_name" class="block w-full rounded-xl px-3 py-2.5 input-neon text-sm"
                            type="text" name="first_name" :value="old('first_name')" required autocomplete="given-name" />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-1 text-red-400 text-[10px] font-bold" />
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-purple-300 text-[10px] font-bold uppercase tracking-widest mb-1 block" />
                    <x-text-input id="email" class="block w-full rounded-xl px-3 py-2.5 input-neon text-sm"
                        type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-400 text-[10px] font-bold" />
                </div>

                {{-- Jelszó mezők --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="password" :value="__('Jelszó')" class="text-purple-300 text-[10px] font-bold uppercase tracking-widest mb-1 block" />
                        <x-text-input id="password" class="block w-full rounded-xl px-3 py-2.5 input-neon text-sm"
                            type="password" name="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-400 text-[10px] font-bold" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Megerősítés')" class="text-purple-300 text-[10px] font-bold uppercase tracking-widest mb-1 block" />
                        <x-text-input id="password_confirmation" class="block w-full rounded-xl px-3 py-2.5 input-neon text-sm"
                            type="password" name="password_confirmation" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-red-400 text-[10px] font-bold" />
                    </div>
                </div>

                {{-- Hírlevél Feliratkozás --}}
                <div class="flex items-center gap-3 mb-4">
                    <input type="checkbox" name="privacy_policy" id="privacy_policy" required class="w-4 h-4 rounded border-gray-300">
                    <label for="privacy_policy" class="text-xs text-gray-400">
                        Elolvastam és elfogadom az <a href="/adatkezeles" class="text-indigo-400 underline">Adatkezelési tájékoztatót</a>. *
                    </label>
                </div>

                <div class="flex items-center gap-3 mb-6">
                    <input type="checkbox" name="wants_newsletter" id="wants_newsletter" class="w-4 h-4 rounded border-gray-300">
                    <label for="wants_newsletter" class="text-xs text-gray-400">
                        Szeretnék feliratkozni a hírlevélre és értesülni az akciókról.
                    </label>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pt-4">
                    <a href="{{ route('login') }}"
                        class="text-[11px] font-bold text-gray-400 hover:text-white underline decoration-purple-500/50 hover:decoration-purple-500 transition-all order-2 sm:order-1">
                        Már van fiókod?
                    </a>

                    <button type="submit"
                        class="w-full sm:w-auto bg-purple-600 hover:bg-purple-500 text-white font-bold text-sm uppercase tracking-wider px-8 py-3 rounded-xl shadow-[0_0_15px_rgba(191,64,255,0.4)] hover:shadow-[0_0_25px_rgba(191,64,255,0.6)] hover:-translate-y-0.5 transition-all duration-300 order-1 sm:order-2">
                        Regisztrálok
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>