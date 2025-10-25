@extends('layouts.app')

@section('content')
<h1>👤 Profilom</h1>

@if(session('success'))
    <p class="text-green-600">{{ session('success') }}</p>
@endif

<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    <label>Új név:</label>
    <input type="text" name="name" value="{{ $user->name }}">

    <label>Új jelszó:</label>
    <input type="password" name="password">
    <label>Jelszó újra:</label>
    <input type="password" name="password_confirmation">

    <label>Diákigazolvány feltöltése:</label>
    <input type="file" name="student_card">

    <button type="submit">Mentés</button>
</form>
@endsection
