<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gym;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $city = $request->input('city', null);

        // Város alapján szűrés, ha van város kiválasztva
        $query = Gym::query();
        if ($city) {
            $query->where('city', $city);
        }

        // Kiemelt konditermek: pl. legújabb 3-4 terem
        $highlightedGyms = $query->orderBy('created_at', 'desc')->take(4)->get();

        return view('home', compact('highlightedGyms', 'city'));
    }
}
