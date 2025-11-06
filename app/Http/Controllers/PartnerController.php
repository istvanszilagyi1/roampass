<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PartnerController extends Controller
{
    public function index()
    {
        $gyms = Gym::all();

        // Minden gym-hez hozzárakunk egy ->coords metódusban számolt mezőt
        foreach ($gyms as $gym) {
            $gym->coords = $this->getCoordinates($gym->address, $gym->city);
        }

        return view('partners.index', compact('gyms'));
    }

    public function show($id)
    {
        $gym = Gym::findOrFail($id);

        // Csak show oldalon is számoljuk ki
        $gym->coords = $this->getCoordinates($gym->address, $gym->city);

        return view('partners.show', compact('gym'));
    }

    private function getCoordinates($address, $city)
    {
        $query = urlencode("$address $city Hungary");

        $response = Http::withoutVerifying()
        ->get("https://nominatim.openstreetmap.org/search?format=json&q={$query}&limit=1");

        if ($response->successful() && count($response->json()) > 0) {
            $data = $response->json()[0];
            return [
                'lat' => $data['lat'],
                'lng' => $data['lon']
            ];
        }

        return null; // Ha nem található
    }
}
