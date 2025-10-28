<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use App\Models\Scan;
use App\Models\Scanner;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartnerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Ha nincs gym hozzárendelve
        $gym = Gym::where('owner_id', $user->id)->first();
        if (!$gym) {
            return view('partner.no-gym');
        }

        // Statisztikák
        $totalScans = Scan::where('gym_id', $gym->id)->count();
        $monthlyRevenue = Scan::where('gym_id', $gym->id)
            ->whereMonth('scanned_at', Carbon::now()->month)
            ->sum('revenue_amount');
        $uniqueUsers = Scan::where('gym_id', $gym->id)
            ->distinct('user_id')
            ->count('user_id');
        $dailyScans = Scan::select(
                DB::raw('DATE(scanned_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('gym_id', $gym->id)
            ->whereBetween('scanned_at', [Carbon::now()->subDays(30), Carbon::now()])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Scanner profilok lekérése
        $scanners = Scanner::where('gym_id', $gym->id)->get();

        $stats = [
            'total_scans' => $totalScans,
            'monthly_revenue' => $monthlyRevenue,
            'unique_users' => $uniqueUsers,
        ];

        return view('partner.dashboard', compact('gym', 'stats', 'dailyScans', 'scanners'));
    }

    public function storeScanner(Request $request)
    {
        // --- Validáció
        $request->validate([
            'scanner_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $partner = auth()->user();

        // --- Lekérjük a partner gym-jét
        $gym = Gym::where('owner_id', $partner->id)->firstOrFail();

        // --- Létrehozzuk a Scanner felhasználót
        $scannerUser = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => false,
            'student_type' => null, // nincs relevanciája scannernek
        ]);

        // --- Létrehozzuk a Scanner profilt, összekapcsolva a felhasználóval és a gym-mel
        Scanner::create([
            'gym_id' => $gym->id,
            'name' => $request->scanner_name,
            'user_id' => $scannerUser->id, // scanner user
        ]);

        return back()->with('success', 'Új scanner profil létrehozva!');
    }

    public function destroyScanner(Scanner $scanner)
    {
        $partner = auth()->user();

        // --- Ellenőrizzük, hogy a partner tényleg a gym tulajdonosa
        if ($scanner->gym->owner_id !== $partner->id) {
            abort(403, 'Nincs jogosultságod a scanner törléséhez.');
        }

        // --- Töröljük a scannerhez tartozó felhasználót is
        $scannerUser = $scanner->user;
        if ($scannerUser) {
            $scannerUser->delete();
        }

        // --- Töröljük magát a scanner profilt
        $scanner->delete();

        return back()->with('success', 'Scanner profil és a hozzá tartozó felhasználó törölve.');
    }
}
