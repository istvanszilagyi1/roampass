<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use App\Models\Invoice;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function generateGymInvoice(Gym $gym)
    {
        // Előző hónap meghatározása
        $lastMonth = Carbon::now()->subMonth();

        // Beolvasások száma az adott teremnél az előző hónapban
        $scanCount = $gym->scans()
            ->whereMonth('scanned_at', $lastMonth->month)
            ->whereYear('scanned_at', $lastMonth->year)
            ->count();

        $totalAmount = $scanCount * $gym->payout_per_scan;

        Invoice::create([
            'gym_id'         => $gym->id,
            'invoice_number' => 'RP-' . now()->format('Ymd') . '-' . $gym->id,
            'amount'         => $totalAmount,
            'pdf_url'        => 'https://szamlazz.hu/placeholder.pdf',
            'issue_date'     => now(),
            'status'         => 'pending',
        ]);

        return back()->with('success', "Számla elkészült: {$scanCount} beolvasás × {$gym->payout_per_scan} Ft.");
    }
}