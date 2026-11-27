<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Matchmaking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function show(Request $request): View
    {
        $id = $request->user()->id;
        $contacts = $this->pairs($id)
            ->join('users', fn ($join) => $join->on('users.id', '=', DB::raw(
                'case when player_contacts.player_one_id = '.(int) $id.' then player_contacts.player_two_id else player_contacts.player_one_id end'
            )))
            ->orderByDesc('player_contacts.updated_at')
            ->get(['player_contacts.id', 'sender_id', 'status', 'users.name']);

        return view('dashboard', [
            'player' => $request->user(),
            'games' => Matchmaking::GAMES,
            'reasons' => Matchmaking::REASONS,
            'filters' => [
                'platform' => ['Plateforme', Matchmaking::PLATFORMS], 'level' => ['Niveau', Matchmaking::LEVELS],
                'play_style' => ['Style', Matchmaking::STYLES], 'slot' => ['Créneau', Matchmaking::SLOTS],
                'language' => ['Langue', Matchmaking::LANGUAGES],
            ],
            'contacts' => $contacts,
            'notifications' => $contacts->filter(fn ($contact) =>
                ($contact->status === 'pending' && $contact->sender_id !== $id)
                || ($contact->status !== 'pending' && $contact->sender_id === $id)),
        ]);
    }

    public function players(Request $request): JsonResponse
    {
        $viewer = $request->user();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:80'],
            'game' => ['nullable', Rule::in(array_keys(Matchmaking::GAMES))],
            'platform' => ['nullable', Rule::in(Matchmaking::PLATFORMS)],
            'level' => ['nullable', Rule::in(Matchmaking::LEVELS)],
            'play_style' => ['nullable', Rule::in(Matchmaking::STYLES)],
            'language' => ['nullable', Rule::in(Matchmaking::LANGUAGES)],
            'slot' => ['nullable', Rule::in(Matchmaking::SLOTS)],
            'page' => ['nullable', 'integer', 'min:1', 'max:10000'],
        ]);
        $query = User::query()->whereKeyNot($viewer->id)->whereJsonLength('games', '>', 0);
        if (!empty($filters['search'])) $query->whereLike('name', '%'.$filters['search'].'%');
        if (!empty($filters['game'])) $query->whereJsonContains('games', $filters['game']);
        if (!empty($filters['slot'])) $query->whereJsonContains('availability_slots', $filters['slot']);
        foreach (['platform', 'level', 'play_style', 'language'] as $field) {
            if (!empty($filters[$field])) $query->where($field, $filters[$field]);
        }

        $rows = $query->get(['id', 'name', 'games', 'platform', 'level', 'play_style', 'language', 'bio', 'availability', 'availability_slots'])
            ->map(fn (User $player) => [...$player->getAttributes(),
                'games' => $player->games, 'availability_slots' => $player->availability_slots ?? [],
                'score' => Matchmaking::score($viewer, $player),
            ])->sortBy([['score', 'desc'], ['name', 'asc'], ['id', 'asc']])->values();

        $page = (int) ($filters['page'] ?? 1);
        $visible = $rows->forPage($page, 12)->values();
        $pairs = $this->pairs($viewer->id)->get()->keyBy(
            fn ($contact) => (int) $contact->player_one_id === $viewer->id ? $contact->player_two_id : $contact->player_one_id
        );

        return response()->json([
            'players' => $visible->map(fn (array $player) => [
                ...$player, 'contact_status' => $pairs->get($player['id'])?->status,
                'contact_url' => route('players.contact', $player['id']),
                'report_url' => route('players.report', $player['id']),
            ]),
            'total' => $rows->count(),
            'next_page' => $rows->count() > $page * 12 ? $page + 1 : null,
        ])->header('Cache-Control', 'private, no-store');
    }

    public function contact(Request $request, User $player): JsonResponse
    {
        $viewer = $request->user();
        abort_if($viewer->is($player) || !$player->games, 404);
        $pair = ['player_one_id' => min($viewer->id, $player->id), 'player_two_id' => max($viewer->id, $player->id)];
        $now = now();
        $created = DB::table('player_contacts')->insertOrIgnore([
            ...$pair, 'sender_id' => $viewer->id, 'status' => 'pending', 'created_at' => $now, 'updated_at' => $now,
        ]);
        if (!$created) {
            return response()->json(['message' => 'Une demande existe déjà pour ce joueur.'], 409);
        }

        return response()->json(['message' => 'Demande envoyée.', 'status' => 'pending'], 201);
    }

    public function respond(Request $request, int $contact): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(['accepted', 'declined'])]]);
        $id = $request->user()->id;
        $updated = $this->pairs($id)->where('player_contacts.id', $contact)->where('sender_id', '!=', $id)
            ->where('status', 'pending')
            ->update(['status' => $data['status'], 'updated_at' => now()]);
        abort_unless($updated, 404);

        return redirect(route('dashboard').'#contacts')->with('status', 'Ta réponse a été enregistrée.');
    }

    public function report(Request $request, User $player): JsonResponse
    {
        abort_if($request->user()->is($player) || !$player->games, 404);
        $data = $request->validate([
            'reason' => ['required', Rule::in(Matchmaking::REASONS)],
            'details' => ['nullable', 'string', 'max:500'],
        ]);
        DB::table('player_reports')->insert([
            ...$data, 'reporter_id' => $request->user()->id, 'player_id' => $player->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Signalement enregistré.'], 201);
    }

    private function pairs(int $userId): \Illuminate\Database\Query\Builder
    {
        return DB::table('player_contacts')->where(fn ($query) =>
            $query->where('player_one_id', $userId)->orWhere('player_two_id', $userId));
    }
}
