<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public SoundBridge Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/explore', [\App\Http\Controllers\SearchController::class, 'explore'])->name('explore');

// Authentication Routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify', [AuthController::class, 'showVerify'])->name('verify.show');
Route::post('/verify', [AuthController::class, 'verify'])->name('verify');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::any('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/recover', [AuthController::class, 'showRecover'])->name('recover.show');
Route::post('/recover', [AuthController::class, 'recover'])->name('recover');
Route::get('/reset', [AuthController::class, 'showReset'])->name('reset.show');
Route::post('/reset', [AuthController::class, 'reset'])->name('reset');
// Language Switcher Route
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'sw'])) {
        session(['locale' => $locale]);
    }
    // Safe redirect fallback to home page if previous page is the same route
    $previous = redirect()->getUrlGenerator()->previous();
    if ($previous === url()->current() || empty($previous)) {
        return redirect()->to('/');
    }
    return redirect()->back(302, [], '/');
})->name('locale.switch');

// Protected Routes (Required Authentication)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Management (FR4-FR5)
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.password');
    Route::post('/profile/settings', [\App\Http\Controllers\ProfileController::class, 'updateSettings'])->name('profile.settings');
    Route::post('/profile/deactivate', [\App\Http\Controllers\ProfileController::class, 'deactivate'])->name('profile.deactivate');
    Route::post('/profile/upgrade', [\App\Http\Controllers\PaymentController::class, 'processUpgrade'])->name('profile.upgrade');
    
    // Artist Verification & Profiles (FR6-FR7)
    Route::post('/artist/verify', [\App\Http\Controllers\ArtistController::class, 'submitVerification'])->name('artist.verify');
    Route::post('/artist/profile', [\App\Http\Controllers\ArtistController::class, 'updateProfile'])->name('artist.profile.update');
    Route::post('/artist/store', [\App\Http\Controllers\ArtistController::class, 'storeArtist'])->name('artist.store');
    
    // Catalog Management (FR8)
    Route::get('/catalogue', [\App\Http\Controllers\ArtistController::class, 'catalogue'])->name('catalogue');

    // Music Distribution (Wizard) (FR9-FR15)
    Route::get('/releases/create', [\App\Http\Controllers\ReleaseController::class, 'create'])->name('releases.create');
    Route::post('/releases/store', [\App\Http\Controllers\ReleaseController::class, 'store'])->name('releases.store');
    Route::get('/releases/{release}', [\App\Http\Controllers\ReleaseController::class, 'show'])->name('releases.show');
    Route::get('/releases/{release}/edit', [\App\Http\Controllers\ReleaseController::class, 'edit'])->name('releases.edit');
    Route::post('/releases/{release}/update', [\App\Http\Controllers\ReleaseController::class, 'update'])->name('releases.update');
    Route::post('/releases/{release}/takedown', [\App\Http\Controllers\ReleaseController::class, 'takedown'])->name('releases.takedown');
    Route::post('/releases/{release}/pay', [\App\Http\Controllers\ReleaseController::class, 'processPayment'])->name('releases.pay');
    
    // Search (FR31-FR33)
    Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search');

    // Finance & Royalties (FR21-FR25)
    Route::get('/finance', [\App\Http\Controllers\PaymentController::class, 'financeIndex'])->name('finance');
    Route::post('/finance/withdraw', [\App\Http\Controllers\PaymentController::class, 'requestWithdrawal'])->name('finance.withdraw');
    Route::post('/finance/payout-account', [\App\Http\Controllers\PaymentController::class, 'updatePayoutAccount'])->name('finance.payout_account');
    Route::get('/finance/invoice/{payment}', [\App\Http\Controllers\PaymentController::class, 'viewInvoice'])->name('finance.invoice');
    Route::get('/finance/withdrawal/invoice/{withdrawal}', [\App\Http\Controllers\PaymentController::class, 'viewWithdrawalInvoice'])->name('finance.withdrawal.invoice');
    Route::post('/finance/subscribe', [\App\Http\Controllers\PaymentController::class, 'subscribePremium'])->name('finance.subscribe');
    
    // Analytics (FR26-FR30)
    Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/report', [\App\Http\Controllers\AnalyticsController::class, 'generateReport'])->name('analytics.report');

    // Admin Module (FR38-FR43)
    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function () {
        Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin');
        
        // Admin - User Management
        Route::get('/admin/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
        Route::post('/admin/users/{user}/status', [\App\Http\Controllers\AdminController::class, 'updateUserStatus'])->name('admin.users.status');
        
        // Admin - Catalog Verification & Reviews
        Route::get('/admin/releases', [\App\Http\Controllers\AdminController::class, 'releases'])->name('admin.releases');
        Route::post('/admin/releases/{release}/review', [\App\Http\Controllers\AdminController::class, 'reviewRelease'])->name('admin.releases.review');
        Route::get('/admin/artists', [\App\Http\Controllers\AdminController::class, 'artists'])->name('admin.artists');
        Route::post('/admin/artists/{artist}/verify', [\App\Http\Controllers\AdminController::class, 'verifyArtist'])->name('admin.artists.verify');
        
        // Admin - Payments & Withdrawals
        Route::get('/admin/payments', [\App\Http\Controllers\AdminController::class, 'payments'])->name('admin.payments');
        Route::post('/admin/withdrawals/{withdrawal}/status', [\App\Http\Controllers\AdminController::class, 'updateWithdrawalStatus'])->name('admin.withdrawals.status');
        Route::post('/admin/platform-payout-account', [\App\Http\Controllers\AdminController::class, 'updatePlatformPayoutAccount'])->name('admin.platform_payout_account');
        
        // Admin - System Monitoring & Logs
        Route::get('/admin/logs', [\App\Http\Controllers\AdminController::class, 'logs'])->name('admin.logs');
        Route::get('/admin/reports', [\App\Http\Controllers\AdminController::class, 'reports'])->name('admin.reports');
    });

});
