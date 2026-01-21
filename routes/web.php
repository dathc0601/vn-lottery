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

// Load more results API
Route::get('/api/load-more/{region}/{date}', [LotteryController::class, 'loadMoreResults'])
    ->where(['region' => 'xsmb|xsmt|xsmn', 'date' => '\d{2}-\d{2}-\d{4}'])
    ->name('lottery.loadMore');

// Day of week routes (must be before province routes)
Route::get('/{region}/{day}', [LotteryController::class, 'resultsByDayOfWeek'])
    ->where([
        'region' => 'xsmb|xsmt|xsmn',
        'day' => 'thu-2|thu-3|thu-4|thu-5|thu-6|thu-7|chu-nhat'
    ])
    ->name('lottery.byDayOfWeek');

// Individual province pages
Route::get('/{region}/{slug}', [LotteryController::class, 'provinceDetail'])
    ->where(['region' => 'xsmb|xsmt|xsmn'])
    ->name('province.detail');

// Other pages
Route::get('/so-ket-qua', [ResultsBookController::class, 'index'])->name('results.book');
// Statistics pages
Route::prefix('thong-ke')->group(function () {
    Route::get('/', [StatisticsController::class, 'index'])->name('statistics');
    Route::get('/tan-suat-loto', [StatisticsController::class, 'frequency'])->name('statistics.frequency');
    Route::get('/loto-gan', [StatisticsController::class, 'overdue'])->name('statistics.overdue');
    Route::get('/dau-duoi-loto', [StatisticsController::class, 'headTail'])->name('statistics.head-tail');
    Route::get('/thong-ke-nhanh', [StatisticsController::class, 'quick'])->name('statistics.quick');
    Route::get('/theo-tong', [StatisticsController::class, 'bySum'])->name('statistics.by-sum');
    Route::get('/quan-trong', [StatisticsController::class, 'important'])->name('statistics.important');
    Route::get('/dac-biet-tuan', [StatisticsController::class, 'weeklySpecial'])->name('statistics.weekly-special');
    Route::get('/dac-biet-thang', [StatisticsController::class, 'monthlySpecial'])->name('statistics.monthly-special');
});
Route::get('/do-ve-so', [TicketController::class, 'verify'])->name('ticket.verify');
Route::post('/do-ve-so', [TicketController::class, 'verify']);
Route::get('/lich-mo-thuong', [ScheduleController::class, 'index'])->name('schedule');
Route::get('/quay-thu-xo-so-hom-nay', [TrialDrawController::class, 'index'])->name('trial.draw');
Route::get('/quay-thu-xsmb', [TrialDrawController::class, 'xsmb'])->name('trial.xsmb');
Route::get('/quay-thu-xsmt', [TrialDrawController::class, 'xsmt'])->name('trial.xsmt');
Route::get('/quay-thu-xsmn', [TrialDrawController::class, 'xsmn'])->name('trial.xsmn');
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
