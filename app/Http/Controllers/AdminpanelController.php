<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Gym;
use App\Models\GymPass;
use Illuminate\Support\Facades\DB;

class AdminpanelController extends Controller
{
    public function index()
    {
        $totalPasses = GymPass::count();
        $totalGyms = Gym::count();
        $totalRevenue = GymPass::sum('price'); // vagy fix összeg, ha nincs mező
        $activeUsers = User::whereHas('gymPasses')->count();
        $users = User::with('gymPasses')->get(); // összes felhasználó a bérletekkel

        return view('admin.dashboard', compact('totalPasses','totalGyms','totalRevenue','activeUsers','users'));
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
}
