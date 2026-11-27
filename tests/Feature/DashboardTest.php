<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_authentication_and_ranks_filtered_players(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->getJson(route('players.index'))->assertUnauthorized();
        $values = [
            'games' => ['Valorant'], 'platform' => 'PC', 'level' => 'Intermédiaire',
            'language' => 'Français', 'availability_slots' => ['Soir'], 'play_style' => 'Détente',
        ];
        $viewer = User::factory()->create($values);
        $match = User::factory()->create([...$values, 'level' => 'Avancé', 'name' => 'Nova', 'bio' => 'Joue en soirée']);
        User::factory()->create([...$values, 'platform' => 'Xbox', 'name' => 'Orion']);
        User::factory()->create();

        $this->actingAs($viewer)->get(route('dashboard'))->assertOk()->assertSee('Trouve tes coéquipiers');
        $this->getJson(route('players.index'))
            ->assertJsonPath('total', 2)
            ->assertJsonPath('players.0.id', $match->id)
            ->assertJsonPath('players.0.score', 100)
            ->assertJsonPath('players.1.score', 60)
            ->assertJsonMissingPath('players.0.email');
        $this->getJson(route('players.index', ['game' => 'Valorant', 'platform' => 'PC', 'slot' => 'Soir', 'language' => 'Français', 'search' => 'Nov']))
            ->assertJsonPath('total', 1)->assertJsonPath('players.0.bio', 'Joue en soirée');
        $this->getJson(route('players.index', ['slot' => 'Matin']))->assertJsonPath('total', 0);
        $this->getJson(route('players.index', ['game' => 'inconnu']))->assertUnprocessable();
    }

    public function test_contacts_and_reports_are_persisted_and_authorized(): void
    {
        $sender = User::factory()->create(['games' => ['Minecraft']]);
        $receiver = User::factory()->create(['games' => ['Minecraft']]);
        $outsider = User::factory()->create(['games' => ['Minecraft']]);
        $this->actingAs($sender)->postJson(route('players.contact', $sender))->assertNotFound();
        $this->postJson(route('players.contact', $receiver))->assertCreated();
        $this->postJson(route('players.contact', $receiver))->assertConflict();
        $this->actingAs($receiver)->postJson(route('players.contact', $sender))->assertConflict();
        $this->get(route('dashboard'))->assertOk()->assertSee('souhaite jouer avec toi');

        $this->actingAs($outsider)->patch(route('contacts.respond', 1), ['status' => 'accepted'])->assertNotFound();
        $this->actingAs($sender)->patch(route('contacts.respond', 1), ['status' => 'accepted'])->assertNotFound();
        $this->actingAs($receiver)->patch(route('contacts.respond', 1), ['status' => 'accepted'])->assertRedirect(route('dashboard').'#contacts');
        $this->assertDatabaseHas('player_contacts', ['sender_id' => $sender->id, 'status' => 'accepted']);

        $this->actingAs($sender)->postJson(route('players.report', $receiver), ['reason' => 'Spam', 'details' => 'Exemple'])->assertCreated();
        $this->assertDatabaseHas('player_reports', ['reporter_id' => $sender->id, 'player_id' => $receiver->id, 'reason' => 'Spam']);
        $this->postJson(route('players.report', $receiver), ['reason' => 'inconnu'])->assertUnprocessable();
    }
}
