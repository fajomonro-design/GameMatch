<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

// Page d'accueil
Route::get('/', function () {
    return view('home');
})->name('home');

// Choix entre connexion et inscription
Route::get('/rejoindre', function () {
    return view('auth.choice');
})->name('join');

// Redirection après connexion ou inscription
Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware('auth')->name('dashboard');

// Afficher le formulaire de profil
Route::get('/profil', function () {
    return view('profile.edit', [
        'user' => Auth::user(),
    ]);
})->middleware('auth')->name('profile.edit');

// Enregistrer les modifications du profil
Route::post('/profil', function (Request $request) {
    $user = Auth::user();

    $validated = $request->validate([
        'gamer_name' => [
            'required',
            'string',
            'min:2',
            'max:30',
            Rule::unique('users', 'gamer_name')->ignore($user->id),
        ],
        'avatar' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:2048',
        ],
    ], [
        'gamer_name.required' => 'Choisis un pseudo gamer.',
        'gamer_name.min' => 'Le pseudo doit contenir au moins 2 caractères.',
        'gamer_name.max' => 'Le pseudo ne peut pas dépasser 30 caractères.',
        'gamer_name.unique' => 'Ce pseudo est déjà utilisé.',
        'avatar.image' => 'Le fichier doit être une image.',
        'avatar.mimes' => 'Utilise une image JPG, PNG ou WEBP.',
        'avatar.max' => 'La photo ne doit pas dépasser 2 Mo.',
    ]);

    $user->gamer_name = $validated['gamer_name'];

    if ($request->hasFile('avatar')) {
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->avatar_path = $request->file('avatar')
            ->store('avatars', 'public');
    }

    /** @var \App\Models\User $user */
$user = Auth::user();

if (!$user) {
    abort(401);
}

$user->save();

    return redirect()
        ->route('home')
        ->with('success', 'Ton profil a bien été mis à jour !');
})->middleware('auth')->name('profile.update');

// Tableau de bord administrateur
Route::get('/admin', function () {
    $user = Auth::user();

    abort_unless($user && $user->is_admin, 403);

    return view('admin.dashboard', [
        'totalUsers' => User::count(),
        'adminUsers' => User::where('is_admin', true)->count(),
        'recentUsers' => User::latest()->take(5)->get(),
    ]);
})->middleware('auth')->name('admin.dashboard');