<?php

namespace App\Services;

use App\Models\User;

final class Matchmaking
{
    public const GAMES = [
        'Valorant' => null, 'Fortnite' => null, 'Minecraft' => null, 'Marvel Rivals' => 2767030,
        'League of Legends' => null, 'Rocket League' => 252950, 'Apex Legends' => 1172470,
        'Call of Duty' => 1938090, 'Counter-Strike 2' => 730,
    ];

    public const PLATFORMS = ['PC', 'PlayStation', 'Xbox', 'Nintendo Switch', 'Mobile'];
    public const LEVELS = ['Débutant', 'Intermédiaire', 'Avancé', 'Expert'];
    public const STYLES = ['Compétitif', 'Détente', 'Coopération', 'Progression'];
    public const SLOTS = ['Matin', 'Après-midi', 'Soir', 'Week-end'];
    public const LANGUAGES = ['Français', 'Anglais', 'Espagnol', 'Allemand', 'Italien', 'Portugais'];
    public const REASONS = ['Spam', 'Harcèlement', 'Faux profil', 'Lien suspect', 'Autre'];

    public static function score(User $viewer, User $player): int
    {
        $left = array_search($viewer->level, self::LEVELS, true);
        $right = array_search($player->level, self::LEVELS, true);

        // Adjacent ranks are close enough to play together. Free-form availability remains informational.
        $similarLevel = $left !== false && $right !== false && abs($left - $right) <= 1;

        return (array_intersect($viewer->games ?? [], $player->games ?? []) ? 20 : 0)
            + ($viewer->platform && $viewer->platform === $player->platform ? 40 : 0)
            + ($similarLevel ? 10 : 0)
            + (array_intersect($viewer->availability_slots ?? [], $player->availability_slots ?? []) ? 30 : 0);
    }
}
