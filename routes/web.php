<?php

use App\Http\Controllers\Admin\CommentsController as AdminCommentsController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\RankingsController as AdminRankingsController;
use App\Http\Controllers\Admin\RulesController as AdminRulesController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\UhcWinnersController as AdminUhcWinnersController;
use App\Http\Controllers\Admin\UsersController as AdminUsersController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlayerProfileController;
use App\Http\Controllers\RankingsController;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\ServerInfoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Community Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/server-info', [ServerInfoController::class, 'index'])->name('server-info');
Route::get('/rules', [RulesController::class, 'index'])->name('rules');
Route::get('/rankings', [RankingsController::class, 'index'])->name('rankings');
Route::get('/players', [PlayerProfileController::class, 'index'])->name('players.index');
Route::get('/players/{gamertag}', [PlayerProfileController::class, 'show'])->name('players.show');
Route::get('/api/server-status', [ServerInfoController::class, 'statusJson'])->name('api.server-status');
Route::post('/api/server/verify-player', [\App\Http\Controllers\PlayerVerificationController::class, 'verifyFromGame'])->name('api.server.verify-player');

/*
|--------------------------------------------------------------------------
| Member Action Routes
|--------------------------------------------------------------------------
*/
Route::post('/news/{post:slug}/comments', [CommentController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');

/*
|--------------------------------------------------------------------------
| Authenticated Account & Profile Routes
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    if (auth()->user()?->isStaff()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('home');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // In-Game Player Verification Actions
    Route::post('/profile/verify/generate', [\App\Http\Controllers\PlayerVerificationController::class, 'generate'])->name('profile.verify.generate');
    Route::get('/profile/verify/status', [\App\Http\Controllers\PlayerVerificationController::class, 'status'])->name('profile.verify.status');
    Route::post('/profile/verify/unlink', [\App\Http\Controllers\PlayerVerificationController::class, 'unlink'])->name('profile.verify.unlink');
});

/*
|--------------------------------------------------------------------------
| Protected Admin & Moderator Routes (Strict RBAC)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,moderator'])->prefix('admin')->name('admin.')->group(function () {
    // Shared Staff Dashboard & Comment Moderation
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/comments', [AdminCommentsController::class, 'index'])->name('comments.index');
    Route::post('/comments/{comment}/toggle', [AdminCommentsController::class, 'toggle'])->name('comments.toggle');
    Route::delete('/comments/{comment}', [AdminCommentsController::class, 'destroy'])->name('comments.destroy');

    // Admin-Only Routes
    Route::middleware('role:admin')->group(function () {
        // News Management
        Route::resource('news', AdminNewsController::class)->except(['show']);

        // Server Rules Management
        Route::get('/rules', [AdminRulesController::class, 'index'])->name('rules.index');
        Route::post('/rules', [AdminRulesController::class, 'store'])->name('rules.store');
        Route::put('/rules/{rule}', [AdminRulesController::class, 'update'])->name('rules.update');
        Route::delete('/rules/{rule}', [AdminRulesController::class, 'destroy'])->name('rules.destroy');

        // Leaderboard Rankings Management
        Route::get('/rankings', [AdminRankingsController::class, 'index'])->name('rankings.index');
        Route::post('/rankings/players', [AdminRankingsController::class, 'storePlayer'])->name('rankings.players.store');
        Route::put('/rankings/players/{player}', [AdminRankingsController::class, 'updatePlayer'])->name('rankings.players.update');
        Route::delete('/rankings/players/{player}', [AdminRankingsController::class, 'destroyPlayer'])->name('rankings.players.destroy');

        Route::post('/rankings/clans', [AdminRankingsController::class, 'storeClan'])->name('rankings.clans.store');
        Route::put('/rankings/clans/{clan}', [AdminRankingsController::class, 'updateClan'])->name('rankings.clans.update');
        Route::delete('/rankings/clans/{clan}', [AdminRankingsController::class, 'destroyClan'])->name('rankings.clans.destroy');

        // UHC Tournament Winners Management
        Route::resource('uhc', AdminUhcWinnersController::class)->except(['show']);

        // Site Settings Management
        Route::get('/settings', [AdminSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
    });

    // Super Admin Exclusive Routes: Account Manipulation, Password Reset, Role Promotion
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/users', [AdminUsersController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [AdminUsersController::class, 'updateRole'])->name('users.role');
        Route::patch('/users/{user}/password', [AdminUsersController::class, 'updatePassword'])->name('users.password');
        Route::patch('/users/{user}/email', [AdminUsersController::class, 'updateEmail'])->name('users.email');
        Route::post('/users/{user}/unlink-gamertag', [AdminUsersController::class, 'unlinkGamertag'])->name('users.unlink-gamertag');
        Route::post('/users/{user}/toggle-suspension', [AdminUsersController::class, 'toggleSuspension'])->name('users.toggle-suspension');
    });
});

require __DIR__.'/auth.php';
