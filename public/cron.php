<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

// 1. Hibakeresés bekapcsolása a teszt idejére
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2. Pontos útvonal a roampass mappához
$laravelRoot = __DIR__ . '/../roampass/';


try {
    // 3. Alapkövek betöltése
    if (!file_exists($laravelRoot . 'vendor/autoload.php')) {
        throw new Exception("Nem találom a vendor mappát! Útvonal: " . realpath($laravelRoot));
    }
    
    require $laravelRoot . 'vendor/autoload.php';
    $app = require_once $laravelRoot . 'bootstrap/app.php';

    // 4. A Kernel példányosítása ÉS beüzemelése (Bootstrap)
    // Ez a lépés kapcsolja össze a Facade-okat az alkalmazással
    $kernel = $app->make(Kernel::class);
    $kernel->bootstrap(); 

    echo "<h1>RoamPass Karbantartó</h1>";

    // 5. A parancs futtatása
    // Itt a saját parancsod nevét használd
    $status = Artisan::call('gympass:check-expiration'); 

    echo "<h3>A folyamat lefutott!</h3>";
    echo "Státusz kód: $status <br>";
    echo "Kimenet: <pre>" . Artisan::output() . "</pre>";

} catch (\Exception $e) {
    echo "<h1>Hiba történt!</h1>";
    echo "Üzenet: " . $e->getMessage();
}