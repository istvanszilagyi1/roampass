<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Models\ActionLog;
use App\Jobs\ProcessStudentID;
use App\Models\Setting;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $isUploadRequired = Setting::getValue('student_id_upload_required', '0') === '1';
        $favoriteGym = \App\Models\Scan::where('user_id', $user->id)
            ->select('gym_id', \DB::raw('count(*) as total'))
            ->groupBy('gym_id')
            ->orderBy('total', 'desc')
            ->with('gym')
            ->first();

        $chartData = collect(range(5, 0))->map(function($i) use ($user) {
            $month = now()->subMonths($i);
            return [
                'label' => $month->translatedFormat('F'),
                'count' => \App\Models\Scan::where('user_id', $user->id)
                            ->whereMonth('scanned_at', $month->month)
                            ->whereYear('scanned_at', $month->year)
                            ->count()
            ];
        });
        
        return view('profile.edit', [
            'user' => $user,
            'isUploadRequired' => $isUploadRequired,
            'favoriteGym' => $favoriteGym ? $favoriteGym->gym->name : 'Még nincs edzésed',
            'totalScans' => $user->scans()->count(),
            'chartLabels' => $chartData->pluck('label'),
            'chartValues' => $chartData->pluck('count'),
        ]);
    }
    public function exportData(Request $request) 
    {
        $user = $request->user()->load(['gymPasses', 'scans.gym']);
        $userReviews = \App\Models\Review::with('gym')
                            ->where('user_id', $user->id)
                            ->get();

        $data = [
            'fiók_adatai' => [
                'név' => $user->last_name . ' ' . $user->first_name,
                'email_cím' => $user->email,
                'diákigazolvány_szám' => $user->student_id_number ?? 'Nincs megadva',
                'diákigazolvány_ellenőrizve' => $user->student_id_verified ? 'Igen' : 'Nem',
                'regisztráció_ideje' => $user->created_at ? $user->created_at->format('Y.m.d H:i') : 'Ismeretlen',
            ],
            
            'bérletek' => $user->gymPasses->map(function ($pass) {
                return [
                    'bérlet_azonosító' => 'PASS-' . $pass->id,
                    'vásárlás_ideje' => \Carbon\Carbon::parse($pass->purchase_date)->format('Y.m.d H:i'),
                    'lejárati_idő' => $pass->expires_at ? \Carbon\Carbon::parse($pass->expires_at)->format('Y.m.d') : 'Végtelen',
                    'fennmaradó_alkalmak' => $pass->remaining_uses,
                ];
            }),

            'edzések_története' => $user->scans->map(function ($scan) {
                return [
                    'belépés_ideje' => \Carbon\Carbon::parse($scan->scanned_at)->format('Y.m.d H:i'),
                    'edzőterem' => $scan->gym ? $scan->gym->name : 'Törölt/Ismeretlen terem',
                    'státusz' => $scan->status === 'success' ? 'Sikeres belépés' : 'Egyéb',
                ];
            }),

            'írt_értékelések' => $userReviews->map(function ($review) {
                return [
                    'edzőterem' => $review->gym ? $review->gym->name : 'Törölt/Ismeretlen terem',
                    'értékelés' => $review->rating . ' csillag',
                    'szöveg' => $review->comment ?? 'Nem írt szöveges értékelést.',
                    'dátum' => $review->created_at ? $review->created_at->format('Y.m.d H:i') : 'Ismeretlen',
                ];
            }),

            'exportálva' => now()->format('Y.m.d H:i:s'),
        ];

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="roampass_szemelyes_adatok.json"',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function toggleNewsletter(Request $request)
    {
        $user = auth()->user();
        $user->wants_newsletter = $request->boolean('wants_newsletter');
        $user->save();
        ActionLog::log('NEWSLETTER_TOGGLE', "Hírlevél beállítás frissítve, új érték: {$user->wants_newsletter}");

        return response()->json([
            'success' => true,
            'message' => 'Hírlevél beállítások frissítve!'
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $isUploadRequired = Setting::getValue('student_id_upload_required', '0') === '1';

        // 1. Validációs szabályok és ALAP hibaüzenetek
        $rules = [
            'student_id_number' => 'required|string|size:10',
        ];

        $messages = [
            'student_id_number.required' => 'A diákigazolvány szám megadása kötelező!',
            'student_id_number.size' => 'A diákigazolvány számának pontosan 10 karakter hosszúnak kell lennie!',
        ];

        // Ha kötelező a fotó és még nincs verifikálva (vagy épp most tölt fel újat)
        if ($isUploadRequired) {
            $rules['student_card_front'] = 'required_without:user_has_front|image|max:5120';
            $rules['student_card_back']  = 'required_without:user_has_back|image|max:5120';
            
            // EGYEDI HIBAÜZENETEK SZIGORÚ MÓDBAN
            $messages['student_id_number.required'] = 'Kérjük, a fotók feltöltése mellé írd be a diákigazolványod számát is!';
            $messages['student_card_front.required_without'] = 'Az igazolvány előlapjának feltöltése kötelező!';
            $messages['student_card_front.image'] = 'Az előlap csak kép formátumú lehet!';
            $messages['student_card_front.max'] = 'Az előlap képe nem lehet nagyobb 5MB-nál!';
            $messages['student_card_back.required_without'] = 'Az igazolvány hátlapjának feltöltése kötelező!';
            $messages['student_card_back.image'] = 'A hátlap csak kép formátumú lehet!';
            $messages['student_card_back.max'] = 'A hátlap képe nem lehet nagyobb 5MB-nál!';

            if ($user->student_card_front) {
                $request->request->add(['user_has_front' => true]);
            }
            if ($user->student_card_back) {
                $request->request->add(['user_has_back' => true]);
            }
        }

        // Itt adjuk át a szabályokat ÉS a saját üzeneteinket is a Laravelnek!
        $request->validate($rules, $messages);

        // 2. Alapadat frissítése
        // Biztonsági frissítés: ha átírja a számot, újra kell validálni!
        if ($user->student_id_number !== $request->student_id_number) {
            $user->student_id_verified = false;
            $user->ocr_status = 'pending';
        }
        
        $user->student_id_number = $request->student_id_number;

        // --- ÚJ KÉPEK KEZELÉSE ---
        $newImagesUploaded = false;

        // Előlap cseréje
        if ($request->hasFile('student_card_front')) {
            // Töröljük a régit, ha van
            if ($user->student_card_front && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->student_card_front)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->student_card_front);
            }
            $user->student_card_front = $request->file('student_card_front')->store('student_ids', 'public');
            $newImagesUploaded = true;
        }

        // Hátlap cseréje
        if ($request->hasFile('student_card_back')) {
            // Töröljük a régit, ha van
            if ($user->student_card_back && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->student_card_back)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->student_card_back);
            }
            $user->student_card_back = $request->file('student_card_back')->store('student_ids', 'public');
            $newImagesUploaded = true;
        }

        // Ha ÚJ képet töltött fel, akkor LE KELL NULLÁZNI a verifikációs státuszt
        // hogy a rendszer vagy az admin újra megvizsgálja.
        if ($newImagesUploaded) {
            $user->student_id_verified = false;
            $user->ocr_status = 'pending';
        }

        // FONTOS: Rögtön mentünk, hogy a legújabb képek és státuszok bent legyenek!
        $user->save();

        // 3. OCR Folyamat
        // Csak akkor futtatjuk, ha szigorú mód van, VANNAK képei, NEM verifikált, 
        if ($isUploadRequired && $user->student_card_front && $user->student_card_back && !$user->student_id_verified) {
            
            $apiUrl = env('OCR_SERVICE_URL');
            
            if ($apiUrl) {
                try {
                    $imageUrl = asset('storage/' . $user->student_card_back);
                    // Bekérjük az admin által beállított színt
                    $activeColor = \App\Models\Setting::getValue('active_sticker_color', '#B300B3');
                    
                    $response = Http::timeout(10)->post($apiUrl, [
                        'url' => $imageUrl,
                        'color' => $activeColor
                    ]);

                    if ($response->successful()) {
                        $result = $response->json();

                        // SIKERES AUTOMATA AZONOSÍTÁS
                        if (isset($result['is_valid']) && $result['is_valid'] === true) {
                            $user->student_id_verified = true;
                            $now = now();
                            if ($now->month >= 4 && $now->month <= 10) {
                                // Április 1. és Október 31. között a legközelebbi lejárat az idei Október 31.
                                $expiry = Carbon::create($now->year, 10, 31, 23, 59, 59);
                            } else {
                                // November 1. és Március 31. között a legközelebbi lejárat Március 31.
                                // Ha nov-december van, akkor a JÖVŐ év márciusa, ha jan-márc, akkor az IDEI év márciusa.
                                $expiryYear = $now->month >= 11 ? $now->year + 1 : $now->year;
                                $expiry = Carbon::create($expiryYear, 3, 31, 23, 59, 59);
                            }
                            $user->student_id_expiry = $expiry;
                            $user->ocr_status = 'high';
                            $user->ocr_confidence = $result['confidence'] ?? 99.9;
                            $user->save(); 

                            ActionLog::log('AUTO_VERIFY_SUCCESS', "Automata rendszer elfogadta: {$user->email}");
                            return back()->with('success', 'A rendszer sikeresen azonosította az új igazolványodat, a profilod aktív!');
                        } 
                        // SIKERTELEN AUTOMATA AZONOSÍTÁS
                        else {
                            $user->student_id_verified = false;
                            $user->ocr_status = 'fail';
                            $user->save(); 
                            
                            ActionLog::log('AUTO_VERIFY_FAILED', "Sikertelen automata azonosítás: {$user->email}");
                            return back()->with('error', 'Az automata ellenőrzés sikertelen. Az adminisztrátor hamarosan manuálisan ellenőrzi a képeket!');
                        }
                    } else {
                        return back()->with('error', 'Képek frissítve! Az automata ellenőrzés nem sikerült, adminisztrátori jóváhagyásra vár.');
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("OCR Kapcsolati hiba: " . $e->getMessage());
                    return back()->with('error', 'Képek frissítve! A hitelesítő szerver jelenleg nem elérhető, egy adminisztrátor fogja manuálisan jóváhagyni.');
                }
            } 
            else {
                return back()->with('error', 'Képek sikeresen frissítve! Egy adminisztrátor hamarosan manuálisan ellenőrzi azokat.');
            }
        } 
        // 4. Ha NINCS bekapcsolva a szigorú mód (És eddig nem volt validálva)
        elseif (!$isUploadRequired && !$user->student_id_verified) {
            $user->student_id_verified = true;
            $user->save();
            ActionLog::log('PROFILE_UPDATE', "Diákigazolvány frissítve (fotó nélkül): {$user->student_id_number}");
            return back()->with('success', 'Adatok elmentve! (Nincs szükség manuális jóváhagyásra).');
        }

        return back()->with('success', 'Profil adatai frissítve!');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->with('error', 'A régi jelszó hibás!');
        }

        $user->update([
            'password' => bcrypt($validated['password']),
        ]);

        ActionLog::log(
            'PASSWORD_CHANGED',
            "Jelszó sikeresen megváltoztatva: user_id={$user->id}"
        );

        return back()->with('success', 'Sikeres jelszóváltoztatás!');
    }
}
