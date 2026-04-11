@extends('layouts.app')

@section('title', 'Hírlevél Küldés - RoamPass')

@section('content')
<script src="https://unpkg.com/lucide@latest"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<style>
    :root {
        --card-bg: #0f172a;
        --card-border: #1e293b;
        --input-bg: #020617;
    }

    .glass-panel {
        @apply bg-gray-900/60 backdrop-blur-md border border-gray-800/80 rounded-3xl shadow-2xl;
    }

    .input-style {
        background-color: var(--input-bg) !important;
        border: 1px solid var(--card-border) !important;
        @apply w-full p-3 rounded-2xl text-sm text-gray-200 placeholder-gray-600 outline-none transition-all duration-300;
    }
    .input-style:focus {
        border-color: #bf40ff !important;
        box-shadow: 0 0 0 3px rgba(191, 64, 255, 0.15);
    }

    .ql-toolbar {
        background-color: #1e293b;
        border-color: #334155 !important;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
    }
    .ql-container {
        background-color: #020617;
        border-color: #334155 !important;
        border-bottom-left-radius: 1rem;
        border-bottom-right-radius: 1rem;
        color: #e2e8f0 !important;
        font-family: inherit !important;
        font-size: 1rem !important;
        min-height: 300px;
    }

    .ql-stroke { stroke: #cbd5e1 !important; }
    .ql-fill { fill: #cbd5e1 !important; }
    .ql-picker { color: #cbd5e1 !important; }
    
    .ql-active .ql-stroke { stroke: #bf40ff !important; }
    .ql-active .ql-fill { fill: #bf40ff !important; }
</style>

<section class="py-12 bg-[#020617] text-gray-100 min-h-screen bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-gray-900 via-[#020617] to-[#020617]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div id="alert-container" class="fixed top-6 left-1/2 -translate-x-1/2 z-[100] w-full max-w-lg px-4">
            @if(session('success'))
                <div id="success-alert" class="glass-panel p-4 border-emerald-500/30 bg-emerald-500/10 text-emerald-400 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500 mb-3">
                    <i data-lucide="check-circle" class="w-5 h-5 shadow-[0_0_15px_rgba(16,185,129,0.5)]"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div id="error-alert" class="glass-panel p-4 border-red-500/30 bg-red-500/10 text-red-400 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
                    <i data-lucide="alert-circle" class="w-5 h-5 shadow-[0_0_15px_rgba(239,68,68,0.5)]"></i>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold uppercase tracking-wider">Hiba</span>
                        <span class="text-xs opacity-80">{{ session('error') }}</span>
                    </div>
                </div>
            @endif
        </div>
        {{-- Vissza gomb --}}
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-white mb-8 transition-colors text-sm font-bold uppercase tracking-wider">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Vissza a vezérlőpultra
        </a>

        <div class="glass-panel p-8 border-purple-500/30 relative overflow-hidden">
            
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-purple-500/20 rounded-xl border border-purple-500/30">
                        <i data-lucide="mail-plus" class="text-purple-400 w-8 h-8"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black uppercase tracking-tight text-white">Hírlevél Szerkesztő</h1>
                        <p class="text-sm text-purple-300/60 font-medium">Címzettek száma: <span class="text-white">{{ $subscriberCount }} fő</span></p>
                    </div>
                </div>
            </div>

            <form id="newsletterForm" action="{{ route('admin.newsletter.send') }}" method="POST" class="space-y-6">
                @csrf
                
                {{-- Tárgy --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase ml-1 tracking-widest">Email Tárgya</label>
                    <input type="text" name="subject" placeholder="Pl: Havi összefoglaló, Új akciók..." 
                           class="input-style pl-4 !bg-black/40 !border-purple-500/20 focus:!border-purple-500 font-bold text-lg" required>
                </div>

                <input type="hidden" name="message" id="messageInput">

                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase ml-1 tracking-widest">Tartalom szerkesztése</label>
                    <div id="editor"></div>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-white/5 mt-8">
                    <div class="text-xs text-gray-500 italic max-w-md">
                        <i data-lucide="lock" class="w-3 h-3 inline mr-1"></i>
                        Biztonságos küldés: A címzettek nem látják egymás email címét (BCC).
                    </div>
                    <button type="submit" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white px-10 py-4 rounded-xl font-black uppercase text-xs tracking-widest shadow-[0_0_20px_rgba(147,51,234,0.3)] hover:shadow-[0_0_30px_rgba(147,51,234,0.5)] transition-all flex items-center gap-3 transform hover:scale-105">
                        <i data-lucide="send" class="w-5 h-5"></i> Küldés Most
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    lucide.createIcons();

     document.addEventListener('DOMContentLoaded', function() {
        const successAlert = document.getElementById('success-alert');
        const errorAlert = document.getElementById('error-alert');

        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'all 0.5s ease';
                successAlert.style.opacity = '0';
                successAlert.style.transform = 'translateY(-20px)';
                setTimeout(() => successAlert.remove(), 500);
            }, 4000);
        }

        if (errorAlert) {
            setTimeout(() => {
                errorAlert.style.transition = 'all 0.5s ease';
                errorAlert.style.opacity = '0';
                errorAlert.style.transform = 'translateY(-20px)';
                setTimeout(() => errorAlert.remove(), 500);
            }, 4000);
        }
    });

    var quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Írd ide a hírlevél tartalmát... Használhatsz félkövér, dőlt kiemelést, listákat és linkeket is.',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                ['link', 'clean']
            ]
        }
    });

    // Form küldésekor átmásoljuk a HTML tartalmat a rejtett inputba
    document.getElementById('newsletterForm').onsubmit = function() {
        // A "confirm" ablak
        if(!confirm('Biztosan kiküldöd a levelet {{ $subscriberCount }} feliratkozónak?')) {
            return false;
        }
        
        var htmlContent = quill.root.innerHTML;
        document.getElementById('messageInput').value = htmlContent;
    };
</script>
@endsection