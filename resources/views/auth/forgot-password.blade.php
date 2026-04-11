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

        .text-neon {
            color: #bf40ff;
            text-shadow: 0 0 10px rgba(191, 64, 255, 0.6);
        }
    </style>

    <div class="fixed inset-0 z-[-1] bg-cover bg-center" style="background-image: url('{{ asset('images/gym-bg2.png') }}'); filter: brightness(0.2);"></div>

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="neon-soft p-8 sm:p-10 rounded-[2rem] w-full max-w-md relative overflow-hidden animate-fade-up">
            
            <div class="absolute -top-20 -right-20 w-40 h-40 bg-purple-600/20 rounded-full blur-[50px]"></div>
            <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-purple-600/20 rounded-full blur-[50px]"></div>

            <div class="text-center mb-8 relative z-10">
                <img src="{{ asset('images/logo.png') }}" alt="RoamPass" class="mx-auto h-14 mb-3 drop-shadow-[0_0_15px_rgba(191,64,255,0.4)]">
                <h1 class="text-2xl font-black text-white uppercase tracking-tight">
                    Elfelejtett jelszó
                </h1>
                <p class="text-gray-400 text-xs mt-2 font-medium tracking-wide leading-relaxed">
                    Add meg az e-mail címed, és küldünk egy linket, amivel új jelszót állíthatsz be.
                </p>
            </div>

            <x-auth-session-status class="mb-4 text-xs font-bold text-green-400 text-center" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6 relative z-10">
                @csrf

                <div>
                    <x-input-label for="email" :value="__('Email cím')" class="text-purple-300 text-[10px] font-bold uppercase tracking-widest mb-1 block" />
                    <x-text-input id="email" 
                        class="block w-full rounded-xl px-3 py-2.5 input-neon text-sm"
                        type="email" name="email" :value="old('email')" required autofocus 
                        placeholder="pelda@email.hu" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-400 text-[10px] font-bold" />
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pt-2">
                    <a href="{{ route('login') }}"
                        class="text-[11px] font-bold text-gray-400 hover:text-white underline decoration-purple-500/50 hover:decoration-purple-500 transition-all order-2 sm:order-1">
                        Vissza a belépéshez
                    </a>

                    <button type="submit"
                        class="w-full sm:w-auto bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs uppercase tracking-wider px-6 py-3 rounded-xl shadow-[0_0_15px_rgba(191,64,255,0.4)] hover:shadow-[0_0_25px_rgba(191,64,255,0.6)] hover:-translate-y-0.5 transition-all duration-300 order-1 sm:order-2">
                        Link küldése
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>