<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;
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
});

require __DIR__.'/auth.php';
