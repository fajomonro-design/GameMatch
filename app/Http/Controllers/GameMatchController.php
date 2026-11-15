<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GameMatchController extends Controller
{
    public function discover(Request $request)
    {
        $query = User::query()
            ->where('id', '!=', $request->user()->id)
            ->with('games');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($request->filled('game')) {
            $gameId = (int) $request->input('game');

            $query->whereHas('games', function ($q) use ($gameId) {
                $q->where('games.id', $gameId);
            });
        }

        if ($request->filled('platform')) {
            $query->where('platform', $request->input('platform'));
        }

        $players = $query->latest()
            ->paginate(12)
            ->withQueryString();

        $favoriteIds = $request->user()
            ->favorites()
            ->pluck('users.id')
            ->all();

        return view('discover', compact('players', 'favoriteIds'));
    }

    public function favorites(Request $request)
    {
        $players = $request->user()
            ->favorites()
            ->with('games')
            ->latest('favorites.created_at')
            ->get();

        return view('favorites', compact('players'));
    }

    public function toggleFavorite(Request $request, User $player)
    {
        abort_if($player->id === $request->user()->id, 422);

        $favorites = $request->user()->favorites();

        if ($favorites->where('users.id', $player->id)->exists()) {
            $favorites->detach($player->id);
            $message = 'Joueur retiré des favoris.';
        } else {
            $favorites->attach($player->id);
            $message = 'Joueur ajouté aux favoris.';
        }

        return back()->with('success', $message);
    }

    public function games(Request $request)
    {
        $games = Game::orderBy('name')->get();

        $selected = $request->user()
            ->games()
            ->pluck('games.id')
            ->all();

        return view('games', compact('games', 'selected'));
    }

    public function saveGames(Request $request)
    {
        $data = $request->validate([
            'games' => ['array', 'max:15'],
            'games.*' => ['integer', 'distinct', 'exists:games,id'],
        ]);

        $request->user()
            ->games()
            ->sync($data['games'] ?? []);

        return back()->with('success', 'Tes jeux ont été mis à jour.');
    }

    public function profile(Request $request)
    {
        $games = Game::orderBy('name')->get();

        return view('profile', compact('games'));
    }

    public function saveProfile(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'name')
                    ->ignore($request->user()->id),
            ],
            'bio' => ['nullable', 'string', 'max:500'],
            'platform' => [
                'nullable',
                Rule::in(['PC', 'PS5', 'Xbox', 'Switch', 'Mobile']),
            ],
            'play_style' => [
                'nullable',
                Rule::in(['Détente', 'Compétitif', 'Coopération']),
            ],
            'level' => [
                'nullable',
                Rule::in(['Débutant', 'Intermédiaire', 'Avancé', 'Expert']),
            ],
            'availability' => ['nullable', 'string', 'max:80'],
        ]);

        $request->user()->update($data);

        return back()->with('success', 'Profil mis à jour.');
    }

    public function conversations(Request $request)
    {
        $myId = $request->user()->id;

        $contactIds = Message::where('sender_id', $myId)
            ->orWhere('receiver_id', $myId)
            ->get()
            ->map(function ($message) use ($myId) {
                return $message->sender_id === $myId
                    ? $message->receiver_id
                    : $message->sender_id;
            })
            ->unique()
            ->values();

        $contacts = User::whereIn('id', $contactIds)
            ->orderBy('name')
            ->get();

        return view('messages.index', compact('contacts'));
    }

    public function conversation(Request $request, User $player)
    {
        $myId = $request->user()->id;

        abort_if($player->id === $myId, 404);

        $messages = Message::where(function ($query) use ($myId, $player) {
            $query->where('sender_id', $myId)
                ->where('receiver_id', $player->id);
        })
        ->orWhere(function ($query) use ($myId, $player) {
            $query->where('sender_id', $player->id)
                ->where('receiver_id', $myId);
        })
        ->orderBy('id')
        ->limit(200)
        ->get();

        return view('messages.show', compact('player', 'messages'));
    }

    public function sendMessage(Request $request, User $player)
    {
        abort_if($player->id === $request->user()->id, 422);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $player->id,
            'body' => $data['body'],
        ]);

        return redirect()
            ->route('messages.show', $player)
            ->with('success', 'Message envoyé.');
    }
}
