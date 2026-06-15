<?php

use App\Http\Controllers\Auth\OtpAuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('home');

Route::prefix('auth')->name('auth.')->group(function (): void {
    Route::get('/email', [OtpAuthController::class, 'showEmailForm'])->name('email');
    Route::post('/email', [OtpAuthController::class, 'sendOtp'])->name('send-otp');
    Route::get('/verify', [OtpAuthController::class, 'showVerifyForm'])->name('verify');
    Route::post('/verify', [OtpAuthController::class, 'verifyOtp'])->name('verify-otp');
    Route::post('/logout', [OtpAuthController::class, 'logout'])->name('logout');
});

Route::prefix('chat')->name('chat.')->group(function (): void {
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::get('/new', [ChatController::class, 'create'])->name('new');
    Route::post('/', [ChatController::class, 'store'])->name('store');
});
