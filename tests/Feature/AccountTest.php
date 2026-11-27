<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_and_login_use_the_same_account(): void
    {
        $password = 'un-mot-de-passe-solide';
        $this->get(route('register'))->assertOk()->assertSee('Créer un compte');
        $this->post(route('register'), $this->registration($password))->assertRedirect(route('profile'));
        $user = User::where('email', 'joueur@example.test')->firstOrFail();
        $this->assertTrue(Hash::check($password, $user->password));
        $this->assertAuthenticatedAs($user);
        $this->get(route('profile'))->assertOk()->assertSee('NovaRush');

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
        $this->get(route('profile'))->assertRedirect(route('login'));
        $this->post(route('login'), ['email' => $user->email, 'password' => 'incorrect'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->post(route('login'), ['email' => $user->email, 'password' => $password])->assertRedirect(route('profile'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_rejects_an_existing_email_and_a_failed_captcha(): void
    {
        $input = $this->registration('un-mot-de-passe-solide');
        User::factory()->create(['email' => mb_strtolower($input['email'])]);
        $this->post(route('register'), $input)->assertSessionHasErrors('email');

        config(['services.turnstile.secret' => 'test-secret']);
        Http::fake(['challenges.cloudflare.com/*' => Http::sequence()->push(['success' => false])->push(['success' => true])]);
        $input['email'] = 'nouveau@example.test';
        $this->post(route('register'), $input)->assertSessionHasErrors('cf-turnstile-response');
        $input['cf-turnstile-response'] = 'token-refuse';
        $this->post(route('register'), $input)->assertSessionHasErrors('cf-turnstile-response');
        $this->assertDatabaseMissing('users', ['email' => $input['email']]);

        $this->post(route('register'), $input)->assertRedirect(route('profile'));
        $this->assertDatabaseHas('users', ['email' => $input['email']]);
    }

    public function test_profile_changes_are_persisted_for_the_signed_in_player_only(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $profile = [
            'name' => 'NovaRush',
            'age_group' => '18–24 ans',
            'platform' => 'PC',
            'games' => ['Valorant', 'Rocket League'],
            'level' => 'Avancé',
            'play_style' => 'Détente',
            'availability' => 'Le mercredi soir',
            'id' => $other->id,
        ];
        $this->patch(route('profile'), $profile)->assertRedirect(route('login'));
        $this->actingAs($user)->patch(route('profile'), $profile)->assertRedirect(route('profile'));
        $this->assertSame($profile['games'], $user->fresh()->games);
        $this->assertSame($profile['availability'], $user->fresh()->availability);
        $this->assertSame($other->name, $other->fresh()->name);
        $this->assertNull($other->fresh()->games);

        unset($profile['games']);
        $this->patch(route('profile'), $profile)->assertRedirect(route('profile'));
        $this->assertSame([], $user->fresh()->games);
    }

    private function registration(string $password): array
    {
        return [
            'name' => 'NovaRush',
            'email' => 'Joueur@example.test',
            'password' => $password,
            'password_confirmation' => $password,
            'age_group' => '18–24 ans',
            'platform' => 'PC',
        ];
    }
}
