<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LotteryController;
use App\Http\Controllers\ResultsBookController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TrialDrawController;
use App\Http\Controllers\VietlottController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Regional lottery pages
Route::get('/xsmb/{date?}', [LotteryController::class, 'xsmb'])
    ->name('xsmb')
    ->where('date', '\d{2}-\d{2}-\d{4}');
Route::get('/xsmt/{date?}', [LotteryController::class, 'xsmt'])
    ->name('xsmt')
    ->where('date', '\d{2}-\d{2}-\d{4}');
Route::get('/xsmn/{date?}', [LotteryController::class, 'xsmn'])
    ->name('xsmn')
    ->where('date', '\d{2}-\d{2}-\d{4}');

// Individual province pages
Route::get('/{region}/{slug}', [LotteryController::class, 'provinceDetail'])
    ->where(['region' => 'xsmb|xsmt|xsmn'])
    ->name('province.detail');

// Other pages
Route::get('/so-ket-qua', [ResultsBookController::class, 'index'])->name('results.book');
Route::get('/thong-ke', [StatisticsController::class, 'index'])->name('statistics');
Route::get('/do-ve-so', [TicketController::class, 'verify'])->name('ticket.verify');
Route::post('/do-ve-so', [TicketController::class, 'verify']);
Route::get('/lich-mo-thuong', [ScheduleController::class, 'index'])->name('schedule');
Route::get('/quay-thu-xo-so-hom-nay', [TrialDrawController::class, 'index'])->name('trial.draw');
Route::get('/xo-so-vietlott', [VietlottController::class, 'index'])->name('vietlott');

// Admin authentication routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
