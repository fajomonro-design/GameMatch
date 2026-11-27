<?php

namespace App\Services;

use App\Models\User;

final class Matchmaking
{
    public const GAMES = [
        'Valorant' => 'https://cmsassets.rgpub.io/sanity/images/dsfx7636/news/cbf4460132cdfeb2a97fad5f9dd25ba0bc058f76-128x128.png',
        'Fortnite' => 'https://cms-assets.unrealengine.com/cm6l5gfpm05kr07my04cqgy2x/cmfo4y74z03yw07ohsjgs0qj7',
        'Minecraft' => 'https://www.minecraft.net/content/dam/minecraftnet/franchise/logos/minecraft-creeper-face.jpg',
        'Marvel Rivals' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/2767030/library_600x900.jpg',
        'League of Legends' => 'https://cmsassets.rgpub.io/sanity/images/dsfx7636/content_organization_live/bdc123b2cae1ff76fea465b287240cab8f3208a7-1200x630.jpg',
        'Rocket League' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/252950/library_600x900.jpg',
        'Apex Legends' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/1172470/library_600x900.jpg',
        'Call of Duty' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/1938090/library_600x900.jpg',
        'Counter-Strike 2' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/730/library_600x900.jpg',
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
