
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameMatchController;

Route::view('/', 'home')->name('home');

Route::middleware('guest')->group(function () {

    Route::get('/inscription', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/inscription', [AuthController::class, 'register'])
        ->name('register.store');

    Route::get('/connexion', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/connexion', [AuthController::class, 'login'])
        ->name('login.store');
});

Route::middleware('auth')->group(function () {

    Route::post('/deconnexion', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/decouvrir', [GameMatchController::class, 'discover'])
        ->name('discover');

    Route::get('/favoris', [GameMatchController::class, 'favorites'])
        ->name('favorites');

    Route::post('/favoris/{player}', [GameMatchController::class, 'toggleFavorite'])
        ->name('favorites.toggle');

    Route::get('/mes-jeux', [GameMatchController::class, 'games'])
        ->name('games');

    Route::post('/mes-jeux', [GameMatchController::class, 'saveGames'])
        ->name('games.save');

    Route::get('/profil', [GameMatchController::class, 'profile'])
        ->name('profile');

    Route::post('/profil', [GameMatchController::class, 'saveProfile'])
        ->name('profile.save');

    Route::get('/messages', [GameMatchController::class, 'conversations'])
        ->name('messages');

    Route::get('/messages/{player}', [GameMatchController::class, 'conversation'])
        ->name('messages.show');

    Route::post('/messages/{player}', [GameMatchController::class, 'sendMessage'])
        ->name('messages.send');
});
