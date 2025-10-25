<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    // Listázza az összes konditermet
    public function index()
    {
        $gyms = Gym::all();
        return view('partners.index', compact('gyms'));
    }

    // Részletek egy konditerről
    public function show(Gym $gym)
    {
        return view('partners.show', compact('gym'));
    }
}
