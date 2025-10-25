<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'student_card' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        $user->name = $request->name;

        if($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if($request->hasFile('student_card')) {
            $path = $request->file('student_card')->store('student_cards', 'public');
            $user->student_card_url = $path;
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil frissítve!');
    }
}
