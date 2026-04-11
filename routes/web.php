<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\GymPassController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminpanelController;
use App\Http\Controllers\PartnerDashboardController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PartnerApplicationController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');
Route::get('/partners/{partner}', [PartnerController::class, 'show'])->name('partners.show');

Route::post('/partner-apply', [PartnerApplicationController::class, 'store'])->name('partner.apply');

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
    Route::post('/profile/newsletter', [ProfileController::class, 'toggleNewsletter'])->name('profile.newsletter');
    Route::get('/partner/dashboard', [PartnerDashboardController::class, 'index'])->name('partner.dashboard');
    Route::delete('/partner/scanner/delete', [PartnerDashboardController::class, 'destroyScanner'])->name('partner.scanner.destroy');
    Route::post('/partner/scanner/store', [PartnerDashboardController::class, 'storeScanner'])->name('partner.scanner.store');
    Route::post('/gyms/{gym}/review', [ReviewController::class, 'store'])->name('gyms.review.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::get('/passes/{pass}/qr', [App\Http\Controllers\GymPassController::class, 'renderDynamicQr'])->name('passes.dynamic-qr');
    Route::get('/profile/export', [ProfileController::class, 'exportData'])->name('profile.export');

});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function() {
    Route::get('/', [AdminpanelController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [AdminpanelController::class, 'users'])->name('admin.users');
    Route::get('/gyms', [AdminpanelController::class, 'gyms'])->name('admin.gyms');
    Route::delete('/gyms/{gym}', [AdminpanelController::class, 'deleteGym'])->name('admin.deleteGym');
    Route::post('/gym/store', [AdminpanelController::class, 'storeGym'])->name('admin.storeGym');
    Route::post('/user/{user}/update-pass', [AdminpanelController::class, 'updatePass'])->name('admin.updatePass');
    Route::delete('/user/{user}', [AdminpanelController::class, 'deleteUser'])->name('admin.deleteUser');
    Route::get('/student-ids', [AdminpanelController::class, 'studentIds'])->name('admin.studentIds');
    Route::post('/user/{user}/verify-student', [AdminpanelController::class, 'verifyStudent'])->name('admin.verifyStudent');
    Route::post('/admin/gyms/{gym}/assign-owner', [AdminPanelController::class, 'assignOwner'])->name('admin.assignOwner');
    Route::get('/admin/users/search', [AdminpanelController::class, 'searchUsers'])->name('admin.users.search');

    Route::get('/admin/users/search', [AdminpanelController::class, 'searchUsers'])->name('admin.users.search');
    Route::get('/admin/users/select2', [AdminpanelController::class, 'usersForSelect'])->name('admin.users.select2');
    Route::put('/admin/gyms/{gym}', [AdminpanelController::class, 'updateGym'])->name('admin.updateGym');
    Route::post('/gyms/{gym}/generate-invoice', [AdminpanelController::class, 'generateInvoice'])->name('admin.generateInvoice');
    Route::post('/admin/settings/update', [AdminpanelController::class, 'updateSettings'])->name('admin.settings.update');
    Route::get('/admin/logs/download', [AdminpanelController::class, 'downloadLogs'])->name('admin.logs.download');
    Route::get('/admin/logs', [AdminpanelController::class, 'logs'])->name('admin.logs');
    Route::get('/admin/newsletter', [AdminpanelController::class, 'newsletterView'])->name('admin.newsletter');
    Route::post('/admin/newsletter/send', [AdminpanelController::class, 'sendNewsletter'])->name('admin.newsletter.send');
    Route::post('/admin/trigger-expiration-check', [AdminpanelController::class, 'triggerExpirationCheck'])->name('admin.triggerExpirationCheck');
});

Route::middleware(['auth', 'scanner'])->group(function () {

    // Scanner dashboard
    Route::get('/scanner/dashboard', [ScannerController::class, 'index'])
        ->name('scanner.dashboard');

    // QR kód beolvasás (AJAX hívás)
    Route::post('/scanner/scan', [ScannerController::class, 'scanUser'])
        ->name('scanner.scan');

    Route::post('/scanner/cancel', [ScannerController::class, 'cancelScan'])->name('scanner.cancel');

});

require __DIR__.'/auth.php';
