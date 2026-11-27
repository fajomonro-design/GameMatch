<?php

namespace App\Services;

use App\Models\User;

final class Matchmaking
{
    public const GAMES = [
        'Valorant' => 'images/games/valorant.webp',
        'Fortnite' => 'images/games/fortnite.webp',
        'Minecraft' => 'images/games/minecraft.webp',
        'Marvel Rivals' => 'images/games/marvel-rivals.webp',
        'League of Legends' => 'images/games/league-of-legends.webp',
        'Rocket League' => 'images/games/rocket-league.webp',
        'Apex Legends' => 'images/games/apex-legends.webp',
        'Call of Duty' => 'images/games/call-of-duty.webp',
        'Counter-Strike 2' => 'images/games/counter-strike-2.webp',
    ];

    public const PLATFORMS = ['PC', 'PlayStation', 'Xbox', 'Nintendo Switch', 'Mobile'];
    public const LEVELS = ['Débutant', 'Intermédiaire', 'Avancé', 'Expert'];
    public const STYLES = ['Compétitif', 'Détente', 'Coopération', 'Progression'];
    public const SLOTS = ['Matin', 'Après-midi', 'Soir', 'Week-end'];
    public const LANGUAGES = ['Français', 'Anglais', 'Espagnol', 'Allemand', 'Italien', 'Portugais'];
    public const REASONS = ['Spam', 'Harcèlement', 'Faux profil', 'Lien suspect', 'Autre'];

    public static function breakdown(User $viewer, User $player): array
    {
        $left = array_search($viewer->level, self::LEVELS, true);
        $right = array_search($player->level, self::LEVELS, true);

        // Adjacent ranks are close enough to play together. Free-form availability remains informational.
        $similarLevel = $left !== false && $right !== false && abs($left - $right) <= 1;

        return [
            'game' => array_intersect($viewer->games ?? [], $player->games ?? []) ? 20 : 0,
            'platform' => $viewer->platform && $viewer->platform === $player->platform ? 40 : 0,
            'level' => $similarLevel ? 10 : 0,
            'availability' => array_intersect($viewer->availability_slots ?? [], $player->availability_slots ?? []) ? 30 : 0,
        ];
    }
}
