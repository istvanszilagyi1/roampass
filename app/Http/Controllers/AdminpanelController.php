<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Gym;
use App\Models\GymPass;
use App\Models\Setting;
use App\Models\ActionLog;
use App\Models\Invoice as LocalInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewsletterMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

use Omisai\Szamlazzhu\Facades\SzamlazzHu;
use Omisai\Szamlazzhu\Document\Invoice\Invoice as SzamlazzHuInvoice;
use Omisai\Szamlazzhu\Item\InvoiceItem;
use Omisai\Szamlazzhu\Buyer;
use Omisai\Szamlazzhu\Currency;
use Omisai\Szamlazzhu\PaymentMethod;
use Omisai\Szamlazzhu\Language;
use Omisai\Szamlazzhu\TaxPayer;
use Omisai\Szamlazzhu\SzamlaAgent;

class AdminpanelController extends Controller
{
    public function index(Request $request)
    {
        ActionLog::log('ADMIN_DASHBOARD_VIEW', 'Admin dashboard megnyitva');

        $settings = Setting::pluck('value', 'key')->all();
        $totalPasses = GymPass::count();
        $totalGyms = Gym::count();
        $totalRevenue = GymPass::sum('price');
        $newUsersLast30Days = User::where('created_at', '>=', now()->subDays(30))->count();

        $selectedMonth = (int) $request->get('month', now()->month);
        $selectedYear = (int) $request->get('year', now()->year);

        $query = User::with('gymPasses');
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $users = $query->paginate(5)->withQueryString();

        $gyms = Gym::with(['owner', 'invoices'])
            ->withCount(['scans as monthly_scans' => function($query) use ($selectedMonth, $selectedYear) {
                $query->whereMonth('scanned_at', $selectedMonth)
                      ->whereYear('scanned_at', $selectedYear);
            }])
            ->get();

        $monthlyNewUsers = User::select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->all();

        $pendingUsers = User::whereNotNull('student_card_front')
            ->whereNotNull('student_card_back')
            ->where('student_id_verified', false)
            ->get();

        return view('admin.dashboard', compact(
            'totalPasses','totalGyms','totalRevenue','newUsersLast30Days','users','gyms','monthlyNewUsers',
            'selectedMonth', 'selectedYear', 'settings', 'pendingUsers'
        ));
    }
    public function newsletterView()
    {
        $subscriberCount = User::where('wants_newsletter', true)->count();
        return view('admin.newsletter', compact('subscriberCount'));
    }

    public function sendNewsletter(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $subscribers = User::where('wants_newsletter', true)->pluck('email')->toArray();

        if (empty($subscribers)) {
            return back()->with('error', 'Nincs feliratkozott felhasználó.');
        }
        try {
            Mail::to(config('mail.from.address'))
                ->bcc($subscribers)
                ->send(new NewsletterMail($request->subject, $request->message));

            ActionLog::log('NEWSLETTER_SENT', 'Hírlevél elküldve ' . count($subscribers) . ' feliratkozónak!');
            return redirect()->route('admin.dashboard')
                ->with('success', 'A hírlevél sikeresen kiküldve ' . count($subscribers) . ' feliratkozónak!');

        } catch (\Exception $e) {
            return back()->with('error', 'Hiba történt a küldéskor! Valószínűleg van egy érvénytelen email cím a listában. Hibaüzenet: ' . $e->getMessage());
        }

        

        return redirect()->route('admin.dashboard')->with('success', 'A hírlevél sikeresen kiküldve ' . count($subscribers) . ' feliratkozónak!');
    }

    public function triggerExpirationCheck()
    {
        try {
            Artisan::call('gympass:check-expiration');
            
            $output = Artisan::output();

            return redirect()->back()->with('success', 'A karbantartó folyamat sikeresen lefutott! Kimenet: ' . $output);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hiba történt a futtatás során: ' . $e->getMessage());
        }
    }
    public function deleteGym(Gym $gym)
    {
        if ($gym->image_url && !Str::startsWith($gym->image_url, 'http')) {
            Storage::disk('public')->delete($gym->image_url);
        }

        if ($gym->reviews()->exists()) {
            $gym->reviews()->delete();
        }
        
        if ($gym->invoices()->exists()) {
            $gym->invoices()->delete();
        }

        if (method_exists($gym, 'scanners') && $gym->scanners()->exists()) {
            $gym->scanners()->delete();
        }
        if (method_exists($gym, 'scans') && $gym->scans()->exists()) {
            $gym->scans()->delete();
        }

        $gym->delete();

        return redirect()->back()->with('success', 'A partner és minden hozzá tartozó adat sikeresen törölve lett!');
    }

    public function logs(Request $request)
    {
        ActionLog::log('LOGS_VIEW', 'Rendszerlog oldal megnyitva');
        $query = ActionLog::with('user')->orderBy('created_at', 'desc');

        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                ->orWhereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('email', 'like', "%{$search}%");
                });
            });
        }

        if ($actionType = $request->get('action_type')) {
            $query->where('action', $actionType);
        }

        if ($date = $request->get('date')) {
            $query->whereDate('created_at', $date);
        }

        $logs = $query->paginate(20)->withQueryString();
        
        $actionTypes = \App\Models\ActionLog::select('action')->distinct()->pluck('action');

        return view('admin.logs', compact('logs', 'actionTypes'));
    }
    public function updateSettings(Request $request)
    {
        $input = $request->except('_token');

        foreach ($input as $key => $value) {
            $oldValue = Setting::where('key', $key)->value('value');

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );

            if ($oldValue !== $value) {
                $statusText = $value == '1' ? 'BEKAPCSOLVA' : 'KIKAPCSOLVA';
                ActionLog::log('SETTING_CHANGED', "Rendszerbeállítás módosítva: [$key] -> $statusText");
            }
        }

        return back()->with('success', 'Beállítások sikeresen frissítve!');
    }

    public function updatePass(Request $request, User $user)
    {
        $request->validate(['remaining_uses' => 'required|integer|min:0|max:12']);
        $pass = $user->gymPasses()->first();

        if($pass) {
            if ($request->remaining_uses == 0) {
                $pass->delete(); 
                ActionLog::log('PASS_DELETED', "Bérlet törölve: {$user->email}");
                return back()->with('success', 'A bérlet alkalmai elfogytak, ezért törlésre került!');
            }
            $pass->remaining_uses = $request->remaining_uses;
            $pass->save();
            ActionLog::log('PASS_UPDATE', "Módosítva bérlet: {$user->email}. Új alkalmak: {$request->remaining_uses}");
            return back()->with('success', 'Alkalmak frissítve!');
        }
        ActionLog::log('PASS_UPDATE_FAILED', "Nincs bérlet: {$user->email}");
        return back()->with('error', 'Nincs bérlet ehhez a felhasználóhoz!');
    }
    public function downloadLogs()
    {
        ActionLog::log('LOG_EXPORT', 'Rendszerlogok letöltése');
        $logs = ActionLog::with('user')->orderBy('created_at', 'desc')->get();
        
        $content = "ROAMPASS RENDSZERLOGOK - " . now()->format('Y-m-d H:i:s') . "\n";
        $content .= str_repeat("=", 50) . "\n\n";

        foreach ($logs as $log) {
            $userMail = $log->user ? $log->user->email : 'Rendszer/Vendég';
            $content .= "[{$log->created_at}] | {$log->action} | User: {$userMail} | IP: {$log->ip_address}\n";
            $content .= "Info: {$log->description}\n";
            $content .= str_repeat("-", 30) . "\n";
        }

        return response($content)
            ->withHeaders([
                'Content-Type' => 'text/plain',
                'Content-Disposition' => 'attachment; filename="roampass_logs_'.now()->format('Ymd_His').'.txt"',
            ]);
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        ActionLog::log('USER_DELETED', "Felhasználó törölve: {$user->email}");
        return back()->with('success', 'Felhasználó törölve!');
    }

    public function storeGym(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'opening_hours' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:5120',
            'billing_name' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'payout_per_scan' => 'required|numeric|min:0',
        ]);

        $gymData = $request->only([
            'name', 'city', 'address', 'opening_hours', 
            'billing_name', 'billing_address', 'tax_number', 'payout_per_scan'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('gyms', 'public');
            $gymData['image_url'] = $path;
        }

        Gym::create($gymData);
        ActionLog::log('GYM_CREATED', "Új partner létrehozva: {$gymData['name']}");
        return back()->with('success', 'Új partner hozzáadva a kifizetési adatokkal!');
    }

    public function updateGym(Request $request, Gym $gym)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'opening_hours' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:5120',
            'billing_name' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'payout_per_scan' => 'required|numeric|min:0',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $gymData = $request->only([
            'name', 'city', 'address', 'opening_hours',
            'billing_name', 'billing_address', 'tax_number', 'payout_per_scan', 'owner_id'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('gyms', 'public');
            $gymData['image_url'] = $path;
        }

        $gym->update($gymData);
        ActionLog::log('GYM_UPDATED', "Partner frissítve: {$gym->name}");
        return back()->with('success', '✅ Konditerem adatai és pénzügyi beállításai frissítve!');
    }

    /**
     * Számla generálása az Omisai csomaggal
     * Kezeli a PDF mentést és az email küldést is.
     */
    public function generateInvoice(Request $request, Gym $gym)
    {
        ActionLog::log(
            'INVOICE_GENERATION_START',
            "Számlagenerálás indítva: {$gym->name} ({$request->year}/{$request->month})"
        );

        if (empty($gym->billing_name) || empty($gym->billing_address) || empty($gym->tax_number)) {
            ActionLog::log('INVOICE_FAILED', 'Hiányzó számlázási adatok');
            return back()->with('error', 'Hiányzó számlázási adatok a partnernél!');
        }

        $month = (int) $request->input('month');
        $year = (int) $request->input('year');

        $scanCount = $gym->scans()
            ->whereMonth('scanned_at', $month)
            ->whereYear('scanned_at', $year)
            ->count();

        if ($scanCount == 0) {
            return back()->with('error', "Nincs beolvasás ebben az időszakban.");
        }

        $netUnitPrice = (float) $gym->payout_per_scan;
        $netTotal = $netUnitPrice * $scanCount;
        $vatRate = 0.27; 
        $vatAmount = $netTotal * $vatRate;
        $grossTotal = $netTotal + $vatAmount;

        try {
            $buyer = new Buyer();
            $buyer->setName($gym->billing_name)
                  ->setZipCode(!empty($gym->zip) ? $gym->zip : '1000')
                  ->setCity($gym->city ?? 'Budapest')
                  ->setAddress($gym->billing_address)
                  ->setEmail($gym->owner->email ?? '')
                  ->setSendEmailState(true)
                  ->setTaxNumber($gym->tax_number)
                  ->setTaxPayer(TaxPayer::TAXPAYER_HAS_TAXNUMBER);

            $item = new InvoiceItem();
            $item->setName("Közvetített szolgáltatás - Belépések ($year/$month)")
                 ->setQuantity((float)$scanCount)
                 ->setQuantityUnit('alkalom')
                 ->setNetUnitPrice($netUnitPrice)
                 ->setNetPrice($netTotal)
                 ->setVat('27')
                 ->setVatAmount($vatAmount)
                 ->setGrossAmount($grossTotal);

            $invoice = new SzamlazzHuInvoice(SzamlazzHuInvoice::INVOICE_TYPE_E_INVOICE);
            
            // Fejléc beállítása setterekkel
            $invoice->getHeader()
                ->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_TRANSFER)
                ->setCurrency(Currency::HUF)
                ->setLanguage(Language::HU)
                ->setIssueDate(Carbon::now())
                ->setFulfillment(Carbon::now())
                ->setPaymentDue(Carbon::now()->addDays(8));

            $invoice->setBuyer($buyer);
            $invoice->addItem($item);

            $agent = SzamlaAgent::createWithAPIkey(config('szamlazzhu.api_key'), true);
            $response = $agent->generateInvoice($invoice);

            // PDF KEZELÉS (Opcionális letöltés)
            // A legtöbb verzióban a $response->invoiceNumber elérhető
            // A PDF tartalom ($response->pdf) csak bizonyos config beállítások mellett jön vissza
            
            // Megpróbáljuk kinyerni a számlaszámot

            if ($response->isSuccess()) {
                $invoiceNumber = $response->getInvoiceNumber();
                
                $pdfUrl = '';
                $pdfData = $response->getPdfFile(); 
            
                if (!empty($pdfData)) {
                    // Fájlnév tisztítása (perjelek kicserélése, ha lennének a számlaszámban)
                    $safeInvoiceNumber = str_replace(['/', '\\'], '-', $invoiceNumber);
                    $fileName = 'invoice_' . $safeInvoiceNumber . '.pdf';
                    $publicPath = 'invoices/' . $fileName;

                    // Mappa létrehozása, ha nem létezik
                    if (!Storage::disk('public')->exists('invoices')) {
                        Storage::disk('public')->makeDirectory('invoices');
                    }

                    Storage::disk('public')->put($publicPath, $pdfData);
                    $pdfUrl = Storage::url($publicPath);
                }

                $gym->invoices()->create([
                    'invoice_number' => $invoiceNumber,
                    'amount'         => $grossTotal,
                    'pdf_url'        => $pdfUrl,
                    'issue_date'     => now(),
                    'status'         => 'issued',
                ]);
                return back()->with('success', "Számla elkészült: $invoiceNumber");
            } else {
                return back()->with('error', 'Számlázz.hu hiba: ' . $response->getErrorMessage());
            }
        } catch (\Exception $e) {
            \Log::error('Számlázási hiba: ' . $e->getMessage());
            return back()->with('error', 'Számlázási hiba: ' . $e->getMessage());
        }
    }

    public function verifyStudent(Request $request, User $user)
    {
        $request->validate(['expiry_date' => 'required|date|after_or_equal:today']); 

        $user->student_id_verified = true; 
        $user->student_id_expiry = $request->expiry_date;
        
        $user->save();

        ActionLog::log('STUDENT_VERIFIED', "Diákigazolvány elfogadva: {$user->email}");
        return redirect()->back()->with('success', 'Diákigazolvány elfogadva.');
    }

    public function studentIds()
    {
        $users = User::whereNotNull('student_card_front')
            ->whereNotNull('student_card_back')
            ->where('student_id_verified', false)
            ->get();
        ActionLog::log('STUDENT_IDS_VIEW', 'Diákigazolvány ellenőrző oldal megnyitva');
        return view('admin.student_ids', compact('users'));
    }

    public function assignOwner(Request $request, Gym $gym)
    {
        $request->validate(['owner_id' => 'nullable|exists:users,id']);
        $gym->update(['owner_id' => $request->owner_id]);
        ActionLog::log('GYM_OWNER_ASSIGNED', "Tulajdonos frissítve: {$gym->name}");
        return back()->with('success', 'Partner tulajdonos frissítve.');
    }

    public function searchUsers(Request $request)
    {
        $search = $request->get('q','');
        ActionLog::log('USER_SEARCH', "Admin keresés: {$request->get('q')}");
        $users = User::with('gymPasses')
            ->where('first_name','like',"%{$search}%")
            ->orWhere('last_name','like',"%{$search}%")
            ->orWhere('email','like',"%{$search}%")
            ->paginate(5);

        if($request->ajax()) {
            return view('admin.partials.users_table', compact('users'))->render();
        }

        return redirect()->route('admin.dashboard');
    }

    public function usersForSelect(Request $request)
    {
        $search = $request->get('q','');
        ActionLog::log('USER_SELECT_SEARCH', "Select user keresés: {$request->get('q')}");
        $users = User::where('first_name','like',"%{$search}%")
            ->orWhere('last_name','like',"%{$search}%")
            ->orWhere('email','like',"%{$search}%")
            ->limit(20)
            ->get();

        return response()->json($users->map(function($user){
            return ['id'=>$user->id,'text'=>$user->last_name.' '.$user->first_name.' ('.$user->email.')'];
        }));
    }
}