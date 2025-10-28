<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Gym;
use App\Models\GymPass;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminpanelController extends Controller
{
    public function index()
    {
        $totalPasses = GymPass::count();
        $totalGyms = Gym::count();
        $totalRevenue = GymPass::sum('price'); // vagy fix összeg, ha nincs mező
        $activeUsers = User::whereHas('gymPasses')->count();
        $users = User::with('gymPasses')->get(); // összes felhasználó a bérletekkel
        $gyms = Gym::with('owner')->get();

        return view('admin.dashboard', compact('totalPasses','totalGyms','totalRevenue','activeUsers','users', 'gyms'));
    }

    public function users()
    {
        $users = User::with('gymPasses')->get();
        return view('admin.users', compact('users'));
    }

    public function gyms()
    {
        $gyms = Gym::all();
        return view('admin.gyms', compact('gyms'));
    }

    // Alkalmak hozzáadása/eltávolítása
    public function updatePass(Request $request, User $user)
    {
        $request->validate([
            'remaining_uses' => 'required|integer|min:0|max:12',
        ]);

        $pass = $user->gymPasses()->first();
        if($pass) {
            $pass->remaining_uses = $request->remaining_uses;
            $pass->save();
            return back()->with('success', 'Alkalmak frissítve!');
        }

        return back()->with('error', 'Nincs bérlet ehhez a felhasználóhoz!');
    }

    // Profil törlése
    public function deleteUser(User $user)
    {
        $user->delete();
        return back()->with('success', 'Felhasználó törölve!');
    }

    // Új partner hozzáadása
    public function storeGym(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'opening_hours' => 'nullable|string|max:255',
            'image_url' => 'nullable|string|max:255',
        ]);

        Gym::create($request->all());
        return back()->with('success', 'Új partner hozzáadva!');
    }
    public function verifyStudent(Request $request, User $user)
    {
        $request->validate([
            'expiry_date' => 'required|date|after:today'
        ]);

        $user->update([
            'student_id_verified' => true,
            'student_id_expiry' => $request->expiry_date
        ]);

        return redirect()->back()->with('success', '✅ Diákigazolvány elfogadva és lejárat beállítva.');
    }

    public function studentIds()
    {
        // Csak azok a felhasználók, akik feltöltötték mindkét oldalt, de még nincs elfogadva
        $users = User::where('student_card_front', '!=', null)
                    ->where('student_card_back', '!=', null)
                    ->where('student_id_verified', false)
                    ->get();

        return view('admin.student_ids', compact('users'));
    }

    public function assignOwner(Request $request, Gym $gym)
    {
        $request->validate([
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $gym->update(['owner_id' => $request->owner_id]);

        return back()->with('success', '✅ Partner tulajdonos sikeresen frissítve.');
    }

}
