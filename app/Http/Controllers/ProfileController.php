<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'password' => 'nullable|string|confirmed|min:6',
            'student_card_front' => 'required_with:student_card_back|file|image|max:2048',
            'student_card_back' => 'required_with:student_card_front|file|image|max:2048',
        ]);

        if (!$request->hasFile('student_card_front') or !$request->hasFile('student_card_back')) {
            return back()->with('error', 'Mindkét oldal feltöltése kötelező!');
        }

        if ($request->hasFile('student_card_front')) {
            $frontPath = $request->file('student_card_front')->store('student_cards', 'public');
            $validated['student_card_front'] = $frontPath;
            $validated['student_id_verified'] = false; // új feltöltés = újra ellenőrzés kell
        }

        if ($request->hasFile('student_card_back')) {
            $backPath = $request->file('student_card_back')->store('student_cards', 'public');
            $validated['student_card_back'] = $backPath;
            $validated['student_id_verified'] = false;
        }

        $user->update($validated);

        return back()->with('success', 'Profil frissítve. Az új diákigazolvány ellenőrzésre vár.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        // Validáció
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|confirmed|min:6',
        ]);

        // Ellenőrizzük a régi jelszót
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->with('error', 'A megadott régi jelszó nem egyezik a jelenlegivel!');
        }

        // Jelszó frissítése
        $user->update([
            'password' => bcrypt($validated['password']),
        ]);

        return back()->with('success', '✅ Sikeres jelszóváltoztatás!');
}


}
