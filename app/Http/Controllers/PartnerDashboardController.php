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

        // Partner gym
        $gym = Gym::where('owner_id', $user->id)->first();
        if (!$gym) {
            return view('partner.no-gym');
        }

        // Összes beolvasás
        $totalScans = Scan::where('gym_id', $gym->id)->count();

        // Havi bevétel
        $monthlyRevenue = Scan::where('gym_id', $gym->id)
            ->whereMonth('scanned_at', Carbon::now()->month)
            ->sum('revenue_amount');

        // Legutolsó beolvasás
        $lastScan = Scan::where('gym_id', $gym->id)->latest('scanned_at')->first();

        // Napi beolvasások az elmúlt 30 napban
        $dailyScansRaw = Scan::select(
        DB::raw('DATE(scanned_at) as date'),
        DB::raw('COUNT(*) as total')
        )
        ->where('gym_id', $gym->id)
        ->whereBetween('scanned_at', [Carbon::now()->subDays(29)->startOfDay(), Carbon::now()->endOfDay()])
        ->groupBy('date')
        ->orderBy('date', 'ASC')
        ->get();

        // 30 nap előállítása 0 értékekkel
        $dailyScans = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $scan = $dailyScansRaw->firstWhere('date', $date);
            $dailyScans->push([
                'date' => $date,
                'total' => $scan ? (int)$scan->total : 0
            ]);
        }


        // Scanner profilok
        $scanners = Scanner::where('gym_id', $gym->id)->get();

        $stats = [
            'total_scans' => $totalScans,
            'monthly_revenue' => $monthlyRevenue,
        ];

        return view('partner.dashboard', compact('gym', 'stats', 'dailyScans', 'scanners', 'lastScan'));
    }

    public function storeScanner(Request $request)
    {
        $request->validate([
            'scanner_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $partner = auth()->user();
        $gym = Gym::where('owner_id', $partner->id)->firstOrFail();

        $scannerUser = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => false,
        ]);

        Scanner::create([
            'gym_id' => $gym->id,
            'name' => $request->scanner_name,
            'user_id' => $scannerUser->id,
        ]);

        return back()->with('success', 'Új scanner profil létrehozva!');
    }

    public function destroyScanner(Scanner $scanner)
    {
        $partner = auth()->user();

        if ($scanner->gym->owner_id !== $partner->id) {
            abort(403, 'Nincs jogosultságod a scanner törléséhez.');
        }

        if ($scanner->user) {
            $scanner->user->delete();
        }

        $scanner->delete();

        return back()->with('success', 'Scanner profil és a hozzá tartozó felhasználó törölve.');
    }
}
