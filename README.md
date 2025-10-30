<p align="center">
    <img src="images/logo.png" width="300" alt="RoamPass Logó">
</p>

<h1 align="center">✨ RoamPass - Konditerem Beléptető Rendszer</h1>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
</p>

---

## 🚀 A Projektről

A **RoamPass** egy modern, **Laravel** alapú webalkalmazás, amelyet kifejezetten konditermek és fitneszközpontok beléptetésének és jegykezelésének digitális kezelésére fejlesztettek ki. Célja a beléptetési folyamat gyorsítása és automatizálása, miközben biztonságosan kezeli a felhasználói adatokat és a bérleteket.

### Fő Funkciók

* **QR-kódos Azonosítás:** Gyors beolvasás a dedikált scanner felületen.
* **Alkalomlevonás:** Valós idejű bérlet- és alkalomszám-kezelés.
* **Dokumentumkezelés:** Diákigazolványok biztonságos feltöltése és jóváhagyása.
* **Adminisztrációs Felület:** Felhasználók, jegyek és bevételek központi menedzselése.
* **Konditermi Adminisztrációs Felület:** Saját kezelőpanel a konditerem tulajdonosoknak (bevétel megtekintése, saját alkalmazottak kezelése)
* **Laravel Breeze:** Letisztult, mobilra optimalizált felület.

---

## 🛠️ Telepítés

A projekt helyi futtatásához kövesd az alábbi lépéseket:

1.  **Klónozás:** `git clone [Tároló URL-je]`
2.  **Függőségek:** `composer install`
3.  **Előkészítés:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    php artisan migrate --seed
    php artisan storage:link # Fontos a feltöltött igazolványokhoz!
    ```
4.  **Frontend:**
    ```bash
    npm install
    npm run dev
    ```
5.  **Futtatás:** `php artisan serve`

---

## ✨ Laravel Kredit (Kivonat)

Ez a projekt a **Laravel** webalkalmazás keretrendszerre épül, amely elegáns és kifejező szintaktikát kínál. A Laravel megkönnyíti az olyan gyakori feladatokat, mint a gyors útválasztás, az Eloquent ORM adatbázis kezelés, és a robusztus háttérfolyamatok kezelése.

### További Információk

A Laravel teljes dokumentációja a [laravel.com/docs](https://laravel.com/docs) oldalon érhető el.

---

## 📜 Licenc

A Laravel keretrendszer **MIT Licenc** alatt érhető el.
