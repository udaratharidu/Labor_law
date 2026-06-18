<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LawController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('home');

    Route::prefix('chat')->name('chat.')->group(function (): void {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/new', [ChatController::class, 'create'])->name('new');
        Route::post('/', [ChatController::class, 'store'])->name('store');
    });

    Route::prefix('laws')->name('laws.')->group(function (): void {
        Route::get('/', [LawController::class, 'index'])->name('index');
        Route::get('/{act}', [LawController::class, 'show'])->name('show');
    });
});

require __DIR__.'/auth.php';
