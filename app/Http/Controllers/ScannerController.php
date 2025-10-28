<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scanner;
use App\Models\Scan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ScannerController extends Controller
{
    // Scanner főoldal: beolvasó felület
    public function index()
    {
        $user = auth()->user();
        $scanner = $user->scannerProfile; // a hozzárendelt kondi
        $gym = $scanner->gym;

        // Beolvasások csak ehhez a kondihoz
        $scans = Scan::where('gym_id', $gym->id)->latest()->get();

        return view('scanner.dashboard', compact('gym', 'scans'));
    }

    // QR kód beolvasás
    public function scanUser(Request $request)
    {
        $scanner = Scanner::where('user_id', auth()->id())->firstOrFail();
        $gymId = $scanner->gym_id;

        $qrCode = $request->input('qr_code');
        $deduct = $request->input('deduct', false);

        // Példa: QR kód = user_id
        $user = User::find($qrCode);
        if(!$user) {
            return response()->json(['success' => false, 'message' => 'Felhasználó nem található']);
        }

        $gymPass = $user->gymPasses()->first();
        if(!$gymPass){
            return response()->json(['success' => false, 'message' => 'Nincs bérlete a felhasználónak']);
        }

        if($deduct){
            if($gymPass->remaining_uses <= 0){
                return response()->json(['success' => false, 'message' => 'Nincs több alkalom!']);
            }
            $gymPass->decrement('remaining_uses');

            // Scan logolása
            Scan::create([
                'user_id' => $user->id,
                'scanner_id' => $scanner->id,
                'gym_id' => $gymId,
                'scanned_at' => now(),
                'revenue_amount' => 1000 // példa ár
            ]);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'student_id_verified' => $user->student_id_verified,
                'remaining_uses' => $gymPass->remaining_uses
            ]
        ]);
    }


}
