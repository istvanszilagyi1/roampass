<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\ActionLog;

class PartnerApplicationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'gym_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'nullable|string',
        ]);

        // Email küldése
        Mail::send([], [], function($message) use ($data) {
            $message->to('info@roampass.hu')
                    ->from('istvan.szilagyi@roampass.hu', 'RoamPass Rendszer')
                    ->replyTo($data['email'])
                    ->subject('🔥 ÚJ PARTNER JELENTKEZÉS: ' . $data['gym_name'])
                    ->text("Új partner jelentkező érkezett!
-------------------------------
Edzőterem: {$data['gym_name']}
Email: {$data['email']}
Üzenet: " . ($data['message'] ?? 'Nincs üzenet'));
        });


        ActionLog::log(
            'PARTNER_APPLICATION_RECEIVED',
            "Új partner jelentkezés érkezett: gym={$data['gym_name']}, email={$data['email']}"
        );
        return back()->with('success', 'Sikeresen elküldtük üzenetét. Hamarosan felvesszük Önnel a kapcsolatot!');
    }
}