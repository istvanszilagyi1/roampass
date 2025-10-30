<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\GymPassController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminpanelController;
use App\Http\Controllers\PartnerDashboardController;
use App\Http\Controllers\ScannerController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');
Route::get('/partners/{partner}', [PartnerController::class, 'show'])->name('partners.show');

Route::middleware(['auth'])->group(function () {

    // Saját bérletek
    Route::get('/my-passes', [GymPassController::class, 'index'])->name('passes.index');

    // Bérlet vásárlás
    Route::get('/buy-pass', [GymPassController::class, 'create'])->name('passes.create');
    Route::post('/buy-pass', [GymPassController::class, 'store'])->name('passes.store');

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::get('/partner/dashboard', [PartnerDashboardController::class, 'index'])->name('partner.dashboard');
    Route::delete('/partner/scanner/delete', [PartnerDashboardController::class, 'destroyScanner'])->name('partner.scanner.destroy');
    Route::post('/partner/scanner/store', [PartnerDashboardController::class, 'storeScanner'])->name('partner.scanner.store');

});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function() {
    Route::get('/', [AdminpanelController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [AdminpanelController::class, 'users'])->name('admin.users');
    Route::get('/gyms', [AdminpanelController::class, 'gyms'])->name('admin.gyms');
    Route::post('/gym/store', [AdminpanelController::class, 'storeGym'])->name('admin.storeGym');
    Route::post('/user/{user}/update-pass', [AdminpanelController::class, 'updatePass'])->name('admin.updatePass');
    Route::delete('/user/{user}', [AdminpanelController::class, 'deleteUser'])->name('admin.deleteUser');
    Route::get('/student-ids', [AdminpanelController::class, 'studentIds'])->name('admin.studentIds');
    Route::post('/user/{user}/verify-student', [AdminpanelController::class, 'verifyStudent'])->name('admin.verifyStudent');
    Route::post('/admin/gyms/{gym}/assign-owner', [AdminPanelController::class, 'assignOwner'])->name('admin.gyms.assignOwner');
});

Route::middleware(['auth', 'scanner'])->group(function () {

    // Scanner dashboard
    Route::get('/scanner/dashboard', [ScannerController::class, 'index'])
        ->name('scanner.dashboard');

    // QR kód beolvasás (AJAX hívás)
    Route::post('/scanner/scan', [ScannerController::class, 'scanUser'])
        ->name('scanner.scan');
});

require __DIR__.'/auth.php';
