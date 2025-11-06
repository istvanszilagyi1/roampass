<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Jobs\ProcessStudentID;

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
            'student_card_front' => 'required_with:student_card_back|file|image|max:5120',
            'student_card_back' => 'required_with:student_card_front|file|image|max:5120',
        ]);

        if (!$request->hasFile('student_card_front') || !$request->hasFile('student_card_back')) {
            return back()->with('error', 'Mindkét oldal feltöltése kötelező!');
        }

        // Csak a fájlok elmentése
        $frontPath = $request->file('student_card_front')->store('student_ids', 'public');
        $backPath = $request->file('student_card_back')->store('student_ids', 'public');

        $validatedData = [
            'student_card_front' => $frontPath,
            'student_card_back' => $backPath,
            'student_id_verified' => false,
            'ocr_status' => null, // nincs automatikus ellenőrzés
            'ocr_confidence' => null,
        ];

        if ($request->filled('name')) {
            $validatedData['name'] = $request->name;
        }

        $user->update($validatedData);

        return back()->with('success', '✅ Profil frissítve. A diákigazolvány ellenőrzés alatt áll.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->with('error', 'A régi jelszó hibás!');
        }

        $user->update([
            'password' => bcrypt($validated['password']),
        ]);

        return back()->with('success', '✅ Sikeres jelszóváltoztatás!');
    }
}
