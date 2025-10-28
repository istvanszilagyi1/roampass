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
        $passes = Auth::user()->gymPasses()->get();
        $user = Auth::user();
        if (!$user->hasValidStudentCard()) {
            return redirect()->route('profile.edit')
                ->with('error', '⚠️ A diákigazolványod lejárt vagy még nincs elfogadva. Kérjük, töltsd fel újra!');
        }
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

        if (!$user->student_id_verified || !$user->student_id_expiry || $user->student_id_expiry->isPast()) {
            return back()->with('error', 'Csak érvényes és elfogadott diákigazolvánnyal vásárolhatsz bérletet.');
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
