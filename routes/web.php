<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/inscription', [AccountController::class, 'registration'])->name('register');
    Route::post('/inscription', [AccountController::class, 'register'])->middleware('throttle:10,1');
    Route::view('/connexion', 'account.login')->name('login');
    Route::post('/connexion', [AccountController::class, 'login'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');
    Route::get('/dashboard/joueurs', [DashboardController::class, 'players'])->name('players.index');
    Route::post('/dashboard/joueurs/{player}/contact', [DashboardController::class, 'contact'])->middleware('throttle:20,1')->name('players.contact');
    Route::post('/dashboard/joueurs/{player}/signalement', [DashboardController::class, 'report'])->middleware('throttle:5,1')->name('players.report');
    Route::patch('/dashboard/contacts/{contact}', [DashboardController::class, 'respond'])->name('contacts.respond');
    Route::get('/profil', [AccountController::class, 'profile'])->name('profile');
    Route::patch('/profil', [AccountController::class, 'update']);
    Route::post('/deconnexion', [AccountController::class, 'logout'])->name('logout');
});
