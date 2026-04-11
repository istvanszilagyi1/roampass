<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActionLog;

class ReviewController extends Controller
{
    public function store(Request $request, Gym $gym)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();

        // Megnézzük, van-e már értékelése ehhez a teremhez
        $existingReview = Review::where('user_id', $user->id)
                                            ->where('gym_id', $gym->id)
                                            ->first();

        if ($existingReview) {
            // Ha már van, frissítjük (ez a legfelhasználóbarátabb megoldás)
            // Így nem kap hibaüzenetet, hanem módosíthatja a véleményét.
            $existingReview->update([
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);
            ActionLog::log(
                'REVIEW_UPDATED',
                "Értékelés frissítve: review_id={$existingReview->id}, user_id={$user->id}, gym_id={$gym->id}, rating={$request->rating}"
            );
            $message = 'Véleményed sikeresen frissítve!';
        } else {
            // Ha nincs, létrehozunk egy újat
            Review::create([
                'user_id' => $user->id,
                'gym_id' => $gym->id,
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);
            $message = 'Köszönjük az értékelésedet!';
        }
        ActionLog::log(
                'REVIEW_CREATED',
                "Új értékelés létrehozva: user_id={$user->id}, gym_id={$gym->id}, rating={$request->rating}"
            );
        return back()->with('success', $message);
    }
    public function destroy(Review $review)
    {
        $user = auth()->user();


        $isAdmin = $user->is_admin;
        $isOwner = $user->id === $review->user_id;

        if (!$isAdmin && !$isOwner) {
            ActionLog::log(
                'REVIEW_DELETE_FORBIDDEN',
                "Jogosulatlan értékelés törlés: review_id={$review->id}, user_id={$user->id}"
            );
            abort(403, 'Nincs jogosultságod törölni ezt a véleményt.');
        }

        ActionLog::log(
            'REVIEW_DELETED',
            "Értékelés törölve: review_id={$review->id}, deleted_by={$user->id}"
        );
        $review->delete();

        return back()->with('success', 'A vélemény sikeresen törölve.');
    }
}
