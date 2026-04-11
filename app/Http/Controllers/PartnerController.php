<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PartnerController extends Controller
{
    public function index()
    {
        $gyms = Gym::with('reviews')->get();

        foreach ($gyms as $gym) {
            $gym->coords = $this->getCoordinates($gym->address, $gym->city);
        }

        return view('partners.index', compact('gyms'));
    }

    public function show($id)
    {
        $gym = Gym::findOrFail($id);
        $gym->coords = $this->getCoordinates($gym->address, $gym->city);

        return view('partners.show', compact('gym'));
    }

    private function getCoordinates($address, $city)
    {
        // Létrehozunk egy egyedi azonosítót a címből (pl. coords_e4d909c290...)
        $cacheKey = 'coords_' . md5($address . $city);

        // Megkérjük a Cache-t, hogy 30 napig emlékezzen erre az adatra.
        // Ha már ismeri a címet, visszaadja a memóriából. Ha nem, csak akkor futtatja le az API hívást.
        return Cache::remember($cacheKey, now()->addDays(30), function () use ($address, $city) {
            
            $apiKey = env('OPENCAGE_API_KEY');
            $query = urlencode("$address $city Hungary");
            $url = "https://api.opencagedata.com/geocode/v1/json?q={$query}&key={$apiKey}&limit=1&no_annotations=1";

            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['results'])) {
                    return [
                        'lat' => $data['results'][0]['geometry']['lat'],
                        'lng' => $data['results'][0]['geometry']['lng'],
                    ];
                }
            }

            return null;
        });
    }
}