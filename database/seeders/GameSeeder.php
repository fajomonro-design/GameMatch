<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            'VALORANT',
            'League of Legends',
            'Minecraft',
            'Fortnite',
            'Rocket League',
            'Counter-Strike 2',
            'Overwatch 2',
            'Marvel Rivals',
            'Apex Legends',
            'EA SPORTS FC',
            'Call of Duty',
            'GTA Online',
            'Rainbow Six Siege',
            'Dead by Daylight',
            'Roblox',
        ] as $name) {
            Game::firstOrCreate(['name' => $name]);
        }
    }
}
