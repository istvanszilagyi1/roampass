<?php

namespace App\Http\Controllers;

use App\Models\GymPass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class GymPassController extends Controller
{
    // Saját bérletek
    public function index()
    {
        $user = Auth::user();
        $passes = $user->gymPasses()->get();

        // Ellenőrizzük a diákigazolványt
        if (!$user->hasValidStudentCard()) {
            return redirect()->route('profile.edit')
                ->with('error', '⚠️ A diákigazolványod lejárt vagy még nincs elfogadva. Kérjük, töltsd fel újra!');
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


        if (!$user->hasValidStudentCard()) {
            return redirect()->route('profile.edit')
                ->with('error', '❌ Csak érvényes és elfogadott diákigazolvánnyal vásárolhatsz bérletet.');
        }

        // Ellenőrizzük, hogy van-e aktív bérlete
        $activePass = GymPass::where('user_id', $user->id)
                              ->where('remaining_uses', '>', 0)
                              ->first();

        if ($activePass) {
            return redirect()->route('passes.index')
                             ->with('error', 'Már van aktív bérleted, amíg el nem fogy a 12 alkalom, nem vásárolhatsz újat.');
        }

        if (!$user->student_id_verified || !$user->student_id_expiry) {
            return back()->with('error', 'Csak érvényes és elfogadott diákigazolvánnyal vásárolhatsz bérletet.');
        }


        // Ha nincs aktív bérlet, létrehozzuk az újat
        $gymPass = GymPass::create([
            'user_id' => $user->id,
            'remaining_uses' => 12,
            'purchase_date' => now(),
            'qr_code_url' => '', // ideiglenesen üres string
        ]);

        Storage::disk('public')->makeDirectory('qrcodes');

        // QR kód generálása: a QR kód tartalmazza a user_id és pass_id-t
        $qrContent = $user->id . ':' . $gymPass->id;
        $qrFileName = 'pass_' . $gymPass->id . '_' . time() . '.png';
        $qrPath = storage_path('app/public/qrcodes/' . $qrFileName);
        $qrRelativePath = 'qrcodes/' . $qrFileName;

        // QR kód mentése
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($qrContent)
            ->size(300)
            ->margin(10)
            ->build();

        Storage::disk('public')->put($qrRelativePath, $result->getString());

        // Mentjük a qr_path-ot a bérlethez
        $gymPass->update([
            'qr_code_url' => Storage::url($qrRelativePath),
        ]);

        return redirect()->route('passes.index')
                        ->with('success', 'Bérlet vásárlás sikeres! A QR kódodat megtalálod a bérletednél.');
    }
}
