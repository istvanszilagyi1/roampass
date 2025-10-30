@extends('layouts.app')

@section('title', 'Partner Dashboard - RoamPass')

@section('content')
<section class="py-16 bg-gray-950 text-white min-h-screen">
    @if(session('success'))
            <div class="bg-green-700/40 text-green-200 py-3 px-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-700/40 text-red-200 py-3 px-4 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-700/40 text-red-200 py-3 px-4 rounded-lg mb-6">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
    @endif
    <div class="max-w-6xl mx-auto px-6">
        <h1 class="text-4xl font-bold mb-8 text-blue-400">
            🏋️‍♂️ Partner Dashboard
        </h1>

        <div class="grid md:grid-cols-3 gap-6 mb-10">
            <div class="bg-gray-900 p-6 rounded-2xl shadow">
                <h2 class="text-gray-400 text-sm">Jelenleg nem vagy hozzárendelve egyetlen konditeremhez sem.</h2>
            </div>
        </div>



    </div>
</section>
@endsection
