<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/inscription', [AccountController::class, 'registration'])->name('register');
    Route::post('/inscription', [AccountController::class, 'register'])->middleware('throttle:10,1');
    Route::view('/connexion', 'account.login')->name('login');
    Route::post('/connexion', [AccountController::class, 'login'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/profil', [AccountController::class, 'profile'])->name('profile');
    Route::patch('/profil', [AccountController::class, 'update']);
    Route::post('/deconnexion', [AccountController::class, 'logout'])->name('logout');
});
