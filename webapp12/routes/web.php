<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TotpController;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'totp'])
    ->name('dashboard');

Route::middleware(['auth', 'totp'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::middleware('auth')->group(function () {
    Route::get('totp/setup', [TotpController::class, 'setup'])->name('totp.setup');
    Route::post('totp/setup', [TotpController::class, 'confirm'])->middleware('throttle:5,1')->name('totp.confirm');
    Route::get('totp/challenge', [TotpController::class, 'challenge'])->name('totp.challenge');
    Route::post('totp/challenge', [TotpController::class, 'verify'])->middleware('throttle:5,1')->name('totp.verify');
});

require __DIR__.'/auth.php';
