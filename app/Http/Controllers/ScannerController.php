<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scanner;
use App\Models\Scan;
use App\Models\User;
use App\Models\GymPass;
use App\Models\ActionLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\LowBalanceMail;

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
        $gym = $scanner->gym;

        $qrData = $request->input('qr_code'); 
        $deduct = $request->input('deduct', false);

        // 1. Üres adat validáció
        if (!$qrData) {
            ActionLog::log('SCAN_FAILED_NO_QR', "Üres QR kód: scanner_id={$scanner->id}");
            return response()->json([
                'status' => 'invalid_qr',
                'message' => 'Üres QR kód.'
            ]);
        }

        // 2. Szétszedjük a QR kódot a "|" karakter mentén
        $parts = explode('|', $qrData);

        // Ha a formátum nem egyezik, akkor ez egy régi bérlet vagy hibás beolvasás
        if (count($parts) !== 3) {
            ActionLog::log('SCAN_FAILED_FORMAT', "Érvénytelen QR formátum próbálkozás: scanner_id={$scanner->id}");
            return response()->json([
                'status' => 'invalid_qr',
                'message' => 'Érvénytelen QR kód formátum! Kérlek frissíts az új bérletre.'
            ], 400);
        }

        $passId = $parts[0];
        $scannedTime = $parts[1];
        $scannedSignature = $parts[2];

        // Eltelt több mint 30 másodperc?
        $currentTime = time();
        if ($currentTime - (int)$scannedTime > 30) {
            ActionLog::log('SCAN_REJECTED_TIMEOUT', "Lejárt képernyőfotós bérlet! Pass ID: {$passId}, scanner_id={$scanner->id}");
            return response()->json([
                'status' => 'error',
                'message' => 'A QR kód lejárt! Kérlek, nyisd meg az alkalmazást, ne használj képernyőfotót.'
            ], 403);
        }

        $gymPass = GymPass::with('user')->find($passId);

        if (!$gymPass) {
            ActionLog::log('SCAN_FAILED_INVALID_PASS', "Érvénytelen Pass ID: {$passId}, scanner_id={$scanner->id}");
            return response()->json([
                'status' => 'no_pass',
                'message' => 'Érvénytelen QR kód: Bérlet nem található.'
            ]);
        }

        // Valóban a mi szerverünk írta alá?
        $expectedSignature = hash_hmac('sha256', $passId . '-' . $scannedTime, $gymPass->qr_token);
        
        if (!hash_equals($expectedSignature, $scannedSignature)) {
            ActionLog::log('SCAN_MANIPULATED', "Manipulált QR kód! Pass ID: {$passId}, scanner_id={$scanner->id}");
            return response()->json([
                'status' => 'error',
                'message' => 'A QR kódot manipulálták! Belépés megtagadva.'
            ], 403);
        }

        $user = $gymPass->user;

        try {
            // Innentől az eredeti levonási logika indul
            if ($deduct) {
                if ($gymPass->remaining_uses <= 0) {
                    ActionLog::log('SCAN_FAILED_NO_USES', "Nincs több alkalom: user_id={$user->id}, scanner_id={$scanner->id}");
                    return response()->json([
                        'status' => 'no_uses',
                        'message' => 'Nincs több alkalom a bérleten!',
                        'user' => $this->userData($user, $gymPass)
                    ]);
                }

                // Lejárt-e a bérlet dátuma?
                if ($gymPass->expires_at && \Carbon\Carbon::now()->greaterThan($gymPass->expires_at)) {
                    ActionLog::log('SCAN_FAILED_EXPIRED', "Lejárt bérlet: user_id={$user->id}, scanner_id={$scanner->id}");
                    return response()->json([
                        'status' => 'expired',
                        'message' => 'A bérlet érvényessége lejárt!',
                        'user' => $this->userData($user, $gymPass)
                    ]);
                }

                $gymPass->decrement('remaining_uses');
                
                $gymPass->refresh();

                Scan::create([
                    'user_id' => $user->id,
                    'scanner_id' => $scanner->id,
                    'gym_id' => $gymId,
                    'gym_pass_id' => $gymPass->id,
                    'scanned_at' => now(),
                    'revenue_amount' => $gym->payout_per_scan,
                    'status' => 'success'
                ]);

                // Figyelmeztető email
                if ($gymPass->remaining_uses == 5 || $gymPass->remaining_uses == 3) {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\LowBalanceMail($user, $gymPass->remaining_uses));
                }

                return response()->json([
                    'status' => 'deducted',
                    'user' => $this->userData($user, $gymPass)
                ]);
            }

            // Ha deduct=false (csak ellenőrzés)
            return response()->json([
                'status' => 'scanned',
                'user' => $this->userData($user, $gymPass)
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ScanUser hiba: '.$e->getMessage());
            ActionLog::log('SCAN_ERROR', "Belső hiba a beolvasás során: user_id={$user->id}, scanner_id={$scanner->id}");
            return response()->json([
                'status' => 'error',
                'message' => 'Belső hiba történt.'
            ]);
        }
    }

    public function cancelScan(Request $request)
    {
        try {
            $qrData = $request->input('qr_code');
            
            Log::info('Cancel scan attempt', [
                'qr_code' => $qrData,
                'scanner_user_id' => auth()->id()
            ]);

            if (!$qrData) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hiányzó QR kód'
                ], 400);
            }

            $parts = explode('|', $qrData);

            if (count($parts) !== 3) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Érvénytelen QR kód formátum a visszavonáshoz!'
                ], 400);
            }

            $passId = $parts[0]; 

            $gymPass = GymPass::with('user')->find($passId);

            if (!$gymPass) {
                ActionLog::log(
                    'CANCEL_SCAN_FAILED_NOT_FOUND',
                    "Visszavonás sikertelen (bérlet nem található): pass_id={$passId}, scanner_id=" . auth()->id()
                );
                
                Log::warning('GymPass not found for cancellation', ['pass_id' => $passId]);
                
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Bérlet nem található (Visszavonásnál)'
                ], 404);
            }

            $user = $gymPass->user;
            $scannerProfile = auth()->user()->scannerProfile;
            
            if (!$scannerProfile) {
                Log::error('Scanner profile not found for user', ['user_id' => auth()->id()]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nem található scanner profil'
                ], 404);
            }

            $scan = Scan::where('user_id', $user->id)
                ->where('gym_pass_id', $gymPass->id)
                ->where('scanner_id', $scannerProfile->id)
                ->latest()
                ->first();

            if (!$scan) {
                Log::warning('No scan found for cancellation', [
                    'user_id' => $user->id,
                    'gym_pass_id' => $gymPass->id,
                    'scanner_profile_id' => $scannerProfile->id
                ]);

                ActionLog::log(
                    'CANCEL_SCAN_NOT_FOUND',
                    "Visszavonás sikertelen, nincs scan: user_id={$user->id}, scanner_profile_id={$scannerProfile->id}"
                );

                return response()->json([
                    'status' => 'error',
                    'message' => 'Nem található visszavonható beolvasás.'
                ], 404);
            }

            Log::info('Scan found for cancellation', [
                'scan_id' => $scan->id,
                'scan_created_at' => $scan->created_at
            ]);
            
            ActionLog::log(
                'SCAN_CANCELED',
                "Beléptetés visszavonva: scan_id={$scan->id}, user_id={$user->id}, scanner_profile_id={$scannerProfile->id}"
            );
    
            $scan->delete();
            
            $gymPass->increment('remaining_uses');
            $gymPass->refresh();

            return response()->json([
                'status' => 'canceled',
                'message' => '✅ Beléptetés visszavonva.',
                'user' => $this->userData($user, $gymPass) 
            ]);

        } catch (\Exception $e) {
            Log::error('Cancel scan exception', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Szerver hiba: ' . $e->getMessage()
            ], 500);
        }
    }

    private function userData(User $user, GymPass $gymPass)
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'student_id_number' => $user->student_id_number,
            'student_id_verified' => $user->student_id_verified,
            'remaining_uses' => $gymPass->remaining_uses
        ];
    }
}