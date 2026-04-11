<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use App\Models\Scan;
use App\Models\Scanner;
use App\Models\User;
use App\Models\Invoice;
use App\Models\ActionLog;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartnerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        ActionLog::log(
            'PARTNER_DASHBOARD_VIEW',
            "Partner dashboard megnyitva: user_id={$user->id}"
        );

        // Partner gym lekérése
        $gym = Gym::where('owner_id', $user->id)->first();
        if (!$gym) {
            ActionLog::log(
                'PARTNER_NO_GYM',
                "Partnernek nincs edzőterme: user_id={$user->id}"
            );

            return view('partner.no-gym');
        }

        // Dátum szűrő
        $selectedMonth = (int) $request->get('month', now()->month);
        $selectedYear = (int) $request->get('year', now()->year);

        ActionLog::log(
            'PARTNER_DASHBOARD_FILTER',
            "Dashboard szűrés: gym_id={$gym->id}, month={$selectedMonth}, year={$selectedYear}"
        );

        // Összes beolvasás
        $totalScans = Scan::where('gym_id', $gym->id)->count();

        // Havi statisztikák
        $monthlyRevenue = Scan::where('gym_id', $gym->id)
            ->whereMonth('scanned_at', $selectedMonth)
            ->whereYear('scanned_at', $selectedYear)
            ->sum('revenue_amount');

        $monthlyScansCount = Scan::where('gym_id', $gym->id)
            ->whereMonth('scanned_at', $selectedMonth)
            ->whereYear('scanned_at', $selectedYear)
            ->count();

        // Legutolsó scan
        $lastScan = Scan::where('gym_id', $gym->id)->latest('scanned_at')->first();

        // Napi beolvasások (30 nap)
        $dailyScansRaw = Scan::select(
            DB::raw('DATE(scanned_at) as date'),
            DB::raw('COUNT(*) as total')
        )
        ->where('gym_id', $gym->id)
        ->whereBetween('scanned_at', [
            Carbon::now()->subDays(29)->startOfDay(),
            Carbon::now()->endOfDay()
        ])
        ->groupBy('date')
        ->orderBy('date', 'ASC')
        ->get();

        $dailyScans = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $scan = $dailyScansRaw->firstWhere('date', $date);
            $dailyScans->push([
                'date' => $date,
                'total' => $scan ? (int) $scan->total : 0
            ]);
        }

        // Scanner profilok
        $scanners = Scanner::where('gym_id', $gym->id)->get();

        // Számlák
        $invoices = $gym->invoices()->orderBy('issue_date', 'desc')->get();

        $stats = [
            'total_scans' => $totalScans,
            'monthly_revenue' => $monthlyRevenue,
            'monthly_scans_count' => $monthlyScansCount,
        ];

        return view('partner.dashboard', compact(
            'gym',
            'stats',
            'dailyScans',
            'scanners',
            'lastScan',
            'selectedMonth',
            'selectedYear',
            'invoices'
        ));
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

        $scanner = Scanner::create([
            'gym_id' => $gym->id,
            'name' => $request->scanner_name,
            'user_id' => $scannerUser->id,
        ]);

        ActionLog::log(
            'SCANNER_CREATED',
            "Új scanner létrehozva: gym_id={$gym->id}, scanner_id={$scanner->id}, user_id={$scannerUser->id}"
        );

        return back()->with('success', 'Új scanner profil létrehozva!');
    }

    public function destroyScanner(Scanner $scanner)
    {
        $partner = auth()->user();

        if ($scanner->gym->owner_id !== $partner->id) {
            ActionLog::log(
                'SCANNER_DELETE_FORBIDDEN',
                "Jogosulatlan scanner törlés: scanner_id={$scanner->id}, user_id={$partner->id}"
            );

            abort(403, 'Nincs jogosultságod a scanner törléséhez.');
        }

        ActionLog::log(
            'SCANNER_DELETED',
            "Scanner törölve: scanner_id={$scanner->id}, gym_id={$scanner->gym_id}"
        );

        if ($scanner->user) {
            $scanner->user->delete();
        }

        $scanner->delete();

        return back()->with('success', 'Scanner profil és a hozzá tartozó felhasználó törölve.');
    }
}
