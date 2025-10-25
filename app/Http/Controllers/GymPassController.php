<?php

namespace App\Http\Controllers;

use App\Models\GymPass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GymPassController extends Controller
{
    // Saját bérletek
    public function index()
    {
        $passes = Auth::user()->gymPasses()->get(); // már nincs gym kapcsolat
        return view('passes.index', compact('passes'));
    }

    // Bérlet vásárlás form
    public function create()
    {
        return view('passes.create'); // nincs gym selector
    }

    // Bérlet vásárlás feldolgozása
    public function store(Request $request)
    {
        $user = Auth::user();

        // Ellenőrizzük, hogy van-e aktív bérlete
        $activePass = GymPass::where('user_id', $user->id)
                              ->where('remaining_uses', '>', 0)
                              ->first();

        if ($activePass) {
            return redirect()->route('passes.index')
                             ->with('error', 'Már van aktív bérleted, amíg el nem fogy a 12 alkalom, nem vásárolhatsz újat.');
        }

        // Ha nincs aktív bérlet, létrehozzuk az újat
        GymPass::create([
            'user_id' => $user->id,
            'remaining_uses' => 12,
            'purchase_date' => now(),
            'qr_code_url' => '/images/qrcodes/' . uniqid('pass_') . '.png', // QR kód helye
        ]);

        return redirect()->route('passes.index')
                         ->with('success', 'Bérlet vásárlás sikeres! A QR kódodat megtalálod a bérletednél.');
    }
}
