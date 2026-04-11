<x-guest-layout>
    <style>
        .neon-soft {
            background: rgba(10, 12, 30, 0.95);
            border: 1px solid rgba(191, 64, 255, 0.5);
            backdrop-filter: blur(20px);
            box-shadow: 0 0 40px rgba(191, 64, 255, 0.15);
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

        .text-neon {
            color: #bf40ff;
            text-shadow: 0 0 10px rgba(191, 64, 255, 0.6);
        }
    </style>

    <div class="fixed inset-0 z-[-1] bg-cover bg-center" style="background-image: url('{{ asset('images/gym-bg2.png') }}'); filter: brightness(0.2);"></div>

    <div class="min-h-screen flex items-center justify-center px-6 py-12">
        <div class="neon-soft p-10 rounded-[2.5rem] w-full max-w-md relative overflow-hidden animate-fade-up">

            <div class="absolute -top-20 -right-20 w-40 h-40 bg-purple-600/20 rounded-full blur-[50px]"></div>
            <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-purple-600/20 rounded-full blur-[50px]"></div>

            <div class="text-center mb-8 relative z-10">
                <img src="{{ asset('images/logo.png') }}" alt="RoamPass" class="mx-auto h-20 mb-4 drop-shadow-[0_0_15px_rgba(191,64,255,0.4)]">
                
                <h1 class="text-3xl font-black text-white uppercase tracking-tight">
                    Bejelentkezés
                </h1>
                <p class="text-gray-400 text-sm mt-2 font-medium tracking-wide">Üdv újra a <span class="text-neon font-bold">RoamPass</span> világában 🌍</p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6 relative z-10">
                @csrf

                {{-- Email --}}
                <div>
                    <x-input-label for="email" :value="__('Email cím')" class="text-purple-300 text-xs font-bold uppercase tracking-widest mb-2 block" />
                    <x-text-input id="email"
                        class="block mt-1 w-full rounded-xl p-3 input-neon"
                        type="email" name="email"
                        :value="old('email')" required autofocus autocomplete="username" 
                        placeholder="pelda@email.hu" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-xs font-bold" />
                </div>

                {{-- Jelszó --}}
                <div>
                    <x-input-label for="password" :value="__('Jelszó')" class="text-purple-300 text-xs font-bold uppercase tracking-widest mb-2 block" />
                    <x-text-input id="password"
                        class="block mt-1 w-full rounded-xl p-3 input-neon"
                        type="password" name="password"
                        required autocomplete="current-password" 
                        placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-xs font-bold" />
                </div>

                {{-- Emlékezz rám & Elfelejtett jelszó --}}
                <div class="flex items-center justify-between text-sm text-gray-400">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                        <input id="remember_me" type="checkbox"
                            class="rounded bg-black border-purple-500/50 text-purple-600 focus:ring-purple-500 focus:ring-offset-gray-900"
                            name="remember">
                        <span class="ml-2 group-hover:text-white transition-colors text-xs font-bold uppercase tracking-wide">Emlékezz rám</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-bold uppercase tracking-wide hover:text-neon transition-colors">
                            Elfelejtett jelszó?
                        </a>
                    @endif
                </div>

                {{-- Gombok --}}
                <div class="flex flex-col gap-4 pt-4">
                    <button type="submit"
                        class="w-full bg-purple-600 hover:bg-purple-500 text-white font-black text-sm uppercase tracking-[0.2em] py-4 rounded-xl shadow-[0_0_20px_rgba(191,64,255,0.4)] hover:shadow-[0_0_30px_rgba(191,64,255,0.6)] hover:-translate-y-1 transition-all duration-300">
                        Bejelentkezem
                    </button>

                    <div class="text-center mt-2">
                        <span class="text-gray-500 text-xs">Nincs még fiókod?</span>
                        <a href="{{ route('register') }}"
                            class="ml-2 text-sm font-bold text-white hover:text-neon underline decoration-purple-500/50 hover:decoration-purple-500 transition-all">
                            Regisztrálj itt
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>