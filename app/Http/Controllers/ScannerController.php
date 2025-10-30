<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scanner;
use App\Models\Scan;
use App\Models\User;
use App\Models\GymPass;
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

        // QR ellenőrzés
        if (!$qrCode || !str_contains($qrCode, ':')) {
            return response()->json([
                'status' => 'invalid_qr',
                'message' => 'Érvénytelen QR kód'
            ]);
        }

        list($userId, $passId) = explode(':', $qrCode);

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'status' => 'user_not_found',
                'message' => 'Felhasználó nem található'
            ]);
        }

        $gymPass = GymPass::find($passId);
        if (!$gymPass) {
            return response()->json([
                'status' => 'no_pass',
                'message' => 'Nincs bérlete a felhasználónak'
            ]);
        }

        try {
            if ($deduct) {
                if ($gymPass->remaining_uses <= 0) {
                    return response()->json([
                        'status' => 'no_uses',
                        'user' => $this->userData($user, $gymPass)
                    ]);
                }

                // Alkalom levonása
                $gymPass->decrement('remaining_uses');
                $gymPass->refresh();

                // Scan logolása
                Scan::create([
                    'user_id' => $user->id,
                    'scanner_id' => $scanner->id,
                    'gym_id' => $gymId,
                    'gym_pass_id' => $gymPass->id,
                    'scanned_at' => now(),
                    'revenue_amount' => 1000
                ]);

                return response()->json([
                    'status' => 'deducted',
                    'user' => $this->userData($user, $gymPass)
                ]);
            }

            // Csak beolvasás
            return response()->json([
                'status' => 'scanned',
                'user' => $this->userData($user, $gymPass)
            ]);

        } catch (\Exception $e) {
            \Log::error('ScanUser hiba: '.$e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Belso hiba tortent: '.$e->getMessage()
            ]);
        }
    }

    // Segédfüggvény a felhasználói adat JSON-hoz
    private function userData(User $user, GymPass $gymPass)
    {
        return [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'student_id_verified' => (bool)$user->student_id_verified,
            'remaining_uses' => $gymPass->remaining_uses,
            'student_card_front' => $user->student_card_front ? asset('storage/' . $user->student_card_front) : null,
            'student_card_back' => $user->student_card_back ? asset('storage/' . $user->student_card_back) : null,
        ];
    }





}
