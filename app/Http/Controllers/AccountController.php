<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Matchmaking;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AccountController extends Controller
{
    private const AGES = ['Moins de 18 ans', '18–24 ans', '25–34 ans', '35 ans et plus'];

    public function registration(): View
    {
        return view('account.register', ['ages' => self::AGES, 'platforms' => Matchmaking::PLATFORMS]);
    }

    public function register(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:24'],
            'email' => ['required', 'email', 'max:254', Rule::unique(User::class)],
            'password' => ['bail', 'required', 'string', 'min:12', 'confirmed',
                function (string $attribute, string $value, \Closure $fail): void {
                    if (strlen($value) > 72) {
                        $fail('Le mot de passe est trop long. Réduis le nombre de caractères.');
                    }
                },
            ],
            'age_group' => ['required', Rule::in(self::AGES)],
            'platform' => ['required', Rule::in(Matchmaking::PLATFORMS)],
        ]);
        $this->verifyCaptcha($request);

        try {
            $user = User::create($data);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['email' => 'Cette adresse e-mail est déjà utilisée.']);
        }
        Auth::login($user);
        $request->session()->regenerate();

        return to_route('dashboard')->with('status', 'Ton compte GameMatch a été créé.');
    }

    public function login(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:254'],
            'password' => ['required', 'string', 'max:72'],
        ]);
        if (strlen($credentials['password']) > 72 || !Auth::attempt($credentials)) {
            throw ValidationException::withMessages(['email' => 'Adresse e-mail ou mot de passe incorrect.']);
        }
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('home');
    }

    public function profile(Request $request): View
    {
        return view('account.profile', [
            'player' => $request->user(),
            'options' => [
                'age_group' => ['Tranche d’âge', self::AGES],
                'platform' => ['Plateforme principale', Matchmaking::PLATFORMS],
                'level' => ['Niveau', Matchmaking::LEVELS],
                'play_style' => ['Style de jeu', Matchmaking::STYLES],
                'language' => ['Langue de jeu', Matchmaking::LANGUAGES],
            ],
            'games' => array_keys(Matchmaking::GAMES),
            'slots' => Matchmaking::SLOTS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:24'],
            'age_group' => ['required', Rule::in(self::AGES)],
            'platform' => ['required', Rule::in(Matchmaking::PLATFORMS)],
            'games' => ['nullable', 'array', 'list', 'max:9'],
            'games.*' => ['required', 'distinct', Rule::in(array_keys(Matchmaking::GAMES))],
            'level' => ['nullable', Rule::in(Matchmaking::LEVELS)],
            'play_style' => ['nullable', Rule::in(Matchmaking::STYLES)],
            'language' => ['nullable', Rule::in(Matchmaking::LANGUAGES)],
            'bio' => ['nullable', 'string', 'max:500'],
            'availability_slots' => ['nullable', 'array', 'list', 'max:4'],
            'availability_slots.*' => ['distinct', Rule::in(Matchmaking::SLOTS)],
            'availability' => ['nullable', 'string', 'max:240'],
        ]);
        $data['games'] ??= [];
        $data['availability_slots'] ??= [];
        $request->user()->update($data);

        return to_route('profile')->with('status', 'Ton profil a été enregistré.');
    }

    private function normalizeEmail(Request $request): void
    {
        if (is_string($request->input('email'))) {
            $request->merge(['email' => mb_strtolower($request->input('email'))]);
        }
    }

    private function verifyCaptcha(Request $request): void
    {
        $secret = config('services.turnstile.secret');
        if (!$secret && app()->environment('local', 'testing')) {
            return;
        }
        $token = $request->validate(['cf-turnstile-response' => ['required', 'string', 'max:2048']],
            ['cf-turnstile-response.required' => 'Valide le contrôle anti-robot.'])['cf-turnstile-response'];
        try {
            $valid = $secret && Http::asForm()->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', ['secret' => $secret, 'response' => $token])
                ->json('success') === true;
        } catch (ConnectionException) {
            $valid = false;
        }
        if (!$valid) {
            throw ValidationException::withMessages(['cf-turnstile-response' => 'Le contrôle anti-robot a échoué. Réessaie.']);
        }
    }
}
