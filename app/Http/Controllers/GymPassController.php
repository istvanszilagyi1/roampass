<?php

namespace App\Http\Controllers;

use App\Models\GymPass;
use App\Models\ActionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GymPassController extends Controller
{
    // Saját bérletek
    public function index()
    {
        $user = Auth::user();
        $passes = $user->gymPasses()->get();

        // Ellenőrizzük a diákigazolványt
        if (!$user->hasValidStudentCard()) {
            ActionLog::log(
                'STUDENT_CARD_INVALID_ACCESS',
                "Érvénytelen diákigazolvánnyal próbálta elérni a bérleteit: {$user->email}"
            );

            return redirect()->route('profile.edit')
                ->with('error', 'Csak hitelesítés után tekintheted meg bérletedet.');
        }

        // Ha van bérlet, de az összes alkalom elfogyott, navigáljunk a vásárlásra
        $passes = GymPass::where('user_id', $user->id)
                     ->orderBy('purchase_date', 'desc')
                     ->get();

        return view('passes.index', compact('passes'));
    }

    public function create()
    {
        return view('passes.create');
    }

    // Bérlet vásárlás feldolgozása
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validációk
        if (!$user->hasValidStudentCard()) {
            ActionLog::log(
                'PASS_PURCHASE_BLOCKED',
                "Bérlet vásárlás blokkolva – érvénytelen diákigazolvány: {$user->email}"
            );

            return redirect()->route('profile.edit')
                ->with('error', 'Csak hitelesítés után vásárolhatsz bérletet.');
        }

        // Aktív bérlet ellenőrzése
        $activePass = GymPass::where('user_id', $user->id)
                             ->where('remaining_uses', '>', 0)
                             ->where('expires_at', '>', now())
                             ->first();

        if ($activePass) {
            ActionLog::log(
                'PASS_PURCHASE_BLOCKED',
                "Új bérlet vásárlás blokkolva – már van aktív bérlet: {$user->email}"
            );

            return redirect()->route('passes.index')
                ->with('error', 'Már van aktív bérleted. Amíg el nem fogy vagy le nem jár, nem vásárolhatsz újat.');
        }

        // Bérlet létrehozása biztonságos tokennel
        $token = Str::random(32);

        $gymPass = GymPass::create([
            'user_id' => $user->id,
            'remaining_uses' => 12,
            'purchase_date' => now(),
            'expires_at' => now()->addMonth(),
            'qr_code_url' => '',
            'qr_token' => $token,
        ]);
        
        ActionLog::log(
            'PASS_PURCHASE_COMPLETED',
            "Bérlet vásárlás sikeresen befejezve: user={$user->email}, pass_id={$gymPass->id}"
        );

        return redirect()->route('passes.index')
            ->with('success', 'Bérlet vásárlás sikeres! A QR kódodat megtalálod a bérletednél.');
    }

    public function renderDynamicQr(GymPass $pass)
    {
        if ($pass->user_id !== Auth::id()) {
            abort(403);
        }

        $time = time(); 
        
        $signature = hash_hmac('sha256', $pass->id . '-' . $time, $pass->qr_token);

        $qrData = $pass->id . '|' . $time . '|' . $signature;

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($qrData)
            ->size(300)
            ->margin(10)
            ->build();

        return response($result->getString())->header('Content-Type', 'image/png');
    }
}
